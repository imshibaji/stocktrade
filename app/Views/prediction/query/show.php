<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white"><?= esc($query['name']) ?></h1>
                <?php $status = prediction_status_meta($query['status']); ?>
                <span class="text-xs px-3 py-1 rounded-full border <?= $status['class'] ?>">
                    <i class="fas fa-circle text-[8px] mr-1"></i><?= $status['label'] ?>
                </span>
            </div>
            <p class="text-gray-400 mt-1">Prediction query details and latest forecast results.</p>
        </div>
        <div class="flex items-center space-x-2 mt-4 md:mt-0">
            <?php if ($query['user_id'] == current_user_id()): ?>
            <button onclick="runQuery(<?= (int) $query['id'] ?>)" class="bg-green-900/40 hover:bg-green-800/50 text-green-300 border border-green-700 font-semibold px-4 py-2.5 rounded-lg transition text-sm">
                <i class="fas fa-play mr-1"></i>Run Now
            </button>
            <a href="/predictions/<?= (int) $query['id'] ?>/edit" class="bg-surface hover:bg-page border border-gray-600 text-gray-300 hover:text-white px-4 py-2.5 rounded-lg transition text-sm font-semibold">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
            <button onclick="deleteQuery(<?= (int) $query['id'] ?>)" class="bg-surface hover:bg-red-900/20 border border-red-500 text-red-400 px-4 py-2.5 rounded-lg transition text-sm font-semibold">
                <i class="fas fa-trash mr-1"></i>Delete
            </button>
            <?php else: ?>
            <a href="/predictions/public" class="border border-gray-600 text-gray-300 hover:border-accent hover:text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-arrow-left mr-1"></i>Back to Public
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface rounded-xl border border-gray-700 p-4">
            <p class="text-gray-500 text-xs mb-1">Method</p>
            <div class="flex items-center">
                <span class="text-xs font-semibold px-2 py-0.5 rounded border <?= prediction_method_chip($query['method']) ?>">
                    <?= esc(prediction_method_label($query['method'])) ?>
                </span>
            </div>
        </div>
        <div class="bg-surface rounded-xl border border-gray-700 p-4">
            <p class="text-gray-500 text-xs mb-1">Horizon</p>
            <p class="text-white font-bold"><?= (int) $query['horizon_days'] ?> days</p>
        </div>
        <div class="bg-surface rounded-xl border border-gray-700 p-4">
            <p class="text-gray-500 text-xs mb-1">Matched stocks</p>
            <p class="text-white font-bold"><?= (int) ($query['results_count'] ?? $query['total_results'] ?? 0) ?></p>
        </div>
        <div class="bg-surface rounded-xl border border-gray-700 p-4">
            <p class="text-gray-500 text-xs mb-1">Avg confidence</p>
            <p class="text-white font-bold">
                <?php if (isset($query['avg_confidence']) && $query['avg_confidence'] !== null): ?>
                <?= round((float) $query['avg_confidence']) ?>%
                <?php else: ?>
                —
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-white">Forecast Results</h2>
        <a href="/predictions/<?= (int) $query['id'] ?>/results" class="text-sm text-accent hover:text-accent-2">
            View all results <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <?php if (empty($query['results'])): ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-12 text-center">
        <i class="fas fa-hourglass-half text-5xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-lg font-semibold mb-2">No predictions yet</h3>
        <p class="text-gray-400 mb-6">Run this query to scan every stock and generate a forecast.</p>
        <?php if ($query['user_id'] == current_user_id()): ?>
        <button onclick="runQuery(<?= (int) $query['id'] ?>)" class="inline-flex items-center bg-accent hover:bg-accent-2 text-on-accent font-semibold px-6 py-2.5 rounded-lg transition text-sm">
            <i class="fas fa-play mr-1"></i>Run Query
        </button>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-page text-left text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3 font-semibold">Symbol</th>
                        <th class="px-4 py-3 font-semibold">Name</th>
                        <th class="px-4 py-3 font-semibold text-right">Predicted Price</th>
                        <th class="px-4 py-3 font-semibold text-right">Change</th>
                        <th class="px-4 py-3 font-semibold">Signal</th>
                        <th class="px-4 py-3 font-semibold">Confidence</th>
                        <th class="px-4 py-3 font-semibold">Forecast</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    <?php foreach (array_slice($query['results'], 0, 10) as $result): ?>
                    <?php $signal = prediction_signal_meta($result['signal']); ?>
                    <?php $change = (float) $result['predicted_change_pct']; ?>
                    <tr class="hover:bg-page/40 transition">
                        <td class="px-4 py-3">
                            <a href="/stocks/<?= (int) $result['stock_id'] ?>" class="text-white font-mono font-semibold hover:text-accent"><?= esc($result['stock_symbol']) ?></a>
                        </td>
                        <td class="px-4 py-3 text-gray-400"><?= esc($result['stock_name']) ?></td>
                        <td class="px-4 py-3 text-right text-white"><?= number_format((float) $result['predicted_price'], 2) ?></td>
                        <td class="px-4 py-3 text-right font-semibold <?= $change >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <?= $change >= 0 ? '+' : '' ?><?= $change ?>%
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full border <?= $signal['class'] ?>">
                                <i class="fas <?= $signal['icon'] ?> mr-1"></i><?= $signal['label'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-2">
                                <div class="h-1.5 w-20 rounded-full bg-page overflow-hidden">
                                    <div class="h-full rounded-full bg-accent" style="width: <?= min(100, max(2, (int) $result['confidence_score'])) ?>%"></div>
                                </div>
                                <span class="text-gray-400 text-xs"><?= (int) $result['confidence_score'] ?>%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500"><?= esc($result['forecast_date']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (count($query['results']) > 10): ?>
        <div class="border-t border-gray-700 p-3 text-center">
            <a href="/predictions/<?= (int) $query['id'] ?>/results" class="text-sm text-accent hover:text-accent-2">
                Show all <?= count($query['results']) ?> results <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>

<script>
(function() {
    window.runQuery = function(id) {
        if (!confirm('Run this prediction query now?')) return;
        fetch('/predictions/' + id + '/run', { method: 'POST' })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    window.location.href = '/predictions/' + id + '/results';
                } else {
                    alert('Error: ' + (data.message || 'Could not run query'));
                }
            })
            .catch(function() { alert('Error running query'); });
    };

    window.deleteQuery = function(id) {
        if (!confirm('Delete this prediction query? This cannot be undone.')) return;
        fetch('/predictions/' + id + '/delete', { method: 'POST' })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    window.location.href = '/predictions';
                } else {
                    alert('Error: ' + (data.message || 'Could not delete query'));
                }
            })
            .catch(function() { alert('Error deleting query'); });
    };
})();
</script>
