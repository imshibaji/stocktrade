<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white">Portfolio Summary</h1>
                <span id="marketBadge" class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>
                </span>
            </div>
            <p class="text-gray-400 mt-1">Complete overview of your investment portfolio with tax calculations</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="/investments" class="bg-gold hover:bg-gold2 text-navy font-semibold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-plus mr-2"></i>Add Investment
            </a>
            <a href="/investments/history" class="border border-gray-600 text-gray-300 hover:border-gold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-history mr-2"></i>History
            </a>
        </div>
    </div>

    <?php if (empty($portfolio['investments'])): ?>
    <div class="bg-navy2 rounded-xl border border-gray-700 p-12 text-center">
        <i class="fas fa-chart-pie text-6xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-xl font-semibold mb-2">No investments yet</h3>
        <p class="text-gray-400 mb-6">Add investments to see your portfolio summary with profit/loss analysis.</p>
        <a href="/investments" class="bg-gold hover:bg-gold2 text-navy font-semibold px-8 py-3 rounded-lg transition inline-block">
            Start Investing
        </a>
    </div>
    <?php else: ?>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8" id="portfolioCards">
        <div class="bg-navy2 rounded-xl p-5 border border-gray-700 text-center">
            <p class="text-gray-400 text-xs mb-1">Total Invested</p>
            <p id="totalInvested" class="text-xl font-bold text-white" data-val="<?= $portfolio['total_invested'] ?>"><?= format_price($portfolio['total_invested']) ?></p>
        </div>
        <div class="bg-navy2 rounded-xl p-5 border border-gray-700 text-center">
            <p class="text-gray-400 text-xs mb-1">Current Value</p>
            <p id="totalValue" class="text-xl font-bold text-white"><?= format_price($portfolio['total_current_value']) ?></p>
        </div>
        <div class="bg-navy2 rounded-xl p-5 border border-gray-700 text-center">
            <p class="text-gray-400 text-xs mb-1">Gross P/L</p>
            <p id="totalGross" class="text-xl font-bold <?= $portfolio['total_gross_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                <?= $portfolio['total_gross_profit'] >= 0 ? '+' : '' ?><?= format_price($portfolio['total_gross_profit']) ?>
            </p>
        </div>
        <div class="bg-navy2 rounded-xl p-5 border border-gray-700 text-center">
            <p class="text-gray-400 text-xs mb-1">Total Fees</p>
            <p id="totalFeesCard" class="text-xl font-bold text-orange-400"><?= format_price($portfolio['total_fees'] ?? 0) ?></p>
        </div>
        <div class="bg-navy2 rounded-xl p-5 border border-gray-700 text-center">
            <p class="text-gray-400 text-xs mb-1">Total Tax</p>
            <p id="totalTax" class="text-xl font-bold text-yellow-400"><?= format_price($portfolio['total_tax']) ?></p>
        </div>
        <div class="bg-navy2 rounded-xl p-5 border border-gray-700 text-center">
            <p class="text-gray-400 text-xs mb-1">Net P/L</p>
            <p id="totalNet" class="text-xl font-bold <?= $portfolio['total_net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                <?= $portfolio['total_net_profit'] >= 0 ? '+' : '' ?><?= format_price($portfolio['total_net_profit']) ?>
            </p>
        </div>
        <div class="bg-navy2 rounded-xl p-5 border border-gray-700 text-center">
            <p class="text-gray-400 text-xs mb-1">Net Return</p>
            <p id="totalReturn" class="text-xl font-bold <?= $portfolio['total_invested'] > 0 && $portfolio['total_net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                <?= $portfolio['total_invested'] > 0 ? round(($portfolio['total_net_profit'] / $portfolio['total_invested']) * 100, 2) : 0 ?>%
            </p>
        </div>
    </div>

    <div class="bg-navy2 rounded-xl border border-gray-700 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-700">
            <h2 class="text-white font-bold text-lg">Investment Breakdown</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="text-left px-6 py-3">Stock</th>
                        <th class="text-right px-6 py-3">Buy Price</th>
                        <th class="text-right px-6 py-3">Current</th>
                        <th class="text-right px-6 py-3">Shares</th>
                        <th class="text-right px-6 py-3">Invested</th>
                        <th class="text-right px-6 py-3">Value</th>
                        <th class="text-right px-6 py-3">Gross P/L</th>
                        <th class="text-right px-6 py-3">Fees</th>
                        <th class="text-right px-6 py-3">Tax Type</th>
                        <th class="text-right px-6 py-3">Tax</th>
                        <th class="text-right px-6 py-3">Net P/L</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($portfolio['investments'] as $item): 
                        $inv = $item['stock'];
                        $pfData = [
                            'sid' => (int) $inv['stock_id'],
                            'shares' => (int) $inv['shares'],
                            'buyPrice' => (float) $inv['buy_price'],
                            'invested' => (float) $item['total_invested'],
                            'buyDate' => $inv['buy_date'],
                        ];
                    ?>
                    <tr class="pf-row border-b border-gray-700/50 hover:bg-navy/50" data-pf='<?= json_encode($pfData) ?>'>
                        <td class="px-6 py-4">
                            <span class="text-white font-semibold"><?= esc($inv['symbol']) ?></span>
                            <div class="text-gray-500 text-xs"><?= esc($inv['name']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= format_price($inv['buy_price']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300 pf-cp"><?= format_price($inv['current_price']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= (int) $inv['shares'] ?></td>
                        <td class="px-6 py-4 text-right text-gray-300 pf-invested"><?= format_price($item['total_invested']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300 pf-value"><?= format_price($item['current_value']) ?></td>
                        <td class="px-6 py-4 text-right pf-gl <?= $item['gross_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <span class="pf-gross"><?= $item['gross_profit'] >= 0 ? '+' : '' ?><?= format_price($item['gross_profit']) ?></span>
                            <div class="text-xs pf-gpct"><?= $item['gross_profit_pct'] >= 0 ? '+' : '' ?><?= $item['gross_profit_pct'] ?>%</div>
                        </td>
                        <td class="px-6 py-4 text-right text-orange-400 pf-fees"><?= format_price($item['total_fees']) ?></td>
                        <td class="px-6 py-4 text-right pf-tax-type">
                            <span class="text-xs <?= $item['held_days'] < 365 ? 'text-yellow-400' : 'text-blue-400' ?>">
                                <?= $item['type'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-yellow-400 pf-tax"><?= format_price($item['total_tax']) ?></td>
                        <td class="px-6 py-4 text-right pf-net <?= $item['net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?> font-semibold">
                            <span class="pf-net-val"><?= $item['net_profit'] >= 0 ? '+' : '' ?><?= format_price($item['net_profit']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-navy">
                        <td class="px-6 py-4 font-bold text-white" colspan="4">TOTAL</td>
                        <td class="px-6 py-4 text-right text-white font-bold" id="totalInvFooter"><?= format_price($portfolio['total_invested']) ?></td>
                        <td class="px-6 py-4 text-right text-white font-bold" id="totalValFooter"><?= format_price($portfolio['total_current_value']) ?></td>
                        <td class="px-6 py-4 text-right font-bold <?= $portfolio['total_gross_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>" id="totalGrossFooter">
                            <?= $portfolio['total_gross_profit'] >= 0 ? '+' : '' ?><?= format_price($portfolio['total_gross_profit']) ?>
                        </td>
                        <td class="px-6 py-4 text-right text-orange-400 font-bold" id="totalFeesFooter"><?= format_price($portfolio['total_fees'] ?? 0) ?></td>
                        <td class="px-6 py-4"></td>
                        <td class="px-6 py-4 text-right text-yellow-400 font-bold" id="totalTaxFooter"><?= format_price($portfolio['total_tax']) ?></td>
                        <td class="px-6 py-4 text-right font-bold <?= $portfolio['total_net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>" id="totalNetFooter">
                            <?= $portfolio['total_net_profit'] >= 0 ? '+' : '' ?><?= format_price($portfolio['total_net_profit']) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-navy2 rounded-xl border border-yellow-700/50 p-6">
            <h3 class="text-yellow-400 font-semibold text-lg mb-3">Short Term Capital Gains (STCG)</h3>
            <p class="text-gray-400 text-sm mb-2">Applied when shares are held less than 1 year.</p>
            <ul class="space-y-2 text-sm text-gray-300">
                <li><i class="fas fa-check text-green-500 mr-2"></i>Tax Rate: <strong class="text-white">15% on all gains</strong></li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>No exemption limit</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Holding period: &lt; 365 days</li>
            </ul>
        </div>
        <div class="bg-navy2 rounded-xl border border-blue-700/50 p-6">
            <h3 class="text-blue-400 font-semibold text-lg mb-3">Long Term Capital Gains (LTCG)</h3>
            <p class="text-gray-400 text-sm mb-2">Applied when shares are held more than 1 year.</p>
            <ul class="space-y-2 text-sm text-gray-300">
                <li><i class="fas fa-check text-green-500 mr-2"></i>Tax Rate: <strong class="text-white">10% on gains above &#8377;1,00,000</strong></li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Exemption: First &#8377;1,00,000 tax-free</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Holding period: &gt;= 365 days</li>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</section>

<script>
(function() {
    var TAX_CFG = <?= json_encode([
        'stcg_rate' => (float) ($taxRates['stcg_rate'] ?? 0.15),
        'ltcg_rate' => (float) ($taxRates['ltcg_rate'] ?? 0.10),
        'fee_rates' => $taxRates['fee_rates'] ?? [],
    ]) ?>;

    function formatPrice(v) { return '\u20B9' + parseFloat(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    var totalInvested = parseFloat(document.getElementById('totalInvested').getAttribute('data-val'));

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

    function calcFees(amount) {
        var r = TAX_CFG.fee_rates || {};
        var b = amount * (parseFloat(r.brokerage_pct || 0) / 100);
        var s = amount * (parseFloat(r.stt_pct || 0) / 100);
        var e = amount * (parseFloat(r.exchange_pct || 0) / 100);
        var g = (b + e) * (parseFloat(r.gst_pct || 18) / 100);
        var d = amount * (parseFloat(r.stamp_duty_pct || 0) / 100);
        var se = amount * (parseFloat(r.sebi_fees || 0) / 100);
        return b + s + e + g + d + se;
    }

    function calcTax(gross, buyDate) {
        if (gross <= 0) return 0;
        var held = (new Date() - new Date(buyDate)) / 86400000;
        if (held < 365) return gross * TAX_CFG.stcg_rate;
        var exemption = 100000;
        return Math.max(0, gross - exemption) * TAX_CFG.ltcg_rate;
    }

    function updateAll(stocks) {
        var priceMap = {};
        stocks.forEach(function(s) { priceMap[s.id] = s.current_price; });

        var totalValue = 0, totalGross = 0, totalFees = 0, totalTax = 0, totalNet = 0;

        document.querySelectorAll('.pf-row').forEach(function(row) {
            var pf = JSON.parse(row.getAttribute('data-pf'));
            var livePrice = priceMap[pf.sid];
            if (!livePrice) return;

            var currentValue = pf.shares * livePrice;
            var grossProfit = currentValue - pf.invested;
            var grossPct = pf.invested > 0 ? (grossProfit / pf.invested) * 100 : 0;
            var fees = calcFees(currentValue);
            var profitAfterFees = grossProfit - fees;
            var tax = calcTax(profitAfterFees, pf.buyDate);
            var netProfit = profitAfterFees - tax;

            totalValue += currentValue;
            totalGross += grossProfit;
            totalFees += fees;
            totalTax += tax;
            totalNet += netProfit;

            var cp = row.querySelector('.pf-cp');
            var val = row.querySelector('.pf-value');
            var gl = row.querySelector('.pf-gl');
            var grossEl = row.querySelector('.pf-gross');
            var gpctEl = row.querySelector('.pf-gpct');
            var feesEl = row.querySelector('.pf-fees');
            var taxEl = row.querySelector('.pf-tax');
            var net = row.querySelector('.pf-net');
            var netVal = row.querySelector('.pf-net-val');

            if (cp) cp.textContent = formatPrice(livePrice);
            if (val) val.textContent = formatPrice(currentValue);
            if (grossEl) grossEl.textContent = (grossProfit >= 0 ? '+' : '') + formatPrice(grossProfit);
            if (gpctEl) gpctEl.textContent = (grossPct >= 0 ? '+' : '') + grossPct.toFixed(2) + '%';
            if (feesEl) feesEl.textContent = formatPrice(fees);
            if (taxEl) taxEl.textContent = formatPrice(tax);
            if (netVal) netVal.textContent = (netProfit >= 0 ? '+' : '') + formatPrice(netProfit);

            if (grossProfit >= 0) {
                if (gl) gl.className = 'px-6 py-4 text-right pf-gl text-green-400';
                if (gpctEl) gpctEl.className = 'text-xs pf-gpct text-green-500';
                if (net) net.className = 'px-6 py-4 text-right pf-net text-green-400 font-semibold';
            } else {
                if (gl) gl.className = 'px-6 py-4 text-right pf-gl text-red-400';
                if (gpctEl) gpctEl.className = 'text-xs pf-gpct text-red-500';
                if (net) net.className = 'px-6 py-4 text-right pf-net text-red-400 font-semibold';
            }
        });

        var tv = document.getElementById('totalValue');
        var tg = document.getElementById('totalGross');
        var tf = document.getElementById('totalFeesCard');
        var tx = document.getElementById('totalTax');
        var tn = document.getElementById('totalNet');
        var tr = document.getElementById('totalReturn');

        if (tv) tv.textContent = formatPrice(totalValue);
        if (tg) { tg.textContent = (totalGross >= 0 ? '+' : '') + formatPrice(totalGross); tg.className = 'text-xl font-bold ' + (totalGross >= 0 ? 'text-green-400' : 'text-red-400'); }
        if (tf) tf.textContent = formatPrice(totalFees);
        if (tx) tx.textContent = formatPrice(totalTax);
        if (tn) { tn.textContent = (totalNet >= 0 ? '+' : '') + formatPrice(totalNet); tn.className = 'text-xl font-bold ' + (totalNet >= 0 ? 'text-green-400' : 'text-red-400'); }
        if (tr) { tr.textContent = (totalInvested > 0 ? ((totalNet / totalInvested) * 100).toFixed(2) : 0) + '%'; tr.className = 'text-xl font-bold ' + (totalNet >= 0 ? 'text-green-400' : 'text-red-400'); }

        var tvf = document.getElementById('totalValFooter');
        var tgf = document.getElementById('totalGrossFooter');
        var tff = document.getElementById('totalFeesFooter');
        var txf = document.getElementById('totalTaxFooter');
        var tnf = document.getElementById('totalNetFooter');
        if (tvf) tvf.textContent = formatPrice(totalValue);
        if (tgf) { tgf.textContent = (totalGross >= 0 ? '+' : '') + formatPrice(totalGross); tgf.className = 'px-6 py-4 text-right font-bold ' + (totalGross >= 0 ? 'text-green-400' : 'text-red-400'); }
        if (tff) tff.textContent = formatPrice(totalFees);
        if (txf) txf.textContent = formatPrice(totalTax);
        if (tnf) { tnf.textContent = (totalNet >= 0 ? '+' : '') + formatPrice(totalNet); tnf.className = 'px-6 py-4 text-right font-bold ' + (totalNet >= 0 ? 'text-green-400' : 'text-red-400'); }
    }

    function poll() {
        fetch('/api/live-prices')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                updateBadge(data.market);
                updateAll(data.stocks);
            })
            .catch(function() {});
    }

    poll();
    setInterval(poll, 5000);
})();
</script>
