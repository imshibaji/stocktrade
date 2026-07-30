<?php

namespace App\Controllers;

use App\Models\StockModel;
use App\Models\WatchlistModel;

class Stocks extends BaseController
{
    public function index(): string
    {
        $stockModel = new StockModel();

        if ($this->request->isAJAX()) {
            $query = $this->request->getGet('q');
            if ($query) {
                $results = $stockModel->searchWithYahooFallback($query, 20);
                return $this->response->setJSON(['results' => $results, 'query' => $query]);
            }
        }

        $search = $this->request->getGet('search');
        $sector = $this->request->getGet('sector');

        if ($search) {
            $stocks = $stockModel->searchStocks($search);
        } elseif ($sector) {
            $stocks = $stockModel->where('sector', $sector)->findAll();
        } else {
            $stocks = $stockModel->findAll();
        }

        $sectors = $stockModel->select('sector')->distinct()->findAll();

        $watchlistMap = [];
        if (is_logged_in()) {
            $watchlistModel = new WatchlistModel();
            $watched = $watchlistModel->where('user_id', current_user_id())->findAll();
            foreach ($watched as $w) {
                $watchlistMap[(int) $w['stock_id']] = true;
            }
        }

        $data = [
            'title'        => 'Stocks - StockTrade Tips',
            'stocks'       => $stocks,
            'sectors'      => $sectors,
            'search'       => $search,
            'sector'       => $sector,
            'watchlistMap' => $watchlistMap,
        ];

        return view('templates/header', $data)
            . view('stocks/index', $data)
            . view('templates/footer');
    }

    public function create()
    {
        $symbol = strtoupper(trim($this->request->getPost('symbol')));
        $name = trim($this->request->getPost('name'));
        $sector = trim($this->request->getPost('sector'));
        $exchange = strtoupper(trim($this->request->getPost('exchange') ?? 'NSE'));
        $price = (float) $this->request->getPost('price');

        if (empty($symbol) || empty($name) || empty($sector) || $price <= 0) {
            return redirect()->back()->with('error', 'All fields are required with a valid price.');
        }

        $stockModel = new StockModel();
        $existing = $stockModel->where('symbol', $symbol)->first();

        if ($existing) {
            return redirect()->to('/stocks/' . $existing['id'])
                ->with('info', 'Stock ' . $symbol . ' already exists.');
        }

        $stockId = $stockModel->insert([
            'symbol'         => $symbol,
            'name'           => $name,
            'sector'         => $sector,
            'exchange'       => $exchange,
            'current_price'  => $price,
            'previous_close' => round($price * 0.99, 2),
            'market_cap'     => null,
            'avg_volume'     => null,
            'pe_ratio'       => null,
            'week_52_high'   => round($price * 1.15, 2),
            'week_52_low'    => round($price * 0.85, 2),
            'dividend_yield' => null,
            'beta'           => null,
        ]);

        generate_price_history($stockId, $price);
        generate_predictions($stockId, $price);

        return redirect()->to('/stocks/' . $stockId)
            ->with('success', 'Stock ' . $symbol . ' added successfully.');
    }

    public function show($id): string
    {
        $stockModel = new StockModel();
        $stock = $stockModel->getWithPriceHistory((int) $id);

        if (!$stock) {
            return redirect()->to('/stocks')->with('error', 'Stock not found.');
        }

        $priceData = [];
        $predictionData = [];
        $predictionDates = [];

        foreach ($stock['price_history'] as $p) {
            $priceData[] = (float) $p['close'];
        }

        foreach ($stock['predictions'] as $p) {
            $predictionData[] = (float) $p['predicted_price'];
            $predictionDates[] = date('M d', strtotime($p['predicted_date']));
        }

        $priceChange = get_price_change(
            (float) $stock['current_price'],
            (float) $stock['previous_close']
        );

        $isWatched = false;
        if (is_logged_in()) {
            $watchlistModel = new \App\Models\WatchlistModel();
            $isWatched = $watchlistModel->isWatched(current_user_id(), (int) $stock['id']);
        }

        $data = [
            'title'           => $stock['symbol'] . ' - StockTrade Tips',
            'stock'           => $stock,
            'priceChange'     => $priceChange,
            'priceData'       => $priceData,
            'predictionData'  => $predictionData,
            'predictionDates' => $predictionDates,
            'isWatched'       => $isWatched,
            'showChartJs'     => true,
        ];

        return view('templates/header', $data)
            . view('stocks/show', $data)
            . view('templates/footer');
    }

