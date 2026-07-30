<?php

namespace App\Controllers;

use App\Analysis\StockTechnicalAnalysisEngine;
use App\Models\StockListModel;
use App\Models\StockModel;
use App\Models\StockPriceModel;

class Screener extends BaseController
{
    public function index(): string
    {
        $stockListModel = new StockListModel();
        $userId = current_user_id();
        $savedLists = $stockListModel->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'title'      => 'Stock Screener - StockTrade Tips',
            'savedLists' => $savedLists,
        ];
        return view('templates/header', $data)
            . view('screener/index', $data)
            . view('templates/footer');
    }

    public function run()
    {
        $stockModel = new StockModel();
        $stocks     = $stockModel->findAll();

        $matchMode   = $this->request->getGet('match_mode') ?? 'all';
        $filters     = $this->request->getGet('filters');
        $filters     = $filters ? json_decode($filters, true) : [];
        $techFilters = $this->request->getGet('tech_filters');
        $techFilters = $techFilters ? json_decode($techFilters, true) : [];

        $results = [];
        foreach ($stocks as $s) {
            if ($this->matchesFilters($s, $filters, $matchMode)) {
                $results[] = $s;
            }
        }

        if (!empty($techFilters) && !empty($results)) {
            $results = $this->applyTechnicalFilters($results, $techFilters, $matchMode);
        }

        return $this->response->setJSON([
            'total'  => count($results),
            'stocks' => array_values($results),
        ]);
    }

    public function save()
    {
        $userId = current_user_id();
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $name          = $this->request->getPost('name');
        $matchMode     = $this->request->getPost('match_mode') ?? 'all';
        $criteria      = $this->request->getPost('criteria');
        $techCriteria  = $this->request->getPost('technical_criteria');
        $stockIds      = $this->request->getPost('stock_ids');
        $stockSymbols  = $this->request->getPost('stock_symbols');

        if (empty($name)) {
            return $this->response->setJSON(['success' => false, 'message' => 'List name is required']);
        }

        $criteriaData = ['match_mode' => $matchMode, 'filters' => is_array($criteria) ? $criteria : json_decode($criteria ?? '[]', true)];
        $techData     = ['match_mode' => $matchMode, 'filters' => is_array($techCriteria) ? $techCriteria : json_decode($techCriteria ?? '[]', true)];

        $model = new StockListModel();
        $model->save([
            'user_id'            => $userId,
            'name'               => $name,
            'criteria'           => json_encode($criteriaData),
            'technical_criteria' => json_encode($techData),
            'stock_ids'          => is_array($stockIds) ? json_encode($stockIds) : $stockIds,
            'stock_symbols'      => is_array($stockSymbols) ? json_encode($stockSymbols) : $stockSymbols,
            'stock_count'        => is_array($stockIds) ? count($stockIds) : count((array) json_decode($stockIds ?? '[]', true)),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'List saved']);
    }

    public function lists()
    {
        $userId = current_user_id();
        if (!$userId) {
            return $this->response->setJSON([]);
        }
        $model = new StockListModel();
        $lists = $model->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
        return $this->response->setJSON($lists);
    }

    public function loadList()
    {
        $listId = (int) $this->request->getGet('id');
        if ($listId <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid ID']);
        }
        $userId = current_user_id();
        $model  = new StockListModel();
        $list   = $model->where('id', $listId)->where('user_id', $userId)->first();
        if (!$list) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found']);
        }

        $stockIds = json_decode($list['stock_ids'] ?? '[]', true);
        $stockModel = new StockModel();
        $stocks = !empty($stockIds) ? $stockModel->whereIn('id', $stockIds)->findAll() : [];

        $criteriaData     = json_decode($list['criteria'] ?? '{}', true);
        $techData         = json_decode($list['technical_criteria'] ?? '{}', true);
        $matchMode        = $criteriaData['match_mode'] ?? 'all';
        $filters          = $criteriaData['filters'] ?? [];
        $techFilters      = $techData['filters'] ?? [];

        return $this->response->setJSON([
            'success'            => true,
            'list'               => $list,
            'stocks'             => $stocks,
            'match_mode'         => $matchMode,
            'criteria'           => $filters,
            'technical_criteria' => $techFilters,
        ]);
    }

    public function deleteList()
    {
        $listId = (int) $this->request->getPost('id');
        if ($listId <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid ID']);
        }
        $userId = current_user_id();
        $model  = new StockListModel();
        $list   = $model->where('id', $listId)->where('user_id', $userId)->first();
        if (!$list) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found']);
        }
        $model->delete($listId);
        return $this->response->setJSON(['success' => true, 'message' => 'Deleted']);
    }

    // ------------------------------------------------------------------ //
    //  Private Helpers
    // ------------------------------------------------------------------ //

    private function resolveFilterValue(array $f, array $stock): float
    {
        if (!empty($f['value_is_field'])) {
            $fieldVal = $this->getFieldValue($stock, $f['value'] ?? '');
            return $fieldVal !== null ? (float) $fieldVal : 0.0;
        }
        return (float) ($f['value'] ?? 0);
    }

    private function matchesFilters(array $stock, array $filters, string $mode = 'all'): bool
    {
        if (empty($filters)) return true;

        $results = [];
        foreach ($filters as $f) {
            $field    = $f['field'] ?? '';
            $op       = $f['op'] ?? '';
            $mathOp   = $f['math_op'] ?? '=';
            $mathVal  = (float) ($f['math_value'] ?? 0);
            $stockVal = $this->getFieldValue($stock, $field);

            if ($stockVal === null) { $results[] = false; continue; }

            $stockVal = (float) $stockVal;
            if ($mathOp !== '=') {
                switch ($mathOp) {
                    case '+': $stockVal += $mathVal; break;
                    case '-': $stockVal -= $mathVal; break;
                    case '*': $stockVal *= $mathVal; break;
                    case '/': $stockVal = $mathVal != 0 ? $stockVal / $mathVal : $stockVal; break;
                    case '%': $stockVal = $mathVal != 0 ? fmod($stockVal, $mathVal) : $stockVal; break;
                }
            }

            $compareVal = $this->resolveFilterValue($f, $stock);

            switch ($op) {
                case '>':  $results[] = $stockVal > $compareVal; break;
                case '>=': $results[] = $stockVal >= $compareVal; break;
                case '<':  $results[] = $stockVal < $compareVal; break;
                case '<=': $results[] = $stockVal <= $compareVal; break;
                case '==': $results[] = (float) $stockVal == $compareVal; break;
                default:   $results[] = false;
            }
        }

        return $mode === 'any' ? in_array(true, $results, true) : !in_array(false, $results, true);
    }

    private function getFieldValue(array $stock, string $field): ?float
    {
        $map = [
            'price'          => $stock['current_price'] ?? null,
            'current_price'  => $stock['current_price'] ?? null,
            'previous_close' => $stock['previous_close'] ?? null,
            'market_cap'     => $stock['market_cap'] ?? null,
            'pe_ratio'       => $stock['pe_ratio'] ?? null,
            'dividend_yield' => $stock['dividend_yield'] ?? null,
            'beta'           => $stock['beta'] ?? null,
            'avg_volume'     => $stock['avg_volume'] ?? null,
            'week_52_high'   => $stock['week_52_high'] ?? null,
            'week_52_low'    => $stock['week_52_low'] ?? null,
        ];
        $v = $map[$field] ?? null;
        return $v !== null ? (float) $v : null;
    }

    private function applyTechnicalFilters(array $stocks, array $techFilters, string $matchMode = 'all'): array
    {
        $stockIds = array_column($stocks, 'id');
        $priceModel = new StockPriceModel();
        $allPrices = $priceModel
            ->whereIn('stock_id', $stockIds)
            ->orderBy('stock_id', 'ASC')
            ->orderBy('price_date', 'ASC')
            ->findAll();

        $pricesByStock = [];
        foreach ($allPrices as $p) {
            $sid = (int) $p['stock_id'];
            $pricesByStock[$sid][] = $p;
        }

        $maxDays  = 200;
        $filtered = [];
        foreach ($stocks as $s) {
            $sid   = (int) $s['id'];
            $ohlcv = array_slice($pricesByStock[$sid] ?? [], -$maxDays);
            if ($this->matchesTechnicalFilters($ohlcv, $techFilters, $matchMode, $s)) {
                $filtered[] = $s;
            }
        }
        return $filtered;
    }

    private function matchesTechnicalFilters(array $ohlcv, array $techFilters, string $mode = 'all', ?array $stock = null): bool
    {
        if (empty($techFilters)) return true;
        if (empty($ohlcv)) return false;

        $results = [];
        foreach ($techFilters as $f) {
            $indicator = $f['indicator'] ?? '';
            $op        = $f['op'] ?? '';
            $period    = (int) ($f['period'] ?? 14);
            $mathOp    = $f['math_op'] ?? '=';
            $mathVal   = (float) ($f['math_value'] ?? 0);

            $indicatorValue = $this->calculateIndicator($ohlcv, $indicator, $period);
            if ($indicatorValue === null) { $results[] = false; continue; }

            if ($mathOp !== '=') {
                switch ($mathOp) {
                    case '+': $indicatorValue += $mathVal; break;
                    case '-': $indicatorValue -= $mathVal; break;
                    case '*': $indicatorValue *= $mathVal; break;
                    case '/': $indicatorValue = $mathVal != 0 ? $indicatorValue / $mathVal : $indicatorValue; break;
                    case '%': $indicatorValue = $mathVal != 0 ? fmod($indicatorValue, $mathVal) : $indicatorValue; break;
                }
            }

            $compareVal = $this->resolveFilterValue($f, $stock ?? []);

            switch ($op) {
                case '>':  $results[] = $indicatorValue > $compareVal; break;
                case '>=': $results[] = $indicatorValue >= $compareVal; break;
                case '<':  $results[] = $indicatorValue < $compareVal; break;
                case '<=': $results[] = $indicatorValue <= $compareVal; break;
                case '==': $results[] = $indicatorValue == $compareVal; break;
                default:   $results[] = false;
            }
        }

        return $mode === 'any' ? in_array(true, $results, true) : !in_array(false, $results, true);
    }

    private function calculateIndicator(array $ohlcv, string $indicator, int $period): ?float
    {
        if (empty($ohlcv)) return null;

        $prices  = array_map('floatval', array_column($ohlcv, 'close'));
        $highs   = array_map('floatval', array_column($ohlcv, 'high'));
        $lows    = array_map('floatval', array_column($ohlcv, 'low'));
        $volumes = array_map('floatval', array_column($ohlcv, 'volume'));

        $engine = new StockTechnicalAnalysisEngine();
        $engine->loadData($ohlcv);

        $p = max($period, 2);

        switch ($indicator) {
            case 'sma_pct':
                $sma = $engine->calculateSMA($prices, $p);
                return $sma !== null && $sma > 0 ? round((end($prices) / $sma) * 100, 2) : null;
            case 'ema_pct':
                $ema = $engine->calculateEMA($prices, $p);
                return $ema !== null && $ema > 0 ? round((end($prices) / $ema) * 100, 2) : null;
            case 'vwap_ratio':
                $vwap = $engine->calculateVWAP();
                return $vwap !== null && $vwap > 0 ? round((end($prices) / $vwap) * 100, 2) : null;
            case 'macd':
                return $engine->calculateMACD($prices)['macd'] ?? null;
            case 'macd_signal':
                return $engine->calculateMACD($prices)['signal'] ?? null;
            case 'macd_histogram':
                return $engine->calculateMACD($prices)['histogram'] ?? null;
            case 'atr':
                return $engine->calculateATR($p);
            case 'natr':
                return $engine->calculateNATR($p);
            case 'bb_pct':
                $bands = $engine->calculateBollingerBands($prices, $p);
                if ($bands['upper'] === null || $bands['lower'] === null || $bands['upper'] == $bands['lower']) return null;
                return round((($l = end($prices)) - $bands['lower']) / ($bands['upper'] - $bands['lower']) * 100, 2);
            case 'bb_width':
                $bands = $engine->calculateBollingerBands($prices, $p);
                if ($bands['upper'] === null || $bands['middle'] === null || $bands['middle'] == 0) return null;
                return round(($bands['upper'] - $bands['lower']) / $bands['middle'] * 100, 2);
            case 'kc_pct':
                $kc = $engine->calculateKeltnerChannels($prices, $p, $p, 2.0);
                if ($kc['upper'] === null || $kc['lower'] === null || $kc['upper'] == $kc['lower']) return null;
                return round((($l = end($prices)) - $kc['lower']) / ($kc['upper'] - $kc['lower']) * 100, 2);
            case 'dc_pct':
                $dc = $engine->calculateDonchianChannels($p);
                if ($dc['upper'] === null || $dc['lower'] === null || $dc['upper'] == $dc['lower']) return null;
                return round((($l = end($prices)) - $dc['lower']) / ($dc['upper'] - $dc['lower']) * 100, 2);
            case 'rsi':
                return $engine->calculateRSI($prices, $p);
            case 'stoch_k':
                $s = $engine->calculateStochastic($p);
                return $s['percent_k'] ?? null;
            case 'stoch_d':
                $s = $engine->calculateStochastic($p);
                return $s['percent_d'] ?? null;
            case 'cci':
                return $engine->calculateCCI($p);
            case 'roc':
                return $engine->calculateROC($prices, $p);
            case 'williams_r':
                return $engine->calculateWilliamsR($p);
            case 'rvi':
                return $engine->calculateRVI($prices, $p);
            case 'coppock':
                return $engine->calculateCoppockCurve($prices, 10, 14, 11);
            case 'supertrend':
                $st = $engine->calculateSupertrend($p, 3.0);
                return $st['supertrend'] ?? null;
            case 'supertrend_dir':
                $st = $engine->calculateSupertrend($p, 3.0);
                if ($st['trend'] === 'BULLISH') return 1.0;
                if ($st['trend'] === 'BEARISH') return -1.0;
                return 0.0;
            case 'psar':
                return $engine->calculateParabolicSAR();
            case 'obv':
                return $engine->calculateOBV();
            case 'cmf':
                return $engine->calculateCMF($p);
            case 'vpt':
                return $engine->calculateVPT($prices, $volumes);
            case 'mfi':
                return $engine->calculateMFI($p);
            case 'volume_ratio':
                return $engine->calculateVolumeRatio($p);
            case 'force_index':
                return $engine->calculateForceIndex($p);
            case 'eom':
                return $engine->calculateEaseOfMovement($p);
            case 'pivot':
                $h = end($highs); $l = end($lows); $c = end($prices);
                $pp = $engine->calculatePivotPoints($h, $l, $c);
                return $pp['pivot'] ?? null;
            case 'fib_61.8':
                $h = max($highs); $l = min($lows);
                $fib = $engine->calculateFibonacciRetracement($h, $l);
                $f = $fib['level_61.8'] ?? null;
                return $f !== null ? round((end($prices)) / $f, 4) : null;
            case 'linreg_slope':
                return $engine->calculateLinearRegression($prices, $p)['slope'] ?? null;
            case 'linreg_rsq':
                return $engine->calculateLinearRegression($prices, $p)['r_squared'] ?? null;
            case 'zscore':
                return $engine->calculateZScore($prices, $p);
            case 'efficiency_ratio':
                return $engine->calculateEfficiencyRatio($prices, $p);
            case 'chop':
                return $engine->calculateChoppinessIndex($p);
            case 'hurst':
                return $engine->calculateHurstExponent($prices, $p);
            case 'dpo':
                return $engine->calculateDPO($prices, $p);
            case 'ulcer_index':
                return $engine->calculateUlcerIndex($prices, $p);
            case 'kama':
                return $engine->calculateKAMA($prices, $p);
            case 'volume_delta':
                return $engine->calculateVolumeDelta();
            case 'downside_dev': {
                $returns = $this->computeReturns($prices);
                return $engine->calculateDownsideDeviation($returns);
            }
            case 'sortino_ratio': {
                $returns = $this->computeReturns($prices);
                return $engine->calculateSortinoRatio($returns);
            }
            case 'cvar': {
                $returns = $this->computeReturns($prices);
                return $engine->calculateCVaR($returns);
            }
            case 'historical_var': {
                $returns = $this->computeReturns($prices);
                return $engine->calculateHistoricalVaR($returns);
            }
            case 'martin_ratio':
                return $engine->calculateMartinRatio($prices);
            case 'ttm_squeeze':
                $sq = $engine->calculateTTMSqueeze($prices, $p);
                return $sq['is_squeezed'] ? 1.0 : 0.0;
            case 'ttm_momentum':
                return $engine->calculateTTMSqueeze($prices, $p)['momentum'] ?? null;
            case 'aroon_up':
                return $engine->calculateAroon($p)['up'] ?? null;
            case 'aroon_down':
                return $engine->calculateAroon($p)['down'] ?? null;
            case 'aroon_osc':
                return $engine->calculateAroon($p)['oscillator'] ?? null;
            case 'tsi':
                return $engine->calculateTSI($prices);
            case 'vi_plus':
                return $engine->calculateVortex($p)['vi_plus'] ?? null;
            case 'vi_minus':
                return $engine->calculateVortex($p)['vi_minus'] ?? null;
            case 'cmo':
                return $engine->calculateCMO($prices, $p);
            case 'mass_index':
                return $engine->calculateMassIndex();
            case 'connors_rsi':
                return $engine->calculateConnorsRSI($prices);
            case 'rmi':
                return $engine->calculateRMI($prices, $p);
            case 'klinger_osc':
                return $engine->calculateKlingerOscillator();
            case 'rainbow_sma1':
                $rb = $engine->calculateRainbowMA($prices, $p, 1);
                $v = $rb['sma1'] ?? null;
                return $v !== null && $v > 0 ? round((end($prices) / $v) * 100, 2) : null;
            case 'vp_poc':
                return $engine->calculateVolumeProfileLevels($p, 10)['poc'] ?? null;
            case 'vp_vah':
                return $engine->calculateVolumeProfileLevels($p, 10)['vah'] ?? null;
            case 'vp_val':
                return $engine->calculateVolumeProfileLevels($p, 10)['val'] ?? null;
            default:
                return null;
        }
    }

    private function computeReturns(array $prices): array
    {
        $returns = [];
        for ($i = 1; $i < count($prices); $i++) {
            if ($prices[$i - 1] > 0) {
                $returns[] = ($prices[$i] - $prices[$i - 1]) / $prices[$i - 1];
            }
        }
        return $returns;
    }
}
