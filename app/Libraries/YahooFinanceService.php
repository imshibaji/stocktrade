<?php

namespace App\Libraries;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use Scheb\YahooFinanceApi\Results\Quote;

class YahooFinanceService
{
    private ApiClient $client;

    public function __construct(?ApiClient $client = null)
    {
        $this->client = $client ?? ApiClientFactory::createApiClient(
            clientOptions: [
                'headers' => ['User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36...']
            ],
            retries: 3,
            retryDelay: 2000,
        );
    }

    public static function toYahooSymbol(string $symbol, string $exchange = 'GLOBAL'): string
    {
        $symbol = strtoupper(trim($symbol));
        $exchange = strtoupper(trim($exchange));
        if (str_ends_with($symbol, '.NS') || str_ends_with($symbol, '.BO')) return $symbol;
        return match ($exchange) { 'BSE' => $symbol . '.BO', 'GLOBAL' => $symbol, default => $symbol . '.NS' };
    }

    public static function fromYahooSymbol(string $s): string
    {
        $s = trim($s);
        if (str_ends_with($s, '.NS')) return substr($s, 0, -3);
        if (str_ends_with($s, '.BO')) return substr($s, 0, -3);
        return $s;
    }

    public static function detectExchange(string $symbol): string
    {
        if (str_ends_with($symbol, '.NS')) return 'NSE';
        if (str_ends_with($symbol, '.BO')) return 'BSE';
        return 'GLOBAL';
    }

    public static function cleanQuoteData(array $data): array
    {
        $drop = ['ask', 'askSize', 'bid', 'bidSize'];
        $keep = [];
        foreach ($data as $k => $v) {
            if ($v === null || $v === []) continue;
            if (in_array($k, $drop, true) && $v === 0) continue;
            $keep[$k] = $v;
        }
        return $keep;
    }

    public function quoteToArray(Quote $quote): array
    {
        $out = [];
        $ref = new \ReflectionClass($quote);
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $m) {
            $name = $m->getName();
            if (!str_starts_with($name, 'get') || $name === 'getHistoricalData') continue;
            $key = lcfirst(substr($name, 3));
            try {
                $v = $m->invoke($quote);
                $out[$key] = $v instanceof \DateTimeInterface ? $v->format('Y-m-d H:i:s') : $v;
            } catch (\Throwable) {}
        }
        $out['symbol'] = self::fromYahooSymbol($out['symbol'] ?? '');
        if (!isset($out['dividendYield']) && isset($out['trailingAnnualDividendYield'])) {
            $out['dividendYield'] = $out['trailingAnnualDividendYield'] * 100;
        }
        return self::cleanQuoteData($out);
    }

    public function getSearch(string $q): array { return $this->client->search($q, region: 'IN'); }

    public function getQuote(string $s, string $ex = 'GLOBAL'): ?Quote { return $this->client->getQuote(self::toYahooSymbol($s, $ex)); }

    public function getQuotes(array $symbols, string $ex = 'GLOBAL'): array
    {
        return $this->client->getQuotes(array_map(fn($s) => self::toYahooSymbol($s, $ex), $symbols));
    }

    public function getHistorical(string $s, string $int, \DateTimeInterface $start, \DateTimeInterface $end, string $ex = 'GLOBAL'): array
    {
        return $this->client->getHistoricalQuoteData(self::toYahooSymbol($s, $ex), $int, $start, $end);
    }

    public function getDividends(string $s, \DateTimeInterface $start, \DateTimeInterface $end, string $ex = 'GLOBAL'): array
    {
        return $this->client->getHistoricalDividendData(self::toYahooSymbol($s, $ex), $start, $end);
    }

    public function getSplits(string $s, \DateTimeInterface $start, \DateTimeInterface $end, string $ex = 'GLOBAL'): array
    {
        return $this->client->getHistoricalSplitData(self::toYahooSymbol($s, $ex), $start, $end);
    }

    public function getExchangeRate(string $from, string $to): ?Quote { return $this->client->getExchangeRate($from, $to); }

    public function getOptionChain(string $s, string $ex = 'GLOBAL'): array { return $this->client->getOptionChain(self::toYahooSymbol($s, $ex)); }

    public function getSummary(string $s, string $ex = 'GLOBAL', array $modules = []): array
    {
        $r = $this->client->getStockSummary(self::toYahooSymbol($s, $ex), $modules ?: ['summaryProfile', 'assetProfile']);
        return $r[0] ?? [];
    }
}
