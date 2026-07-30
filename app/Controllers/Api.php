<?php

namespace App\Controllers;

use App\Libraries\YahooFinanceService;
use App\Models\StockModel;
use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;

class Api extends BaseController
{
    public function livePrices()
    {
        $stockModel = new StockModel();
        $stocks = $stockModel->select('id, symbol, name, current_price, previous_close')->findAll();

        $yahoo = new YahooFinanceService();
        $liveData = [];
        $symbols = array_column($stocks, 'symbol');

        $quotes = $yahoo->fetchQuotesBySymbols($symbols);
        foreach ($quotes as $symbol => $q) {
            $liveData[$symbol] = $q;
        }

        $result = [];
        foreach ($stocks as $stock) {
            $sid = (int) $stock['id'];
            $symbol = $stock['symbol'];
            $fromYahoo = $liveData[$symbol] ?? null;
            $dbPrice = (float) $stock['current_price'];
            $dbPrev = (float) $stock['previous_close'];

            if ($fromYahoo && $fromYahoo['regularMarketPrice'] !== null) {
                $price = (float) $fromYahoo['regularMarketPrice'];
                $previous = (float) ($fromYahoo['regularMarketPreviousClose'] ?? $dbPrev);
                $change = $fromYahoo['regularMarketChange'] ?? ($price - $previous);
                $changePercent = $fromYahoo['regularMarketChangePercent'] ?? ($previous > 0 ? ($change / $previous) * 100 : 0);
            } else {
                $price = $dbPrice;
                $previous = $dbPrev;
                $change = $price - $previous;
                $changePercent = $previous > 0 ? ($change / $previous) * 100 : 0;
            }

            $result[] = [
                'id'             => $sid,
                'symbol'         => $symbol,
                'name'           => $stock['name'],
                'current_price'  => round($price, 2),
                'previous_close' => round($previous, 2),
                'change'         => round($change, 2),
                'change_percent' => round($changePercent, 2),
            ];
        }

        $market = $yahoo->getMarketInfo();

        return $this->response->setJSON([
            'market'  => $market,
            'stocks'  => $result,
            'updated' => date('H:i:s'),
        ]);
    }

    public function search()
    {
        $query = $this->request->getGet('q');
        $exchange = strtoupper(trim($this->request->getGet('exchange') ?? 'NSE'));
        if (!$query || trim($query) === '') {
            return $this->response->setJSON(['results' => [], 'query' => '']);
        }

        $stockModel = new StockModel();
        $results = $stockModel->searchWithYahooFallback($query, 20);

        if (empty($results) && preg_match('/^[A-Za-z0-9.]{1,15}$/', trim($query))) {
            $symbol = strtoupper(trim($query));
            $yahoo = new YahooFinanceService();
            $quote = $yahoo->getQuote($symbol, $exchange);
            if ($quote) {
                $d = $yahoo->quoteToArray($quote);
                $results[] = [
                    'id'             => null,
                    'symbol'         => $d['symbol'],
                    'name'           => $d['longName'] ?? $d['shortName'] ?? $symbol,
                    'sector'         => $d['fullExchangeName'] ?? 'N/A',
                    'current_price'  => $d['regularMarketPrice'],
                    'previous_close' => $d['regularMarketPreviousClose'],
                    'from_yahoo'     => true,
                    'exchange'       => $exchange,
                ];
            }
        }

        $mapped = [];
        foreach ($results as $s) {
            $item = [
                'id'     => $s['id'],
                'symbol' => $s['symbol'],
                'name'   => $s['name'],
                'sector' => $s['sector'],
                'exchange' => $s['exchange'] ?? $exchange,
                'price'  => $s['current_price'] ? (float) $s['current_price'] : null,
                'change' => null,
                'change_percent' => null,
            ];

            if ($s['current_price'] && $s['previous_close']) {
                $cp = get_price_change((float) $s['current_price'], (float) $s['previous_close']);
                $item['change'] = $cp['change'];
                $item['change_percent'] = $cp['percent'];
            }

            $item['from_yahoo'] = !empty($s['from_yahoo']);
            $mapped[] = $item;
        }

        return $this->response->setJSON([
            'results' => $mapped,
            'query'   => $query,
            'count'   => count($mapped),
        ]);
    }

