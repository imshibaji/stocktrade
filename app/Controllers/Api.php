<?php

namespace App\Controllers;

use App\Libraries\YahooFinanceService;
use App\Models\StockModel;

class Api extends BaseController
{
    protected function setApiCache(int $ttl = 5): void
    {
        $this->response->setHeader('Cache-Control', "public, max-age={$ttl}, s-maxage={$ttl}");
    }

    public function index()
    {
        return $this->response->setJSON([
            'app_name' => 'Trade API',
            'status'   => 'OK',
            'version'  => '1.0.0',
            'timestamp' => time(),
            'search' => [
                'url'   => '/api/search/recltd',
                'query' => 'RECLTD',
            ],
            'quote' => [
                'url'      => '/api/quote/pfc/nse',
                'symbol'   => 'PFC',
                'exchange' => 'NSE',
            ],
            'quotes' => [
                'url'      => '/api/quotes/pfc,pnb/nse',
                'symbol'   => 'PFC,PNB',
                'exchange' => 'NSE',
            ],
            'historical' => [
                'url'      => '/api/historical/pfc/nse/14 days',
                'symbol'   => 'PFC',
                'exchange' => 'NSE',
                'time'     => '14 days',
            ],
            'dividends' => [
                'url'      => '/api/dividends/pfc/nse/5 years',
                'symbol'   => 'PFC',
                'exchange' => 'NSE',
                'time'     => '5 years',
            ],
            'splits' => [
                'url'      => '/api/splits/pfc/nse/5 years',
                'symbol'   => 'PFC',
                'exchange' => 'NSE',
                'time'     => '5 years',
            ],
            'option' => [
                'url'      => '/api/options/pfc/nse',
                'symbol'   => 'PFC',
                'exchange' => 'NSE',
            ],
            'exchange_url' => [
                'url'  => '/api/exchange/usd/inr',
                'from' => 'USD',
                'to'   => 'INR',
            ],
            'summary' => [
                'url'      => '/api/summary/pfc/nse?modules=summaryProfile,netSharePurchaseActivity,earnings,sectorTrend,indexTrend',
                'symbol'   => 'PFC',
                'exchange' => 'NSE',
                'modules'  => 'summaryProfile,netSharePurchaseActivity,earnings,sectorTrend,indexTrend',
            ],
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

        $yahooResultsNoPrice = [];
        foreach ($results as $s) {
            if (!empty($s['from_yahoo']) && empty($s['current_price'])) {
                $yahooResultsNoPrice[] = $s;
            }
        }

        if (!empty($yahooResultsNoPrice) && preg_match('/^[A-Za-z0-9.\-]{1,15}$/', trim($query))) {
            $symbol = strtoupper(trim($yahooResultsNoPrice[0]['symbol']));
            $exch = $yahooResultsNoPrice[0]['exchange'] ?? $exchange;
            $yahoo = new YahooFinanceService();
            try {
                $quote = $yahoo->getQuote($symbol, $exch);
                if ($quote) {
                    $d = $yahoo->quoteToArray($quote);
                    foreach ($results as &$r) {
                        if (!empty($r['from_yahoo']) && strtoupper($r['symbol']) === $symbol) {
                            $r['current_price'] = $d['regularMarketPrice'] ?? null;
                            $r['previous_close'] = $d['regularMarketPreviousClose'] ?? null;
                            $r['name'] = $d['longName'] ?? $d['shortName'] ?? $r['name'];
                            break;
                        }
                    }
                    unset($r);
                }
            } catch (\Throwable $e) {
                log_message('error', 'Yahoo quote enrich error: ' . $e->getMessage());
            }
        }

        if (empty($results) && preg_match('/^[A-Za-z0-9.\-]{1,15}$/', trim($query))) {
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

        $now = date('Y-m-d H:i:s');
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

        generate_price_history($stockId, $price);
        generate_predictions($stockId, $price);

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

    public function refreshStock()
    {
        $id = (int) $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Stock ID required.'])->setStatusCode(400);
        }

        $stockModel = new StockModel();
        $stock = $stockModel->find($id);
        if (!$stock) {
            return $this->response->setJSON(['success' => false, 'message' => 'Stock not found.'])->setStatusCode(404);
        }

        $yahoo = new YahooFinanceService();
        $quote = $yahoo->getQuote($stock['symbol'], $stock['exchange']);
        if (!$quote) {
            return $this->response->setJSON(['success' => false, 'message' => 'Could not fetch data from Yahoo Finance.'])->setStatusCode(404);
        }

        $d = $yahoo->quoteToArray($quote);

        $now = date('Y-m-d H:i:s');
        $update = [
            'name'           => $d['longName'] ?? $d['shortName'] ?? $stock['name'],
            'current_price'  => $d['regularMarketPrice'] ?? $stock['current_price'],
            'previous_close' => $d['regularMarketPreviousClose'] ?? $stock['previous_close'],
            'market_cap'     => $d['marketCap'] ?? $stock['market_cap'],
            'avg_volume'     => $d['averageDailyVolume3Month'] ?? $stock['avg_volume'],
            'pe_ratio'       => $d['trailingPE'] ?? $stock['pe_ratio'],
            'week_52_high'   => $d['fiftyTwoWeekHigh'] ?? $stock['week_52_high'],
            'week_52_low'    => $d['fiftyTwoWeekLow'] ?? $stock['week_52_low'],
            'dividend_yield' => $d['trailingAnnualDividendYield'] ?? $stock['dividend_yield'],
            'beta'           => $d['beta'] ?? $stock['beta'],
            'updated_at'     => $now,
        ];

        $stockModel->update($id, $update);

        return $this->response->setJSON([
            'success' => true,
            'message' => $stock['symbol'] . ' updated from Yahoo Finance.',
            'stock'   => $stockModel->find($id),
        ]);
    }

    public function lookup($symbol = null)
    {
        $this->setApiCache(60);
        if (!$symbol) {
            $symbol = $this->request->getGet('symbol');
        }
        $symbol = strtoupper(trim($symbol ?? ''));
        if (!$symbol) {
            return $this->response->setJSON(['error' => 'Symbol required'])->setStatusCode(400);
        }

        $exchange = YahooFinanceService::detectExchange($symbol);
        $yahoo = new YahooFinanceService();

        $quote = $yahoo->getQuote($symbol, $exchange);
        $d = $quote ? $yahoo->quoteToArray($quote) : [];

        $summary = $yahoo->getSummary($symbol, $exchange);
        $summaryData = $summary['summaryProfile'] ?? [];

        $rp = $d['regularMarketPrice'] ?? null;
        $pc = $d['regularMarketPreviousClose'] ?? null;
        $chg = $d['regularMarketChange'] ?? ($pc !== null && $rp !== null ? $rp - $pc : null);
        $chgPct = $d['regularMarketChangePercent'] ?? ($pc > 0 && $chg !== null ? ($chg / $pc) * 100 : null);

        $data = array_merge($d, [
            'sector'         => $summaryData['sector'] ?? $d['sector'] ?? '',
            'industry'       => $summaryData['industry'] ?? $d['sector'] ?? null,
            'current_price'  => $rp,
            'previous_close' => $pc,
            'change'         => $chg,
            'change_percent' => $chgPct,
        ]);
        $data['symbol'] = $symbol;
        return $this->response->setJSON(YahooFinanceService::cleanQuoteData($data));
    }

    public function getSearch(string $query)
    {
        $this->setApiCache(30);
        $queryInput = strtoupper(trim($query));

        try {
            $yahoo = new YahooFinanceService();
            $results = $yahoo->getSearch($queryInput);

            $mapped = array_map(function ($r) {
                $arr = [];
                $ref = new \ReflectionClass($r);
                foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $m) {
                    $name = $m->getName();
                    if (str_starts_with($name, 'get')) {
                        $key = lcfirst(substr($name, 3));
                        try {
                            $v = $m->invoke($r);
                            $arr[$key] = $v instanceof \DateTimeInterface ? $v->format(\DateTime::ATOM) : $v;
                        } catch (\Throwable) {
                            continue;
                        }
                    }
                }
                return $arr;
            }, $results);

            return $this->response->setStatusCode(200)->setJSON($mapped);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Search failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getQuote(string $symbol, string $exchange = 'GLOBAL')
    {
        $this->setApiCache(10);
        try {
            $yahoo = new YahooFinanceService();
            $quote = $yahoo->getQuote($symbol, $exchange);

            if (!$quote) {
                return $this->response->setStatusCode(404)->setJSON(["error" => "Ticker not found."]);
            }

            $data = $yahoo->quoteToArray($quote);

            return $this->response->setStatusCode(200)->setJSON($data);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getQuotes(string $symbols, string $exchange = 'GLOBAL')
    {
        $this->setApiCache(10);
        $symbolList = array_map('trim', explode(",", $symbols));

        try {
            $yahoo = new YahooFinanceService();
            $quotes = $yahoo->getQuotes($symbolList, $exchange);

            if (empty($quotes)) {
                return $this->response->setStatusCode(404)->setJSON(["error" => "Tickers not found."]);
            }

            $finalRows = [];
            foreach ($quotes as $quote) {
                $finalRows[] = $yahoo->quoteToArray($quote);
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
        $this->setApiCache(300);
        try {
            $yahoo     = new YahooFinanceService();
            $startDate = new \DateTime("-$time", new \DateTimeZone('Asia/Kolkata'));
            $endDate   = new \DateTime("today", new \DateTimeZone('Asia/Kolkata'));

            $records = $yahoo->getHistorical($symbol, '1d', $startDate, $endDate, $exchange);

            return $this->response->setStatusCode(200)->setJSON($records);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getDividends(string $symbol, string $exchange = 'GLOBAL', string $time = '5 years')
    {
        $this->setApiCache(3600);
        try {
            $yahoo     = new YahooFinanceService();
            $startDate = new \DateTime("-$time", new \DateTimeZone('Asia/Kolkata'));
            $endDate   = new \DateTime("today", new \DateTimeZone('Asia/Kolkata'));

            $records = $yahoo->getDividends($symbol, $startDate, $endDate, $exchange);

            return $this->response->setStatusCode(200)->setJSON($records);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getSplits(string $symbol, string $exchange = 'GLOBAL', string $time = '5 years')
    {
        $this->setApiCache(3600);
        try {
            $yahoo     = new YahooFinanceService();
            $startDate = new \DateTime("-$time", new \DateTimeZone('Asia/Kolkata'));
            $endDate   = new \DateTime("today", new \DateTimeZone('Asia/Kolkata'));

            $records = $yahoo->getSplits($symbol, $startDate, $endDate, $exchange);

            return $this->response->setStatusCode(200)->setJSON($records);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getExchangeRate(string $from, string $to)
    {
        $this->setApiCache(300);
        $fromCurrency = strtoupper(trim($from));
        $toCurrency   = strtoupper(trim($to));

        try {
            $yahoo = new YahooFinanceService();
            $quote = $yahoo->getExchangeRate($fromCurrency, $toCurrency);

            if (!$quote) {
                return $this->response->setStatusCode(404)->setJSON(["error" => "Currency pair not found."]);
            }

            return $this->response->setStatusCode(200)->setJSON($yahoo->quoteToArray($quote));
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getOptionChain(string $symbol, string $exchange = 'GLOBAL')
    {
        $this->setApiCache(60);
        try {
            $yahoo  = new YahooFinanceService();
            $options = $yahoo->getOptionChain($symbol, $exchange);

            return $this->response->setStatusCode(200)->setJSON($options);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                "error"   => "Fetch failed",
                "details" => $e->getMessage()
            ]);
        }
    }

    public function getSummary(string $symbol, string $exchange = 'GLOBAL')
    {
        $this->setApiCache(60);
        try {
            $yahoo = new YahooFinanceService();
            $modules = $this->request->getGet('modules') ?? 'summaryProfile,assetProfile';
            $summary = $yahoo->getSummary($symbol, $exchange, explode(',', $modules));

            return $this->response->setStatusCode(200)->setJSON($summary ?: ['error' => 'No data']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function getNewsStream(string $symbol, string $exchange = 'GLOBAL')
    {
        $this->setApiCache(120);
        $ticker = urlencode(YahooFinanceService::toYahooSymbol($symbol, $exchange));
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
