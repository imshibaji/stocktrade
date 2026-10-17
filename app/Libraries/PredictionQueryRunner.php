<?php

namespace App\Libraries;

use App\Models\PredictionQueryResultModel;
use App\Models\StockModel;
use App\Models\StockPriceModel;

/**
 * Executes a saved prediction query: matches stocks against its criteria,
 * runs the chosen forecast method for each match, and replaces the stored
 * results for that query.
 *
 * Extracted from PredictionQuery controller so the same logic can be reused
 * by CLI tooling (e.g. `spark predictions:refresh`).
 */
class PredictionQueryRunner
{
    protected StockQueryService $stockQueryService;
    protected ForecastEngine $forecastEngine;

    public function __construct(?StockQueryService $stockQueryService = null, ?ForecastEngine $forecastEngine = null)
    {
        $this->stockQueryService = $stockQueryService ?? new StockQueryService();
        $this->forecastEngine = $forecastEngine ?? new ForecastEngine();
    }

    /**
     * @return array<int, array<string, mixed>> The freshly computed results.
     */
    public function run(array $query): array
    {
        $stockModel = new StockModel();
        $stockPriceModel = new StockPriceModel();
        $resultsModel = new PredictionQueryResultModel();

        $stockQuery = $query['criteria'] ? json_decode($query['criteria'], true) : [];
        $techQuery = $query['technical_criteria'] ? json_decode($query['technical_criteria'], true) : [];
        $matchMode = $query['match_mode'] ?? 'all';

        $stockQuery = $this->normalizeLegacyFilters($stockQuery);
        $techQuery = $this->normalizeLegacyFilters($techQuery);

        $allFilters = array_merge(
            is_array($stockQuery) ? $stockQuery : [],
            is_array($techQuery) ? $techQuery : []
        );

        $stocks = $stockModel->findAll();
        $stockIds = array_column($stocks, 'id');

        $allPrices = $stockPriceModel
            ->whereIn('stock_id', $stockIds)
            ->orderBy('stock_id', 'ASC')
            ->orderBy('price_date', 'ASC')
            ->findAll();

        $pricesByStock = [];
        foreach ($allPrices as $p) {
            $sid = (int) $p['stock_id'];
            $pricesByStock[$sid][] = $p;
        }

        $filteredStocks = [];
        foreach ($stocks as $s) {
            $sid = (int) $s['id'];
            $ohlcv = $pricesByStock[$sid] ?? [];

            $fundFilters = $this->stockQueryService->extractFundamentalFilters($stockQuery);
            $techFilters = $this->stockQueryService->extractTechnicalFilters($allFilters);

            if (!$this->stockQueryService->matchesFilters($s, $fundFilters, $matchMode)) {
                continue;
            }

            if (!empty($techFilters) && empty($ohlcv)) {
                continue;
            }

            if (empty($techFilters) || $this->stockQueryService->matchesTechnicalFilters($ohlcv, $techFilters, $matchMode, $s)) {
                $filteredStocks[] = ['stock' => $s, 'ohlcv' => $ohlcv];
            }
        }

        $results = [];
        foreach ($filteredStocks as $fs) {
            $stock = $fs['stock'];
            $ohlcv = $fs['ohlcv'];

            if (empty($ohlcv)) continue;

            $forecast = $this->forecastEngine->loadData($ohlcv)
                ->predict($query['method'], $query['horizon_days']);

            if ($forecast) {
                $forecast['stock_id'] = $stock['id'];
                $forecast['stock_symbol'] = $stock['symbol'];
                $forecast['stock_name'] = $stock['name'];
                $forecast['query_id'] = $query['id'];
                $forecast['forecast_date'] = date('Y-m-d');

                $results[] = $forecast;
            }
        }

        $batchResults = [];
        foreach ($results as $result) {
            $result['query_id'] = $query['id'];
            $batchResults[] = $result;
        }

        // Always replace the stored results so they reflect the current match
        // set; otherwise a query that now matches nothing would keep stale rows.
        $resultsModel->where('query_id', $query['id'])->delete();
        if (!empty($batchResults)) {
            $resultsModel->insertBatch($batchResults);
        }

        return $results;
    }

    private function normalizeLegacyFilters(array $filters): array
    {
        $techNames = [
            'sma', 'ema', 'sma_pct', 'ema_pct', 'vwap_ratio', 'macd', 'macd_signal', 'macd_histogram',
            'atr', 'natr', 'bb_pct', 'bb_width', 'kc_pct', 'dc_pct',
            'rsi', 'stoch_k', 'stoch_d', 'cci', 'roc', 'williams_r', 'rvi', 'coppock',
            'supertrend', 'supertrend_dir', 'psar',
            'obv', 'cmf', 'vpt', 'mfi', 'volume_ratio', 'force_index', 'eom',
            'pivot', 'fib_61.8',
            'linreg_slope', 'linreg_rsq', 'zscore', 'efficiency_ratio', 'chop', 'hurst', 'dpo', 'ulcer_index',
            'kama', 'volume_delta',
            'ttm_squeeze', 'ttm_momentum', 'sortino_ratio', 'cvar', 'historical_var', 'martin_ratio', 'downside_dev',
            'aroon_up', 'aroon_down', 'aroon_osc', 'tsi', 'vi_plus', 'vi_minus', 'cmo', 'mass_index', 'connors_rsi', 'rmi',
            'klinger_osc', 'rainbow_sma1',
            'vp_poc', 'vp_vah', 'vp_val',
            'close', 'open', 'high', 'low', 'volume',
        ];
        $normalized = [];
        foreach ($filters as $f) {
            if (!is_array($f)) { $normalized[] = $f; continue; }
            $field = strtolower($f['field'] ?? '');
            $value = $f['value'] ?? '';

            if (preg_match('~^([a-z_]+)\((\d+)\)$~i', (string) $field, $m)) {
                $f['is_technical'] = true;
                $f['indicator'] = strtolower($m[1]);
                $f['period'] = (int) $m[2];
            } elseif (in_array($field, $techNames, true)) {
                $f['is_technical'] = true;
                $f['indicator'] = $field;
                $f['period'] = (int) ($f['period'] ?? 14);
            }

            if (!empty($f['is_string']) && is_string($value) && preg_match('~^([a-z_]+)\((\d+)\)$~i', $value, $m)) {
                $f['is_string'] = false;
                $f['is_indicator_ref'] = true;
                $f['indicator_period'] = (int) $m[2];
                $f['value'] = strtolower($m[1]);
            }

            $normalized[] = $f;
        }
        return $normalized;
    }
}
