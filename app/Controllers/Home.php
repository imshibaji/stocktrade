<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $stockModel = new \App\Models\StockModel();
        $allStocks  = $stockModel->orderBy('market_cap', 'DESC')->findAll();

        $topPerformer = null;
        $topLoser     = null;
        foreach ($allStocks as $stock) {
            $change = get_price_change((float) $stock['current_price'], (float) $stock['previous_close']);
            $entry  = ['stock' => $stock, 'change' => $change];
            if ($topPerformer === null || $change['percent'] > $topPerformer['change']['percent']) {
                $topPerformer = $entry;
            }
            if ($topLoser === null || $change['percent'] < $topLoser['change']['percent']) {
                $topLoser = $entry;
            }
        }

        $sameStock = $topPerformer !== null && $topLoser !== null
            && (int) $topPerformer['stock']['id'] === (int) $topLoser['stock']['id'];

        $topStocks = $this->featuredTopStocks($allStocks);

        $publicLists = (new \App\Models\StockListModel())
            ->where('is_public', 1)
            ->orderBy('updated_at', 'DESC')
            ->limit(6)
            ->findAll();

        if (!empty($publicLists)) {
            $userIds = array_unique(array_column($publicLists, 'user_id'));
            $users = (new \App\Models\UserModel())->whereIn('id', $userIds)->findAll();
            $names = [];
            foreach ($users as $user) {
                $names[(int) $user['id']] = $user['name'];
            }
            foreach ($publicLists as &$list) {
                $list['owner_name'] = $names[(int) $list['user_id']] ?? 'Member';
            }
            unset($list);
        }

        $stocksBySector = [];
        foreach ($allStocks as $stock) {
            $sector = $stock['sector'] ?? 'Uncategorized';
            if (!isset($stocksBySector[$sector])) {
                $stocksBySector[$sector] = [];
            }
            $stocksBySector[$sector][] = $stock;
        }
        ksort($stocksBySector);

        $data = [
            'title'         => '',
            'topStocks'     => $topStocks,
            'activeStocks'  => array_slice($allStocks, 0, 4),
            'topPerformer'  => $sameStock ? null : $topPerformer,
            'topLoser'      => $sameStock ? null : $topLoser,
            'publicLists'   => $publicLists,
            'predictionsMap'=> $this->buildPredictionsMap($allStocks),
            'stocksBySector'=> $stocksBySector,
        ];
        return view('templates/header', $data)
            . view('home', $data)
            . view('templates/footer');
    }

    private function featuredTopStocks(array $allStocks): array
    {
        $raw = home_setting('home_featured_stocks', '');
        if (trim($raw) === '') {
            return array_slice($allStocks, 0, 6);
        }

        $stocksById = [];
        foreach ($allStocks as $stock) {
            $stocksById[(int) $stock['id']] = $stock;
        }

        $featured = [];
        foreach (explode(',', $raw) as $id) {
            $sid = (int) trim($id);
            if ($sid > 0 && isset($stocksById[$sid])) {
                $featured[] = $stocksById[$sid];
            }
        }

        return array_slice($featured, 0, 6);
    }

    private function buildPredictionsMap(array $stocks): array
    {
        $ids = array_column($stocks, 'id');
        $predictions = (new \App\Models\PredictionModel())->getPredictionsForStocks($ids, 30);

        $map = [];
        foreach ($predictions as $p) {
            $sid = (int) $p['stock_id'];
            if (!isset($map[$sid])) {
                $map[$sid] = ['prices' => [], 'scores' => []];
            }
            $map[$sid]['prices'][] = (float) $p['predicted_price'];
            $map[$sid]['scores'][] = (float) $p['confidence_score'];
        }

        foreach ($map as $sid => $d) {
            $prices = $d['prices'];
            $avg = array_sum($prices) / count($prices);
            $map[$sid] = [
                'high'  => max($prices),
                'low'   => min($prices),
                'avg'   => $avg,
                'conf'  => round(array_sum($d['scores']) / count($d['scores']), 0),
                'prices'=> $prices,
            ];
        }

        return $map;
    }
}
