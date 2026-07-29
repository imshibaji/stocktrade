<?php
$watchlistModel = new \App\Models\WatchlistModel();
$isWatched = is_logged_in() ? $watchlistModel->isWatched(current_user_id(), (int) $stock['id']) : false;
$lastPrice = !empty($priceData) ? (float) $priceData[count($priceData) - 1] : 0;
?>
<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white"><?= esc($stock['symbol']) ?> <span class="text-gold">Predictions</span></h1>
                <span id="marketBadge" class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>
                </span>
            </div>
            <p class="text-gray-400 mt-1">30-Day forecast using Monte Carlo & EMA analysis</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="/stocks/<?= $stock['id'] ?>" class="border border-gray-600 text-gray-300 hover:border-gold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to Stock
            </a>
            <?php if (is_logged_in()): ?>
                <?php if ($isWatched): ?>
                <a href="/watchlist/remove/<?= $stock['id'] ?>" class="border border-red-500 text-red-400 hover:bg-red-900/20 px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-star mr-1"></i> Unwatch
                </a>
                <?php else: ?>
                <a href="/watchlist/add/<?= $stock['id'] ?>" class="border border-gold text-gold hover:bg-gold/10 px-4 py-2 rounded-lg text-sm transition">
                    <i class="far fa-star mr-1"></i> Watch
                </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-navy2 rounded-xl border border-gray-700 p-6">
            <h3 class="text-white font-semibold mb-4">Price Prediction Chart</h3>
            <div class="h-72">
                <canvas id="predictionChart"></canvas>
            </div>
            <div class="flex justify-center space-x-6 mt-4 text-sm">
                <div class="flex items-center"><div class="w-3 h-3 bg-blue-400 rounded mr-2"></div> Historical</div>
                <div class="flex items-center"><div class="w-3 h-3 bg-gold rounded mr-2"></div> Predicted</div>
            </div>
        </div>
        <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
            <h3 class="text-white font-semibold mb-4">Prediction Summary</h3>
            <?php 
            $currentPrice = (float) $stock['current_price'];
            $predPrices = array_map('floatval', $predictionPrices);
            $maxPred = !empty($predPrices) ? max($predPrices) : $currentPrice;
            $minPred = !empty($predPrices) ? min($predPrices) : $currentPrice;
            $avgPred = !empty($predPrices) ? array_sum($predPrices) / count($predPrices) : $currentPrice;
            $trend = $avgPred > $currentPrice ? 'Bullish' : 'Bearish';
            ?>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Current Price</span>
                    <span id="predCurrentPrice" class="text-white"><?= format_price($currentPrice) ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Predicted High</span>
                    <span class="text-green-400"><?= format_price($maxPred) ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Predicted Low</span>
                    <span class="text-red-400"><?= format_price($minPred) ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Average Prediction</span>
                    <span class="text-white"><?= format_price($avgPred) ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Max Upside</span>
                    <span class="text-green-400">+<?= round((($maxPred - $currentPrice) / $currentPrice) * 100, 2) ?>%</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Max Downside</span>
                    <span class="text-red-400"><?= round((($minPred - $currentPrice) / $currentPrice) * 100, 2) ?>%</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-400 text-sm">30-Day Outlook</span>
                    <span class="<?= $trend === 'Bullish' ? 'text-green-400' : 'text-red-400' ?> font-semibold">
                        <i class="fas fa-<?= $trend === 'Bullish' ? 'arrow-up' : 'arrow-down' ?> mr-1"></i><?= $trend ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-navy2 rounded-xl border border-gray-700 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-white font-bold text-lg">Daily Predictions (Next 30 Days)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="text-left px-6 py-3">Date</th>
                        <th class="text-right px-6 py-3">Predicted Price</th>
                        <th class="text-right px-6 py-3">Change</th>
                        <th class="text-right px-6 py-3">Change %</th>
                        <th class="text-right px-6 py-3">Confidence</th>
                        <th class="text-right px-6 py-3">Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stock['predictions'] as $p): 
                        $change = (float) $p['predicted_price'] - $currentPrice;
                        $changePct = $currentPrice > 0 ? ($change / $currentPrice) * 100 : 0;
                    ?>
                    <tr class="border-b border-gray-700/50 hover:bg-navy/50">
                        <td class="px-6 py-3 text-gray-300"><?= date('M d, Y', strtotime($p['predicted_date'])) ?></td>
                        <td class="px-6 py-3 text-right text-white font-medium"><?= format_price($p['predicted_price']) ?></td>
                        <td class="px-6 py-3 text-right <?= $change >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <?= $change >= 0 ? '+' : '' ?><?= format_price($change) ?>
                        </td>
                        <td class="px-6 py-3 text-right <?= $changePct >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <?= $changePct >= 0 ? '+' : '' ?><?= round($changePct, 2) ?>%
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <div class="w-20 h-2 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full <?= $p['confidence_score'] >= 80 ? 'bg-green-500' : ($p['confidence_score'] >= 65 ? 'bg-yellow-500' : 'bg-red-500') ?>"
                                         style="width: <?= $p['confidence_score'] ?>%"></div>
                                </div>
                                <span class="text-gray-300 w-12 text-left"><?= $p['confidence_score'] ?>%</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-right text-gray-500"><?= esc($p['method']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-navy2 rounded-xl border border-yellow-700/50 p-6">
        <div class="flex items-start space-x-3">
            <i class="fas fa-exclamation-triangle text-yellow-400 text-xl mt-1"></i>
            <div>
                <h4 class="text-yellow-400 font-semibold mb-2">Disclaimer</h4>
                <p class="text-gray-400 text-sm">
                    Predictions are generated using a combination of Monte Carlo simulation and Exponential Moving Average (EMA) analysis based on historical price data. 
                    These predictions are for informational purposes only and should not be considered as financial advice. 
                    Stock market investments are subject to market risks. Please consult with a qualified financial advisor before making investment decisions.
                </p>
            </div>
        </div>
    </div>
