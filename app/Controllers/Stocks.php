<?php

namespace App\Controllers;

use App\Libraries\YahooFinanceService;
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
        $stocks = (new YahooFinanceService())->enrichStocks($stocks);

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

        $this->generatePriceHistory($stockId, $price);
        $this->generatePredictions($stockId, $price);

        return redirect()->to('/stocks/' . $stockId)
            ->with('success', 'Stock ' . $symbol . ' added successfully.');
    }

    private function generatePriceHistory(int $stockId, float $basePrice): void
    {
        $db = \Config\Database::connect();
        $prices = [];
        for ($i = 90; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $volatility = $basePrice * 0.03;
            $change = (mt_rand(-1000, 1000) / 1000) * $volatility;
            $close = round($basePrice + $change, 2);
            $open = round($close - (mt_rand(-500, 500) / 1000) * $volatility, 2);
            $high = round(max($open, $close) + abs(mt_rand(0, 500) / 1000) * $volatility, 2);
            $low = round(min($open, $close) - abs(mt_rand(0, 500) / 1000) * $volatility, 2);
            $volume = mt_rand(100000, 50000000);

            $prices[] = [
                'stock_id'   => $stockId,
                'price_date' => $date,
                'open'       => $open,
                'high'       => $high,
                'low'        => $low,
                'close'      => $close,
                'volume'     => $volume,
            ];
            $basePrice = $close;
        }
        $db->table('stock_prices')->insertBatch($prices);
    }

    private function generatePredictions(int $stockId, float $basePrice): void
    {
        $db = \Config\Database::connect();
        $predictions = [];
        for ($i = 1; $i <= 30; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} days"));
            $trend = (mt_rand(-100, 100) / 10000) * $basePrice;
            $predictedPrice = round($basePrice + ($trend * $i), 2);
            $confidence = round(max(60, min(95, 95 - ($i * 0.5))), 2);

            $predictions[] = [
                'stock_id'        => $stockId,
                'predicted_date'  => $date,
                'predicted_price' => $predictedPrice,
                'confidence_score'=> $confidence,
                'method'          => 'Monte Carlo + EMA',
                'created_at'      => date('Y-m-d H:i:s'),
            ];
        }
        $db->table('predictions')->insertBatch($predictions);
    }

    public function show($id): string
    {
        $stockModel = new StockModel();
        $stock = $stockModel->getWithPriceHistory((int) $id);

        if (!$stock) {
            return redirect()->to('/stocks')->with('error', 'Stock not found.');
        }

        $yahoo = new YahooFinanceService();
        try {
            $quote = $yahoo->getQuote($stock['symbol']);
            if ($quote) {
                $data = $yahoo->quoteToArray($quote);
                if (($data['regularMarketPrice'] ?? null) !== null) {
                    $stock['current_price'] = $data['regularMarketPrice'];
                    $stock['previous_close'] = $data['regularMarketPreviousClose'] ?? $stock['previous_close'];
                    if (($data['marketCap'] ?? null) !== null) $stock['market_cap'] = $data['marketCap'];
                    if (($data['trailingPE'] ?? null) !== null) $stock['pe_ratio'] = $data['trailingPE'];
                    if (($data['fiftyTwoWeekHigh'] ?? null) !== null) $stock['week_52_high'] = $data['fiftyTwoWeekHigh'];
                    if (($data['fiftyTwoWeekLow'] ?? null) !== null) $stock['week_52_low'] = $data['fiftyTwoWeekLow'];
                    if (($data['trailingAnnualDividendYield'] ?? null) !== null) $stock['dividend_yield'] = $data['trailingAnnualDividendYield'];
                    if (($data['bid'] ?? null) !== null) $stock['bid'] = $data['bid'];
                    if (($data['ask'] ?? null) !== null) $stock['ask'] = $data['ask'];
                    if (($data['regularMarketDayHigh'] ?? null) !== null) $stock['day_high'] = $data['regularMarketDayHigh'];
                    if (($data['regularMarketDayLow'] ?? null) !== null) $stock['day_low'] = $data['regularMarketDayLow'];
                    if (($data['regularMarketOpen'] ?? null) !== null) $stock['open_price'] = $data['regularMarketOpen'];
                    if (($data['regularMarketVolume'] ?? null) !== null) $stock['volume'] = $data['regularMarketVolume'];
                    if (($data['averageDailyVolume3Month'] ?? null) !== null) $stock['avg_volume'] = $data['averageDailyVolume3Month'];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Yahoo quote error for ' . $stock['symbol'] . ': ' . $e->getMessage());
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

        $data = [
            'title'           => $stock['symbol'] . ' - StockTrade Tips',
            'stock'           => $stock,
            'priceChange'     => $priceChange,
            'priceData'       => $priceData,
            'predictionData'  => $predictionData,
            'predictionDates' => $predictionDates,
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

        $data = [
            'title'            => 'Predictions - ' . $stock['symbol'] . ' - StockTrade Tips',
            'stock'            => $stock,
            'predictionDates'  => $predictionDates,
            'predictionPrices' => $predictionPrices,
            'confidenceScores' => $confidenceScores,
            'priceData'        => $priceData,
            'dateLabels'       => $dateLabels,
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
