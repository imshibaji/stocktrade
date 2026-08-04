<div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">Stocks</h1>
                    <p class="text-gray-400 mt-1">Manage all stocks in the system</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="/admin/stocks/create" class="px-4 py-2 bg-accent text-on-accent font-medium rounded-lg hover:bg-accent-2 transition">Add Stock</a>
                    <a href="/admin/stocks/bulk" class="px-4 py-2 bg-page border border-gray-600 text-gray-300 rounded-lg hover:text-white transition">Bulk Add</a>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-900/30 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-4">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-900/30 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-4">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form method="get" class="mb-4 flex flex-wrap items-center gap-3" id="searchForm">
                <div class="relative flex-1 min-w-[200px]">
                    <input type="text"
                           name="search"
                           value="<?= esc($search ?? '') ?>"
                           placeholder="Search symbol, name, exchange, sector..."
                           class="w-full px-3 py-2 bg-page border border-gray-600 rounded-lg text-white text-sm focus:outline-hidden focus:border-accent pl-10">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <select name="exchange"
                        class="px-3 py-2 bg-page border border-gray-600 rounded-lg text-white text-sm focus:outline-hidden focus:border-accent">
                    <option value="">All Exchanges</option>
                    <?php foreach (($exchangeOptions ?? []) as $opt): ?>
                        <option value="<?= esc($opt['exchange']) ?>" <?= (($exchange ?? '') === $opt['exchange']) ? 'selected' : '' ?>><?= esc($opt['exchange']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="sector"
                        class="px-3 py-2 bg-page border border-gray-600 rounded-lg text-white text-sm focus:outline-hidden focus:border-accent">
                    <option value="">All Sectors</option>
                    <?php foreach (($sectorOptions ?? []) as $opt): ?>
                        <option value="<?= esc($opt['sector']) ?>" <?= (($sector ?? '') === $opt['sector']) ? 'selected' : '' ?>><?= esc($opt['sector'] !== '' ? $opt['sector'] : 'N/A') ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="per_page"
                        class="px-3 py-2 bg-page border border-gray-600 rounded-lg text-white text-sm focus:outline-hidden focus:border-accent">
                    <?php foreach ([10, 25, 50, 100] as $opt): ?>
                        <option value="<?= $opt ?>" <?= (($perPage ?? 25) === $opt) ? 'selected' : '' ?>><?= $opt ?> per page</option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-4 py-2 bg-page border border-gray-600 text-gray-300 rounded-lg text-sm hover:text-white transition">Filter</button>
            </form>

            <div class="mb-3 text-sm text-gray-400">
                <?php if (isset($pager)): ?>
                    <?= $pager->getTotal() ?> stock<?= $pager->getTotal() === 1 ? '' : 's' ?> match<?= $pager->getTotal() === 1 ? 'es' : '' ?> the current filters.
                <?php endif; ?>
                <?php if (($exchange ?? '') !== '' || ($sector ?? '') !== ''): ?>
                    <a href="/admin/stocks" class="text-accent hover:underline ml-1">Clear filters</a>
                <?php endif; ?>
            </div>

            <form method="post" id="bulkForm">
                <?= csrf_field() ?>
                <input type="hidden" name="sort" value="<?= esc($sort ?? 'symbol') ?>">
                <input type="hidden" name="dir" value="<?= esc($dir ?? 'asc') ?>">
                <input type="hidden" name="per_page" value="<?= esc($perPage ?? 25) ?>">
                <input type="hidden" name="search" value="<?= esc($search ?? '') ?>">
                <input type="hidden" name="exchange" value="<?= esc($exchange ?? '') ?>">
                <input type="hidden" name="sector" value="<?= esc($sector ?? '') ?>">
                <div class="bg-surface rounded-xl border border-gray-700 p-4 mb-4 flex flex-wrap items-center gap-3">
                    <span class="text-sm text-gray-400">Select stocks to</span>
                    <div class="flex items-center gap-2">
                        <button type="submit" formaction="/admin/stocks/bulk-edit" data-bulk-confirm="Refresh selected stocks from Yahoo Finance? Price history and predictions will be rebuilt."
                                class="px-4 py-2 bg-accent text-on-accent text-sm font-medium rounded-lg hover:bg-accent-2 transition">Refresh from Yahoo</button>
                        <button type="submit" formaction="/admin/stocks/bulk-delete" data-bulk-confirm="Delete selected stocks? This cannot be undone."
                                class="px-4 py-2 bg-red-900/30 text-red-400 text-sm rounded-lg hover:bg-red-900/50 transition">Delete Selected</button>
                    </div>
                </div>

                <div class="bg-surface rounded-xl border border-gray-700 overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-page border-b border-gray-700">
                                <th class="px-4 py-3 w-10">
                                    <input type="checkbox" id="selectAll" class="row-check w-4 h-4 accent-accent">
                                </th>
                                <?php
                                $sortable = ['id', 'symbol', 'name', 'exchange', 'sector'];
                                foreach ($sortable as $col):
                                    $currentSort = $sort ?? 'symbol';
                                    $currentDir  = $dir ?? 'asc';
                                    $isActive    = $currentSort === $col;
                                    $newDir      = $isActive && $currentDir === 'asc' ? 'desc' : 'asc';
                                    $icon        = $isActive ? ($currentDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
                                    $query       = http_build_query(['sort' => $col, 'dir' => $newDir, 'per_page' => $perPage, 'search' => $search ?? '', 'exchange' => $exchange ?? '', 'sector' => $sector ?? '']);
                                    $url         = current_url() . '?' . $query;
                                ?>
                                <th class="text-left px-4 py-3 text-gray-300 font-medium cursor-pointer hover:text-white select-none">
                                    <a href="<?= $url ?>" class="flex items-center gap-1">
                                        <?= ucfirst(str_replace('_', ' ', $col)) ?>
                                        <i class="fas <?= $icon ?> text-xs text-gray-500"></i>
                                    </a>
                                </th>
                                <?php endforeach; ?>
                                <th class="text-left px-4 py-3 text-gray-300 font-medium">Current Price</th>
                                <th class="text-right px-4 py-3 text-gray-300 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stocks)): ?>
                                <?php foreach ($stocks as $stock): ?>
                                    <tr class="border-b border-gray-700/50">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="ids[]" value="<?= (int) $stock['id'] ?>" class="row-check w-4 h-4 accent-accent">
                                        </td>
                                        <td class="px-4 py-3 text-gray-400"><?= $stock['id'] ?></td>
                                        <td class="px-4 py-3 text-white font-mono"><?= esc($stock['symbol']) ?></td>
                                        <td class="px-4 py-3 text-gray-300"><?= esc($stock['name']) ?></td>
                                        <td class="px-4 py-3 text-gray-400"><?= esc($stock['exchange']) ?></td>
                                        <td class="px-4 py-3 text-gray-400"><?= esc($stock['sector'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-3 text-right text-white font-mono"><?= number_format((float) $stock['current_price'], 2) ?></td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <a href="/stocks/<?= $stock['id'] ?>" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white">View</a>
                                            <a href="/admin/stocks/edit/<?= $stock['id'] ?>" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white">Edit</a>
                                            <a href="/admin/stocks/delete/<?= $stock['id'] ?>" class="px-2 py-1 bg-red-900/30 text-red-400 text-xs rounded hover:bg-red-900/50" data-confirm="Delete stock <?= esc($stock['symbol']) ?>?">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">No stocks found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="text-sm text-gray-400">
                            <?php
                                $page     = $pager->getCurrentPage();
                                $perPage  = $pager->getPerPage();
                                $total    = $pager->getTotal();
                                $first    = ($page - 1) * $perPage + 1;
                                $last     = min($page * $perPage, $total);
                            ?>
                            Showing <?= $first ?>–<?= $last ?> of <?= $total ?> stocks
                        </div>
                        <nav class="flex items-center gap-1" aria-label="Pagination">
                            <?= $pager->links('default', 'default_full') ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </form>

            <script>
            document.getElementById('selectAll').addEventListener('change', function() {
                var checked = this.checked;
                document.querySelectorAll('.row-check').forEach(function(cb) {
                    cb.checked = checked;
                });
            });

            document.getElementById('bulkForm').addEventListener('submit', function(e) {
                var anyChecked = document.querySelectorAll('input[name="ids[]"]:checked').length > 0;
                if (!anyChecked) {
                    e.preventDefault();
                    alert('Select at least one stock.');
                    return;
                }
                var confirmMsg = e.submitter && e.submitter.getAttribute('data-bulk-confirm');
                if (confirmMsg && !confirm(confirmMsg)) {
                    e.preventDefault();
                }
            });

            document.getElementById('searchForm').addEventListener('submit', function(e) {
                var bulkForm = document.getElementById('bulkForm');
                var searchVal = this.querySelector('input[name="search"]').value;
                var perPageVal = this.querySelector('select[name="per_page"]').value;
                var exchangeVal = this.querySelector('select[name="exchange"]').value;
                var sectorVal = this.querySelector('select[name="sector"]').value;
                bulkForm.querySelector('input[name="search"]').value = searchVal;
                bulkForm.querySelector('input[name="per_page"]').value = perPageVal;
                bulkForm.querySelector('input[name="exchange"]').value = exchangeVal;
                bulkForm.querySelector('input[name="sector"]').value = sectorVal;
            });

            document.getElementById('searchForm').addEventListener('reset', function() {
                var bulkForm = document.getElementById('bulkForm');
                bulkForm.querySelector('input[name="search"]').value = '';
                bulkForm.querySelector('input[name="per_page"]').value = '25';
                bulkForm.querySelector('input[name="exchange"]').value = '';
                bulkForm.querySelector('input[name="sector"]').value = '';
            });

            document.querySelectorAll('select[name="per_page"]').forEach(function(sel) {
                sel.addEventListener('change', function() {
                    document.getElementById('searchForm').submit();
                });
            });
            </script>