</section>

<script>
(function() {
    function formatPrice(v) { return '\u20B9' + parseFloat(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

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

    function poll() {
        fetch('/api/tick/<?= $stock['id'] ?>')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                updateBadge(data.market);
                document.getElementById('predCurrentPrice').textContent = formatPrice(data.current_price);
            })
            .catch(function() {});
    }

    poll();
    setInterval(poll, 5000);
})();

var ctx = document.getElementById('predictionChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_merge(
            array_map(function($i) { return 'D-' . (90 - $i); }, range(0, 89)),
            $predictionDates
        )) ?>,
        datasets: [{
            label: 'Historical',
            data: <?= json_encode($priceData) ?>,
            borderColor: '#60a5fa',
            backgroundColor: 'transparent',
            tension: 0.3,
            pointRadius: 0,
            borderWidth: 1.5,
        }, {
            label: 'Predicted',
            data: <?= json_encode(array_merge([$lastPrice], $predictionPrices)) ?>,
            borderColor: '#d4a853',
            borderDash: [5, 5],
            backgroundColor: 'transparent',
            tension: 0.3,
            pointRadius: 0,
            borderWidth: 2,
        }, {
            label: 'Confidence Upper',
            data: <?php 
                $upperBound = [];
                foreach ($predictionPrices as $p) {
                    $upperBound[] = round((float) $p * 1.05, 2);
                }
                echo json_encode(array_merge([$lastPrice], $upperBound));
            ?>,
            borderColor: 'rgba(212, 168, 83, 0.2)',
            backgroundColor: 'rgba(212, 168, 83, 0.05)',
            fill: 2,
            tension: 0.3,
            pointRadius: 0,
            borderWidth: 0.5,
        }, {
            label: 'Confidence Lower',
            data: <?php 
                $lowerBound = [];
                foreach ($predictionPrices as $p) {
                    $lowerBound[] = round((float) $p * 0.95, 2);
                }
                echo json_encode(array_merge([$lastPrice], $lowerBound));
            ?>,
            borderColor: 'rgba(212, 168, 83, 0.2)',
            backgroundColor: 'rgba(212, 168, 83, 0.05)',
            tension: 0.3,
            pointRadius: 0,
            borderWidth: 0.5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: {
                ticks: {
                    callback: function(value, index) {
                        var labels = <?= json_encode(array_merge(
                            array_map(function($i) { return 'D-' . (90 - $i); }, range(0, 89)),
                            $predictionDates
                        )) ?>;
                        if (index % 15 === 0) return labels[index];
                        return '';
                    },
                    color: '#888',
                    maxTicksLimit: 10,
                },
                grid: { color: 'rgba(75, 85, 99, 0.3)' }
            },
            y: {
                ticks: { color: '#888', callback: function(v) { return '\u20B9' + v; } },
                grid: { color: 'rgba(75, 85, 99, 0.3)' }
            }
        }
    }
});
</script>
