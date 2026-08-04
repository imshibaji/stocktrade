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

        try {
            $yahoo   = new \App\Libraries\YahooFinanceService();
            $quote   = $yahoo->getQuote($stock['symbol'], $stock['exchange']);
            if ($quote) {
                $q = $yahoo->quoteToArray($quote);
                $stock['open_price']          = $q['regularMarketOpen'] ?? $stock['open_price'] ?? null;
                $stock['day_high']            = $q['regularMarketDayHigh'] ?? $stock['day_high'] ?? null;
                $stock['day_low']             = $q['regularMarketDayLow'] ?? $stock['day_low'] ?? null;
                $stock['volume']              = $q['regularMarketVolume'] ?? $stock['volume'] ?? null;
                $stock['bid']                 = $q['bid'] ?? $stock['bid'] ?? null;
                $stock['ask']                 = $q['ask'] ?? $stock['ask'] ?? null;
                $stock['current_price']       = $q['regularMarketPrice'] ?? $stock['current_price'];
                $stock['previous_close']      = $q['regularMarketPreviousClose'] ?? $stock['previous_close'];
                if (!$stock['market_cap'] && isset($q['marketCap'])) $stock['market_cap'] = $q['marketCap'];
                if (!$stock['avg_volume'] && isset($q['averageDailyVolume3Month'])) $stock['avg_volume'] = $q['averageDailyVolume3Month'];
                if (!$stock['pe_ratio'] && isset($q['trailingPE'])) $stock['pe_ratio'] = $q['trailingPE'];
                if (!$stock['week_52_high'] && isset($q['fiftyTwoWeekHigh'])) $stock['week_52_high'] = $q['fiftyTwoWeekHigh'];
                if (!$stock['week_52_low'] && isset($q['fiftyTwoWeekLow'])) $stock['week_52_low'] = $q['fiftyTwoWeekLow'];
                if (!($stock['dividend_yield'] ?? 0) && isset($q['dividendYield'])) $stock['dividend_yield'] = $q['dividendYield'];
                if (!($stock['beta'] ?? 0) && isset($q['beta'])) $stock['beta'] = $q['beta'];
            }
        } catch (\Throwable $e) {
            log_message('error', 'Live quote fetch failed: ' . $e->getMessage());
        }

        $predictionData = [];
        $predictionDates = [];

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
            'predictionData'  => $predictionData,
            'predictionDates' => $predictionDates,
            'isWatched'       => $isWatched,
            'base_currency'   => get_user_base_currency(),
        ];

        return view('templates/header', $data)
            . view('stocks/show', $data)
            . view('templates/footer');
    }

    public function edit($id): string
    {
        $stockModel = new StockModel();
        $stock = $stockModel->find((int) $id);
        // dd($stock);
        if (!$stock) {
            return redirect()->to('/stocks')->with('error', 'Stock not found.');
        }
        $sectors = $stockModel->distinct()->select('sector')->findAll();
        $sectorList = array_unique(array_column($sectors, 'sector'));
        sort($sectorList);
        $exchangeList = ['GLOBAL', 'NSE', 'BSE', 'NYSE', 'NASDAQ', 'LSE'];
        $data = [
            'title' => 'Edit ' . $stock['symbol'] . ' - StockTrade Tips',
            'stock' => $stock,
            'sectors' => $sectorList,
            'exchanges' => $exchangeList,
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

        $symbol = strtoupper(trim((string) $this->request->getPost('symbol')));
        $exchange = strtoupper(trim((string) $this->request->getPost('exchange')));

        if ($symbol === '' || !preg_match('/^[A-Z0-9.\-]+$/', $symbol)) {
            return redirect()->to('/stocks/' . $id . '/edit')->with('error', 'A valid stock symbol is required.');
        }

        if ($exchange === '') {
            $exchange = 'GLOBAL';
        }

        if (strtoupper($symbol) !== strtoupper($stock['symbol'])) {
            $duplicate = $stockModel->where('symbol', $symbol)->where('id !=', (int) $id)->first();
            if ($duplicate) {
                return redirect()->to('/stocks/' . $id . '/edit')->with('error', 'Stock with symbol ' . $symbol . ' already exists.');
            }
        }

        $data = [
            'symbol'         => $symbol,
            'exchange'       => $exchange,
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
        return redirect()->to('/stocks/' . $id)->with('success', $symbol . ' updated successfully.');
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
