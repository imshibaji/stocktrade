<?php

namespace App\Libraries;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use Scheb\YahooFinanceApi\Results\Quote;

class YahooFinanceService
{
    private ApiClient $client;

    private static string $cacheFile = '';

    private static function cachePath(): string
    {
        if (self::$cacheFile === '') {
            self::$cacheFile = WRITEPATH . 'cache/yahoo_quotes.json';
        }
        return self::$cacheFile;
    }

    private static function readCache(): array
    {
        $path = self::cachePath();
        if (!is_file($path)) return [];
        $raw = file_get_contents($path);
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private static function writeCache(array $data): void
    {
        $path = self::cachePath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        file_put_contents($path, json_encode($data), LOCK_EX);
    }

    public function __construct(bool $searchMode = false, ?ApiClient $client = null)
    {
        $guzzleOptions = [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ],
            'connect_timeout' => $searchMode ? 2 : 4,
            'timeout' => $searchMode ? 3 : 5,
            'cookies' => new \GuzzleHttp\Cookie\FileCookieJar(WRITEPATH . 'cache/yahoo_api_cookies.json', true),
        ];

        $this->client = $client ?? ApiClientFactory::createApiClient(
            clientOptions: $guzzleOptions,
            retries: $searchMode ? 0 : 1,
            retryDelay: $searchMode ? 0 : 500,
        );
    }

    public static function toYahooSymbol(string $symbol): string
    {
        return $symbol . '.NS';
    }

    public static function fromYahooSymbol(string $yahooSymbol): string
    {
        if (str_ends_with($yahooSymbol, '.NS')) {
            return substr($yahooSymbol, 0, -3);
        }
        return $yahooSymbol;
    }

    public function search(string $query): array
    {
        return $this->client->search($query);
    }

    public function getHistoricalData(string $symbol, string $interval, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->client->getHistoricalQuoteData($symbol, $interval, $start, $end);
    }

    public function getDividends(string $symbol, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->client->getHistoricalDividendData($symbol, $start, $end);
    }

    public function getSplits(string $symbol, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->client->getHistoricalSplitData($symbol, $start, $end);
    }

    public function getExchangeRate(string $from, string $to): ?Quote
    {
        return $this->client->getExchangeRate($from, $to);
    }

    public function getOptions(string $symbol): array
    {
        return $this->client->getOptionChain($symbol);
    }

    public function getSummary(string $symbol, array $modules = []): array
    {
        try {
            $results = $this->client->getStockSummary(self::toYahooSymbol($symbol), $modules);
            return $results[0] ?? [];
        } catch (\Throwable $e) {
            log_message('error', "Yahoo summary error for {$symbol}: " . $e->getMessage());
            return [];
        }
    }

    public function getQuote(string $symbol): ?Quote
    {
        $quotes = $this->getQuotesFromChartApi([$symbol]);
        return $quotes[0] ?? null;
    }

    public function getQuotes(array $symbols): array
    {
        return $this->getQuotesFromChartApi($symbols);
    }