    public function edit($id): string
    {
        $stockModel = new StockModel();
        $stock = $stockModel->find((int) $id);
        if (!$stock) {
            return redirect()->to('/stocks')->with('error', 'Stock not found.');
        }
        $data = [
            'title' => 'Edit ' . $stock['symbol'] . ' - StockTrade Tips',
            'stock' => $stock,
        ];
        return view('templates/header', $data)
            . view('stocks/edit', $data)
            . view('templates/footer');
    }

    public function update($id)
    {
        $stockModel = new StockModel();
        $stock = $stockModel->find((int) $id);
        if (!$stock) {
            return redirect()->to('/stocks')->with('error', 'Stock not found.');
        }
        $data = [
            'name'           => $this->request->getPost('name'),
            'sector'         => $this->request->getPost('sector'),
            'current_price'  => (float) $this->request->getPost('current_price'),
            'previous_close' => (float) $this->request->getPost('previous_close') ?: null,
            'market_cap'     => $this->request->getPost('market_cap') ? (float) $this->request->getPost('market_cap') : null,
            'avg_volume'     => $this->request->getPost('avg_volume') ? (float) $this->request->getPost('avg_volume') : null,
            'pe_ratio'       => $this->request->getPost('pe_ratio') ? (float) $this->request->getPost('pe_ratio') : null,
            'week_52_high'   => $this->request->getPost('week_52_high') ? (float) $this->request->getPost('week_52_high') : null,
            'week_52_low'    => $this->request->getPost('week_52_low') ? (float) $this->request->getPost('week_52_low') : null,
            'dividend_yield' => $this->request->getPost('dividend_yield') ? (float) $this->request->getPost('dividend_yield') : null,
            'beta'           => $this->request->getPost('beta') ? (float) $this->request->getPost('beta') : null,
        ];
        $stockModel->update((int) $id, $data);
        return redirect()->to('/stocks/' . $id)->with('success', $stock['symbol'] . ' updated successfully.');
    }

    public function delete($id)
    {
        $stockModel = new StockModel();
        $stock = $stockModel->find((int) $id);
        if (!$stock) {
            return redirect()->to('/stocks')->with('error', 'Stock not found.');
        }
        $db = \Config\Database::connect();
        $db->table('watchlist')->where('stock_id', (int) $id)->delete();
        $db->table('stock_prices')->where('stock_id', (int) $id)->delete();
        $db->table('predictions')->where('stock_id', (int) $id)->delete();
        $db->table('investments')->where('stock_id', (int) $id)->delete();
        $stockModel->delete((int) $id);
        return redirect()->to('/stocks')->with('success', $stock['symbol'] . ' deleted successfully.');
    }

    public function predictions($id): string
    {
        $stockModel = new StockModel();
        $stock = $stockModel->getWithPriceHistory((int) $id);

        if (!$stock) {
            return redirect()->to('/stocks')->with('error', 'Stock not found.');
        }

        $predictionDates = [];
        $predictionPrices = [];
        $confidenceScores = [];

        foreach ($stock['predictions'] as $p) {
            $predictionDates[] = date('M d', strtotime($p['predicted_date']));
            $predictionPrices[] = (float) $p['predicted_price'];
            $confidenceScores[] = (float) $p['confidence_score'];
        }

        $priceData = [];
        $dateLabels = [];
        foreach ($stock['price_history'] as $p) {
            $priceData[] = (float) $p['close'];
            $dateLabels[] = date('M d', strtotime($p['price_date']));
        }

        $isWatched = false;
        if (is_logged_in()) {
            $watchlistModel = new \App\Models\WatchlistModel();
            $isWatched = $watchlistModel->isWatched(current_user_id(), (int) $stock['id']);
        }

        $data = [
            'title'            => 'Predictions - ' . $stock['symbol'] . ' - StockTrade Tips',
            'stock'            => $stock,
            'predictionDates'  => $predictionDates,
            'predictionPrices' => $predictionPrices,
            'confidenceScores' => $confidenceScores,
            'priceData'        => $priceData,
            'dateLabels'       => $dateLabels,
            'showChartJs'      => true,
            'isWatched'        => $isWatched,
        ];

        return view('templates/header', $data)
            . view('stocks/predictions', $data)
            . view('templates/footer');
    }

    public function search()
    {
        return $this->index();
    }
}
