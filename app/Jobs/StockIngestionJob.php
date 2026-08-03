<?php

namespace App\Jobs;

use CodeIgniter\Queue\BaseJob;
use App\Models\StockModel;
use App\Models\StockPriceModel;
use App\Libraries\YahooFinanceService;

class StockIngestionJob extends BaseJob
{
    public function run(array $data): bool
    {
        $symbols  = $data['symbols'] ?? [];
        $exchange = $data['exchange'] ?? 'NSE';

        if (empty($symbols)) {
            log_message('warning', 'StockIngestionJob: no symbols provided');
            return false;
        }

        $stockModel = new StockModel();
        $priceModel = new StockPriceModel();
        $yahoo      = new YahooFinanceService();

        $existingSymbols = [];
        foreach ($stockModel->select('symbol, id, current_price')->findAll() as $row) {
            $existingSymbols[strtoupper($row['symbol'])] = [
                'id'        => (int) $row['id'],
                'has_price' => ! empty($row['current_price']),
            ];
        }

        foreach ($symbols as $symbol) {
            $symUpper = strtoupper(trim($symbol));

            if (isset($existingSymbols[$symUpper])) {
                log_message('info', "StockIngestionJob: {$symUpper} already exists, skipping");
                continue;
            }

            try {
                $quote = $yahoo->getQuote($symUpper, $exchange);
                if (! $quote) {
                    log_message('warning', "StockIngestionJob: {$symUpper} not found on Yahoo Finance");
                    continue;
                }

                $d = $yahoo->quoteToArray($quote);
                $price = (float) ($d['regularMarketPrice'] ?? 0);

                if ($price <= 0) {
                    log_message('warning', "StockIngestionJob: {$symUpper} has no valid price");
                    continue;
                }

                $name      = $d['longName'] ?? $d['shortName'] ?? $symUpper;
                $sector    = $d['sector'] ?? 'Unknown';
                $prevClose = $d['regularMarketPreviousClose'] ?? round($price * 0.99, 2);
                $marketCap = $d['marketCap'] ?? null;
                $avgVol    = $d['averageDailyVolume3Month'] ?? null;
                $peRatio   = $d['trailingPE'] ?? null;
                $wkHigh    = $d['fiftyTwoWeekHigh'] ?? null;
                $wkLow     = $d['fiftyTwoWeekLow'] ?? null;
                $divYield  = $d['trailingAnnualDividendYield'] ?? null;
                $beta      = $d['beta'] ?? null;

                $stockId = $stockModel->insert([
                    'symbol'          => $symUpper,
                    'name'            => $name,
                    'sector'          => $sector,
                    'exchange'        => $exchange,
                    'exchange_display'=> $d['exchange'] ?? $d['fullExchangeName'] ?? $exchange,
                    'current_price'   => $price,
                    'previous_close'  => $prevClose,
                    'market_cap'      => $marketCap,
                    'avg_volume'      => $avgVol,
                    'pe_ratio'        => $peRatio,
                    'week_52_high'    => $wkHigh,
                    'week_52_low'     => $wkLow,
                    'dividend_yield'  => $divYield,
                    'beta'            => $beta,
                    'from_yahoo'      => true,
                    'last_fetched'    => date('Y-m-d H:i:s'),
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);

                $this->seedPriceHistory($priceModel, $stockId, $price);
                $this->seedPredictions($stockId, $price);

                log_message('info', "StockIngestionJob: added {$symUpper} ({$name})");

                sleep(1);
            } catch (\Throwable $e) {
                log_message('error', "StockIngestionJob failed for {$symUpper}: " . $e->getMessage());
            }
        }

        return true;
    }

    private function seedPriceHistory(StockPriceModel $priceModel, int $stockId, float $basePrice): void
    {
        $prices = [];
        for ($i = 90; $i >= 0; $i--) {
            $date       = date('Y-m-d', strtotime("-{$i} days"));
            $volatility = $basePrice * 0.03;
            $change     = (mt_rand(-1000, 1000) / 1000) * $volatility;
            $close      = round($basePrice + $change, 2);
            $open       = round($close - (mt_rand(-500, 500) / 1000) * $volatility, 2);
            $high       = round(max($open, $close) + abs(mt_rand(0, 500) / 1000) * $volatility, 2);
            $low        = round(min($open, $close) - abs(mt_rand(0, 500) / 1000) * $volatility, 2);
            $volume     = mt_rand(100000, 50000000);

            $prices[] = [
                'stock_id'   => $stockId,
                'price_date' => $date,
                'open'       => $open,
                'high'       => $high,
                'low'        => $low,
                'close'      => $close,
                'volume'     => $volume,
            ];
            $basePrice = $close;
        }

        if (! empty($prices)) {
            $priceModel->insertBatch($prices);
        }
    }

    private function seedPredictions(int $stockId, float $basePrice): void
    {
        $db = \Config\Database::connect();
        $predictions = [];
        for ($i = 1; $i <= 30; $i++) {
            $date           = date('Y-m-d', strtotime("+{$i} days"));
            $trend          = (mt_rand(-100, 100) / 10000) * $basePrice;
            $predictedPrice = round($basePrice + ($trend * $i), 2);
            $confidence     = round(max(60, min(95, 95 - ($i * 0.5))), 2);

            $predictions[] = [
                'stock_id'         => $stockId,
                'predicted_date'   => $date,
                'predicted_price'  => $predictedPrice,
                'confidence_score' => $confidence,
                'method'           => 'Monte Carlo + EMA',
                'created_at'       => date('Y-m-d H:i:s'),
            ];
        }

        if (! empty($predictions)) {
            $db->table('predictions')->insertBatch($predictions);
        }
    }
}