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
            <a href="/stocks" class="bg-accent hover:bg-accent-2 text-on-accent font-semibold px-6 py-2 rounded-lg text-sm transition">
                <i class="fas fa-search mr-2"></i>Browse Stocks
            </a>
            <a href="/investments" class="border border-accent text-accent hover:bg-accent/10 px-6 py-2 rounded-lg text-sm transition">
                <i class="fas fa-plus mr-2"></i>New Investment
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-surface rounded-xl p-6 border border-gray-700">
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
        <div class="bg-surface rounded-xl p-6 border border-gray-700">
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
        <div class="bg-surface rounded-xl p-6 border border-gray-700">
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
    <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
            <h2 class="text-white font-bold text-lg">Active Investments</h2>
            <a href="/investments" class="text-accent text-sm hover:text-accent-2 transition">View All</a>
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
                    <tr class="border-b border-gray-700/50 hover:bg-page/50 cursor-pointer" onclick="location.href='/stocks/<?= $inv['stock_id'] ?>'">
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
    <div class="bg-surface rounded-xl border border-gray-700 p-6 mb-8" id="dashSummary">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-white font-bold text-lg">Portfolio Summary</h2>
            <span class="text-gray-400 text-xs">Base Currency: <?= esc($portfolio['base_currency'] ?? 'INR') ?></span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            <div class="text-center p-4 bg-page rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Total Invested</p>
                <p id="dashSumInvested" class="text-white font-bold"><?= format_price_dual($portfolio['total_invested'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?></p>
            </div>
            <div class="text-center p-4 bg-page rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Current Value</p>
                <p id="dashSumValue" class="text-white font-bold"><?= format_price_dual($portfolio['total_current_value'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?></p>
            </div>
            <div class="text-center p-4 bg-page rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Gross P/L</p>
                <p id="dashSumGross" class="font-bold <?= $portfolio['total_gross_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                    <?= $portfolio['total_gross_profit'] >= 0 ? '+' : '' ?><?= format_price_dual($portfolio['total_gross_profit'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?>
                </p>
            </div>
            <div class="text-center p-4 bg-page rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Total Fees</p>
                <p id="dashSumFees" class="text-orange-400 font-bold"><?= format_price_dual($portfolio['total_fees'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?></p>
            </div>
            <div class="text-center p-4 bg-page rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Total Tax</p>
                <p id="dashSumTax" class="text-yellow-400 font-bold"><?= format_price_dual($portfolio['total_tax'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?></p>
            </div>
            <div class="text-center p-4 bg-page rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Net P/L</p>
                <p id="dashSumNet" class="font-bold <?= $portfolio['total_net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                    <?= $portfolio['total_net_profit'] >= 0 ? '+' : '' ?><?= format_price_dual($portfolio['total_net_profit'] ?? 0, 'INR', $portfolio['base_currency'] ?? 'INR') ?>
                </p>
            </div>
            <div class="text-center p-4 bg-page rounded-lg">
                <p class="text-gray-400 text-xs mb-1">Return</p>
                <p id="dashSumReturn" class="font-bold <?= $portfolio['total_invested'] > 0 && $portfolio['total_net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                    <?= $portfolio['total_invested'] > 0 ? round(($portfolio['total_net_profit'] / $portfolio['total_invested']) * 100, 2) : 0 ?>%
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($watchlistStocks)): ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-white font-bold text-lg">
                <i class="fas fa-star text-yellow-400 mr-2"></i>My Watchlist
            </h2>
            <a href="/watchlist" class="text-accent text-sm hover:text-accent-2 transition">Manage</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($watchlistStocks as $ws): ?>
            <?php
                $change = get_price_change((float) $ws['current_price'], (float) $ws['previous_close']);
                $sid = (int) $ws['stock_id'];
                $pred = $watchlistPredictions[$sid] ?? null;
                $cur = stock_currency($ws['exchange'] ?? null);
            ?>
            <div class="wl-card p-4 bg-page rounded-lg border border-transparent hover:border-accent transition cursor-pointer" onclick="location.href='/stocks/<?= $sid ?>'" data-wlid="<?= $ws['id'] ?>">
                <div class="flex justify-between items-start mb-2">
                    <a href="/stocks/<?= $sid ?>" onclick="event.stopPropagation()" class="min-w-0 flex-1">
                        <p class="text-white font-semibold truncate"><?= esc($ws['symbol']) ?></p>
                        <p class="text-gray-500 text-xs truncate"><?= esc($ws['name']) ?></p>
                    </a>
                    <span class="text-xs px-2 py-0.5 rounded bg-surface border border-gray-600 text-gray-300 shrink-0 ml-2"><?= esc($ws['exchange'] ?? 'NSE') ?></span>
                </div>
                <?php if ($pred && $pred['low'] > 0 && $pred['high'] > 0): ?>
                <div class="text-xs flex items-center gap-2 mb-2">
                    <span class="text-red-400 font-semibold">SL: <?= format_price($pred['low'], $cur) ?></span>
                    <span class="text-gray-600">|</span>
                    <span class="text-green-400 font-semibold">Target: <?= format_price($pred['high'], $cur) ?></span>
                </div>
                <?php
                    $outlookPct = $pred['avg'] > 0 && (float) $ws['current_price'] > 0 ? ((($pred['avg'] - (float) $ws['current_price']) / (float) $ws['current_price']) * 100) : 0;
                    if ($outlookPct > 2) {
                        $outlookIcon = 'arrow-trend-up';
                        $outlookClass = 'text-green-400';
                        $outlookText = 'Bullish (+' . round($outlookPct, 1) . '%)';
                    } elseif ($outlookPct < -2) {
                        $outlookIcon = 'arrow-trend-down';
                        $outlookClass = 'text-red-400';
                        $outlookText = 'Bearish (' . round($outlookPct, 1) . '%)';
                    } else {
                        $outlookIcon = 'arrows-left-right';
                        $outlookClass = 'text-gray-400';
                        $outlookText = 'Sideways';
                    }
                ?>
                <div class="text-xs mb-2">
                    <span class="<?= $outlookClass ?> font-semibold"><i class="fas fa-<?= $outlookIcon ?> mr-1"></i>30-Day: <?= $outlookText ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between items-center mt-1">
                    <p class="text-white font-bold"><?= format_price($ws['current_price'], $cur) ?></p>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-xs font-semibold <?= $change['change'] >= 0 ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400' ?>">
                            <?= $change['change'] >= 0 ? '+' : '' ?><?= $change['percent'] ?>%
                        </span>
                        <button onclick="event.stopPropagation(); removeWatchlist(<?= $ws['id'] ?>, '<?= str_replace(["'", '"'], '', $ws['symbol']) ?>')" class="text-red-400 hover:text-red-300 text-xs" title="Remove from watchlist">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</section>

<script>
(function() {


    var CSRF_NAME = '<?= csrf_token() ?>';
    var CSRF_HASH = '<?= csrf_hash() ?>';

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

    window.removeWatchlist = function(id, symbol) {
        if (!confirm('Remove ' + symbol + ' from watchlist?')) return;
        var csrfInput = document.querySelector('input[name="' + CSRF_NAME + '"]') || document.querySelector('input[name^="csrf_"]');
        var bodyStr = (CSRF_NAME || 'csrf_test_name') + '=' + encodeURIComponent(csrfInput ? csrfInput.value : '');
        fetch('/watchlist/toggle/' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: bodyStr
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.watched) {
                var card = document.querySelector('.wl-card[data-wlid="' + id + '"]');
                if (card) {
                    card.style.transition = 'opacity 0.3s';
                    card.style.opacity = '0';
                    setTimeout(function() { card.remove(); }, 300);
                }
            }
        });
    };
})();
</script>
