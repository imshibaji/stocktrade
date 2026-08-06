<?php

namespace App\Controllers;

use App\Analysis\StockTechnicalAnalysisEngine;
use App\Libraries\ForecastEngine;
use App\Models\StockListModel;
use App\Models\StockModel;
use App\Models\StockPriceModel;

class Screener extends BaseController
{
    public function index(): string
    {
        $stockListModel = new StockListModel();
        $userId = current_user_id();
        $savedLists = $userId
            ? $stockListModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll()
            : [];
        $publicLists = $stockListModel->where('is_public', 1)->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title'       => 'Stock Screener - StockTrade Tips',
            'savedLists'  => $savedLists,
            'publicLists' => $publicLists,
            'isLoggedIn'  => !empty($userId),
        ];
        return view('templates/header', $data)
            . view('screener/index', $data)
            . view('templates/footer');
    }

    public function docs(): string
    {
        return view('templates/header'). view('screener/docs') . view('templates/footer');
    }

    public function publicListPage(): string
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 6);
        if (!in_array($perPage, [6, 12, 24, 48], true)) {
            $perPage = 6;
        }

        $listModel = new StockListModel();
        $lists = $listModel
            ->where('is_public', 1)
            ->orderBy('updated_at', 'DESC')
            ->paginate($perPage);

        if (!empty($lists)) {
            $userIds = array_unique(array_column($lists, 'user_id'));
            $users = (new \App\Models\UserModel())->whereIn('id', $userIds)->findAll();
            $names = [];
            foreach ($users as $user) {
                $names[(int) $user['id']] = $user['name'];
            }
            foreach ($lists as &$list) {
                $list['owner_name'] = $names[(int) $list['user_id']] ?? 'Member';
            }
            unset($list);
        }

        $data = [
            'title'    => 'Community Screener Lists - StockTrade Tips',
            'lists'    => $lists,
            'pager'    => $listModel->pager,
            'perPage'  => $perPage,
        ];
        return view('templates/header', $data)
            . view('screener/public_lists', $data)
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

        $results = $this->attachForecasts($results);

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

        $results = $this->attachForecasts($results);

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

    public function compileManualQuery(string $query): array
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

        // Quoted string literal, e.g. sector == "Technology"
        if (strlen($value) >= 2 && (($value[0] === "'" && $value[strlen($value) - 1] === "'") || ($value[0] === '"' && $value[strlen($value) - 1] === '"'))) {
            return ['math_op' => '=', 'math_value' => '', 'value' => substr($value, 1, -1), 'is_string' => true];
        }

        // <base> <mathop> <number>, where base is an indicator ref, field name, or literal.
        // e.g. close > sma(50) * 1.05, current_price > 100 * 1.05, pe_ratio < 15 + 2,
        //      current_price > fifty_day_average * 1.1
        if (preg_match('~^\\s*([a-z_][a-z0-9_]*\\(\\d+\\)|[a-z_][a-z0-9_]*|\\d+(?:\\.\\d+)?)\\s*([+\\-*/%])\\s*(\\d+(?:\\.\\d+)?)\\s*$~i', $value, $m)) {
            $base      = trim($m[1]);
            $mathOp    = $m[2];
            $mathValue = $m[3];

            if (preg_match('~^([a-z_]+)\\((\\d+)\\)$~i', $base, $bm)) {
                return [
                    'math_op' => $mathOp,
                    'math_value' => $mathValue,
                    'value' => strtolower($bm[1]),
                    'is_indicator_ref' => true,
                    'indicator_period' => (int) $bm[2],
                ];
            }

            if (is_numeric($base)) {
                return ['math_op' => $mathOp, 'math_value' => $mathValue, 'value' => floatval($base)];
            }

            return ['math_op' => $mathOp, 'math_value' => $mathValue, 'value' => $base, 'value_is_field' => true];
        }

        // Indicator reference alone, e.g. ema(21), sma(50)
        if (preg_match('~^([a-z_]+)\\((\\d+)\\)$~i', $value, $m)) {
            return [
                'math_op' => '=',
                'math_value' => '',
                'value' => strtolower($m[1]),
                'is_indicator_ref' => true,
                'indicator_period' => (int) $m[2],
            ];
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
        $listId        = (int) ($this->request->getPost('list_id') ?? 0);

        if (empty($name)) {
            return $this->response->setJSON(['success' => false, 'message' => 'List name is required']);
        }

        if (!empty($queryText)) {
            $criteriaData = ['match_mode' => $matchMode, 'is_manual_query' => true, 'query_text' => $queryText];
        } else {
            $criteriaData = ['match_mode' => $matchMode, 'filters' => is_array($criteria) ? $criteria : json_decode($criteria ?? '[]', true)];
        }
        $techData     = ['match_mode' => $matchMode, 'filters' => is_array($techCriteria) ? $techCriteria : json_decode($techCriteria ?? '[]', true)];

        $data = [
            'user_id'            => $userId,
            'name'               => $name,
            'criteria'           => json_encode($criteriaData),
            'technical_criteria' => json_encode($techData),
            'stock_ids'          => is_array($stockIds) ? json_encode($stockIds) : $stockIds,
            'stock_symbols'      => is_array($stockSymbols) ? json_encode($stockSymbols) : $stockSymbols,
            'stock_count'        => is_array($stockIds) ? count($stockIds) : count((array) json_decode($stockIds ?? '[]', true)),
            'is_public'          => $isPublic,
        ];

        $model = new StockListModel();

        if ($listId > 0) {
            $existing = $model->where('id', $listId)->where('user_id', $userId)->first();
            if (!$existing) {
                return $this->response->setJSON(['success' => false, 'message' => 'List not found']);
            }
            $model->update($listId, $data);
            return $this->response->setJSON(['success' => true, 'message' => 'List updated']);
        }

        $model->save($data);

        return $this->response->setJSON(['success' => true, 'message' => 'List saved']);
    }

    public function lists()
    {
        $userId = current_user_id();
        $model  = new StockListModel();
        if ($userId) {
            $lists = $model->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->findAll();
        } else {
            $lists = $model->where('is_public', 1)
                ->orderBy('created_at', 'DESC')
                ->findAll();
        }
        return $this->response->setJSON($lists);
    }

    public function publicLists()
    {
        $model = new StockListModel();
        $lists = $model->where('is_public', 1)
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
        $list   = $model->where('id', $listId);
        if ($userId) {
            $list = $list->where('user_id', $userId);
        } else {
            $list = $list->where('is_public', 1);
        }
        $list = $list->first();
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
            'stocks'             => array_values($stocks),
            'match_mode'         => $matchMode,
            'criteria'           => $filters,
            'technical_criteria' => $techFilters,
            'is_manual_query'    => $isManualQuery,
            'query_text'         => $queryText,
        ]);
    }

    public function publicListDetail(int $id): string
    {
        $listModel = new StockListModel();
        $list = $listModel->find($id);
        if (!$list) {
            return view('templates/header')
                . view('errors/html/error_404')
                . view('templates/footer');
        }

        $userId = current_user_id();
        if (!$list['is_public'] && (!$userId || (int) $list['user_id'] !== (int) $userId)) {
            return view('templates/header')
                . view('errors/html/error_404')
                . view('templates/footer');
        }

        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $stockIds = json_decode($list['stock_ids'] ?? '[]', true);
        $stockIds = array_values(array_filter(array_map('intval', (array) $stockIds)));

        $stockModel = new StockModel();
        $stocks = !empty($stockIds)
            ? $stockModel->whereIn('id', $stockIds)->orderBy('symbol', 'ASC')->paginate($perPage)
            : [];
        $pager = $stockModel->pager;

        $ownerName = 'Member';
        $user = (new \App\Models\UserModel())->find((int) $list['user_id']);
        if ($user) {
            $ownerName = $user['name'];
        }

        $predictions = [];
        if (!empty($stocks)) {
            $pageIds = array_column($stocks, 'id');
            $predRows = (new \App\Models\PredictionModel())->getPredictionsForStocks($pageIds, 30);
            foreach ($predRows as $p) {
                $sid = (int) $p['stock_id'];
                $predictions[$sid]['prices'][] = (float) $p['predicted_price'];
                $predictions[$sid]['scores'][] = (float) $p['confidence_score'];
            }
            foreach ($predictions as $sid => $d) {
                $predictions[$sid]['avg']  = array_sum($d['prices']) / count($d['prices']);
                $predictions[$sid]['high'] = max($d['prices']);
                $predictions[$sid]['low']  = min($d['prices']);
                $predictions[$sid]['conf'] = round(array_sum($d['scores']) / count($d['scores']), 0);
            }
        }

        $criteriaData = json_decode($list['criteria'] ?? '{}', true);
        $techData     = json_decode($list['technical_criteria'] ?? '{}', true);

        $data = [
            'title'         => $list['name'] . ' - Community Screener List',
            'list'          => $list,
            'stocks'        => $stocks,
            'pager'         => $pager,
            'perPage'       => $perPage,
            'ownerName'     => $ownerName,
            'predictions'   => $predictions,
            'matchMode'     => $criteriaData['match_mode'] ?? 'all',
            'filters'       => $criteriaData['filters'] ?? [],
            'techFilters'   => $techData['filters'] ?? [],
            'isManualQuery' => !empty($criteriaData['is_manual_query']),
            'queryText'     => $criteriaData['query_text'] ?? '',
        ];

        return view('templates/header', $data)
            . view('screener/public_list_detail', $data)
            . view('templates/footer');
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
            $base = $fieldVal !== null ? (float) $fieldVal : 0.0;
        } else {
            $base = (float) ($f['value'] ?? 0);
        }
        return $this->applyMath($base, (string) ($f['math_op'] ?? '='), (float) ($f['math_value'] ?? 0));
    }

    private function applyMath(float $base, string $op, float $val): float
    {
        if ($op === '=') {
            return $base;
        }
        switch ($op) {
            case '+': return $base + $val;
            case '-': return $base - $val;
            case '*': return $base * $val;
            case '/': return $val != 0 ? $base / $val : $base;
            case '%': return $val != 0 ? fmod($base, $val) : $base;
            default:  return $base;
        }
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

    public function matchesFilters(array $stock, array $filters, string $mode = 'all'): bool
    {
        if (empty($filters)) return true;

        $results = [];
        foreach ($filters as $f) {
            $field    = $f['field'] ?? '';
            $op       = $f['op'] ?? '';
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

    public function matchesTechnicalFilters(array $ohlcv, array $techFilters, string $mode = 'all', ?array $stock = null): bool
    {
        if (empty($techFilters)) return true;
        if (empty($ohlcv)) return false;

        $results = [];
        foreach ($techFilters as $f) {
            $indicator = $f['indicator'] ?? '';
            $op        = $f['op'] ?? '';
            $period    = (int) ($f['period'] ?? 14);

            $indicatorValue = $this->calculateIndicator($ohlcv, $indicator, $period);
            if ($indicatorValue === null) { $results[] = false; continue; }

            $compareVal = $this->resolveFilterValue($f, $stock ?? []);

            if (!empty($f['is_indicator_ref'])) {
                $rhsValue = $this->calculateIndicator($ohlcv, $f['value'] ?? '', (int) ($f['indicator_period'] ?? 14));
                if ($rhsValue === null) { $results[] = false; continue; }
                $compareVal = $this->applyMath((float) $rhsValue, (string) ($f['math_op'] ?? '='), (float) ($f['math_value'] ?? 0));
            }

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

    /**
     * Attach a forecast to each matched stock when a forecast method was requested.
     */
    private function attachForecasts(array $stocks): array
    {
        $method = (string) $this->request->getGet('method');
        if ($method === '' && (string) $this->request->getPost('method') !== '') {
            $method = (string) $this->request->getPost('method');
        }

        if ($method === '' || empty($stocks)) {
            return $stocks;
        }

        $supported = (new ForecastEngine())->supportedMethods();
        if (!in_array($method, $supported, true)) {
            return $stocks;
        }

        $horizon = (int) ($this->request->getGet('horizon_days') ?: $this->request->getPost('horizon_days'));
        $horizon = max(1, min((int) $horizon > 0 ? (int) $horizon : 7, 60));

        $priceModel = new StockPriceModel();
        $ids = array_column($stocks, 'id');
        $prices = $priceModel
            ->whereIn('stock_id', $ids)
            ->orderBy('stock_id', 'ASC')
            ->orderBy('price_date', 'ASC')
            ->findAll();
        $byStock = [];
        foreach ($prices as $p) {
            $byStock[(int) $p['stock_id']][] = $p;
        }

        $engine = new ForecastEngine();
        foreach ($stocks as &$s) {
            $sid = (int) $s['id'];
            $ohlcv = $byStock[$sid] ?? [];
            if (empty($ohlcv)) {
                $s['forecast'] = null;
                continue;
            }
            $s['forecast'] = $engine->loadData($ohlcv)->predict($method, $horizon);
        }
        unset($s);

        return $stocks;
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

        $p = $period >= 2 ? $period : 14;

        switch ($indicator) {
            case 'sma':
                return $engine->calculateSMA($prices, $p);
            case 'ema':
                return $engine->calculateEMA($prices, $p);
            case 'close':
                return !empty($prices) ? (float) end($prices) : null;
            case 'open':
                $opens = array_map('floatval', array_column($ohlcv, 'open'));
                return !empty($opens) ? (float) end($opens) : null;
            case 'high':
                return !empty($highs) ? (float) end($highs) : null;
            case 'low':
                return !empty($lows) ? (float) end($lows) : null;
            case 'volume':
                return !empty($volumes) ? (float) end($volumes) : null;
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
