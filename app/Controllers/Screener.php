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

    public function docs(): string
    {
        return view('templates/header'). view('screener/docs') . view('templates/footer');
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

        foreach (['historical_filters', 'summaries_filters'] as $extraParam) {
            $extra = $this->request->getGet($extraParam);
            $extra = $extra ? json_decode($extra, true) : [];
            if (!empty($extra)) {
                $filters = array_merge($filters, $extra);
            }
        }

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

    public function runManualQuery()
    {
        $query  = trim($this->request->getPost('query') ?? '');
        $match  = $this->request->getPost('match_mode') ?? 'all';
        $listName = $this->request->getPost('list_name') ?? '';
        $listDesc = $this->request->getPost('list_desc') ?? '';

        if (empty($query)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Query is required']);
        }

        $compiled = $this->compileManualQuery($query);
        if (!$compiled['valid']) {
            return $this->response->setJSON(['success' => false, 'message' => $compiled['error']]);
        }

         $stockModel = new StockModel();
         $stocks = $stockModel->findAll();

         $techFilters = [];
         $fundFilters = [];
         $needsDerivedFields = false;
         $derivedFieldNames = [
             'fiftyDayAverage', 'fifty_day_average',
             'twoHundredDayAverage', 'two_hundred_day_average',
             'fiftyDayAverageChange', 'fifty_day_average_change',
             'fiftyDayAverageChangePercent', 'fifty_day_average_change_pct',
             'twoHundredDayAverageChange', 'two_hundred_day_average_change',
             'twoHundredDayAverageChangePercent', 'two_hundred_day_average_change_pct',
         ];
         foreach ($compiled['filters'] as $f) {
             if (!empty($f['is_technical'])) {
                 $techFilters[] = $f;
             } else {
                 $fieldName = $f['field'] ?? '';
                 if (in_array($fieldName, $derivedFieldNames)) {
                     $needsDerivedFields = true;
                 }
                 $fundFilters[] = $f;
             }
         }

         if (!empty($techFilters) || $needsDerivedFields) {
             $priceModel = new StockPriceModel();
             $allStockIds = array_column($stocks, 'id');
             $allPrices = $priceModel->whereIn('stock_id', $allStockIds)
                 ->orderBy('stock_id', 'ASC')
                 ->orderBy('price_date', 'ASC')
                 ->findAll();
             $pricesByStock = [];
             foreach ($allPrices as $p) {
                 $sid = (int) $p['stock_id'];
                 $pricesByStock[$sid][] = $p;
             }
             foreach ($stocks as &$s) {
                 $sid = (int) $s['id'];
                 $ohlcv = $pricesByStock[$sid] ?? [];
                 $s = $this->augmentStockWithDerivedFields($s, $ohlcv);
             }
             unset($s);
         }

         $fundResults = [];
         foreach ($stocks as $s) {
             if ($this->matchesFilters($s, $fundFilters, $match)) {
                 $fundResults[] = $s;
             }
         }

         if (!empty($techFilters)) {
             $results = $this->applyTechnicalFilters($fundResults, $techFilters, $match);
         } else {
             $results = $fundResults;
         }

        $response = [
            'success'  => true,
            'total'    => count($results),
            'stocks'   => array_values($results),
            'query'    => $query,
            'compiled' => $compiled['filters'],
            'match_mode' => $match,
        ];

        if (!empty($listName) && !empty($results)) {
            $userId = current_user_id();
            $listModel = new StockListModel();
            $stockIds = array_column($results, 'id');
            $stockSymbols = array_column($results, 'symbol');
            $listModel->save([
                'user_id'            => $userId,
                'name'               => $listName,
                'criteria'           => json_encode(['match_mode' => $match, 'filters' => $compiled['filters'], 'is_manual_query' => true, 'query_text' => $query]),
                'technical_criteria' => json_encode(['match_mode' => $match, 'filters' => []]),
                'stock_ids'          => json_encode($stockIds),
                'stock_symbols'      => json_encode($stockSymbols),
                'stock_count'        => count($results),
                'is_public'          => $this->request->getPost('is_public') ? 1 : 0,
            ]);
            $response['saved'] = true;
        }

        return $this->response->setJSON($response);
    }

    private function compileManualQuery(string $query): array
    {
        $query = trim($query);
        if (empty($query)) {
            return ['valid' => false, 'error' => 'Query is empty', 'filters' => []];
        }

        $validOps = ['>', '>=', '<', '<=', '==', '!='];
        $validMathOps = ['=', '+', '-', '*', '/', '%'];

        $clauses = preg_split('/\s+(AND|OR)\s+/i', $query, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($clauses === false || count($clauses) === 0) {
            return ['valid' => false, 'error' => 'Invalid query syntax.', 'filters' => []];
        }

        $filters = [];
        $currentLogic = 'AND';

        foreach ($clauses as $i => $clause) {
            $clause = trim($clause);
            if ($clause === '') {
                continue;
            }

            $upper = strtoupper($clause);
            if ($upper === 'AND' || $upper === 'OR') {
                $currentLogic = $upper;
                continue;
            }

            $bestOp = null;
            $bestOpLen = 0;
            $bestOpPos = -1;

            foreach ($validOps as $op) {
                $opLen = strlen($op);
                $searchFrom = 0;
                while (($opPos = strpos($clause, $op, $searchFrom)) !== false) {
                    $prefix = trim(substr($clause, 0, $opPos));
                    $suffix = trim(substr($clause, $opPos + $opLen));
                    if ($prefix !== '' && $suffix !== '') {
                        if ($opLen > $bestOpLen) {
                            $validPrefix = preg_match('~^[\w.]+(\(\d+\))?$~', $prefix);
                            if ($validPrefix) {
                                $bestOp = $op;
                                $bestOpLen = $opLen;
                                $bestOpPos = $opPos;
                            }
                        }
                    }
                    $searchFrom = $opPos + 1;
                }
            }

            if ($bestOp === null || $bestOpPos < 0) {
                return ['valid' => false, 'error' => 'Cannot parse condition: "' . $clause . '". Use format: field operator value', 'filters' => []];
            }

             $field = trim(substr($clause, 0, $bestOpPos));
             $value = trim(substr($clause, $bestOpPos + $bestOpLen));

             if ($field === '' || $value === '') {
                 return ['valid' => false, 'error' => 'Missing field or value in: "' . $clause . '"', 'filters' => []];
             }

             $isTech = preg_match('~^([a-z_]+)\((\d+)\)$~i', $field, $m);
             $filterExtra = [];
             if ($isTech) {
                 $filterExtra = ['is_technical' => true, 'indicator' => strtolower($m[1]), 'period' => (int) $m[2]];
             } else {
                 $techIndicatorNames = [
                     'sma_pct', 'ema_pct', 'vwap_ratio', 'macd', 'macd_signal', 'macd_histogram',
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
                 ];
                 if (in_array(strtolower($field), $techIndicatorNames)) {
                     $filterExtra = ['is_technical' => true, 'indicator' => strtolower($field), 'period' => 0];
                 }
             }

             $parsed = $this->parseConditionSide($value, $validMathOps);
            if ($parsed === null) {
                return ['valid' => false, 'error' => 'Cannot parse value: "' . $value . '"', 'filters' => []];
            }

             $filters[] = array_merge([
                 'field' => $field,
                 'op' => $bestOp,
                 'logic' => $currentLogic,
             ] + $filterExtra, $parsed);

            $currentLogic = 'AND';
        }

        if (empty($filters)) {
            return ['valid' => false, 'error' => 'No valid conditions found.', 'filters' => []];
        }

        return ['valid' => true, 'filters' => $filters, 'error' => ''];
    }

    private function parseConditionSide(string $value, array $validMathOps): ?array
    {
        $value = trim($value);

        foreach ($validMathOps as $mo) {
            if ($mo === '=') continue;
            $parts = explode($mo, $value, 2);
            if (count($parts) === 2 && trim($parts[0]) !== '' && trim($parts[1]) !== '') {
                $left = trim($parts[0]);
                $right = trim($parts[1]);
                if (is_numeric($left) && is_numeric($right)) {
                    return ['math_op' => $mo, 'math_value' => $right, 'value' => $left];
                }
                if (is_numeric($left)) {
                    return ['math_op' => $mo, 'math_value' => $right, 'value' => $left];
                }
                return ['math_op' => '=', 'math_value' => '', 'value' => $value];
            }
        }

        if (strlen($value) >= 2 && (($value[0] === "'" && $value[strlen($value) - 1] === "'") || ($value[0] === '"' && $value[strlen($value) - 1] === '"'))) {
            return ['math_op' => '=', 'math_value' => '', 'value' => substr($value, 1, -1), 'is_string' => true];
        }

        if (preg_match('~^([a-zA-Z_][a-zA-Z0-9_]*)\\s*([+\\-*%])\\s*(\\d+\\.?\\d*)$~', $value, $m)) {
            return ['math_op' => $m[2], 'math_value' => $m[3], 'value' => $m[1], 'is_field_ref' => true];
        }

        if (preg_match('~^(\\d+\\.?\\d*)\\s*([+\\-*%])\\s*([a-zA-Z_][a-zA-Z0-9_]*)$~', $value, $m)) {
            return ['math_op' => $m[2], 'math_value' => $m[3], 'value' => $m[1], 'is_field_ref' => true, 'math_value_is_field' => true];
        }

        if (is_numeric($value)) {
            return ['math_op' => '=', 'math_value' => '', 'value' => floatval($value)];
        }

        return ['math_op' => '=', 'math_value' => '', 'value' => $value, 'is_string' => true];
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
        $queryText     = $this->request->getPost('query_text');
        $isPublic      = $this->request->getPost('is_public') ? 1 : 0;

        if (empty($name)) {
            return $this->response->setJSON(['success' => false, 'message' => 'List name is required']);
        }

        if (!empty($queryText)) {
            $criteriaData = ['match_mode' => $matchMode, 'is_manual_query' => true, 'query_text' => $queryText];
        } else {
            $criteriaData = ['match_mode' => $matchMode, 'filters' => is_array($criteria) ? $criteria : json_decode($criteria ?? '[]', true)];
        }
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
            'is_public'          => $isPublic,
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
        $isManualQuery    = !empty($criteriaData['is_manual_query']);
        $queryText        = $criteriaData['query_text'] ?? '';

        return $this->response->setJSON([
            'success'            => true,
            'list'               => $list,
            'stocks'             => $stocks,
            'match_mode'         => $matchMode,
            'criteria'           => $filters,
            'technical_criteria' => $techFilters,
            'is_manual_query'    => $isManualQuery,
            'query_text'         => $queryText,
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

    public function togglePublic()
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
        $newState = $list['is_public'] ? 0 : 1;
        $model->update($listId, ['is_public' => $newState]);
        return $this->response->setJSON(['success' => true, 'is_public' => $newState]);
    }

    public function publicList($listId = null)
    {
        $listId = (int) ($listId ?? $this->request->getGet('id'));
        if ($listId <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid ID']);
        }
        $model = new StockListModel();
        $list  = $model->where('id', $listId)->where('is_public', 1)->first();
        if (!$list) {
            return $this->response->setJSON(['success' => false, 'message' => 'List not found']);
        }

        $stockIds = json_decode($list['stock_ids'] ?? '[]', true);
        $stocks = !empty($stockIds)
            ? (new StockModel())->whereIn('id', $stockIds)->findAll()
            : [];

        return $this->response->setJSON([
            'success' => true,
            'list'    => $list,
            'stocks'  => array_values($stocks),
        ]);
    }

    // ------------------------------------------------------------------ //
    //  Private Helpers
    // ------------------------------------------------------------------ //

    private function resolveFilterValue(array $f, array $stock): ?float
    {
        if (!empty($f['is_string'])) {
            return null;
        }
        if (!empty($f['value_is_field'])) {
            $fieldVal = $this->getFieldValue($stock, $f['value'] ?? '');
            return $fieldVal !== null ? (float) $fieldVal : 0.0;
        }
        return (float) ($f['value'] ?? 0);
    }

    private function resolveFilterStringValue(array $f, array $stock): ?string
    {
        if (empty($f['is_string'])) {
            return null;
        }
        if (!empty($f['value_is_field'])) {
            return $this->getFieldStringValue($stock, $f['value'] ?? '');
        }
        $raw = $f['value'] ?? null;
        if ($raw !== null && preg_match('/^([\'"])(.*)\\1$/', $raw, $m)) {
            return $m[2];
        }
        return $raw;
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
            $filterLogic = $f['logic'] ?? 'AND';
            $isString = !empty($f['is_string']);

            if ($isString) {
                $stockVal = $this->getFieldStringValue($stock, $field);
                if ($stockVal === null) { $results[] = false; continue; }
                $compareVal = $this->resolveFilterStringValue($f, $stock);
                if ($compareVal === null) { $results[] = false; continue; }
                $compareStr = is_array($compareVal) ? ($compareVal['value'] ?? '') : (string) $compareVal;
                switch ($op) {
                    case '==': $results[] = strtolower($stockVal) === strtolower($compareStr); break;
                    case '!=': $results[] = strtolower($stockVal) !== strtolower($compareStr); break;
                    default:   $results[] = false;
                }
                continue;
            }

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

            $pass = false;
            switch ($op) {
                case '>':  $pass = $stockVal > $compareVal; break;
                case '>=': $pass = $stockVal >= $compareVal; break;
                case '<':  $pass = $stockVal < $compareVal; break;
                case '<=': $pass = $stockVal <= $compareVal; break;
                case '==': $pass = (float) $stockVal == $compareVal; break;
                case '!=': $pass = (float) $stockVal != $compareVal; break;
                default:   $pass = false;
            }
            $results[] = $pass;
        }

        if (count($filters) === 1) {
            return $results[0];
        }

        if ($mode === 'any') {
            return in_array(true, $results, true);
        }

        // Check per-filter logic if mixed AND/OR conditions exist
        $hasMixedLogic = false;
        foreach ($filters as $f) {
            if (($f['logic'] ?? 'AND') !== 'AND') {
                $hasMixedLogic = true;
                break;
            }
        }

        if ($hasMixedLogic) {
            $result = true;
            $currentGroup = true;
            foreach ($results as $i => $r) {
                $logic = ($filters[$i]['logic'] ?? 'AND');
                if ($logic === 'OR') {
                    $currentGroup = $currentGroup || $r;
                } else {
                    $result = $result && $currentGroup;
                    $currentGroup = $r;
                }
            }
            return $result && $currentGroup;
        }

        return !in_array(false, $results, true);
    }

    private function getFieldValue(array $stock, string $field): ?float
    {
        $map = [
            'price'                          => $stock['current_price'] ?? null,
            'current_price'                  => $stock['current_price'] ?? null,
            'regularMarketPrice'             => $stock['regularMarketPrice'] ?? null,
            'regularMarketPreviousClose'      => $stock['regularMarketPreviousClose'] ?? null,
            'previous_close'                 => $stock['previous_close'] ?? null,
            'regularMarketOpen'              => $stock['regularMarketOpen'] ?? null,
            'regularMarketDayHigh'           => $stock['regularMarketDayHigh'] ?? null,
            'regularMarketDayLow'            => $stock['regularMarketDayLow'] ?? null,
            'regularMarketChange'            => $stock['regularMarketChange'] ?? null,
            'regularMarketChangePercent'     => $stock['regularMarketChangePercent'] ?? null,
            'market_cap'                     => $stock['market_cap'] ?? null,
            'pe_ratio'                       => $stock['pe_ratio'] ?? null,
            'trailingPE'                     => $stock['trailingPE'] ?? null,
            'forwardPE'                      => $stock['forwardPE'] ?? null,
            'epsTrailingTwelveMonths'        => $stock['epsTrailingTwelveMonths'] ?? null,
            'epsForward'                     => $stock['epsForward'] ?? null,
            'dividend_yield'                 => $stock['dividend_yield'] ?? null,
            'trailingAnnualDividendYield'    => $stock['trailingAnnualDividendYield'] ?? null,
            'trailingAnnualDividendRate'     => $stock['trailingAnnualDividendRate'] ?? null,
            'beta'                           => $stock['beta'] ?? null,
            'avg_volume'                     => $stock['avg_volume'] ?? null,
            'averageDailyVolume10Day'        => $stock['averageDailyVolume10Day'] ?? null,
            'averageDailyVolume3Month'       => $stock['averageDailyVolume3Month'] ?? null,
            'regularMarketVolume'            => $stock['regularMarketVolume'] ?? null,
            'week_52_high'                   => $stock['week_52_high'] ?? null,
            'fiftyTwoWeekHigh'               => $stock['fiftyTwoWeekHigh'] ?? null,
            'week_52_low'                    => $stock['week_52_low'] ?? null,
            'fiftyTwoWeekLow'                => $stock['fiftyTwoWeekLow'] ?? null,
            'fiftyDayAverage'                => $stock['fiftyDayAverage'] ?? null,
            'twoHundredDayAverage'           => $stock['twoHundredDayAverage'] ?? null,
            'bookValue'                      => $stock['bookValue'] ?? null,
            'priceToBook'                    => $stock['priceToBook'] ?? null,
            'priceHint'                      => $stock['priceHint'] ?? null,
            'sharesOutstanding'              => $stock['sharesOutstanding'] ?? null,
            'marketCap'                      => $stock['market_cap'] ?? null,
            'fifty_day_average'              => $stock['fifty_day_average'] ?? null,
            'fiftyDayAverage'               => $stock['fifty_day_average'] ?? null,
            'two_hundred_day_average'        => $stock['two_hundred_day_average'] ?? null,
            'twoHundredDayAverage'           => $stock['two_hundred_day_average'] ?? null,
            'fifty_day_average_change'      => $stock['fifty_day_average_change'] ?? null,
            'fiftyDayAverageChange'         => $stock['fiftyDayAverageChange'] ?? null,
            'fifty_day_average_change_pct'  => $stock['fifty_day_average_change_pct'] ?? null,
            'fiftyDayAverageChangePercent'  => $stock['fiftyDayAverageChangePercent'] ?? null,
            'two_hundred_day_average_change' => $stock['two_hundred_day_average_change'] ?? null,
            'twoHundredDayAverageChange'         => $stock['twoHundredDayAverageChange'] ?? null,
            'two_hundred_day_average_change_pct'  => $stock['two_hundred_day_average_change_pct'] ?? null,
            'twoHundredDayAverageChangePercent'  => $stock['twoHundredDayAverageChangePercent'] ?? null,
            'regularMarketChange'            => $stock['regularMarketChange'] ?? null,
            'regularMarketChangePercent'     => $stock['regularMarketChangePercent'] ?? null,
            'marketState'                    => $stock['marketState'] ?? null,
            'tradeable'                      => $stock['tradeable'] ?? null,
            'shares_outstanding'             => $stock['shares_outstanding'] ?? null,
            'sharesOutstanding'              => $stock['sharesOutstanding'] ?? null,
            'enterprise_value'               => $stock['enterprise_value'] ?? null,
            'enterpriseValue'                => $stock['enterpriseValue'] ?? null,
            'forward_pe'                     => $stock['forward_pe'] ?? null,
            'forwardPE'                      => $stock['forwardPE'] ?? null,
            'peg_ratio'                      => $stock['peg_ratio'] ?? null,
        ];
        $v = $map[$field] ?? null;
        return $v !== null ? (float) $v : null;
    }

    private function getFieldStringValue(array $stock, string $field): ?string
    {
        $map = [
            'name'                    => $stock['name'] ?? null,
            'sector'                  => $stock['sector'] ?? null,
            'symbol'                  => $stock['symbol'] ?? null,
            'exchange'                => $stock['exchange'] ?? null,
            'currency'                => $stock['currency'] ?? null,
            'financialCurrency'       => $stock['financialCurrency'] ?? 'INR',
            'fullExchangeName'        => $stock['fullExchangeName'] ?? 'NSE',
            'exchangeTimezoneName'    => $stock['exchangeTimezoneName'] ?? 'Asia/Kolkata',
            'exchangeTimezoneShortName' => $stock['exchangeTimezoneShortName'] ?? 'IST',
            'quoteSourceName'         => $stock['quoteSourceName'] ?? 'Delayed Quote',
            'quoteType'               => $stock['quoteType'] ?? 'EQUITY',
            'market'                  => $stock['market'] ?? 'in_market',
            'longName'                => $stock['name'] ?? null,
            'shortName'               => $stock['name'] ?? null,
            'marketState'             => $stock['marketState'] ?? null,
            'language'                => $stock['language'] ?? 'en-US',
            'messageBoardId'          => $stock['messageBoardId'] ?? null,
            'sourceInterval'          => $stock['sourceInterval'] ?? null,
            'tradeable'               => $stock['tradeable'] ?? null,
        ];
        return $map[$field] ?? null;
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

    private function computeSMA(array $prices, int $period): ?float
    {
        if (count($prices) < $period) return null;
        $slice = array_slice($prices, -$period);
        return array_sum($slice) / $period;
    }

    private function augmentStockWithDerivedFields(array $stock, array $ohlcv): array
    {
        if (empty($ohlcv)) return $stock;
        $closes = array_map(function ($p) { return (float) ($p['close'] ?? 0); }, $ohlcv);
        if (empty($closes)) return $stock;

        $sma50 = $this->computeSMA($closes, 50);
        $sma200 = $this->computeSMA($closes, 200);
        $currentPrice = (float) ($stock['current_price'] ?? 0);

        if ($sma50 !== null) {
            $stock['fifty_day_average'] = round($sma50, 2);
            if ($sma50 > 0) {
                $stock['fifty_day_average_change'] = round($currentPrice - $sma50, 2);
                $stock['fifty_day_average_change_pct'] = round((($currentPrice - $sma50) / $sma50) * 100, 2);
            }
        }
        if ($sma200 !== null) {
            $stock['two_hundred_day_average'] = round($sma200, 2);
            if ($sma200 > 0) {
                $stock['two_hundred_day_average_change'] = round($currentPrice - $sma200, 2);
                $stock['two_hundred_day_average_change_pct'] = round((($currentPrice - $sma200) / $sma200) * 100, 2);
            }
        }
        return $stock;
    }
}
