<section>
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white">Investment History</h1>
                <span id="marketBadge" class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>
                </span>
            </div>
            <p class="text-gray-400 mt-1">Complete record of all your trades</p>
        </div>
        <a href="/investments" class="border border-gold text-gold hover:bg-gold/10 px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-plus mr-2"></i>New Investment
        </a>
    </div>

    <?php if (empty($investments)): ?>
    <div class="bg-navy2 rounded-xl border border-gray-700 p-12 text-center">
        <i class="fas fa-history text-6xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-xl font-semibold mb-2">No investment history</h3>
        <p class="text-gray-400 mb-6">Start investing to build your trading history.</p>
        <a href="/stocks" class="bg-gold hover:bg-gold2 text-navy font-semibold px-8 py-3 rounded-lg transition inline-block">
            Browse Stocks
        </a>
    </div>
    <?php else: ?>
    <div class="bg-navy2 rounded-xl border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="text-left px-6 py-3">Stock</th>
                        <th class="text-left px-6 py-3">Buy Date</th>
                        <th class="text-right px-6 py-3">Buy Price</th>
                        <th class="text-right px-6 py-3">Shares</th>
                        <th class="text-right px-6 py-3">Invested</th>
                        <th class="text-right px-6 py-3">Current / Sell Price</th>
                        <th class="text-left px-6 py-3">Sell Date</th>
                        <th class="text-center px-6 py-3">Status</th>
                        <th class="text-right px-6 py-3">P/L</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($investments as $inv):
                        $invModel = new \App\Models\InvestmentModel();
                        $isActive = $inv['status'] === 'active';
                        if ($isActive) {
                            $pl = $invModel->calculateProfitLoss($inv, $taxRates ?? []);
                            $actualPl = $pl['gross_profit'];
                            $actualPlPct = $pl['gross_profit_pct'];
                        } else {
                            $actualPl = ((float) $inv['sell_price'] - (float) $inv['buy_price']) * (float) $inv['shares'];
                            $actualPlPct = (float) $inv['buy_price'] > 0 ? (((float) $inv['sell_price'] - (float) $inv['buy_price']) / (float) $inv['buy_price']) * 100 : 0;
                        }
                        $histData = [
                            'sid' => (int) $inv['stock_id'],
                            'shares' => (int) $inv['shares'],
                            'invested' => (float) $inv['total_invested'],
                            'buyDate' => $inv['buy_date'],
                            'status' => $inv['status'],
                        ];
                    ?>
                    <tr class="hist-row border-b border-gray-700/50 hover:bg-navy/50" data-hist='<?= json_encode($histData) ?>'>
                        <td class="px-6 py-4">
                            <a href="/stocks/<?= $inv['stock_id'] ?>" class="text-white font-semibold hover:text-gold"><?= esc($inv['symbol']) ?></a>
                            <div class="text-gray-500 text-xs"><?= esc($inv['name']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-300"><?= date('M d, Y', strtotime($inv['buy_date'])) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= format_price($inv['buy_price']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= (int) $inv['shares'] ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= format_price($inv['total_invested']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300 hist-price">
                            <?php if ($isActive): ?>
                            <span class="hist-live"><?= format_price($inv['current_price']) ?></span>
                            <?php else: ?>
                            <?= format_price($inv['sell_price']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-300">
                            <?= $inv['sell_date'] ? date('M d, Y', strtotime($inv['sell_date'])) : '<span class="text-gray-600">-</span>' ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded text-xs hist-status <?= $isActive ? 'bg-green-900/30 text-green-400' : 'bg-gray-700 text-gray-400' ?>">
                                <?= ucfirst($inv['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right hist-pl font-semibold <?= $actualPl >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <span class="hist-pl-val"><?= $actualPl >= 0 ? '+' : '' ?><?= format_price($actualPl) ?></span>
                            <div class="text-xs hist-pl-pct <?= $actualPlPct >= 0 ? 'text-green-500' : 'text-red-500' ?>">
                                <?= $actualPlPct >= 0 ? '+' : '' ?><?= round($actualPlPct, 2) ?>%
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</section>

<script>
(function() {
    function formatPrice(v) { return '\u20B9' + parseFloat(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function calcTax(gross, buyDate) {
        if (gross <= 0) return 0;
        var held = (new Date() - new Date(buyDate)) / 86400000;
        if (held < 365) return gross * 0.15;
        return Math.max(0, gross - 100000) * 0.10;
    }

    function updateBadge(market) {
        var badge = document.getElementById('marketBadge');
        if (!badge) return;
        if (market.open) {
            badge.className = 'text-xs px-3 py-1 rounded-full border border-green-600 text-green-400';
            badge.innerHTML = '<i class="fas fa-circle text-green-400 text-[8px] mr-1 animate-pulse"></i>' + market.label;
        } else {
            badge.className = 'text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400';
            badge.innerHTML = '<i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>' + market.label;
        }
    }

    function updateRows(stocks) {
        var priceMap = {};
        stocks.forEach(function(s) { priceMap[s.id] = s.current_price; });

        document.querySelectorAll('.hist-row').forEach(function(row) {
            var h = JSON.parse(row.getAttribute('data-hist'));
            if (h.status !== 'active') return;
            var livePrice = priceMap[h.sid];
            if (!livePrice) return;

            var currentValue = h.shares * livePrice;
            var grossProfit = currentValue - h.invested;
            var grossPct = h.invested > 0 ? (grossProfit / h.invested) * 100 : 0;

            var priceEl = row.querySelector('.hist-live');
            var plEl = row.querySelector('.hist-pl');
            var plVal = row.querySelector('.hist-pl-val');
            var plPct = row.querySelector('.hist-pl-pct');

            if (priceEl) priceEl.textContent = formatPrice(livePrice);
            if (plVal) plVal.textContent = (grossProfit >= 0 ? '+' : '') + formatPrice(grossProfit);
            if (plPct) plPct.textContent = (grossPct >= 0 ? '+' : '') + grossPct.toFixed(2) + '%';
            if (plEl) plEl.className = 'px-6 py-4 text-right hist-pl font-semibold ' + (grossProfit >= 0 ? 'text-green-400' : 'text-red-400');
        });
    }

    function poll() {
        fetch('/api/live-prices')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                updateBadge(data.market);
                updateRows(data.stocks);
            })
            .catch(function() {});
    }

    poll();
    setInterval(poll, 5000);
})();
</script>
