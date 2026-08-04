<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Public Prediction Queries</h1>
            <p class="text-gray-400 mt-1">Strategies shared by the community — browse and study their forecasts.</p>
        </div>
        <a href="/predictions" class="border border-gray-600 text-gray-300 hover:border-accent hover:text-white px-4 py-2 rounded-lg text-sm transition mt-4 md:mt-0">
            <i class="fas fa-arrow-left mr-1"></i>My Queries
        </a>
    </div>

    <?php if (empty($queries)): ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-12 text-center">
        <i class="fas fa-globe text-5xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-lg font-semibold mb-2">No public queries yet</h3>
        <p class="text-gray-400 mb-6">When someone makes a query public, it shows up here.</p>
        <?php if (is_logged_in()): ?>
        <a href="/predictions/create" class="inline-flex items-center bg-accent hover:bg-accent-2 text-on-accent font-semibold px-6 py-2.5 rounded-lg transition text-sm">
            <i class="fas fa-plus mr-1"></i>Create a query
        </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($queries as $query): ?>
        <?php $methodMeta = prediction_methods()[$query['method']] ?? null; ?>
        <?php $status = prediction_status_meta($query['status']); ?>
        <?php $avgConfidence = $query['avg_confidence'] ?? null; ?>
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
                        <span class="text-gray-300"><?= $avgConfidence !== null ? round((float) $avgConfidence) . '%' : '—' ?></span>
                    </div>
                </div>

                <div class="mt-3">
                    <?php if ($avgConfidence !== null): ?>
                    <div class="h-1.5 rounded-full bg-page overflow-hidden">
                        <div class="h-full rounded-full bg-accent" style="width: <?= min(100, max(2, round((float) $avgConfidence))) ?>%"></div>
                    </div>
                    <?php else: ?>
                    <div class="h-1.5 rounded-full bg-page overflow-hidden"></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="border-t border-gray-700 flex items-center justify-between px-4 py-3">
                <span class="text-xs text-gray-600">
                    <?php if (!empty($query['creator_name'])): ?>
                    <i class="fas fa-user mr-1"></i><?= esc($query['creator_name']) ?>
                    <?php else: ?>
                    <i class="fas fa-user mr-1"></i>Community
                    <?php endif; ?>
                    <span class="mx-1">·</span>
                    <?= esc(date('M j, Y', strtotime($query['created_at']))) ?>
                </span>
                <a href="/predictions/public/<?= (int) $query['id'] ?>" class="text-sm font-semibold text-accent hover:text-accent-2">
                    View <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
