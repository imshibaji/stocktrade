<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white">Prediction Queries</h1>
                <span class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-chart-line text-accent mr-1"></i><?= count($queries) ?>
                </span>
            </div>
            <p class="text-gray-400 mt-1">Define a screening query, pick a forecast method, and let the engine scan every stock.</p>
        </div>
        <div class="flex items-center space-x-2 mt-4 md:mt-0">
            <a href="/predictions/public" class="bg-surface hover:bg-page border border-gray-600 text-gray-300 hover:text-white px-4 py-2.5 rounded-lg transition text-sm font-semibold">
                <i class="fas fa-globe mr-1"></i>Public Queries
            </a>
            <?php if (is_logged_in()): ?>
            <a href="/predictions/create" class="bg-accent hover:bg-accent-2 text-on-accent font-semibold px-4 py-2.5 rounded-lg transition text-sm">
                <i class="fas fa-plus mr-1"></i>New Query
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!is_logged_in()): ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-12 text-center">
        <i class="fas fa-chart-line text-5xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-lg font-semibold mb-2">Sign in to manage your prediction queries</h3>
        <p class="text-gray-400 mb-6">Create saved queries, run forecasts across all your stocks, and track the outcomes.</p>
        <div class="flex justify-center space-x-3">
            <a href="/login" class="bg-accent hover:bg-accent-2 text-on-accent font-semibold px-6 py-2.5 rounded-lg transition text-sm">Log in</a>
            <a href="/register" class="bg-surface border border-gray-600 text-gray-300 hover:text-white px-6 py-2.5 rounded-lg transition text-sm">Get Started</a>
        </div>
    </div>
    <?php elseif (empty($queries)): ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-12 text-center">
        <i class="fas fa-flask text-5xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-lg font-semibold mb-2">No prediction queries yet</h3>
        <p class="text-gray-400 mb-6">Start with a simple idea — e.g. <span class="text-accent font-mono">rsi &lt; 30 AND close &gt; sma(50)</span> — and pick a method to forecast it.</p>
        <a href="/predictions/create" class="inline-flex items-center bg-accent hover:bg-accent-2 text-on-accent font-semibold px-6 py-2.5 rounded-lg transition text-sm">
            <i class="fas fa-plus mr-1"></i>Create your first query
        </a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($queries as $query): ?>
        <?php
        $methodMeta = prediction_methods()[$query['method']] ?? null;
        $status = prediction_status_meta($query['status']);
        $avgConfidence = $query['avg_confidence'] !== null ? round((float) $query['avg_confidence']) : null;
        $lastRun = $query['last_run_at'] ?? null;
        ?>
        <div class="bg-surface rounded-xl border border-gray-700 hover:border-accent transition flex flex-col">
            <div class="p-6 flex-1">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full border <?= prediction_method_chip($query['method']) ?>">
                        <?= esc(prediction_method_label($query['method'])) ?>
                    </span>
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border <?= $status['class'] ?>">
                        <?= $status['label'] ?>
                    </span>
                </div>

                <h3 class="text-white font-bold text-lg leading-snug"><?= esc($query['name']) ?></h3>

                <p class="text-gray-500 text-xs mt-2 mb-4"><?= esc($methodMeta['description'] ?? '') ?></p>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Horizon</span>
                        <span class="text-gray-300"><?= (int) $query['horizon_days'] ?> days</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Matches</span>
                        <span class="text-gray-300"><?= (int) ($query['results_count'] ?? 0) ?> stocks</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Confidence</span>
                        <span class="text-gray-300"><?= $avgConfidence !== null ? $avgConfidence . '%' : '—' ?></span>
                    </div>
                </div>

                <div class="mt-3">
                    <?php if ($avgConfidence !== null): ?>
                    <div class="h-1.5 rounded-full bg-page overflow-hidden">
                        <div class="h-full rounded-full bg-accent" style="width: <?= min(100, max(2, $avgConfidence)) ?>%"></div>
                    </div>
                    <?php else: ?>
                    <div class="h-1.5 rounded-full bg-page overflow-hidden">
                        <div class="h-full w-0"></div>
                    </div>
                    <?php endif; ?>
                </div>

                <p class="text-gray-600 text-xs mt-4">
                    <?php if ($lastRun): ?>
                    <i class="far fa-clock mr-1"></i>Last run <?= esc(date('M j, Y H:i', strtotime($lastRun))) ?>
                    <?php else: ?>
                    Never run yet
                    <?php endif; ?>
                    <?php if ($query['is_public']): ?>
                    <span class="ml-2 text-accent"><i class="fas fa-globe mr-1"></i>Public</span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="border-t border-gray-700 grid grid-cols-2">
                <a href="/predictions/<?= $query['id'] ?>" class="flex-1 text-center py-3 text-sm text-gray-400 hover:text-accent hover:bg-page transition border-r border-gray-700">
                    <i class="fas fa-eye mr-1"></i>View
                </a>
                <a href="/predictions/<?= $query['id'] ?>/results" class="flex-1 text-center py-3 text-sm text-gray-400 hover:text-accent hover:bg-page transition">
                    <i class="fas fa-table mr-1"></i>Results
                </a>
                <button onclick="runQuery(<?= $query['id'] ?>)" class="flex-1 text-center py-3 text-sm text-green-400 hover:text-green-300 hover:bg-page transition border-r border-gray-700 border-t border-gray-700">
                    <i class="fas fa-play mr-1"></i>Run
                </button>
                <a href="/predictions/<?= $query['id'] ?>/edit" class="flex-1 text-center py-3 text-sm text-gray-400 hover:text-accent hover:bg-page transition border-t border-gray-700">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
            </div>
            <div class="border-t border-gray-700 grid grid-cols-2">
                <button onclick="togglePublic(<?= $query['id'] ?>, this)" class="flex-1 text-center py-3 text-sm text-gray-400 hover:text-accent hover:bg-page transition border-r border-gray-700">
                    <i class="fas fa-globe mr-1"></i><span><?= $query['is_public'] ? 'Make Private' : 'Make Public' ?></span>
                </button>
                <button onclick="deleteQuery(<?= $query['id'] ?>)" class="flex-1 text-center py-3 text-sm text-red-400 hover:text-red-300 hover:bg-red-900/10 transition">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </div>
        </div>
        <?php endforeach; ?>
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

    window.togglePublic = function(id, btn) {
        fetch('/predictions/' + id + '/toggle-public', {
            method: 'POST'
        })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Could not update visibility'));
                }
            })
            .catch(function() { alert('Error updating visibility'); });
    };

    window.deleteQuery = function(id) {
        if (!confirm('Delete this prediction query? This cannot be undone.')) return;
        fetch('/predictions/' + id + '/delete', { method: 'POST' })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Could not delete query'));
                }
            })
            .catch(function() { alert('Error deleting query'); });
    };
})();
</script>
