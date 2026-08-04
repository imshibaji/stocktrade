<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\YahooFinanceService;
use App\Models\StockModel;

class Stocks extends BaseController
{
    public function stocks(): string
    {
        $stockModel = new StockModel();
        $search = trim((string) $this->request->getGet('search'));
        $exchange = trim((string) $this->request->getGet('exchange'));
        $sector = trim((string) $this->request->getGet('sector'));
        $sort   = $this->request->getGet('sort') ?? 'symbol';
        $dir    = $this->request->getGet('dir') === 'desc' ? 'DESC' : 'ASC';
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);

        $allowedSort = ['id', 'symbol', 'name', 'exchange', 'sector', 'current_price', 'created_at'];
        if (!in_array($sort, $allowedSort)) {
            $sort = 'symbol';
        }

        $builder = $stockModel->select('*');
        if ($search !== '') {
            $builder->groupStart()
                ->like('symbol', $search)
                ->orLike('name', $search)
                ->orLike('exchange', $search)
                ->orLike('sector', $search)
                ->groupEnd();
        }
        if ($exchange !== '') {
            $builder->where('exchange', $exchange);
        }
        if ($sector !== '') {
            $builder->where('sector', $sector);
        }
        $builder->orderBy($sort, $dir);

        $stocks = $builder->paginate($perPage);
        $pager = $stockModel->pager;

        $exchangeOptions = $stockModel->select('exchange')->distinct()->orderBy('exchange', 'ASC')->findAll();
        $sectorOptions = $stockModel->select('sector')->distinct()->orderBy('sector', 'ASC')->findAll();