    public function getQuotesFromChartApi(array $symbols): array
    {
        $results = [];
        $client = new \GuzzleHttp\Client([
            'timeout' => 5,
            'headers' => ['User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'],
        ]);

        $requests = [];
        foreach ($symbols as $sym) {
            $yahooSym = self::toYahooSymbol($sym);
            $requests[$sym] = $client->getAsync(
                "https://query1.finance.yahoo.com/v8/finance/chart/{$yahooSym}?interval=1d&range=5d"
            );
        }

        $responses = \GuzzleHttp\Promise\Utils::settle($requests)->wait();

        foreach ($responses as $sym => $outcome) {
            if ($outcome['state'] !== 'fulfilled') {
                log_message('error', "Yahoo Finance chart API error for {$sym}: " . ($outcome['value'] ?? 'timeout'));
                continue;
            }
            try {
                $body = json_decode((string) $outcome['value']->getBody(), true);
                $meta = $body['chart']['result'][0]['meta'] ?? null;
                if (!$meta) continue;
                $price = $meta['regularMarketPrice'] ?? null;
                if ($price === null) continue;
                $prevClose = $meta['chartPreviousClose'] ?? null;
                $change = $prevClose !== null ? $price - $prevClose : null;
                $changePct = $prevClose > 0 ? ($change / $prevClose) * 100 : null;

                $build = [
                    'symbol'                     => $meta['symbol'] ?? self::toYahooSymbol($sym),
                    'shortName'                  => $meta['shortName'] ?? $sym,
                    'longName'                   => $meta['longName'] ?? $meta['shortName'] ?? $sym,
                    'regularMarketPrice'         => $price,
                    'regularMarketPreviousClose' => $prevClose,
                    'regularMarketDayHigh'       => $meta['regularMarketDayHigh'] ?? null,
                    'regularMarketDayLow'        => $meta['regularMarketDayLow'] ?? null,
                    'regularMarketVolume'        => $meta['regularMarketVolume'] ?? null,
                    'regularMarketChange'        => $change,
                    'regularMarketChangePercent' => $changePct,
                    'fiftyTwoWeekHigh'           => $meta['fiftyTwoWeekHigh'] ?? null,
                    'fiftyTwoWeekLow'            => $meta['fiftyTwoWeekLow'] ?? null,
                    'fullExchangeName'           => $meta['fullExchangeName'] ?? null,
                    'currency'                   => $meta['currency'] ?? null,
                    'regularMarketTime'          => \DateTime::createFromFormat('U', (string)($meta['regularMarketTime'] ?? time())),
                ];

                $results[] = new Quote($build);
            } catch (\Throwable $e) {
                log_message('error', "Yahoo Finance chart API parse error for {$sym}: " . $e->getMessage());
            }
        }
        return $results;
    }

    public function quoteToArray(Quote $quote): array
    {
        $out = [];
        $reflection = new \ReflectionClass($quote);
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();
            if (str_starts_with($name, 'get') && $name !== 'getHistoricalData') {
                $key = lcfirst(substr($name, 3));
                try {
                    $value = $method->invoke($quote);
                    if ($value instanceof \DateTimeInterface) {
                        $out[$key] = $value->format('Y-m-d H:i:s');
                    } else {
                        $out[$key] = $value;
                    }
                } catch (\Throwable) {
                    continue;
                }
            }
        }

        $out['symbol'] = self::fromYahooSymbol($out['symbol'] ?? '');

        if (!isset($out['dividendYield']) && isset($out['trailingAnnualDividendYield'])) {
            $out['dividendYield'] = $out['trailingAnnualDividendYield'] * 100;
        }

