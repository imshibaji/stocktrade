<?php

namespace App\Controllers;

use App\Libraries\ForecastEngine;
use App\Libraries\StockQueryService;
use App\Models\PredictionQueryModel;
use App\Models\PredictionQueryResultModel;
use App\Models\StockModel;
use App\Models\StockPriceModel;

class PredictionQuery extends BaseController
{
    protected StockQueryService $stockQueryService;
    protected ForecastEngine $forecastEngine;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->stockQueryService = new StockQueryService();
        $this->forecastEngine = new ForecastEngine();
    }

    public function index(): string
    {
        $userId = current_user_id();
        $model = new PredictionQueryModel();
        $perPage = $this->validPerPage();
        $pager = null;
        $queries = [];

        if ($userId) {
            $queries = $model->getUserWithResults($userId, $perPage);
            $pager = $model->pager;
        }

        $data = [
            'title' => 'Prediction Queries',
            'queries' => $queries,
            'pager' => $pager,
            'perPage' => $perPage,
        ];
        return view('templates/header', $data)
            . view('prediction/query/index', $data)
            . view('templates/footer');
    }

    public function create(): string
    {
        $data = [
            'title' => 'Create Prediction Query',
            'methods' => $this->forecastEngine->supportedMethods(),
        ];
        return view('templates/header', $data)
            . view('prediction/query/create', $data)
            . view('templates/footer');
    }

    public function save()
    {
        $userId = current_user_id();
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $name = $this->request->getPost('name');
        $method = $this->request->getPost('method');
        $horizonDays = (int) ($this->request->getPost('horizon_days') ?? 7);
        $matchMode = $this->request->getPost('match_mode') ?? 'all';
        $isPublic = $this->request->getPost('is_public') ? 1 : 0;

        $criteria = null;
        $technicalCriteria = null;
        $queryText = null;

        if ($this->request->getPost('query_text')) {
            $queryText = $this->request->getPost('query_text');
            $compiled = $this->stockQueryService->compileManualQuery($queryText);
            if (!$compiled['valid']) {
                return $this->response->setJSON(['success' => false, 'message' => $compiled['error']]);
            }
            $criteria = $compiled['filters'];
        } else {
            $criteria = is_array($this->request->getPost('criteria'))
                ? $this->request->getPost('criteria')
                : json_decode($this->request->getPost('criteria') ?? '[]', true);

            $technicalCriteria = is_array($this->request->getPost('technical_criteria'))
                ? $this->request->getPost('technical_criteria')
                : json_decode($this->request->getPost('technical_criteria') ?? '[]', true);
        }

        $model = new PredictionQueryModel();
        $model->save([
            'user_id' => $userId,
            'name' => $name,
            'query_text' => $queryText,
            'criteria' => $criteria ? json_encode($criteria) : null,
            'technical_criteria' => $technicalCriteria ? json_encode($technicalCriteria) : null,
            'match_mode' => $matchMode,
            'method' => $method,
            'horizon_days' => $horizonDays,
            'is_public' => $isPublic,
            'status' => 'pending',
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Prediction query saved']);
    }

    public function show(int $id): string
    {
        $userId = current_user_id();
        $queryModel = new PredictionQueryModel();
        $resultModel = new PredictionQueryResultModel();

        $query = $queryModel->getWithResults($id);
        if (!$query || ($query['user_id'] != $userId && !$query['is_public'])) {
            return view('templates/header')
                . view('errors/html/error_404')
                . view('templates/footer');
        }

        $perPage = $this->validResultsPerPage();
        $results = $resultModel->getByQueryIdPaginated($id, $perPage);
        $query['results'] = $results;
        $query['total_results'] = $query['results_count'] ?? count($results);

        $data = [
            'title' => $query['name'] . ' - Prediction Query',
            'query' => $query,
            'pager' => $resultModel->pager,
            'perPage' => $perPage,
        ];
        return view('templates/header', $data)
            . view('prediction/query/show', $data)
            . view('templates/footer');
    }

    public function edit(int $id): string
    {
        $userId = current_user_id();
        $model = new PredictionQueryModel();
        $query = $model->getById($id);

        if (!$query || $query['user_id'] != $userId) {
            return view('templates/header')
                . view('errors/html/error_404')
                . view('templates/footer');
        }

        $data = [
            'title' => 'Edit Prediction Query',
            'query' => $query,
            'methods' => $this->forecastEngine->supportedMethods(),
        ];
        return view('templates/header', $data)
            . view('prediction/query/edit', $data)
            . view('templates/footer');
    }

    public function update(int $id)
    {
        $userId = current_user_id();
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $model = new PredictionQueryModel();
        $query = $model->getById($id);

        if (!$query || $query['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found']);
        }

        $name = $this->request->getPost('name');
        $method = $this->request->getPost('method');
        $horizonDays = (int) ($this->request->getPost('horizon_days') ?? $query['horizon_days']);
        $matchMode = $this->request->getPost('match_mode') ?? $query['match_mode'];
        $isPublic = $this->request->getPost('is_public') ? 1 : ($query['is_public'] ?? 0);

        $criteria = $query['criteria'];
        $technicalCriteria = $query['technical_criteria'];
        $queryText = null;

        if ($this->request->getPost('query_text')) {
            $queryText = $this->request->getPost('query_text');
            $compiled = $this->stockQueryService->compileManualQuery($queryText);
            if (!$compiled['valid']) {
                return $this->response->setJSON(['success' => false, 'message' => $compiled['error']]);
            }
            $criteria = $compiled['filters'];
            $technicalCriteria = null;
        } else {
            $criteria = is_array($this->request->getPost('criteria'))
                ? $this->request->getPost('criteria')
                : json_decode($this->request->getPost('criteria') ?? $query['criteria'] ?? '[]', true);

            $technicalCriteria = is_array($this->request->getPost('technical_criteria'))
                ? $this->request->getPost('technical_criteria')
                : json_decode($this->request->getPost('technical_criteria') ?? $query['technical_criteria'] ?? '[]', true);
            $queryText = $query['query_text'] ?? null;
        }

        $model->update($id, [
            'name' => $name,
            'query_text' => $queryText,
            'criteria' => $criteria ? json_encode($criteria) : null,
            'technical_criteria' => $technicalCriteria ? json_encode($technicalCriteria) : null,
            'match_mode' => $matchMode,
            'method' => $method,
            'horizon_days' => $horizonDays,
            'is_public' => $isPublic,
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Prediction query updated']);
    }

    public function delete(int $id)
    {
        $userId = current_user_id();
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $model = new PredictionQueryModel();
        $query = $model->getById($id);

        if (!$query || $query['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found']);
        }

        $model->delete($id);

        return $this->response->setJSON(['success' => true, 'message' => 'Prediction query deleted']);
    }

    public function run(int $id)
    {
        $userId = current_user_id();
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $queryModel = new PredictionQueryModel();
        $query = $queryModel->getById($id);

        if (!$query || $query['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found']);
        }

        $queryModel->update($id, ['status' => 'running']);

        try {
            $results = $this->executePredictionQuery($query);

            $queryModel->update($id, [
                'status' => 'completed',
                'last_run_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON(['success' => true, 'results' => $results]);
        } catch (\Exception $e) {
            $queryModel->update($id, ['status' => 'pending']);
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function results(int $id)
    {
        return redirect()->to('/predictions/' . $id);
    }

    public function publicList(): string
    {
        $queryModel = new PredictionQueryModel();
        $perPage = $this->validPerPage();
        $queries = $queryModel->getPublic($perPage);

        $data = [
            'title' => 'Public Prediction Queries',
            'queries' => $queries,
            'pager' => $queryModel->pager,
            'perPage' => $perPage,
        ];
        return view('templates/header', $data)
            . view('prediction/query/public_list', $data)
            . view('templates/footer');
    }

    public function publicShow(int $id): string
    {
        $queryModel = new PredictionQueryModel();
        $resultModel = new PredictionQueryResultModel();
        $query = $queryModel->getWithResults($id);

        if (!$query || !$query['is_public']) {
            return view('templates/header')
                . view('errors/html/error_404')
                . view('templates/footer');
        }

        $perPage = $this->validResultsPerPage();
        $results = $resultModel->getByQueryIdPaginated($id, $perPage);
        $query['results'] = $results;
        $query['total_results'] = $query['results_count'] ?? count($results);

        $data = [
            'title' => $query['name'] . ' - Public Prediction',
            'query' => $query,
            'pager' => $resultModel->pager,
            'perPage' => $perPage,
        ];
        return view('templates/header', $data)
            . view('prediction/query/public_show', $data)
            . view('templates/footer');
    }

    public function togglePublic(int $id)
    {
        $userId = current_user_id();
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $queryModel = new PredictionQueryModel();
        $query = $queryModel->getById($id);

        if (!$query || $query['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found']);
        }

        $queryModel->togglePublic($id);

        $updated = $queryModel->getById($id);

        return $this->response->setJSON(['success' => true, 'is_public' => $updated['is_public']]);
    }

    private function validPerPage(): int
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 6);

        return in_array($perPage, [6, 12, 24, 48], true) ? $perPage : 6;
    }

    private function validResultsPerPage(): int
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);

        return in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
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

    private function executePredictionQuery(array $query): array
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

        if (!empty($batchResults)) {
            $resultsModel->where('query_id', $query['id'])->delete();
            $resultsModel->insertBatch($batchResults);
        }

        return $results;
    }
}