        $data = [
            'title' => 'Stocks - Admin - StockTrade Tips',
            'stocks' => $stocks,
            'pager' => $pager,
            'search' => $search,
            'exchange' => $exchange,
            'sector' => $sector,
            'sort' => $sort,
            'dir' => $dir,
            'perPage' => $perPage,
            'exchangeOptions' => $exchangeOptions,
            'sectorOptions' => $sectorOptions,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'stocks', 'content' => view('admin/stocks/index', $data)])
            . view('templates/footer');
    }

    public function createStock(): string
    {
        $data = [
            'title' => 'Add Stock - Admin - StockTrade Tips',
            'stock' => null,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'stocks', 'content' => view('admin/stocks/form', $data)])
            . view('templates/footer');
    }

    public function saveStock()
    {
        $stockModel = new StockModel();

        $symbol = strtoupper(trim((string) $this->request->getPost('symbol')));
        $exchange = strtoupper(trim((string) $this->request->getPost('exchange')));

        if ($symbol === '' || !preg_match('/^[A-Z0-9.\-]+$/', $symbol)) {
            return redirect()->to('/admin/stocks/create')->with('error', 'A valid stock symbol is required.');
        }

        if ($exchange === '') {
            $exchange = 'GLOBAL';
        }

        if ($stockModel->where('symbol', $symbol)->first()) {
            return redirect()->to('/admin/stocks')->with('error', "Stock with symbol {$symbol} already exists.");
        }

        $data = $this->fetchYahooData($symbol, $exchange);
        if ($data === null) {
            return redirect()->to('/admin/stocks/create')->with('error', "Could not fetch data for {$symbol} from Yahoo Finance.");
        }

        $price = (float) $data['regularMarketPrice'];

        $row = $this->stockRowFromYahoo($data, $symbol, $price, $exchange);
        $row['symbol'] = $symbol;

        $stockId = $stockModel->insert($row);
        if (!$stockId) {
            return redirect()->to('/admin/stocks/create')->with('error', 'Failed to add stock.');
        }

        $this->regenerateStockData((int) $stockId, $price);

        return redirect()->to('/admin/stocks')->with('success', "{$symbol} added from Yahoo Finance.");
    }

    public function bulkAddForm(): string
    {
        $data = [
            'title' => 'Bulk Add Stocks - Admin - StockTrade Tips',
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'stocks', 'content' => view('admin/stocks/bulk_add', $data)])
            . view('templates/footer');
    }

    public function editStock($id)
    {
        $stockModel = new StockModel();
        $stock = $stockModel->find((int) $id);
        if (!$stock) {
            return redirect()->to('/admin/stocks')->with('error', 'Stock not found.');
        }

        $data = [
            'title' => 'Edit Stock - Admin - StockTrade Tips',
            'stock' => $stock,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'stocks', 'content' => view('admin/stocks/form', $data)])
            . view('templates/footer');
    }

    public function updateStock($id)
    {
        $stockModel = new StockModel();
        $stock = $stockModel->find((int) $id);
        if (!$stock) {
            return redirect()->to('/admin/stocks')->with('error', 'Stock not found.');
        }

        $action = strtolower(trim((string) $this->request->getPost('action')));

        return $action === 'refresh'
            ? $this->updateStockFromYahoo((int) $id, $stock)
            : $this->updateStockManually((int) $id, $stock);
    }

    private function updateStockFromYahoo(int $id, array $stock)
    {
        $symbol = strtoupper(trim((string) $this->request->getPost('symbol')));
        $exchange = strtoupper(trim((string) $this->request->getPost('exchange')));

        if ($symbol === '' || !preg_match('/^[A-Z0-9.\-]+$/', $symbol)) {
            return redirect()->to("/admin/stocks/edit/{$id}")->with('error', 'A valid stock symbol is required.');
        }

        if ($exchange === '') {
            $exchange = 'GLOBAL';
        }

        $duplicate = (new StockModel())->where('symbol', $symbol)->where('id !=', $id)->first();
        if ($duplicate) {
            return redirect()->to("/admin/stocks/edit/{$id}")->with('error', "Stock with symbol {$symbol} already exists.");
        }

        $data = $this->fetchYahooData($symbol);
        if ($data === null && $exchange !== 'GLOBAL') {
            $data = $this->fetchYahooData($symbol, $exchange);
        }

        $storedExchange = strtoupper(trim((string) ($stock['exchange'] ?? '')));
        if ($data === null && $storedExchange !== '' && $storedExchange !== 'GLOBAL' && $storedExchange !== $exchange) {
            $data = $this->fetchYahooData($symbol, $storedExchange);
        }

        if ($data === null) {
            return redirect()->to("/admin/stocks/edit/{$id}")->with('error', "Could not fetch data for {$symbol} from Yahoo Finance.");
        }

        $price = (float) $data['regularMarketPrice'];

        $row = $this->stockRowFromYahoo($data, $symbol, $price, $exchange);
        $row['symbol'] = $symbol;

        (new StockModel())->update($id, $row);

        $this->regenerateStockData($id, $price);

        return redirect()->to('/admin/stocks')->with('success', "{$symbol} updated from Yahoo Finance.");
    }

    private function updateStockManually(int $id, array $stock)
    {
        $stockModel = new StockModel();

        $symbol       = strtoupper(trim((string) $this->request->getPost('symbol')));
        $name         = trim((string) $this->request->getPost('name'));
        $sector       = trim((string) $this->request->getPost('sector'));
        $exchange     = strtoupper(trim((string) $this->request->getPost('exchange')));
        $currentPrice = $this->request->getPost('current_price');

        if ($symbol === '' || !preg_match('/^[A-Z0-9.\-]+$/', $symbol)) {
            return redirect()->to("/admin/stocks/edit/{$id}")->with('error', 'A valid stock symbol is required.');
        }

        if ($name === '' || $currentPrice === null || (float) $currentPrice <= 0) {
            return redirect()->to("/admin/stocks/edit/{$id}")->with('error', 'Name and a valid price are required.');
        }

        $duplicate = $stockModel->where('symbol', $symbol)->where('id !=', $id)->first();
        if ($duplicate) {
            return redirect()->to("/admin/stocks/edit/{$id}")->with('error', "Stock with symbol {$symbol} already exists.");
        }

        $data = [
            'symbol'        => $symbol,
            'name'          => $name,
            'sector'        => $sector !== '' ? $sector : 'N/A',
            'exchange'      => $exchange !== '' ? $exchange : 'GLOBAL',
            'current_price' => $currentPrice,
        ];

        $previousClose = $this->request->getPost('previous_close');
        if ($previousClose !== null && (float) $previousClose > 0) {
            $data['previous_close'] = $previousClose;
        }

        foreach (['market_cap', 'avg_volume', 'pe_ratio', 'week_52_high', 'week_52_low', 'dividend_yield', 'beta'] as $field) {
            $value = $this->request->getPost($field);
            if ($value !== null && trim((string) $value) !== '') {
                $data[$field] = $value;
            }
        }

        $stockModel->update($id, $data);

        $this->regenerateStockData($id, (float) $currentPrice);

        return redirect()->to('/admin/stocks')->with('success', "{$symbol} updated.");
    }

    public function bulkAddStocks()
    {
        $stockModel = new StockModel();

        $symbols = array_values(array_unique(array_filter(array_map(
            static fn ($s) => strtoupper(trim((string) $s)),
            (array) $this->request->getPost('symbols'),
        ))));

        if ($symbols === []) {
            return redirect()->to('/admin/stocks')->with('success', 'No symbols provided for bulk add.');
        }

        $exchange = strtoupper(trim((string) $this->request->getPost('exchange')));
        if ($exchange === '') {
            $exchange = 'GLOBAL';
        }

        $existing = [];
        foreach ($stockModel->select('symbol')->findAll() as $row) {
            $existing[strtoupper($row['symbol'])] = true;
        }

        $created = 0;
        $skipped = 0;
        $failed  = 0;
        $failedSymbols = [];

        foreach ($symbols as $symbol) {
            if (isset($existing[$symbol])) {
                $skipped++;
                continue;
            }

            $data = $this->fetchYahooData($symbol, $exchange);
            if ($data === null) {
                $failed++;
                $failedSymbols[] = $symbol;
                continue;
            }

            $price = (float) $data['regularMarketPrice'];

            $row = $this->stockRowFromYahoo($data, $symbol, $price, $exchange);
            $row['symbol'] = $symbol;

            $stockId = $stockModel->insert($row);
            if (!$stockId) {
                $failed++;
                $failedSymbols[] = $symbol;
                continue;
            }

            $existing[$symbol] = true;
            $created++;

            $this->regenerateStockData((int) $stockId, $price);
        }

        $message = "Bulk add complete: {$created} created, {$skipped} skipped";
        if ($failed > 0) {
            $message .= ', ' . $failed . ' not fetched (' . implode(', ', $failedSymbols) . ')';
        }
        $message .= '.';

        return redirect()->to('/admin/stocks')->with('success', $message);
    }

    public function bulkEditStocks()
    {
        $stockModel = new StockModel();

        $ids = array_values(array_unique(array_filter(array_map(
            static fn ($id) => (int) $id,
            (array) $this->request->getPost('ids'),
        ))));

        if ($ids === []) {
            return redirect()->to('/admin/stocks')->with('success', 'No stocks selected for bulk edit.');
        }

        $updated = 0;
        $failed  = 0;
        $failedSymbols = [];

        foreach ($ids as $id) {
            $stock = $stockModel->find($id);
            if (!$stock) {
                $failed++;
                $failedSymbols[] = '#' . $id;
                continue;
            }

            $symbol = (string) $stock['symbol'];
            $exchange = strtoupper(trim((string) ($stock['exchange'] ?? 'NSE')));

            $data = $this->fetchYahooData($symbol, $exchange);
            if ($data === null) {
                $failed++;
                $failedSymbols[] = $symbol;
                continue;
            }

            $price = (float) $data['regularMarketPrice'];

            $stockModel->update($id, $this->stockRowFromYahoo($data, $symbol, $price, $exchange));

            $this->regenerateStockData($id, $price);

            $updated++;
        }

        $message = "Bulk edit complete: {$updated} refreshed from Yahoo Finance";
        if ($failed > 0) {
            $message .= ', ' . $failed . ' not fetched (' . implode(', ', $failedSymbols) . ')';
        }
        $message .= '.';

        return redirect()->to('/admin/stocks')->with('success', $message);
    }

    public function bulkDeleteStocks()
    {
        $stockModel = new StockModel();
        $db = \Config\Database::connect();

        $ids = array_values(array_unique(array_filter(array_map(
            static fn ($id) => (int) $id,
            (array) $this->request->getPost('ids'),
        ))));

        if ($ids === []) {
            return redirect()->to('/admin/stocks')->with('success', 'No stocks selected for bulk delete.');
        }

        foreach ($ids as $id) {
            $db->table('watchlist')->where('stock_id', $id)->delete();
            $db->table('stock_prices')->where('stock_id', $id)->delete();
            $db->table('predictions')->where('stock_id', $id)->delete();
            $db->table('investments')->where('stock_id', $id)->delete();
            $stockModel->delete($id);
        }

        return redirect()->to('/admin/stocks')->with('success', count($ids) . ' stock(s) deleted.');
    }

    public function deleteStock($id)
    {
        $stockModel = new StockModel();
        $stock = $stockModel->find((int) $id);
        if (!$stock) {
            return redirect()->back()->with('error', 'Stock not found.');
        }
        $db = \Config\Database::connect();
        $db->table('watchlist')->where('stock_id', (int) $id)->delete();
        $db->table('stock_prices')->where('stock_id', (int) $id)->delete();
        $db->table('predictions')->where('stock_id', (int) $id)->delete();
        $db->table('investments')->where('stock_id', (int) $id)->delete();
        $stockModel->delete((int) $id);
        return redirect()->back()->with('success', $stock['symbol'] . ' deleted.');
    }

    /**
     * @return YahooFinanceService
     */
    private function yahooService()
    {
        return service('yahoo');
    }

    /**
     * Fetch a live quote for a symbol and return the cleaned quote data.
     *
     * @return array<string, mixed>|null null when the symbol is unknown or has no price
     */
    private function fetchYahooData(string $symbol, string $exchange = 'GLOBAL'): ?array
    {
        try {
            $quote = $this->yahooService()->getQuote($symbol, $exchange);
        } catch (\Throwable $e) {
            log_message('error', 'Yahoo quote error for ' . $symbol . ': ' . $e->getMessage());
            return null;
        }

        if (!$quote) {
            return null;
        }

        $data = $this->yahooService()->quoteToArray($quote);

        $price = (float) ($data['regularMarketPrice'] ?? 0);
        if ($price <= 0) {
            return null;
        }

        $data['_sector'] = $this->fetchYahooSector($symbol, $exchange);
        $data['regularMarketPrice'] = $price;

        return $data;
    }

    /**
     * Fetch the sector from the summaryProfile API response.
     */
    private function fetchYahooSector(string $symbol, string $exchange = 'GLOBAL'): ?string
    {
        try {
            $summary = $this->yahooService()->getSummary($symbol, $exchange, ['summaryProfile']);
            $sector = $summary['summaryProfile']['sector'] ?? null;
            return is_string($sector) && trim($sector) !== '' ? trim($sector) : null;
        } catch (\Throwable $e) {
            log_message('error', 'Yahoo summary error for ' . $symbol . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Map Yahoo quote data to a stocks table row.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function stockRowFromYahoo(array $data, string $symbol, float $price, string $exchange = 'GLOBAL'): array
    {
        $previousClose = isset($data['regularMarketPreviousClose']) && (float) $data['regularMarketPreviousClose'] > 0
            ? $data['regularMarketPreviousClose']
            : round($price * 0.99, 2);

        return [
            'name'            => $data['longName'] ?? $data['shortName'] ?? $symbol,
            'sector'          => $data['_sector'] ?? $data['fullExchangeName'] ?? 'N/A',
            'exchange'        => $exchange,
            'exchange_display' => $data['exchange'] ?? $data['fullExchangeName'] ?? $exchange,
            'current_price'   => $price,
            'previous_close'  => $previousClose,
            'market_cap'      => $data['marketCap'] ?? null,
            'avg_volume'      => $data['averageDailyVolume3Month'] ?? null,
            'pe_ratio'        => $data['trailingPE'] ?? null,
            'week_52_high'    => $data['fiftyTwoWeekHigh'] ?? null,
            'week_52_low'     => $data['fiftyTwoWeekLow'] ?? null,
            'dividend_yield'  => $data['trailingAnnualDividendYield'] ?? null,
            'beta'            => $data['beta'] ?? null,
        ];
    }

    /**
     * Rebuild the generated price history and predictions for a stock.
     */
    private function regenerateStockData(int $stockId, float $price): void
    {
        $db = \Config\Database::connect();
        $db->table('stock_prices')->where('stock_id', $stockId)->delete();
        $db->table('predictions')->where('stock_id', $stockId)->delete();

        generate_price_history($stockId, $price);
        generate_predictions($stockId, $price);
    }
}
