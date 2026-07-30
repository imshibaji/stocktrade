<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white">Dashboard</h1>
                <span id="marketBadge" class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>Checking...
                </span>
            </div>
            <p class="text-gray-400 mt-1">Welcome back, <?= esc(current_user()['name']) ?></p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="/stocks" class="bg-gold hover:bg-gold2 text-navy font-semibold px-6 py-2 rounded-lg text-sm transition">
                <i class="fas fa-search mr-2"></i>Browse Stocks
            </a>
            <a href="/investments" class="border border-gold text-gold hover:bg-gold/10 px-6 py-2 rounded-lg text-sm transition">
                <i class="fas fa-plus mr-2"></i>New Investment
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-navy2 rounded-xl p-6 border border-gray-700">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm mb-1">Active Investments</p>
                    <p class="text-3xl font-bold text-white"><?= count($activeInvestments) ?></p>
                </div>
                <div class="w-12 h-12 bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-briefcase text-green-400 text-xl"></i>
                </div>
            </div>
            <a href="/investments" class="text-green-400 text-sm hover:text-green-300 mt-2 inline-block">Manage investments <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="bg-navy2 rounded-xl p-6 border border-gray-700">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm mb-1">Total Invested</p>
                    <p id="dashInvested" class="text-3xl font-bold text-white"><?= format_price_dual($portfolio['total_invested'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-purple-400 text-xl"></i>
                </div>
            </div>
            <a href="/portfolio" class="text-purple-400 text-sm hover:text-purple-300 mt-2 inline-block">View portfolio <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="bg-navy2 rounded-xl p-6 border border-gray-700">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm mb-1">Net P/L</p>
                    <p id="dashNetPL" class="text-3xl font-bold <?= $portfolio['total_net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                        <?= $portfolio['total_net_profit'] >= 0 ? '+' : '' ?><?= format_price_dual($portfolio['total_net_profit'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?>
                    </p>
                </div>
                <div id="dashNetIcon" class="w-12 h-12 <?= $portfolio['total_net_profit'] >= 0 ? 'bg-green-900/30' : 'bg-red-900/30' ?> rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line <?= $portfolio['total_net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?> text-xl"></i>
                </div>
            </div>
            <a href="/portfolio" id="dashNetLink" class="<?= $portfolio['total_net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?> text-sm mt-2 inline-block">After tax <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
    </div>

    <?php if (!empty($investmentDetails)): ?>
    <div class="bg-navy2 rounded-xl border border-gray-700 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
            <h2 class="text-white font-bold text-lg">Active Investments</h2>
            <a href="/investments" class="text-gold text-sm hover:text-gold2 transition">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-gray-400 text-sm border-b border-gray-700">
                        <th class="text-left px-6 py-3">Stock</th>
                        <th class="text-left px-6 py-3">Buy Price</th>
                        <th class="text-left px-6 py-3">Current</th>
                        <th class="text-left px-6 py-3">Shares</th>
                        <th class="text-left px-6 py-3">Invested</th>
                        <th class="text-left px-6 py-3">Current Value</th>
                        <th class="text-left px-6 py-3">Gross P/L</th>
                        <th class="text-left px-6 py-3">Fees</th>
                        <th class="text-left px-6 py-3">Tax</th>
                        <th class="text-left px-6 py-3">Net P/L</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($investmentDetails as $inv): ?>
                    <?php 
                        $currency = $inv['currency'] ?? 'INR';
                        $baseCurrency = $inv['base_currency'] ?? 'INR';
                    ?>
                    <tr class="border-b border-gray-700/50 hover:bg-navy/50">
                        <td class="px-6 py-4">
                            <span class="text-white font-semibold"><?= esc($inv['symbol']) ?></span>
                            <div class="text-gray-500 text-xs"><?= esc($inv['name']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-300 dash-bp"><?= format_price_dual($inv['buy_price'] ?? 0, $currency, $baseCurrency) ?></td>
                        <td class="px-6 py-4 text-gray-300 dash-cp"><?= format_price_dual($inv['current_price'] ?? 0, $currency, $baseCurrency) ?></td>
                        <td class="px-6 py-4 text-gray-300"><?= (int) $inv['shares'] ?></td>
                        <td class="px-6 py-4 text-gray-300 dash-invested"><?= format_price_dual($inv['total_invested'] ?? 0, $currency, $baseCurrency) ?></td>
                        <td class="px-6 py-4 text-gray-300 dash-value"><?= format_price_dual($inv['current_value'] ?? 0, $currency, $baseCurrency) ?></td>
                        <td class="px-6 py-4 dash-pl <?= $inv['gross_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <span class="dash-gross"><?= $inv['gross_profit'] >= 0 ? '+' : '' ?><?= format_price_dual($inv['gross_profit'] ?? 0, $currency, $baseCurrency) ?></span>
                            <div class="text-xs dash-gpct <?= $inv['gross_profit_pct'] >= 0 ? 'text-green-500' : 'text-red-500' ?>">
                                <?= $inv['gross_profit_pct'] >= 0 ? '+' : '' ?><?= $inv['gross_profit_pct'] ?>%
                            </div>
                        </td>
                        <td class="px-6 py-4 text-orange-400 text-sm dash-fees"><?= format_price_dual($inv['total_fees'] ?? 0, $currency, $baseCurrency) ?></td>
                        <td class="px-6 py-4 text-yellow-400 text-sm dash-tax"><?= format_price_dual($inv['total_tax'] ?? 0, $currency, $baseCurrency) ?></td>
                        <td class="px-6 py-4 dash-net <?= $inv['net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?> font-semibold">
                            <span class="dash-net-val"><?= $inv['net_profit'] >= 0 ? '+' : '' ?><?= format_price_dual($inv['net_profit'] ?? 0, $currency, $baseCurrency) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($investmentDetails)): ?>
    <div class="bg-navy2 rounded-xl border border-gray-700 p-6 mb-8" id="dashSummary">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-white font-bold text-lg">Portfolio Summary</h2>
            <span class="text-gray-400 text-xs">Base Currency: <?= esc($portfolio['base_currency'] ?? 'INR') ?></span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="text-center p-4 bg-navy rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Total Invested</p>
                <p id="dashSumInvested" class="text-white font-bold"><?= format_price_dual($portfolio['total_invested'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?></p>
            </div>
            <div class="text-center p-4 bg-navy rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Current Value</p>
                <p id="dashSumValue" class="text-white font-bold"><?= format_price_dual($portfolio['total_current_value'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?></p>
            </div>
            <div class="text-center p-4 bg-navy rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Gross P/L</p>
                <p id="dashSumGross" class="font-bold <?= $portfolio['total_gross_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                    <?= $portfolio['total_gross_profit'] >= 0 ? '+' : '' ?><?= format_price_dual($portfolio['total_gross_profit'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?>
                </p>
            </div>
            <div class="text-center p-4 bg-navy rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Total Fees</p>
                <p id="dashSumFees" class="text-orange-400 font-bold"><?= format_price_dual($portfolio['total_fees'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?></p>
            </div>
            <div class="text-center p-4 bg-navy rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Total Tax</p>
                <p id="dashSumTax" class="text-yellow-400 font-bold"><?= format_price_dual($portfolio['total_tax'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?></p>
            </div>
            <div class="text-center p-4 bg-navy rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Net P/L</p>
                <p id="dashSumNet" class="font-bold <?= $portfolio['total_net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                    <?= $portfolio['total_net_profit'] >= 0 ? '+' : '' ?><?= format_price_dual($portfolio['total_net_profit'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?>
                </p>
            </div>
            <div class="text-center p-4 bg-navy rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Return</p>
                <p id="dashSumReturn" class="font-bold <?= $portfolio['total_invested'] > 0 && $portfolio['total_net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                    <?= $portfolio['total_invested'] > 0 ? round(($portfolio['total_net_profit'] / $portfolio['total_invested']) * 100, 2) : 0 ?>%
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-3">
                <h2 class="text-white font-bold text-lg">All Stocks — Live Prices</h2>

            </div>
            <div class="flex items-center space-x-3">
                <div class="relative" id="dashSearchContainer">
                    <input type="text" id="dashStockSearch" placeholder="Quick search by symbol or name..."
                        autocomplete="off"
                        class="bg-navy border border-gray-600 rounded-lg pl-8 pr-3 py-1.5 text-sm text-white focus:border-gold focus:outline-none w-40 md:w-56">
                    <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                    <div id="dashSearchDropdown" class="hidden absolute top-full right-0 mt-1 w-72 bg-navy2 border border-gray-600 rounded-lg shadow-xl z-50 max-h-80 overflow-y-auto"></div>
                </div>
                <a href="/stocks" class="text-gold text-sm hover:text-gold2 transition whitespace-nowrap">View All Stocks</a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="marketOverview">
            <?php foreach ($allStocks as $stock): ?>
            <?php $change = get_price_change((float) $stock['current_price'], (float) $stock['previous_close']); ?>
            <a href="/stocks/<?= $stock['id'] ?>" class="market-item flex justify-between items-center p-4 bg-navy rounded-lg hover:border-gold border border-transparent transition" data-sid="<?= $stock['id'] ?>">
                <div>
                    <p class="text-white font-semibold"><?= esc($stock['symbol']) ?></p>
                    <p class="text-gray-500 text-xs"><?= esc($stock['name']) ?></p>
                </div>
                <div class="text-right">
                    <p class="mkt-price text-white font-semibold"><?= format_price($stock['current_price'], stock_currency($stock['exchange'] ?? null)) ?></p>
                    <p class="mkt-change <?= $change['change'] >= 0 ? 'text-green-400' : 'text-red-400' ?> text-sm">
                        <?= $change['change'] >= 0 ? '+' : '' ?><?= $change['percent'] ?>%
                    </p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
(function() {


    var CSRF_NAME = '<?= csrf_token() ?>';
    var CSRF_HASH = '<?= csrf_hash() ?>';
    var dashSearch = document.getElementById('dashStockSearch');
    var dashDropdown = document.getElementById('dashSearchDropdown');
    var dashTimer = null;
    if (!dashSearch) return;

    function escHtml(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    window.importStock = function(sym) {
        var body = CSRF_NAME + '=' + encodeURIComponent(CSRF_HASH) + '&symbol=' + encodeURIComponent(sym);
        var btns = document.querySelectorAll('[data-import="' + sym + '"]');
        btns.forEach(function(btn) { if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; } });
        fetch('/api/stocks/import', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
            .then(function(r){ return r.json(); })
            .then(function(d){
                if (d.success) {
                    window.location.reload();
                } else {
                    alert(d.message);
                    btns.forEach(function(btn) { if (btn) { btn.disabled = false; btn.innerHTML = '+ Add'; } });
                }
            })
            .catch(function(){
                alert('Failed to import stock.');
                btns.forEach(function(btn) { if (btn) { btn.disabled = false; btn.innerHTML = '+ Add'; } });
            });
    }

    dashSearch.addEventListener('input', function() {
        var val = this.value.trim();
        if (dashTimer) clearTimeout(dashTimer);
        if (val.length < 2) {
            dashDropdown.classList.add('hidden');
            return;
        }
        dashTimer = setTimeout(function() {
            fetch('/api/search?q=' + encodeURIComponent(val))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (!data.results || data.results.length === 0) {
                        dashDropdown.innerHTML = '<div class="p-3 text-gray-400 text-sm text-center">No results found</div>';
                        dashDropdown.classList.remove('hidden');
                        return;
                    }
                    var html = '';
                    data.results.forEach(function(s) {
                        var priceStr = s.price ? '\u20B9' + parseFloat(s.price).toLocaleString('en-IN', { minimumFractionDigits: 2 }) : '—';
                        var changeStr = '';
                        if (s.change_percent !== null) {
                            var cls = s.change_percent >= 0 ? 'text-green-400' : 'text-red-400';
                            changeStr = '<span class="' + cls + ' text-xs">' + (s.change_percent >= 0 ? '+' : '') + s.change_percent + '%</span>';
                        }
                        html += '<div class="flex justify-between items-center px-4 py-3 hover:bg-navy border-b border-gray-700/50 last:border-0 transition">' +
                            '<div>' +
                            '<span class="text-white text-sm font-semibold">' + escHtml(s.symbol) + '</span>' +
                            '<div class="text-gray-500 text-xs">' + escHtml(s.name) + '</div>' +
                            '</div>' +
                            '<div class="text-right flex items-center space-x-2">' +
                            '<div>' +
                            '<div class="text-white text-sm">' + priceStr + '</div>' +
                            changeStr +
                            '</div>';
                        if (s.from_yahoo) {
                            html += '<button data-import="' + escHtml(s.symbol) + '" onclick="importStock(\'' + escHtml(s.symbol) + '\')" class="text-xs px-2 py-1 rounded bg-gold text-navy font-semibold hover:bg-gold2 transition whitespace-nowrap">+ Add</button>';
                        } else {
                            html += '<a href="/stocks/' + s.id + '" class="text-xs px-2 py-1 rounded bg-navy border border-gray-600 text-gray-300 hover:text-white transition">View</a>';
                        }
                        html += '</div>' +
                            '</div>';
                    });
                    dashDropdown.innerHTML = html;
                    dashDropdown.classList.remove('hidden');
                })
                .catch(function() { dashDropdown.classList.add('hidden'); });
        }, 250);
    });

    dashSearch.addEventListener('blur', function() {
        setTimeout(function() { dashDropdown.classList.add('hidden'); }, 200);
    });

    dashSearch.addEventListener('focus', function() {
        if (dashDropdown.children.length > 0) {
            dashDropdown.classList.remove('hidden');
        }
    });

})();
</script>