        return $out;
    }

    public function fetchQuotesBySymbols(array $symbols): array
    {
        if (empty($symbols)) return [];

        $cache = self::readCache();
        $now = time();
        $ttl = 120;

        $allCached = true;
        $fromCache = [];
        foreach ($symbols as $s) {
            if (isset($cache[$s]) && ($now - $cache[$s]['_cached_at']) < $ttl) {
                $fromCache[$s] = $cache[$s];
            } else {
                $allCached = false;
            }
        }
        if ($allCached) return $fromCache;

        try {
            $quotes = $this->getQuotes($symbols);
            $results = [];

            foreach ($quotes as $quote) {
                $localSymbol = self::fromYahooSymbol($quote->getSymbol());
                $arr = $this->quoteToArray($quote);
                $arr['_cached_at'] = $now;
                $results[$localSymbol] = $arr;
            }

            if (!empty($results)) {
                $merged = array_merge($cache, $results);
                self::writeCache($merged);
            }

            return $results;
        } catch (\Throwable $e) {
            log_message('error', 'Yahoo Finance API error: ' . $e->getMessage());
            return $fromCache;
        }
    }

    public function fetchAndUpdateStocks(): array
    {
        $stockModel = model('App\Models\StockModel');
        $allStocks = $stockModel->findAll();
        $symbols = array_column($allStocks, 'symbol');
        $symbolMap = [];
        foreach ($allStocks as $s) {
            $symbolMap[$s['symbol']] = $s;
        }

        if (empty($symbols)) return [];

        try {
            $liveQuotes = $this->fetchQuotesBySymbols($symbols);
        } catch (\Throwable $e) {
            log_message('error', 'Yahoo Finance fetchAndUpdateStocks error: ' . $e->getMessage());
            return [];
        }

        $updated = [];

        foreach ($liveQuotes as $symbol => $data) {
            if (($data['regularMarketPrice'] ?? null) === null) continue;

            $updateData = [
                'current_price'  => $data['regularMarketPrice'],
                'previous_close' => $data['regularMarketPreviousClose'] ?? $symbolMap[$symbol]['previous_close'],
                'updated_at'     => date('Y-m-d H:i:s'),
            ];

            if ($data['marketCap'] ?? null)          $updateData['market_cap'] = $data['marketCap'];
            if ($data['trailingPE'] ?? null)          $updateData['pe_ratio'] = $data['trailingPE'];
            if ($data['fiftyTwoWeekHigh'] ?? null)    $updateData['week_52_high'] = $data['fiftyTwoWeekHigh'];
            if ($data['fiftyTwoWeekLow'] ?? null)     $updateData['week_52_low'] = $data['fiftyTwoWeekLow'];
            if ($data['trailingAnnualDividendYield'] ?? null) $updateData['dividend_yield'] = $data['trailingAnnualDividendYield'];
            if ($data['averageDailyVolume3Month'] ?? null)   $updateData['avg_volume'] = $data['averageDailyVolume3Month'];

            $stockModel->where('symbol', $symbol)->set($updateData)->update();

            $updated[$symbol] = array_merge($symbolMap[$symbol], $updateData);
        }

        return $updated;
    }

    public function enrichStocks(array $stocks): array
    {
        if (empty($stocks)) return $stocks;
        $symbols = array_column($stocks, 'symbol');
        $quotes = $this->fetchQuotesBySymbols($symbols);
        foreach ($stocks as &$stock) {
            $sym = $stock['symbol'];
            $q = $quotes[$sym] ?? null;
            if ($q && ($q['regularMarketPrice'] ?? null) !== null) {
                $stock['current_price'] = $q['regularMarketPrice'];
                if (($q['regularMarketPreviousClose'] ?? null) !== null) $stock['previous_close'] = $q['regularMarketPreviousClose'];
            }
        }
        return $stocks;
    }

    private static function readSummaryCache(): array
    {
        $path = WRITEPATH . 'cache/yahoo_summary.json';
        if (!is_file($path)) return [];
        $raw = file_get_contents($path);
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private static function writeSummaryCache(array $data): void
    {
        $path = WRITEPATH . 'cache/yahoo_summary.json';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        file_put_contents($path, json_encode($data), LOCK_EX);
    }

    public function getQuoteSummary(string $symbol): array
    {
        $cache = self::readSummaryCache();
        $now = time();
        $ttl = 3600;

        if (isset($cache[$symbol]) && ($now - $cache[$symbol]['_cached_at']) < $ttl) {
            return $cache[$symbol]['data'];
        }

        $data = [];

        try {
            $result = $this->getSummary($symbol, [
                'summaryProfile', 'summaryDetail', 'defaultKeyStatistics',
                'financialData', 'calendarEvents', 'price', 'quoteType'
            ]);

            if (!$result) return [];

            $profile = $result['summaryProfile'] ?? [];
            if ($profile) {
                $data['sector'] = $profile['sector'] ?? null;
                $data['industry'] = $profile['industry'] ?? null;
            }

            $detail = $result['summaryDetail'] ?? [];
            if ($detail) {
                $map = [
                    'previous_close'           => 'regularMarketPreviousClose',
                    'open'                     => 'regularMarketOpen',
                    'day_low'                  => 'regularMarketDayLow',
                    'day_high'                 => 'regularMarketDayHigh',
                    'volume'                   => 'regularMarketVolume',
                    'avg_volume'               => 'averageDailyVolume3Month',
                    'avg_volume_10d'           => 'averageDailyVolume10Day',
                    'pe_ratio'                 => 'trailingPE',
                    'forward_pe'               => 'forwardPE',
                    'dividend_yield'           => 'dividendYield',
                    'dividend_rate'            => 'dividendRate',
                    'market_cap'               => 'marketCap',
                    'beta'                     => 'beta',
                    'price_to_book'            => 'priceToBook',
                    'book_value'               => 'bookValue',
                    'eps_forward'              => 'epsForward',
                    'eps_trailing'             => 'epsTrailingTwelveMonths',
                    'fifty_day_average'        => 'fiftyDayAverage',
                    'two_hundred_day_average'  => 'twoHundredDayAverage',
                    'fifty_two_week_low'       => 'fiftyTwoWeekLow',
                    'fifty_two_week_high'      => 'fiftyTwoWeekHigh',
                    'regular_market_price'     => 'regularMarketPrice',
                    'regular_market_change'    => 'regularMarketChange',
                    'regular_market_change_percent' => 'regularMarketChangePercent',
                    'market_state'             => 'marketState',
                    'bid'                      => 'bid',
                    'ask'                      => 'ask',
                    'bid_size'                 => 'bidSize',
                    'ask_size'                 => 'askSize',
                    'exchange'                 => 'fullExchangeName',
                    'currency'                 => 'currency',
                ];
                foreach ($map as $key => $field) {
                    $data[$key] = $detail[$field]['raw'] ?? $data[$key] ?? null;
                }
                $data['regular_market_time'] = isset($detail['regularMarketTime'])
                    ? date('Y-m-d H:i:s', $detail['regularMarketTime']['raw'])
                    : null;
            }

            $stats = $result['defaultKeyStatistics'] ?? [];
            if ($stats) {
                if (empty($data['market_cap'])) $data['market_cap'] = $stats['marketCap']['raw'] ?? null;
                if (empty($data['beta'])) $data['beta'] = $stats['beta']['raw'] ?? null;
                $data['shares_outstanding'] = $stats['sharesOutstanding']['raw'] ?? null;
            }

            $finData = $result['financialData'] ?? [];
            if ($finData) {
                $data['target_price'] = $finData['targetMeanPrice']['raw'] ?? null;
                $data['recommendation'] = $finData['recommendationKey'] ?? null;
            }

            $calEvents = $result['calendarEvents'] ?? [];
            if ($calEvents) {
                $earnings = $calEvents['earnings'] ?? [];
                $earningsDates = $earnings['earningsDate'] ?? [];
                if (!empty($earningsDates)) {
                    $data['earnings_timestamp'] = isset($earningsDates[0]['raw']) ? date('Y-m-d\TH:i:sP', $earningsDates[0]['raw']) : null;
                    $data['earnings_timestamp_start'] = $data['earnings_timestamp'];
                    $data['earnings_timestamp_end'] = isset($earningsDates[1]['raw']) ? date('Y-m-d\TH:i:sP', $earningsDates[1]['raw']) : null;
                }
                $data['dividend_date'] = isset($calEvents['exDividendDate']['raw']) ? date('Y-m-d', $calEvents['exDividendDate']['raw']) : null;
            }

            $priceMod = $result['price'] ?? [];
            if ($priceMod) {
                if (empty($data['exchange'])) $data['exchange'] = $priceMod['exchangeName'] ?? null;
                if (empty($data['currency'])) $data['currency'] = $priceMod['currency'] ?? null;
                $data['quote_type'] = $priceMod['quoteType'] ?? null;
                $data['exchange_timezone'] = $priceMod['exchangeTimezoneName'] ?? null;
            }

            $qtType = $result['quoteType'] ?? [];
            if ($qtType) {
                if (empty($data['quote_type'])) $data['quote_type'] = $qtType['quoteType'] ?? null;
                if (empty($data['exchange'])) $data['exchange'] = $qtType['exchange'] ?? null;
                $data['long_name'] = $qtType['longName'] ?? null;
                $data['short_name'] = $qtType['shortName'] ?? null;
            }

            if (!empty($data)) {
                $cache[$symbol] = ['data' => $data, '_cached_at' => $now];
                self::writeSummaryCache($cache);
            }

            return $data;
        } catch (\Throwable $e) {
            log_message('error', "Yahoo Finance quoteSummary error for {$symbol}: " . $e->getMessage());
            if (isset($cache[$symbol])) {
                return $cache[$symbol]['data'];
            }
            return [];
        }
    }

    public function isMarketOpen(): bool
    {
        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $dayOfWeek = (int) $now->format('N');
        $hour = (int) $now->format('G');
        $minute = (int) $now->format('i');
        $currentMinutes = $hour * 60 + $minute;

        if ($dayOfWeek >= 6) return false;

        return $currentMinutes >= 555 && $currentMinutes < 930;
    }

    public function getMarketInfo(): array
    {
        $open = $this->isMarketOpen();
        return [
            'open'  => $open,
            'label' => $open ? 'Market Open (NSE/BSE)' : 'Market Closed',
        ];
    }
}
