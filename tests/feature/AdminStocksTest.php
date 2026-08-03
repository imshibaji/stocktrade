<?php

use App\Database\Seeds\StockSeeder;
use App\Models\StockModel;
use App\Models\StockPriceModel;
use App\Models\PredictionModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use Tests\Support\Fakes\FakeYahooFinanceService;

/**
 * @internal
 */
final class AdminStocksTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = 'App';

    // SQLite can't regress migrations with dropColumn down() (framework
    // limitation), so migrate+seed once per process instead of per-test.
    protected $refresh = false;

    protected $migrateOnce = true;

    protected $seedOnce = true;

    protected $seed = StockSeeder::class;

    protected function setUp(): void
    {
        helper('stock');
        parent::setUp();

        $quotes = [];
        $summaries = [];

        // fullExchangeName is the exchange display name; the real sector comes
        // from the summaryProfile payload so tests prove the summary path is used.
        foreach ([
            'TESTCO'    => ['Test Company Ltd', 'Telecom', 150.00],
            'RELIANCE'  => ['Reliance Updated Co', 'Energy', 3120.50],
            'AAAA'      => ['Alpha Corp', 'Technology', 100.50],
            'BBBB'      => ['Beta Corp', 'Financial Services', 200.00],
            'CCCC'      => ['C Corp', 'Technology', 50.00],
            'TCS'       => ['Tata Consultancy Updated', 'IT Services', 4300.00],
            'INFY'      => ['Infosys Updated Ltd', 'IT Services', 1650.25],
            'EXCHCO'    => ['Exchange Test Co', 'Telecom', 220.00],
            'REFRESHCO' => ['Refresh Test Co', 'Automobile', 500.00],
            'NOSUMMARY' => ['No Summary Co', null, 10.00],
            'GLOBALCO'  => ['Global Test Co', 'Telecom', 330.00],
            'BULKCO'    => ['Bulk Test Co', 'Financial Services', 77.00],
            'FALLBACK.NS'  => ['Fallback NSE Co', 'Energy', 90.00],
            'FALLBACK2.NS' => ['Fallback Two NSE Co', 'Energy', 95.00],
        ] as $symbol => [$name, $sector, $price]) {
            $quotes[$symbol] = [
                'symbol'                      => $symbol,
                'longName'                    => $name,
                'shortName'                   => $name,
                'fullExchangeName'            => 'NSE',
                'exchange'                    => 'NSI',
                'regularMarketPrice'          => $price,
                'regularMarketPreviousClose'  => round($price - 1, 2),
                'marketCap'                   => 1000000000,
                'averageDailyVolume3Month'    => 5000000,
                'trailingPE'                  => 25.5,
                'fiftyTwoWeekHigh'            => round($price * 1.2, 2),
                'fiftyTwoWeekLow'             => round($price * 0.8, 2),
                'trailingAnnualDividendYield' => 0.0125,
                'beta'                        => 1.1,
            ];

            if ($sector !== null) {
                $summaries[$symbol] = ['summaryProfile' => ['sector' => $sector]];
            }
        }

        Services::injectMock('yahoo', new FakeYahooFinanceService($quotes, $summaries));
    }

    private function asAdmin(): self
    {
        return $this->withSession([
            'isLoggedIn' => true,
            'user'       => ['id' => 1, 'name' => 'Admin', 'is_admin' => 1],
        ]);
    }

    private function stockId(string $symbol): int
    {
        $stock = (new StockModel())->where('symbol', $symbol)->first();
        $this->assertNotNull($stock, "Seed stock {$symbol} missing");
        return (int) $stock['id'];
    }

    // ---------- access control ----------

    public function testGuestIsRedirectedToLogin(): void
    {
        $this->get('/admin/stocks')->assertRedirectTo('/login');
    }

    public function testNonAdminGets404(): void
    {
        $this->expectException(PageNotFoundException::class);

        $this->withSession([
            'isLoggedIn' => true,
            'user'       => ['id' => 2, 'name' => 'User', 'is_admin' => 0],
        ])->get('/admin/stocks');
    }

    // ---------- single add ----------

    public function testAdminCanViewCreateForm(): void
    {
        $result = $this->asAdmin()->get('/admin/stocks/create');

        $result->assertStatus(200);
        $result->assertSee('Add Stock');
        $result->assertSee('name="symbol"');
        $result->assertSee('name="exchange"');
        $result->assertNotSee('name="current_price"');
    }

    public function testAdminCanCreateStock(): void
    {
        $result = $this->asAdmin()->post('/admin/stocks/save', [
            'symbol'   => 'TESTCO',
            'exchange' => 'NSE',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $this->seeInDatabase('stocks', ['symbol' => 'TESTCO']);
        $stock = (new StockModel())->where('symbol', 'TESTCO')->first();
        $this->assertSame('Test Company Ltd', $stock['name']);
        $this->assertSame('Telecom', $stock['sector']);
        $this->assertSame('NSE', $stock['exchange']);
        $this->assertEquals(150.00, (float) $stock['current_price']);
        $this->assertEquals(149.00, (float) $stock['previous_close']);
        $this->assertEquals(25.5, (float) $stock['pe_ratio']);

        $id = (int) $stock['id'];
        $this->assertSame(91, (new StockPriceModel())->where('stock_id', $id)->countAllResults());
        $this->assertSame(30, (new PredictionModel())->where('stock_id', $id)->countAllResults());
    }

    public function testAdminCanCreateStockWithBSEExchange(): void
    {
        $result = $this->asAdmin()->post('/admin/stocks/save', [
            'symbol'   => 'EXCHCO',
            'exchange' => 'BSE',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $stock = (new StockModel())->where('symbol', 'EXCHCO')->first();
        $this->assertSame('Exchange Test Co', $stock['name']);
        $this->assertSame('Telecom', $stock['sector']);
        $this->assertSame('BSE', $stock['exchange']);
    }

    public function testAdminCanCreateStockDefaultsToGlobalExchange(): void
    {
        $result = $this->asAdmin()->post('/admin/stocks/save', [
            'symbol' => 'GLOBALCO',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $stock = (new StockModel())->where('symbol', 'GLOBALCO')->first();
        $this->assertSame('Global Test Co', $stock['name']);
        $this->assertSame('GLOBAL', $stock['exchange']);
    }

    public function testCreateStockFallsBackToExchangeNameWhenSummaryMissing(): void
    {
        $result = $this->asAdmin()->post('/admin/stocks/save', [
            'symbol' => 'NOSUMMARY',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $stock = (new StockModel())->where('symbol', 'NOSUMMARY')->first();
        $this->assertSame('NSE', $stock['sector']);
    }

    public function testCreateStockRejectsDuplicateSymbol(): void
    {
        $before = (new StockModel())->countAllResults();

        $result = $this->asAdmin()->post('/admin/stocks/save', [
            'symbol' => 'RELIANCE',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('error');
        $this->assertSame($before, (new StockModel())->countAllResults());
    }

    public function testCreateStockRejectsUnknownSymbol(): void
    {
        $before = (new StockModel())->countAllResults();

        $result = $this->asAdmin()->post('/admin/stocks/save', [
            'symbol' => 'ZZZZ',
        ]);

        $result->assertRedirectTo('/admin/stocks/create');
        $result->assertSessionHas('error');
        $this->assertSame($before, (new StockModel())->countAllResults());
    }

    public function testCreateStockRequiresSymbol(): void
    {
        $before = (new StockModel())->countAllResults();

        $result = $this->asAdmin()->post('/admin/stocks/save', [
            'symbol' => '   ',
        ]);

        $result->assertRedirectTo('/admin/stocks/create');
        $result->assertSessionHas('error');
        $this->assertSame($before, (new StockModel())->countAllResults());
    }

    // ---------- single edit ----------

    public function testAdminCanViewEditForm(): void
    {
        $id = $this->stockId('RELIANCE');

        $result = $this->asAdmin()->get("/admin/stocks/edit/{$id}");

        $result->assertStatus(200);
        $result->assertSee('Edit Stock');
        $result->assertSee('RELIANCE');
        $result->assertSee('name="current_price"');
        $result->assertSee('Save Changes');
        $result->assertSee('Refresh from Yahoo');
    }

    public function testAdminCanUpdateStock(): void
    {
        $id = $this->stockId('RELIANCE');
        $before = (new StockModel())->find($id);

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'   => 'refresh',
            'symbol'   => 'RELIANCE',
            'exchange' => 'NSE',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $stock = (new StockModel())->find($id);
        $this->assertNotSame($before['name'], $stock['name']);
        $this->assertSame('Reliance Updated Co', $stock['name']);
        $this->assertSame('Energy', $stock['sector']);
        $this->assertEquals(3120.50, (float) $stock['current_price']);
        $this->assertEquals(3119.50, (float) $stock['previous_close']);
        $this->assertSame('NSE', $stock['exchange']);

        $this->assertSame(91, (new StockPriceModel())->where('stock_id', $id)->countAllResults());
        $this->assertSame(30, (new PredictionModel())->where('stock_id', $id)->countAllResults());
    }

    public function testAdminCanRefreshStockUsingSelectedExchange(): void
    {
        $id = $this->stockId('RELIANCE');

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'   => 'refresh',
            'symbol'   => 'RELIANCE',
            'exchange' => 'BSE',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $stock = (new StockModel())->find($id);
        $this->assertSame('BSE', $stock['exchange']);
        $this->assertSame('Reliance Updated Co', $stock['name']);
    }

    public function testRefreshFromYahooCanChangeSymbol(): void
    {
        $id = $this->stockId('MARUTI');

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'   => 'refresh',
            'symbol'   => 'REFRESHCO',
            'exchange' => 'NSE',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $stock = (new StockModel())->find($id);
        $this->assertSame('REFRESHCO', $stock['symbol']);
        $this->assertSame('Refresh Test Co', $stock['name']);
    }

    public function testRefreshFallsBackToPostedExchangeWhenSymbolDoesNotMatchExactly(): void
    {
        $id = $this->stockId('HDFCBANK');

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'   => 'refresh',
            'symbol'   => 'FALLBACK',
            'exchange' => 'NSE',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $stock = (new StockModel())->find($id);
        $this->assertSame('FALLBACK', $stock['symbol']);
        $this->assertSame('Fallback NSE Co', $stock['name']);
        $this->assertSame('Energy', $stock['sector']);
        $this->assertSame('NSE', $stock['exchange']);
    }

    public function testRefreshFallsBackToStoredExchangeWhenPostedExchangeMisses(): void
    {
        $id = $this->stockId('LT');

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'   => 'refresh',
            'symbol'   => 'FALLBACK2',
            'exchange' => 'GLOBAL',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $stock = (new StockModel())->find($id);
        $this->assertSame('FALLBACK2', $stock['symbol']);
        $this->assertSame('Fallback Two NSE Co', $stock['name']);
        $this->assertSame('Energy', $stock['sector']);
    }

    public function testUpdateStockNotFoundRedirects(): void
    {
        $result = $this->asAdmin()->post('/admin/stocks/update/99999', [
            'action' => 'refresh',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('error');
    }

    public function testUpdateStockFailsWhenYahooUnavailable(): void
    {
        $id = $this->stockId('WIPRO');
        $before = (new StockModel())->find($id);

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'   => 'refresh',
            'symbol'   => 'WIPRO',
            'exchange' => 'NSE',
        ]);

        $result->assertRedirectTo("/admin/stocks/edit/{$id}");
        $result->assertSessionHas('error');

        $after = (new StockModel())->find($id);
        $this->assertSame($before['name'], $after['name']);
        $this->assertSame($before['current_price'], $after['current_price']);
    }

    // ---------- manual edit ----------

    public function testAdminCanManuallyUpdateStock(): void
    {
        $id = $this->stockId('HCLTECH');

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'         => 'save',
            'symbol'         => 'HCLTECH',
            'name'           => 'HCL Technologies Manual',
            'sector'         => 'IT Services',
            'exchange'       => 'BSE',
            'current_price'  => '1600.75',
            'previous_close' => '1580.00',
            'pe_ratio'       => '27.5',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $stock = (new StockModel())->find($id);
        $this->assertSame('HCLTECH', $stock['symbol']);
        $this->assertSame('HCL Technologies Manual', $stock['name']);
        $this->assertSame('IT Services', $stock['sector']);
        $this->assertSame('BSE', $stock['exchange']);
        $this->assertEquals(1600.75, (float) $stock['current_price']);
        $this->assertEquals(1580.00, (float) $stock['previous_close']);
        $this->assertEquals(27.5, (float) $stock['pe_ratio']);

        $this->assertSame(91, (new StockPriceModel())->where('stock_id', $id)->countAllResults());
        $this->assertSame(30, (new PredictionModel())->where('stock_id', $id)->countAllResults());
    }

    public function testManualUpdateRejectsInvalidPrice(): void
    {
        $id = $this->stockId('HCLTECH');
        $before = (new StockModel())->find($id);

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'        => 'save',
            'symbol'        => 'HCLTECH',
            'name'          => 'HCL Technologies Manual',
            'sector'        => 'IT',
            'exchange'      => 'NSE',
            'current_price' => '0',
        ]);

        $result->assertRedirectTo("/admin/stocks/edit/{$id}");
        $result->assertSessionHas('error');

        $after = (new StockModel())->find($id);
        $this->assertSame($before['name'], $after['name']);
        $this->assertSame($before['current_price'], $after['current_price']);
    }

    public function testManualUpdateRejectsEmptyName(): void
    {
        $id = $this->stockId('HCLTECH');
        $before = (new StockModel())->find($id);

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'        => 'save',
            'symbol'        => 'HCLTECH',
            'name'          => '',
            'sector'        => 'IT',
            'exchange'      => 'NSE',
            'current_price' => '100.00',
        ]);

        $result->assertRedirectTo("/admin/stocks/edit/{$id}");
        $result->assertSessionHas('error');

        $after = (new StockModel())->find($id);
        $this->assertSame($before['name'], $after['name']);
    }

    public function testManualUpdateChangesSymbol(): void
    {
        $id = $this->stockId('ASIANPAINT');

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'        => 'save',
            'symbol'        => 'ASIANNEW',
            'name'          => 'Asian Paints Manual',
            'sector'        => 'Consumer',
            'exchange'      => 'NSE',
            'current_price' => '2900.00',
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $stock = (new StockModel())->find($id);
        $this->assertSame('ASIANNEW', $stock['symbol']);
        $this->assertSame('Asian Paints Manual', $stock['name']);
    }

    public function testManualUpdateRejectsDuplicateSymbol(): void
    {
        $id = $this->stockId('HCLTECH');
        $before = (new StockModel())->find($id);

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'        => 'save',
            'symbol'        => 'TCS',
            'name'          => 'HCL Technologies Manual',
            'sector'        => 'IT',
            'exchange'      => 'NSE',
            'current_price' => '1600.00',
        ]);

        $result->assertRedirectTo("/admin/stocks/edit/{$id}");
        $result->assertSessionHas('error');

        $after = (new StockModel())->find($id);
        $this->assertSame('HCLTECH', $after['symbol']);
        $this->assertSame($before['name'], $after['name']);
    }

    public function testManualUpdateRejectsInvalidSymbol(): void
    {
        $id = $this->stockId('HCLTECH');
        $before = (new StockModel())->find($id);

        $result = $this->asAdmin()->post("/admin/stocks/update/{$id}", [
            'action'        => 'save',
            'symbol'        => '',
            'name'          => 'HCL Technologies Manual',
            'sector'        => 'IT',
            'exchange'      => 'NSE',
            'current_price' => '1600.00',
        ]);

        $result->assertRedirectTo("/admin/stocks/edit/{$id}");
        $result->assertSessionHas('error');

        $after = (new StockModel())->find($id);
        $this->assertSame('HCLTECH', $after['symbol']);
        $this->assertSame($before['name'], $after['name']);
    }

    // ---------- bulk add ----------

    public function testBulkAddCreatesNewStocksAndSkipsExisting(): void
    {
        $before = (new StockModel())->countAllResults();

        $result = $this->asAdmin()->post('/admin/stocks/bulk-add', [
            'symbols' => ['AAAA', 'BBBB', 'RELIANCE'],
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $this->seeInDatabase('stocks', ['symbol' => 'AAAA']);
        $this->seeInDatabase('stocks', ['symbol' => 'BBBB']);
        $this->assertSame($before + 2, (new StockModel())->countAllResults());

        $aaaa = (new StockModel())->where('symbol', 'AAAA')->first();
        $this->assertSame('Alpha Corp', $aaaa['name']);
        $this->assertSame('Technology', $aaaa['sector']);
        $this->assertSame('GLOBAL', $aaaa['exchange']);
        $this->assertEquals(100.50, (float) $aaaa['current_price']);
    }

    public function testBulkAddUsesSelectedExchange(): void
    {
        $result = $this->asAdmin()->post('/admin/stocks/bulk-add', [
            'exchange' => 'BSE',
            'symbols'  => ['BULKCO'],
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $bulk = (new StockModel())->where('symbol', 'BULKCO')->first();
        $this->assertSame('Bulk Test Co', $bulk['name']);
        $this->assertSame('BSE', $bulk['exchange']);
    }

    public function testBulkAddSkipsUnknownSymbols(): void
    {
        $before = (new StockModel())->countAllResults();

        $result = $this->asAdmin()->post('/admin/stocks/bulk-add', [
            'symbols' => ['CCCC', 'ZZZZ'],
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $this->seeInDatabase('stocks', ['symbol' => 'CCCC']);
        $this->assertNull((new StockModel())->where('symbol', 'ZZZZ')->first());
        $this->assertSame($before + 1, (new StockModel())->countAllResults());
    }

    public function testBulkAddWithNoRowsDoesNotError(): void
    {
        $before = (new StockModel())->countAllResults();

        $result = $this->asAdmin()->post('/admin/stocks/bulk-add', [
            'symbols' => [],
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $this->assertSame($before, (new StockModel())->countAllResults());
    }

    // ---------- bulk edit (refresh from Yahoo) ----------

    public function testBulkEditRefreshesSelectedFromYahoo(): void
    {
        $idA     = $this->stockId('TCS');
        $idB     = $this->stockId('INFY');
        $otherId = $this->stockId('RELIANCE');

        $result = $this->asAdmin()->post('/admin/stocks/bulk-edit', [
            'ids' => [(string) $idA, (string) $idB],
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $this->assertSame('Tata Consultancy Updated', (new StockModel())->find($idA)['name']);
        $this->assertSame('IT Services', (new StockModel())->find($idA)['sector']);
        $this->assertEquals(4300.00, (float) (new StockModel())->find($idA)['current_price']);
        $this->assertSame('Infosys Updated Ltd', (new StockModel())->find($idB)['name']);
        $this->assertEquals(1650.25, (float) (new StockModel())->find($idB)['current_price']);
        $this->assertNotSame('Tata Consultancy Updated', (new StockModel())->find($otherId)['name']);
    }

    public function testBulkEditHandlesFailedRefresh(): void
    {
        $idA = $this->stockId('TCS');
        $idB = $this->stockId('WIPRO');
        $before = (new StockModel())->find($idB);

        $result = $this->asAdmin()->post('/admin/stocks/bulk-edit', [
            'ids' => [(string) $idA, (string) $idB],
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $this->assertSame('Tata Consultancy Updated', (new StockModel())->find($idA)['name']);
        $after = (new StockModel())->find($idB);
        $this->assertSame($before['name'], $after['name']);
        $this->assertSame($before['current_price'], $after['current_price']);
    }

    public function testBulkEditWithoutSelectionDoesNotError(): void
    {
        $id = $this->stockId('TCS');
        $before = (new StockModel())->find($id);

        $result = $this->asAdmin()->post('/admin/stocks/bulk-edit', [
            'ids' => [],
        ]);

        $result->assertRedirectTo('/admin/stocks');

        $after = (new StockModel())->find($id);
        $this->assertSame($before['name'], $after['name']);
    }

    // ---------- bulk delete ----------

    public function testBulkDeleteRemovesSelectedStocks(): void
    {
        $idA     = $this->stockId('WIPRO');
        $idB     = $this->stockId('TITAN');
        $otherId = $this->stockId('TCS');

        $result = $this->asAdmin()->post('/admin/stocks/bulk-delete', [
            'ids' => [(string) $idA, (string) $idB],
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $result->assertSessionHas('success');

        $this->assertNull((new StockModel())->find($idA));
        $this->assertNull((new StockModel())->find($idB));
        $this->assertNotNull((new StockModel())->find($otherId));
    }

    public function testBulkDeleteCascadesPriceHistoryAndPredictions(): void
    {
        $id = $this->stockId('SBIN');

        $this->assertGreaterThan(0, (new StockPriceModel())->where('stock_id', $id)->countAllResults());

        $this->asAdmin()->post('/admin/stocks/bulk-delete', [
            'ids' => [(string) $id],
        ]);

        $this->assertNull((new StockModel())->find($id));
        $this->assertSame(0, (new StockPriceModel())->where('stock_id', $id)->countAllResults());
        $this->assertSame(0, (new PredictionModel())->where('stock_id', $id)->countAllResults());
    }

    public function testBulkDeleteWithoutSelectionDoesNotError(): void
    {
        $before = (new StockModel())->countAllResults();

        $result = $this->asAdmin()->post('/admin/stocks/bulk-delete', [
            'ids' => [],
        ]);

        $result->assertRedirectTo('/admin/stocks');
        $this->assertSame($before, (new StockModel())->countAllResults());
    }

    // ---------- pages ----------

    public function testAdminCanViewStocksIndex(): void
    {
        $result = $this->asAdmin()->get('/admin/stocks');

        $result->assertStatus(200);
        $result->assertSee('Add Stock');
        $result->assertSee('Bulk Add');
        $result->assertSee('name="ids[]"');
        $result->assertSee('Refresh from Yahoo');
    }

    public function testAdminCanViewBulkAddForm(): void
    {
        $result = $this->asAdmin()->get('/admin/stocks/bulk');

        $result->assertStatus(200);
        $result->assertSee('Bulk Add Stocks');
        $result->assertSee('name="rows"');
        $result->assertSee('name="exchange"');
        $result->assertSee('<option value="GLOBAL" selected>GLOBAL</option>');
    }
}