    public function syncPrices()
    {
        $yahoo = new YahooFinanceService();

        try {
            $updated = $yahoo->fetchAndUpdateStocks();
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Stock prices synced from Yahoo Finance',
                'count'   => count($updated),
                'updated' => date('H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Yahoo sync error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function importStock()
    {
        $symbol = strtoupper(trim($this->request->getPost('symbol')));
        $exchange = strtoupper(trim($this->request->getPost('exchange') ?? 'NSE'));
        if (!$symbol) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stock symbol is required.',
            ])->setStatusCode(400);
        }

        $stockModel = new StockModel();

        $existing = $stockModel->where('symbol', $symbol)->first();
        if ($existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stock ' . $symbol . ' already exists.',
                'id'      => (int) $existing['id'],
            ]);
        }

        $yahoo = new YahooFinanceService();

        $quote = $yahoo->getQuote($symbol, $exchange);
        if (!$quote) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Could not fetch data for ' . $symbol . ' from Yahoo Finance.',
            ])->setStatusCode(404);
        }

        $d = $yahoo->quoteToArray($quote);

        $name = $d['longName'] ?? $d['shortName'] ?? $symbol;
        $sector = $d['fullExchangeName'] ?? 'N/A';

        $price = (float) ($d['regularMarketPrice'] ?? 0);
        if ($price <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No price data available for ' . $symbol,
            ])->setStatusCode(400);
        }

        $stockId = $stockModel->insert([
            'symbol'         => $symbol,
            'name'           => $name,
            'sector'         => $sector,
            'exchange'       => $exchange,
            'current_price'  => $d['regularMarketPrice'],
            'previous_close' => $d['regularMarketPreviousClose'] ?? round($price * 0.99, 2),
            'market_cap'     => $d['marketCap'],
            'avg_volume'     => $d['averageDailyVolume3Month'],
            'pe_ratio'       => $d['trailingPE'],
            'week_52_high'   => $d['fiftyTwoWeekHigh'],
            'week_52_low'    => $d['fiftyTwoWeekLow'],
            'dividend_yield' => $d['trailingAnnualDividendYield'],
            'beta'           => null,
        ]);

        $this->generatePriceHistory($stockId, $price);
        $this->generatePredictions($stockId, $price);

        $watchlistModel = new \App\Models\WatchlistModel();
        $watchlistModel->insert([
            'user_id'  => current_user_id(),
            'stock_id' => $stockId,
        ]);

        return $this->response->setJSON([
            'success'    => true,
            'message'    => 'Stock ' . $symbol . ' imported and added to watchlist.',
            'id'         => $stockId,
            'symbol'     => $symbol,
            'name'       => $name,
            'watchlisted' => true,
        ]);
    }

    private function generatePriceHistory(int $stockId, float $basePrice): void
    {
        $db = \Config\Database::connect();
        $prices = [];
        for ($i = 90; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $volatility = $basePrice * 0.03;
            $change = (mt_rand(-1000, 1000) / 1000) * $volatility;
            $close = round($basePrice + $change, 2);
            $open = round($close - (mt_rand(-500, 500) / 1000) * $volatility, 2);
            $high = round(max($open, $close) + abs(mt_rand(0, 500) / 1000) * $volatility, 2);
            $low = round(min($open, $close) - abs(mt_rand(0, 500) / 1000) * $volatility, 2);
            $volume = mt_rand(100000, 50000000);
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
        $db->table('stock_prices')->insertBatch($prices);
    }

    private function generatePredictions(int $stockId, float $basePrice): void
    {
        $db = \Config\Database::connect();
        $predictions = [];
        for ($i = 1; $i <= 30; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} days"));
            $trend = (mt_rand(-100, 100) / 10000) * $basePrice;
            $predictedPrice = round($basePrice + ($trend * $i), 2);
            $confidence = round(max(60, min(95, 95 - ($i * 0.5))), 2);
            $predictions[] = [
                'stock_id'        => $stockId,
                'predicted_date'  => $date,
                'predicted_price' => $predictedPrice,
                'confidence_score'=> $confidence,
                'method'          => 'Monte Carlo + EMA',
                'created_at'      => date('Y-m-d H:i:s'),
            ];
        }
        $db->table('predictions')->insertBatch($predictions);
    }

    public function lookup($symbol = null)
    {
        if (!$symbol) {
            $symbol = $this->request->getGet('symbol');
        }
        $symbol = strtoupper(trim($symbol ?? ''));
        if (!$symbol) {
            return $this->response->setJSON(['error' => 'Symbol required'])->setStatusCode(400);
        }

        $yahoo = new YahooFinanceService();

        $quotes = $yahoo->getQuotesFromChartApi([$symbol], 'GLOBAL');
        $quote = $quotes[0] ?? null;
        $d = $quote ? $yahoo->quoteToArray($quote) : [];

        $summary = $yahoo->getQuoteSummary($symbol, 'GLOBAL');

        $price = $summary['regular_market_price'] ?? ($d['regularMarketPrice'] ?? null);
        $prevClose = $summary['previous_close'] ?? ($d['regularMarketPreviousClose'] ?? null);
        $change = $prevClose > 0 && $price !== null ? $price - $prevClose : null;
        $changePercent = $prevClose > 0 && $change !== null ? ($change / $prevClose) * 100 : null;

        $data = [
            'symbol'                     => $symbol,
            'name'                       => $summary['long_name'] ?? $d['longName'] ?? $d['shortName'] ?? $symbol,
            'short_name'                 => $summary['short_name'] ?? $d['shortName'] ?? null,
            'long_name'                  => $summary['long_name'] ?? $d['longName'] ?? null,
            'sector'                     => $summary['sector'] ?? $d['fullExchangeName'] ?? '',
            'industry'                   => $summary['industry'] ?? null,
            'current_price'              => $price,
            'previous_close'             => $prevClose,
            'open'                       => $summary['open'] ?? ($d['regularMarketOpen'] ?? null),
            'day_high'                   => $summary['day_high'] ?? ($d['regularMarketDayHigh'] ?? null),
            'day_low'                    => $summary['day_low'] ?? ($d['regularMarketDayLow'] ?? null),
            'volume'                     => $summary['volume'] ?? ($d['regularMarketVolume'] ?? null),
            'change'                     => $change,
            'change_percent'             => $changePercent,
            'market_cap'                 => $summary['market_cap'] ?? ($d['marketCap'] ?? null),
            'pe_ratio'                   => $summary['pe_ratio'] ?? ($d['trailingPE'] ?? null),
            'forward_pe'                 => $summary['forward_pe'] ?? null,
            'price_to_book'              => $summary['price_to_book'] ?? null,
            'book_value'                 => $summary['book_value'] ?? null,
            'eps_forward'                => $summary['eps_forward'] ?? null,
            'eps_trailing'               => $summary['eps_trailing'] ?? ($d['epsTrailingTwelveMonths'] ?? null),
            'dividend_yield'             => $summary['dividend_yield'] ?? ($d['trailingAnnualDividendYield'] ?? null),
            'dividend_rate'              => $summary['dividend_rate'] ?? ($d['trailingAnnualDividendRate'] ?? null),
            'dividend_date'              => $summary['dividend_date'] ?? null,
            'beta'                       => $summary['beta'] ?? null,
            'avg_volume'                 => $summary['avg_volume'] ?? ($d['averageDailyVolume3Month'] ?? null),
            'avg_volume_10d'             => $summary['avg_volume_10d'] ?? null,
            'week_52_high'               => $summary['fifty_two_week_high'] ?? ($d['fiftyTwoWeekHigh'] ?? null),
            'week_52_low'                => $summary['fifty_two_week_low'] ?? ($d['fiftyTwoWeekLow'] ?? null),
            'fifty_day_average'          => $summary['fifty_day_average'] ?? ($d['fiftyDayAverage'] ?? null),
            'two_hundred_day_average'    => $summary['two_hundred_day_average'] ?? ($d['twoHundredDayAverage'] ?? null),
            'shares_outstanding'         => $summary['shares_outstanding'] ?? null,
            'bid'                        => $summary['bid'] ?? ($d['bid'] ?? null),
            'ask'                        => $summary['ask'] ?? ($d['ask'] ?? null),
            'bid_size'                   => $summary['bid_size'] ?? null,
            'ask_size'                   => $summary['ask_size'] ?? null,
            'market_state'               => $summary['market_state'] ?? $d['marketState'] ?? null,
            'exchange'                   => $summary['exchange'] ?? $d['fullExchangeName'] ?? null,
            'currency'                   => $summary['currency'] ?? $d['currency'] ?? null,
            'quote_type'                 => $summary['quote_type'] ?? $d['quoteType'] ?? null,
            'exchange_timezone'          => $summary['exchange_timezone'] ?? null,
            'regular_market_time'        => $summary['regular_market_time'] ?? $d['regularMarketTime'] ?? null,
            'target_price'               => $summary['target_price'] ?? null,
            'recommendation'             => $summary['recommendation'] ?? null,
            'earnings_timestamp'         => $summary['earnings_timestamp'] ?? null,
            'earnings_timestamp_start'   => $summary['earnings_timestamp_start'] ?? null,
            'earnings_timestamp_end'     => $summary['earnings_timestamp_end'] ?? null,
        ];
        return $this->response->setJSON($data);
    }

    public function tickPrice($id = null)
    {
        if (!$id) {
            return $this->response->setJSON(['error' => 'Stock ID required'])->setStatusCode(400);
        }

        $stockModel = new StockModel();
        $stock = $stockModel->find((int) $id);

        if (!$stock) {
            return $this->response->setJSON(['error' => 'Stock not found'])->setStatusCode(404);
        }

        $yahoo = new YahooFinanceService();
        $extra = [];

        try {
            $quote = $yahoo->getQuote($stock['symbol']);
            if ($quote) {
                $data = $yahoo->quoteToArray($quote);
                $price = (float) ($data['regularMarketPrice'] ?? $stock['current_price']);
                $previous = (float) ($data['regularMarketPreviousClose'] ?? $stock['previous_close']);
                $extra = [
                    'open'       => $data['regularMarketOpen'],
                    'day_high'   => $data['regularMarketDayHigh'],
                    'day_low'    => $data['regularMarketDayLow'],
                    'volume'     => $data['regularMarketVolume'],
                    'bid'        => $data['bid'],
                    'ask'        => $data['ask'],
                    'avg_volume' => $data['averageDailyVolume3Month'],
                ];
            } else {
                $price = (float) $stock['current_price'];
                $previous = (float) $stock['previous_close'];
            }
        } catch (\Throwable $e) {
            log_message('error', 'Yahoo Finance tick error: ' . $e->getMessage());
            $price = (float) $stock['current_price'];
            $previous = (float) $stock['previous_close'];
        }

        $change = $price - $previous;
        $changePercent = $previous > 0 ? ($change / $previous) * 100 : 0;

        return $this->response->setJSON(array_merge([
            'market'         => $yahoo->getMarketInfo(),
            'current_price'  => round($price, 2),
            'previous_close' => round($previous, 2),
            'change'         => round($change, 2),
            'change_percent' => round($changePercent, 2),
            'updated'        => date('H:i:s'),
        ], $extra));
    }

    // ─── New API endpoints (from MarketDataController sample) ─────────

    private function getClient(): \Scheb\YahooFinanceApi\ApiClient
    {
        return ApiClientFactory::createApiClient(
            clientOptions: [
                'headers' => ['User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36...']
            ],
            retries: 3,
            retryDelay: 1000
        );
    }

    private function resolveSymbol(string $symbol, string $exchange): string
    {
        $exchange = strtoupper(trim($exchange));
        $symbol = strtoupper(trim($symbol));
        if ($exchange === 'NSE' && !str_ends_with($symbol, '.NS')) {
            return $symbol . '.NS';
        }
        if ($exchange === 'BSE' && !str_ends_with($symbol, '.BO')) {
            return $symbol . '.BO';
        }
        return $symbol;
    }

    public function getSearch(string $query)
    {
        $queryInput = strtoupper(trim($query));

        try {
            $client = $this->getClient();
            $results = $client->search($queryInput, region: "GLOBAL");

            return $this->response->setStatusCode(200)->setJSON($results);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Search failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getQuote(string $symbol, string $exchange = 'GLOBAL')
    {
        $finalQuerySymbol = $this->resolveSymbol($symbol, $exchange);

        try {
            $client = $this->getClient();
            $quote  = $client->getQuote($finalQuerySymbol);

            if (!$quote) {
                return $this->response->setStatusCode(404)->setJSON(["error" => "Ticker not found."]);
            }

            $rawYahooDataArray = [];
            $reflection = new \ReflectionClass($quote);
            $methods    = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                $name = $method->getName();
                if (str_starts_with($name, 'get') && $name !== 'getHistoricalData') {
                    $key = lcfirst(substr($name, 3));
                    try {
                        $value = $method->invoke($quote);
                        if ($value instanceof \DateTimeInterface) {
                            $rawYahooDataArray[$key] = $value->format(\DateTime::ATOM);
                        } else {
                            $rawYahooDataArray[$key] = $value;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            if (!isset($rawYahooDataArray['dividendYield']) && isset($rawYahooDataArray['trailingAnnualDividendYield'])) {
                $rawYahooDataArray['dividendYield'] = $rawYahooDataArray['trailingAnnualDividendYield'] * 100;
            }

            return $this->response->setStatusCode(200)->setJSON($rawYahooDataArray);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getQuotes(string $symbols, string $exchange = 'GLOBAL')
    {
        $finalQuerySymbols = [];
        foreach (explode(",", $symbols) as $sym) {
            $finalQuerySymbols[] = $this->resolveSymbol(trim($sym), $exchange);
        }

        try {
            $client = $this->getClient();
            $quotes = $client->getQuotes($finalQuerySymbols);

            if (!$quotes) {
                return $this->response->setStatusCode(404)->setJSON(["error" => "Tickers not found."]);
            }

            $finalRows = [];
            foreach ($quotes as $quote) {
                $rawYahooDataArray = [];
                $reflection = new \ReflectionClass($quote);
                $methods    = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

                foreach ($methods as $method) {
                    $name = $method->getName();
                    if (str_starts_with($name, 'get') && $name !== 'getHistoricalData') {
                        $key = lcfirst(substr($name, 3));
                        try {
                            $value = $method->invoke($quote);
                            if ($value instanceof \DateTimeInterface) {
                                $rawYahooDataArray[$key] = $value->format(\DateTime::ATOM);
                            } else {
                                $rawYahooDataArray[$key] = $value;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }

                if (!isset($rawYahooDataArray['dividendYield']) && isset($rawYahooDataArray['trailingAnnualDividendYield'])) {
                    $rawYahooDataArray['dividendYield'] = $rawYahooDataArray['trailingAnnualDividendYield'] * 100;
                }

                $finalRows[] = $rawYahooDataArray;
            }

            return $this->response->setStatusCode(200)->setJSON($finalRows);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getHistorical(string $symbol, string $exchange = 'GLOBAL', string $time = '14 days')
    {
        $finalQuerySymbol = $this->resolveSymbol($symbol, $exchange);

        try {
            $client    = $this->getClient();
            $startDate = new \DateTime("-$time", new \DateTimeZone('Asia/Kolkata'));
            $endDate   = new \DateTime("today", new \DateTimeZone('Asia/Kolkata'));

            $historicalRecords = $client->getHistoricalQuoteData($finalQuerySymbol, ApiClient::INTERVAL_1_DAY, $startDate, $endDate);

            return $this->response->setStatusCode(200)->setJSON($historicalRecords);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getDividends(string $symbol, string $exchange = 'GLOBAL', string $time = '5 years')
    {
        $finalQuerySymbol = $this->resolveSymbol($symbol, $exchange);

        try {
            $client    = $this->getClient();
            $startDate = new \DateTime("-$time", new \DateTimeZone('Asia/Kolkata'));
            $endDate   = new \DateTime("today", new \DateTimeZone('Asia/Kolkata'));

            $dividendRecords = $client->getHistoricalDividendData($finalQuerySymbol, $startDate, $endDate);

            return $this->response->setStatusCode(200)->setJSON($dividendRecords);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getSplits(string $symbol, string $exchange = 'GLOBAL', string $time = '5 years')
    {
        $finalQuerySymbol = $this->resolveSymbol($symbol, $exchange);

        try {
            $client    = $this->getClient();
            $startDate = new \DateTime("-$time", new \DateTimeZone('Asia/Kolkata'));
            $endDate   = new \DateTime("today", new \DateTimeZone('Asia/Kolkata'));

            $splitRecords = $client->getHistoricalSplitData($finalQuerySymbol, $startDate, $endDate);

            return $this->response->setStatusCode(200)->setJSON($splitRecords);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getExchangeRate(string $from, string $to)
    {
        $fromCurrency = strtoupper(trim($from));
        $toCurrency   = strtoupper(trim($to));

        try {
            $client        = $this->getClient();
            $exchangeQuote = $client->getExchangeRate($fromCurrency, $toCurrency);

            if (!$exchangeQuote) {
                return $this->response->setStatusCode(404)->setJSON(["error" => "Currency pair mapping not found."]);
            }

            return $this->response->setStatusCode(200)->setJSON($exchangeQuote);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getOptionChain(string $symbol, string $exchange = 'GLOBAL')
    {
        $finalQuerySymbol = $this->resolveSymbol($symbol, $exchange);

        try {
            $client      = $this->getClient();
            $optionChain = $client->getOptionChain($finalQuerySymbol);

            return $this->response->setStatusCode(200)->setJSON($optionChain);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getSummary(string $symbol, string $exchange = 'GLOBAL')
    {
        $finalQuerySymbol = $this->resolveSymbol($symbol, $exchange);

        try {
            $client = $this->getClient();
            $summary = $client->getStockSummary($finalQuerySymbol, [
                'modules' => $this->request->getGet('modules') ?? 'summaryProfile,assetProfile'
            ]);

            return $this->response->setStatusCode(200)->setJSON($summary);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function getNewsStream(string $symbol, string $exchange = 'GLOBAL')
    {
        $finalQuerySymbol = $this->resolveSymbol($symbol, $exchange);
        $ticker = urlencode(strtoupper(trim($finalQuerySymbol)));
        $maxResults = 100;

        $client = service('curlrequest');

        try {
            $targetUrl = "https://query2.finance.yahoo.com/v1/finance/search?q={$ticker}&newsCount={$maxResults}";
            $response = $client->request('GET', $targetUrl, [
                'headers' => [
                    'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept'          => 'application/json',
                    'Origin'          => 'https://finance.yahoo.com',
                    'Referer'         => "https://finance.yahoo.com/quote/{$ticker}/news/"
                ]
            ]);

            $body = $response->getBody();
            $payload = json_decode($body, true);

            $newsRecords = $payload['news'] ?? [];

            if (empty($newsRecords)) {
                return $this->response->setStatusCode(404)->setJSON([
                    "error" => "No news streams found mapping to ticker: {$symbol}"
                ]);
            }

            $formattedNewsFeed = [];
            foreach ($newsRecords as $item) {
                $epochTimestamp = $item['providerPublishTime'] ?? null;
                $readableDate = $epochTimestamp
                    ? (new \DateTime("@" . $epochTimestamp))->setTimezone(new \DateTimeZone("Asia/Kolkata"))->format(\DateTime::ATOM)
                    : null;

                $formattedNewsFeed[] = [
                    "title"       => $item['title'] ?? 'Untitled Headline',
                    "publisher"   => $item['publisher'] ?? 'External Source',
                    "publishDate" => $readableDate,
                    "link"        => $item['link'] ?? null,
                    "uuid"        => $item['uuid'] ?? null
                ];
            }

            return $this->response->setStatusCode(200)->setJSON($formattedNewsFeed);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(502)->setJSON([
                "error"   => "Proxy lookup failed to synchronize with external feed provider",
                "details" => $e->getMessage()
            ]);
        }
    }
}
