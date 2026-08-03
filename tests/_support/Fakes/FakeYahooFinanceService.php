<?php

namespace Tests\Support\Fakes;

/**
 * Offline stand-in for YahooFinanceService used in HTTP feature tests.
 *
 * getQuote() returns the canned quote array for a symbol (or null when the
 * symbol is unknown), quoteToArray() passes it straight through, and
 * getSummary() returns the canned summaryProfile payload for a symbol (or an
 * empty array when none is registered).
 */
class FakeYahooFinanceService
{
    /** @var array<string, array<string, mixed>|null> */
    private array $quotes;

    /** @var array<string, array<string, mixed>> */
    private array $summaries;

    /**
     * @param array<string, array<string, mixed>|null> $quotes    symbol => quote data
     * @param array<string, array<string, mixed>>      $summaries symbol => summary payload (e.g. summaryProfile)
     */
    public function __construct(array $quotes = [], array $summaries = [])
    {
        $this->quotes = $quotes;
        $this->summaries = $summaries;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getQuote(string $s, string $ex = 'GLOBAL')
    {
        $symbol = strtoupper(trim($s));
        $ticker = $this->ticker($symbol, $ex);

        return $this->quotes[$ticker] ?? $this->quotes[$symbol] ?? null;
    }

    /**
     * @param array<string, mixed> $quote
     *
     * @return array<string, mixed>
     */
    public function quoteToArray($quote): array
    {
        return $quote;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSummary(string $s, string $ex = 'GLOBAL', array $modules = []): array
    {
        $symbol = strtoupper(trim($s));
        $ticker = $this->ticker($symbol, $ex);

        return $this->summaries[$ticker] ?? $this->summaries[$symbol] ?? [];
    }

    /**
     * Mirror YahooFinanceService::toYahooSymbol so tests can prove the exact
     * symbol is tried before falling back to an exchange-qualified ticker.
     */
    private function ticker(string $symbol, string $exchange): string
    {
        return match (strtoupper($exchange)) {
            'BSE'    => $symbol . '.BO',
            'GLOBAL' => $symbol,
            default  => $symbol . '.NS',
        };
    }
}
