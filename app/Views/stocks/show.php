<?php $isWatched = $isWatched ?? false; ?>
<?php $cur = stock_currency($stock['exchange'] ?? null); ?>
<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white"><?= esc($stock['symbol']) ?></h1>
                <span class="text-xs px-3 py-1 rounded bg-navy border border-gray-600 text-gray-300"><?= esc($stock['sector']) ?></span>
                <span id="marketBadge" class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>Checking...
                </span>
            </div>
            <p class="text-gray-400 mt-1"><?= esc($stock['name']) ?></p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <?php if (is_logged_in()): ?>
                <?php if ($isWatched): ?>
                <a href="/watchlist/remove/<?= $stock['id'] ?>" class="border border-red-500 text-red-400 hover:bg-red-900/20 px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-star mr-1"></i>Remove from Watchlist
                </a>
                <?php else: ?>
                <a href="/watchlist/add/<?= $stock['id'] ?>" class="border border-gold text-gold hover:bg-gold/10 px-4 py-2 rounded-lg text-sm transition">
                    <i class="far fa-star mr-1"></i>Add to Watchlist
                </a>
                <?php endif; ?>
            <?php endif; ?>
            <a href="/stocks/<?= $stock['id'] ?>/predictions" class="bg-gold hover:bg-gold2 text-navy font-semibold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-chart-line mr-1"></i> Predictions
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-navy2 rounded-xl border border-gray-700 p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <div class="flex items-center space-x-3">
                        <p id="livePrice" class="text-4xl font-bold text-white"><?= format_price($stock['current_price'], $cur) ?></p>
                        <span id="liveIndicator" class="hidden text-xs px-2 py-1 rounded bg-navy border border-gray-600 text-gray-300">Live</span>
                    </div>
                    <p id="liveChange" class="<?= $priceChange['change'] >= 0 ? 'text-green-400' : 'text-red-400' ?> mt-1">
                        <?= $priceChange['change'] >= 0 ? '+' : '' ?><?= format_price($priceChange['change'], $cur) ?>
                        (<span id="livePct"><?= $priceChange['change'] >= 0 ? '+' : '' ?><?= $priceChange['percent'] ?></span>%)
                    </p>
                </div>
                <div class="text-right text-sm text-gray-400">
                    <p>Prev Close: <?= format_price($stock['previous_close'], $cur) ?></p>
                    <p id="liveUpdated" class="text-gray-600 text-xs mt-1"></p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="priceChart"></canvas>
            </div>
        </div>

        <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
            <h3 class="text-white font-semibold mb-4">Key Metrics</h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Market Cap</span>
                    <span class="text-white text-sm"><?= $stock['market_cap'] ? format_large_number($stock['market_cap']) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Avg Volume</span>
                    <span class="text-white text-sm" id="mktAvgVol"><?= ($stock['avg_volume'] ?? 0) ? format_large_number($stock['avg_volume']) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Open</span>
                    <span class="text-white text-sm" id="mktOpen"><?= isset($stock['open_price']) ? format_price($stock['open_price'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Day High</span>
                    <span class="text-green-400 text-sm" id="mktHigh"><?= isset($stock['day_high']) ? format_price($stock['day_high'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Day Low</span>
                    <span class="text-red-400 text-sm" id="mktLow"><?= isset($stock['day_low']) ? format_price($stock['day_low'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Volume</span>
                    <span class="text-white text-sm" id="mktVol"><?= isset($stock['volume']) ? format_large_number($stock['volume']) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">P/E Ratio</span>
                    <span class="text-white text-sm"><?= ($stock['pe_ratio'] ?? 0) ?: 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">52-Week High</span>
                    <span class="text-green-400 text-sm"><?= ($stock['week_52_high'] ?? 0) ? format_price($stock['week_52_high'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">52-Week Low</span>
                    <span class="text-red-400 text-sm"><?= ($stock['week_52_low'] ?? 0) ? format_price($stock['week_52_low'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Dividend Yield</span>
                    <span class="text-white text-sm"><?= ($stock['dividend_yield'] ?? 0) ? $stock['dividend_yield'] . '%' : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Bid / Ask</span>
                    <span class="text-white text-sm" id="mktBidAsk"><?= (isset($stock['bid']) && isset($stock['ask'])) ? format_price($stock['bid'], $cur) . ' / ' . format_price($stock['ask'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-400 text-sm">Beta</span>
                    <span class="text-white text-sm"><?= ($stock['beta'] ?? 0) ?: 'N/A' ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($stock['predictions'])): ?>
    <div class="bg-navy2 rounded-xl border border-gray-700 p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-white font-bold text-lg">30-Day Price Predictions</h2>
            <a href="/stocks/<?= $stock['id'] ?>/predictions" class="text-gold text-sm hover:text-gold2 transition">View Details</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="text-left px-4 py-2">Date</th>
                        <th class="text-right px-4 py-2">Predicted Price</th>
                        <th class="text-right px-4 py-2">Confidence</th>
                        <th class="text-right px-4 py-2">Change from Current</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 0;
                    foreach ($stock['predictions'] as $p): 
                        if ($count++ >= 7) break;
                        $predChange = (float) $p['predicted_price'] - (float) $stock['current_price'];
                        $predChangePct = (float) $stock['current_price'] > 0 ? ($predChange / (float) $stock['current_price']) * 100 : 0;
                    ?>
                    <tr class="border-b border-gray-700/50 hover:bg-navy/50">
                        <td class="px-4 py-3 text-gray-300"><?= date('M d, Y', strtotime($p['predicted_date'])) ?></td>
                        <td class="px-4 py-3 text-right text-white"><?= format_price($p['predicted_price'], $cur) ?></td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <div class="w-16 h-2 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full <?= $p['confidence_score'] >= 80 ? 'bg-green-500' : ($p['confidence_score'] >= 65 ? 'bg-yellow-500' : 'bg-red-500') ?>" 
                                         style="width: <?= $p['confidence_score'] ?>%"></div>
                                </div>
                                <span class="text-gray-400 text-xs"><?= $p['confidence_score'] ?>%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right <?= $predChange >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <?= $predChange >= 0 ? '+' : '' ?><?= format_price($predChange, $cur)?> (<?= $predChangePct >= 0 ? '+' : '' ?><?= round($predChangePct, 2) ?>%)
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if (is_logged_in()): ?>
    <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
        <h2 class="text-white font-bold text-lg mb-4">Quick Investment Calculator</h2>
        <form action="/investments/create" method="post" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <?= csrf_field() ?>
            <input type="hidden" name="stock_id" value="<?= $stock['id'] ?>">
            <div>
                <label class="block text-gray-300 mb-2 text-sm">Current Price</label>
                <input type="text" value="<?= format_price($stock['current_price'], $cur) ?>" disabled
                    class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-gray-400">
            </div>
            <div>
                <label class="block text-gray-300 mb-2 text-sm">Investment Amount (Rs)</label>
                <input type="number" name="amount" required min="1" step="0.01"
                    class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none"
                    placeholder="e.g. 10000">
            </div>
            <div>
                <label class="block text-gray-300 mb-2 text-sm">Buy Date</label>
                <input type="date" name="buy_date" value="<?= date('Y-m-d') ?>"
                    class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
            </div>
            <div>
                <button type="submit" class="w-full bg-gold hover:bg-gold2 text-navy font-bold py-3 rounded-lg transition">
                    <i class="fas fa-plus mr-1"></i> Add Investment
                </button>
            </div>
        </form>
        <p class="text-gray-500 text-xs mt-3" id="sharePreview">
            Estimated shares: Enter amount above to see
        </p>
    </div>
    <script>
        document.querySelector('input[name="amount"]').addEventListener('input', function() {
            var amount = parseFloat(this.value);
            var priceEl = document.getElementById('livePrice');
            var price = priceEl ? parseFloat(priceEl.textContent.replace(/[^0-9.-]/g, '')) || 0 : 0;
            if (amount > 0 && price > 0) {
                var shares = amount / price;
                document.getElementById('sharePreview').textContent = 'Estimated shares: ' + shares.toFixed(4) + ' at ' + price.toFixed(2) + ' each';
            }
        });
    </script>
    <?php endif; ?>
</section>

<script>
var ctx = document.getElementById('priceChart').getContext('2d');
var gradient = ctx.createLinearGradient(0, 0, 0, 256);
gradient.addColorStop(0, 'rgba(212, 168, 83, 0.15)');
gradient.addColorStop(1, 'rgba(212, 168, 83, 0)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode(range(1, count($priceData))) ?>,
        datasets: [{
            label: 'Price History (90 Days)',
            data: <?= json_encode($priceData) ?>,
            borderColor: '#d4a853',
            backgroundColor: gradient,
            fill: true,
            tension: 0.3,
            pointRadius: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { display: false },
            y: {
                ticks: { color: '#888', callback: function(v) { return '\u20B9' + v; } },
                grid: { color: 'rgba(75, 85, 99, 0.3)' }
            }
        }
    }
});
</script>

<script>
(function() {
    var stockId = <?= $stock['id'] ?>;
    function formatPrice(v) { return '\u20B9' + parseFloat(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function updateBadge(market) {
        var badge = document.getElementById('marketBadge');
        if (!badge) return;
        var indicator = document.getElementById('liveIndicator');
        if (market.open) {
            badge.className = 'text-xs px-3 py-1 rounded-full border border-green-600 text-green-400';
            badge.innerHTML = '<i class="fas fa-circle text-green-400 text-[8px] mr-1 animate-pulse"></i>' + market.label;
            if (indicator) { indicator.className = 'text-xs px-2 py-1 rounded bg-green-900/30 text-green-400'; indicator.textContent = 'Live'; indicator.classList.remove('hidden'); }
        } else {
            badge.className = 'text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400';
            badge.innerHTML = '<i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>' + market.label;
            if (indicator) indicator.classList.add('hidden');
        }
    }

    function formatLarge(v) {
        if (v == null) return 'N/A';
        if (v >= 10000000000000) return (v / 10000000000000).toFixed(2) + ' Lakh Cr';
        if (v >= 10000000) return (v / 10000000).toFixed(2) + ' Cr';
        if (v >= 100000) return (v / 100000).toFixed(2) + ' L';
        return Number(v).toLocaleString('en-IN');
    }

    function updatePrice(data) {
        var priceEl = document.getElementById('livePrice');
        var changeEl = document.getElementById('liveChange');
        var pctEl = document.getElementById('livePct');
        var updatedEl = document.getElementById('liveUpdated');

        if (priceEl) priceEl.textContent = formatPrice(data.current_price);
        if (pctEl) {
            var pct = data.change_percent >= 0 ? '+' + data.change_percent : data.change_percent;
            pctEl.textContent = pct;
        }
        if (updatedEl) updatedEl.textContent = 'Updated: ' + data.updated;
        if (changeEl) {
            var changeStr = (data.change >= 0 ? '+' : '') + formatPrice(data.change) + ' (' + (data.change_percent >= 0 ? '+' : '') + data.change_percent + '%)';
            changeEl.textContent = changeStr;
            changeEl.className = (data.change >= 0 ? 'text-green-400' : 'text-red-400') + ' mt-1';
        }
        if (data.change_percent > 0) {
            if (priceEl) priceEl.className = 'text-4xl font-bold text-green-400 transition-colors duration-500';
            setTimeout(function() { if (priceEl) priceEl.className = 'text-4xl font-bold text-white transition-colors duration-500'; }, 1500);
        } else if (data.change_percent < 0) {
            if (priceEl) priceEl.className = 'text-4xl font-bold text-red-400 transition-colors duration-500';
            setTimeout(function() { if (priceEl) priceEl.className = 'text-4xl font-bold text-white transition-colors duration-500'; }, 1500);
        }

        var openEl = document.getElementById('mktOpen');
        var highEl = document.getElementById('mktHigh');
        var lowEl = document.getElementById('mktLow');
        var volEl = document.getElementById('mktVol');
        var avgVolEl = document.getElementById('mktAvgVol');
        var bidAskEl = document.getElementById('mktBidAsk');

        if (openEl && data.open != null) openEl.textContent = formatPrice(data.open);
        if (highEl && data.day_high != null) highEl.textContent = formatPrice(data.day_high);
        if (lowEl && data.day_low != null) lowEl.textContent = formatPrice(data.day_low);
        if (volEl && data.volume != null) volEl.textContent = formatLarge(data.volume);
        if (avgVolEl && data.avg_volume != null) avgVolEl.textContent = formatLarge(data.avg_volume);
        if (bidAskEl && data.bid != null && data.ask != null) bidAskEl.textContent = formatPrice(data.bid) + ' / ' + formatPrice(data.ask);
    }

    function poll() {
        fetch('/api/tick/' + stockId)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                updateBadge(data.market);
                updatePrice(data);
            })
            .catch(function() {});
    }

    poll();
    setInterval(poll, 5000);
})();
</script>
