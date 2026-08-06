<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Community Screener Lists</h1>
            <p class="text-gray-400 mt-1">Stock lists shared by the community — click a list to view its stocks.</p>
        </div>
        <a href="/" class="border border-gray-600 text-gray-300 hover:border-accent hover:text-white px-4 py-2 rounded-lg text-sm transition mt-4 md:mt-0">
            <i class="fas fa-arrow-left mr-1"></i>Back to Home
        </a>
    </div>

    <?php if (isset($pager) && $pager->getTotal() > 0): ?>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <p class="text-sm text-gray-400">
            <?= $pager->getTotal() ?> list<?= $pager->getTotal() === 1 ? '' : 's' ?> shared by the community.
        </p>
        <form method="get" class="flex items-center gap-3" aria-label="Items per page">
            <label for="perPageSelect" class="text-sm text-gray-400">Show</label>
            <select name="per_page" id="perPageSelect"
                    class="px-3 py-2 bg-page border border-gray-600 rounded-lg text-white text-sm focus:outline-hidden focus:border-accent"
                    onchange="this.form.submit()">
                <?php foreach ([6, 12, 24, 48] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ((int) ($perPage ?? 6) === (int) $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <?= view('partials/public_lists', ['publicLists' => $lists]) ?>
    <?= view('partials/pagination', ['pager' => $pager, 'label' => 'lists']) ?>
    <?php else: ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-12 text-center">
        <i class="fas fa-globe text-5xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-lg font-semibold mb-2">No community lists yet</h3>
        <p class="text-gray-400 mb-6">When someone makes a screener list public, it shows up here.</p>
        <a href="/screener" class="inline-flex items-center bg-accent hover:bg-accent-2 text-on-accent font-semibold px-6 py-2.5 rounded-lg transition text-sm">
            <i class="fas fa-sliders mr-1"></i>Open the Screener
        </a>
    </div>
    <?php endif; ?>
</section>
