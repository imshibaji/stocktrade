<?php

namespace App\Libraries;

use App\Controllers\Screener;

class StockQueryService
{
    private Screener $screener;

    public function __construct()
    {
        $this->screener = new Screener();
    }

    public function compileManualQuery(string $query): array
    {
        return $this->screener->compileManualQuery($query);
    }

    public function extractFundamentalFilters(array $query): array
    {
        $fundFilters = [];
        if (!empty($query)) {
            foreach ($query as $filter) {
                if (empty($filter['is_technical'])) {
                    $fundFilters[] = $filter;
                }
            }
        }
        return $fundFilters;
    }

    public function extractTechnicalFilters(array $query): array
    {
        $techFilters = [];
        if (!empty($query)) {
            foreach ($query as $filter) {
                if (!empty($filter['is_technical'])) {
                    $techFilters[] = $filter;
                }
            }
        }
        return $techFilters;
    }

    public function matchesFilters(array $stock, array $filters, string $mode = 'all'): bool
    {
        return $this->screener->matchesFilters($stock, $filters, $mode);
    }

    public function matchesTechnicalFilters(array $ohlcv, array $techFilters, string $mode = 'all', ?array $stock = null): bool
    {
        return $this->screener->matchesTechnicalFilters($ohlcv, $techFilters, $mode, $stock);
    }
}