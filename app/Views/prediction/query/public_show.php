<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white"><?= esc($query['name']) ?></h1>
                <span class="text-[11px] font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full border <?= prediction_method_chip($query['method']) ?>">
                    <?= esc(prediction_method_label($query['method'])) ?>
                </span>
            </div>
            <p class="text-gray-400 mt-1">A public prediction query shared by the community.</p>
        </div>
        <a href="/predictions/public" class="border border-gray-600 text-gray-300 hover:border-accent hover:text-white px-4 py-2 rounded-lg text-sm transition mt-4 md:mt-0">
            <i class="fas fa-arrow-left mr-1"></i>Back to Public
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
        <div class="bg-surface rounded-xl border border-gray-700 p-4">
            <p class="text-gray-500 text-xs mb-1">Last run</p>
            <p class="text-white font-bold text-sm"><?= $query['last_run_at'] ? esc(date('M j, Y H:i', strtotime($query['last_run_at']))) : 'Never' ?></p>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-white mb-3">Forecast Results</h2>

    <?php if (empty($query['results'])): ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-12 text-center">
        <i class="fas fa-hourglass-half text-5xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-lg font-semibold mb-2">No public results yet</h3>
        <p class="text-gray-400">The query owner hasn't run this forecast, or the results are private.</p>
    </div>
    <?php else: ?>
    <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-page text-left text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3 font-semibold">#</th>
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
                    <?php foreach ($query['results'] as $i => $result): ?>
                    <?php $signal = prediction_signal_meta($result['signal']); ?>
                    <?php $change = (float) $result['predicted_change_pct']; ?>
                    <tr class="hover:bg-page/40 transition">
                        <td class="px-4 py-3 text-gray-600"><?= $i + 1 ?></td>
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
    </div>
    <?php endif; ?>
</section>
