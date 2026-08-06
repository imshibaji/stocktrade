<?php
$logicText = '';
if (!empty($isManualQuery) && $queryText !== '') {
    $logicText = $queryText;
} else {
    $parts = [];
    foreach ((array) $filters as $f) {
        if (!is_array($f)) continue;
        $val = !empty($f['is_string']) ? '"' . $f['value'] . '"' : ($f['value'] ?? '');
        $parts[] = ($f['field'] ?? '') . ' ' . ($f['op'] ?? '') . ' ' . $val;
    }
    foreach ((array) $techFilters as $f) {
        if (!is_array($f)) continue;
        $fld = !empty($f['indicator']) ? $f['indicator'] . (!empty($f['period']) ? '(' . $f['period'] . ')' : '') : ($f['field'] ?? '');
        $val = !empty($f['is_string']) ? '"' . $f['value'] . '"' : ($f['value'] ?? '');
        $parts[] = $fld . ' ' . ($f['op'] ?? '') . ' ' . $val;
    }
    $logicText = implode(' AND ', $parts);
}
$matchLabel = ($matchMode === 'any') ? 'Any condition matches' : 'All conditions must match';
$total = $pager->getTotal();
?>
<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white"><?= esc($list['name']) ?></h1>
                <span class="text-xs px-2.5 py-1 rounded-full border bg-page border-gray-600 text-gray-300"><i class="fas fa-globe mr-1"></i>Public</span>
            </div>
            <p class="text-gray-400 mt-1">
                <i class="fas fa-user mr-1"></i><?= esc($ownerName) ?>
                <span class="mx-1">&middot;</span>
                <?= esc(date('M j, Y', strtotime($list['created_at']))) ?>
                <span class="mx-1">&middot;</span>
                <?= (int) $list['stock_count'] ?> stock<?= (int) $list['stock_count'] === 1 ? '' : 's' ?>
            </p>
        </div>
        <div class="flex items-center space-x-2 mt-4 md:mt-0">
            <a href="/lists" class="border border-gray-600 text-gray-300 hover:border-accent hover:text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-arrow-left mr-1"></i>All Lists
            </a>
            <a href="/screener" class="bg-surface hover:bg-page border border-gray-600 text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-sliders mr-1"></i>Open Screener
            </a>
        </div>
    </div>

    <?php if ($logicText !== ''): ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-5 mb-6">
        <div class="flex items-start justify-between gap-4 mb-2">
            <p class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Screener query logic</p>
            <span class="text-[10px] px-2 py-0.5 rounded-full border bg-purple-900/30 text-purple-300 border-purple-700 whitespace-nowrap"><i class="fas fa-list-check mr-1"></i><?= esc($matchLabel) ?></span>
        </div>
        <code class="block w-full bg-page border border-gray-700 rounded-lg px-4 py-3 text-sm font-mono text-white whitespace-pre-wrap"><?= esc($logicText) ?></code>
    </div>
    <?php endif; ?>

    <?php if (empty($stocks)): ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-12 text-center">
        <i class="fas fa-box-open text-5xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-lg font-semibold mb-2">This list has no stocks</h3>
        <p class="text-gray-400">The owner hasn't populated this list yet.</p>
    </div>
    <?php else: ?>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <p class="text-sm text-gray-400">
            <?= $total ?> stock<?= $total === 1 ? '' : 's' ?> in this list<?= $pager->getPageCount() > 1 ? ' (page ' . $pager->getCurrentPage() . ')' : '' ?>.
        </p>
        <form method="get" class="flex items-center gap-3" aria-label="Items per page">
            <label for="perPageSelect" class="text-sm text-gray-400">Show</label>
            <select name="per_page" id="perPageSelect"
                    class="px-3 py-2 bg-page border border-gray-600 rounded-lg text-white text-sm focus:outline-hidden focus:border-accent"
                    onchange="this.form.submit()">
                <?php foreach ([10, 25, 50, 100] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ((int) ($perPage ?? 10) === (int) $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-page text-left text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3 font-semibold">#</th>
                        <th class="px-4 py-3 font-semibold">Symbol</th>
                        <th class="px-4 py-3 font-semibold">Name</th>
                        <th class="px-4 py-3 font-semibold">Sector</th>
                        <th class="px-4 py-3 font-semibold text-right">Price</th>
                        <th class="px-4 py-3 font-semibold text-right">Change</th>
                        <th class="px-4 py-3 font-semibold">30-Day Outlook</th>
                        <th class="px-4 py-3 font-semibold text-right">Target</th>
                        <th class="px-4 py-3 font-semibold">Confidence</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    <?php
                    $start = ($pager->getCurrentPage() - 1) * $pager->getPerPage();
                    ?>
                    <?php foreach ($stocks as $i => $stock): ?>
                    <?php $change = get_price_change((float) $stock['current_price'], (float) $stock['previous_close']); ?>
                    <?php $cur = stock_currency($stock['exchange'] ?? null); ?>
                    <?php $pred = $predictions[(int) $stock['id']] ?? null; ?>
                    <?php $bullish = $pred !== null && $pred['avg'] >= (float) $stock['current_price']; ?>
                    <tr class="hover:bg-page/40 transition">
                        <td class="px-4 py-3 text-gray-600"><?= $start + $i + 1 ?></td>
                        <td class="px-4 py-3">
                            <a href="/stocks/<?= (int) $stock['id'] ?>" class="text-white font-mono font-semibold hover:text-accent">
                                <?= esc($stock['symbol']) ?>
                                <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-page border border-gray-600 text-gray-400 align-middle"><?= esc(exchange_display($stock['exchange'] ?? null, $stock['exchange_display'] ?? null)) ?></span>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-400"><?= esc($stock['name']) ?></td>
                        <td class="px-4 py-3 text-gray-400"><?= esc($stock['sector']) ?></td>
                        <td class="px-4 py-3 text-right text-white"><?= format_price($stock['current_price'], $cur) ?></td>
                        <td class="px-4 py-3 text-right font-semibold <?= $change['change'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <?= $change['change'] >= 0 ? '+' : '' ?><?= $change['percent'] ?>%
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($pred !== null): ?>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full border <?= $bullish ? 'bg-green-900/30 text-green-400 border-green-700' : 'bg-red-900/30 text-red-400 border-red-700' ?>">
                                <i class="fas fa-<?= $bullish ? 'arrow-trend-up' : 'arrow-trend-down' ?> mr-1"></i><?= $bullish ? 'Bullish' : 'Bearish' ?>
                            </span>
                            <?php else: ?>
                            <span class="text-gray-600 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <?php if ($pred !== null): ?>
                            <span class="text-white font-semibold"><?= format_price($pred['avg'], $cur) ?></span>
                            <span class="text-gray-500 text-xs block"><?= format_price($pred['low'], $cur) ?>–<?= format_price($pred['high'], $cur) ?></span>
                            <?php else: ?>
                            <span class="text-gray-600 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($pred !== null): ?>
                            <div class="flex items-center space-x-2">
                                <div class="h-1.5 w-20 rounded-full bg-page overflow-hidden">
                                    <div class="h-full rounded-full bg-accent" style="width: <?= min(100, max(2, (int) $pred['conf'])) ?>%"></div>
                                </div>
                                <span class="text-gray-400 text-xs"><?= (int) $pred['conf'] ?>%</span>
                            </div>
                            <?php else: ?>
                            <span class="text-gray-600 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?= view('partials/pagination', ['pager' => $pager, 'label' => 'stocks']) ?>
    <?php endif; ?>
</section>
