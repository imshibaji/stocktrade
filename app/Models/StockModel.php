<?php

namespace App\Models;

use App\Libraries\YahooFinanceService;
use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table = 'stocks';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'symbol', 'name', 'sector', 'current_price', 'previous_close',
        'market_cap', 'avg_volume', 'pe_ratio', 'week_52_high',
        'week_52_low', 'dividend_yield', 'beta', 'exchange'
    ];
    protected $useTimestamps = true;

    public function searchStocks(string $query, int $limit = 50)
    {
        $query = trim($query);
        if ($query === '') {
            return $this->limit($limit)->findAll();
        }

        $escaped = $this->db->escapeLikeString($query);
        $upper = strtoupper($query);

        $exact = $this->where('symbol', $upper)
            ->limit($limit)
            ->findAll();

        if (count($exact) >= $limit) {
            return $exact;
        }

        $this->builder()->resetQuery();

        $prefix = $this->like('symbol', $query, 'after')
            ->orLike('name', $query, 'after')
            ->limit($limit)
            ->findAll();

        $seen = [];
        $merged = [];

        foreach ($exact as $s) {
            $id = (int) $s['id'];
            if (!isset($seen[$id])) {
                $seen[$id] = true;
                $merged[] = $s;
            }
        }

        foreach ($prefix as $s) {
            $id = (int) $s['id'];
            if (!isset($seen[$id])) {
                $seen[$id] = true;
                $merged[] = $s;
            }
        }

        if (count($merged) >= $limit) {
            return array_slice($merged, 0, $limit);
        }

        $this->builder()->resetQuery();

        $substr = $this->like('symbol', $query)
            ->orLike('name', $query)
            ->orLike('sector', $query)
            ->limit($limit)
            ->findAll();

        foreach ($substr as $s) {
            $id = (int) $s['id'];
            if (!isset($seen[$id])) {
                $seen[$id] = true;
                $merged[] = $s;
            }
        }

        return $merged;
    }

    public function searchWithYahooFallback(string $query, int $limit = 20): array
    {
        $local = $this->searchStocks($query, $limit);

        if (count($local) >= $limit) {
            return $local;
        }

        $localSymbols = array_column($local, 'symbol');

        $yahoo = new YahooFinanceService();
        try {
            $yahooResults = $yahoo->getSearch($query);
            foreach ($yahooResults as $result) {
                $sym = $result->getSymbol();
                $localSym = \App\Libraries\YahooFinanceService::fromYahooSymbol($sym);
                if (in_array($localSym, $localSymbols, true)) {
                    continue;
                }
                if (count($local) >= $limit) {
                    break;
                }
                $local[] = [
                    'id'             => null,
                    'symbol'         => $localSym,
                    'name'           => $result->getName() ?? $localSym,
                    'sector'         => $result->getTypeDisp() ?? 'N/A',
                    'current_price'  => $result->getPrice(),
                    'previous_close' => $result->getPreviousClose(),
                    'market_cap'     => $result->getMarketCap(),
                    'avg_volume'     => $result->getAvgVolume(),
                    'pe_ratio'       => $result->getPeRatio(),
                    'week_52_high'   => $result->getWeek52High(),
                    'week_52_low'    => $result->getWeek52Low(),
                    'dividend_yield' => $result->getDividendYield(),
                    'beta'           => $result->getBeta(),
                    'exchange'       => $result->getExch(),
                    'from_yahoo'     => true,
                ];
                $localSymbols[] = $localSym;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Yahoo search error: ' . $e->getMessage());
        }

        return $local;
    }

    public function getWithPriceHistory(int $stockId, int $days = 90)
    {
        $stock = $this->find($stockId);
        if (!$stock) return null;

        $priceModel = new StockPriceModel();
        $stock['price_history'] = $priceModel
            ->where('stock_id', $stockId)
            ->orderBy('price_date', 'ASC')
            ->limit($days)
            ->findAll();

        $predictionModel = new PredictionModel();
        $stock['predictions'] = $predictionModel
            ->where('stock_id', $stockId)
            ->where('predicted_date >=', date('Y-m-d'))
            ->orderBy('predicted_date', 'ASC')
            ->findAll();

        return $stock;
    }
}
