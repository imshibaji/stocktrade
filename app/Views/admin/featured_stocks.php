            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">Featured Home Stocks</h1>
                    <p class="text-gray-400 mt-1">Choose which stocks appear in the home page stocks section. Position decides the display order (lower first).</p>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-900/30 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-4">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($selected)): ?>
            <div class="bg-surface rounded-xl border border-gray-700 p-5 mb-6">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Currently Featured</p>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($selected as $i => $sid): ?>
                    <span class="text-xs px-3 py-1.5 rounded-lg bg-accent/15 border border-accent/40 text-accent">
                        #<?= $i + 1 ?> &middot; <?= esc($stocksById[$sid]['symbol'] ?? ('Stock #' . $sid)) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <form method="post" action="/admin/featured-stocks/save">
                <?= csrf_field() ?>
                <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                        <h3 class="text-white font-bold text-lg">Select Stocks</h3>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-400" for="maxCount">Show up to</label>
                            <input type="number" id="maxCount" name="max_count" min="1" max="12" value="<?= $maxCount ?>"
                                   class="w-20 px-3 py-2 bg-page border border-gray-600 rounded-lg text-white text-sm focus:outline-none focus:border-accent">
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-400 border-b border-gray-700">
                                    <th class="px-6 py-3 w-10"></th>
                                    <th class="px-6 py-3 w-24 text-left">Position</th>
                                    <th class="px-6 py-3 text-left">Symbol</th>
                                    <th class="px-6 py-3 text-left">Name</th>
                                    <th class="px-6 py-3 text-left">Sector</th>
                                    <th class="px-6 py-3 text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stocks as $i => $stock): ?>
                                <?php $isSel = in_array((int) $stock['id'], $selected, true); ?>
                                <?php $selPos = array_search((int) $stock['id'], $selected, true); ?>
                                <tr class="border-b border-gray-700/50 hover:bg-page/40">
                                    <td class="px-6 py-3">
                                        <input type="checkbox" name="ids[]" value="<?= (int) $stock['id'] ?>"
                                               <?= $isSel ? 'checked' : '' ?>
                                               class="featured-check w-4 h-4 accent-[--accent]" data-id="<?= (int) $stock['id'] ?>">
                                    </td>
                                    <td class="px-6 py-3">
                                        <input type="number" name="positions[<?= (int) $stock['id'] ?>]"
                                               value="<?= $isSel ? $selPos + 1 : $i + 1 ?>"
                                               class="w-20 px-2 py-1.5 bg-page border border-gray-600 rounded-lg text-white text-sm focus:outline-none focus:border-accent <?= $isSel ? '' : 'opacity-50' ?>">
                                    </td>
                                    <td class="px-6 py-3 text-white font-mono"><?= esc($stock['symbol']) ?></td>
                                    <td class="px-6 py-3 text-gray-300"><?= esc($stock['name']) ?></td>
                                    <td class="px-6 py-3 text-gray-400"><?= esc($stock['sector'] ?? 'N/A') ?></td>
                                    <td class="px-6 py-3 text-right text-gray-300"><?= format_price($stock['current_price'], stock_currency($stock['exchange'] ?? null)) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-2 bg-accent text-on-accent font-medium rounded-lg hover:bg-accent-2 transition">Save Featured Stocks</button>
                    <?php if (!empty($selected)): ?>
                    <a href="/admin/featured-stocks/clear" class="px-4 py-2 bg-page border border-gray-600 text-gray-300 rounded-lg hover:text-white" data-confirm="Clear featured stocks and revert to automatic top-by-market-cap?">Clear Selection</a>
                    <?php endif; ?>
                </div>
            </form>

            <script>
            document.querySelectorAll('.featured-check').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    var posInput = document.querySelector('input[name="positions[' + cb.getAttribute('data-id') + ']"]');
                    if (posInput) posInput.classList.toggle('opacity-50', !cb.checked);
                });
            });
            </script>
