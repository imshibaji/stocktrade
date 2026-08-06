            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white"><?= $stock ? 'Edit Stock' : 'Add Stock' ?></h1>
                    <p class="text-gray-400 mt-1"><?= $stock ? 'Edit the details below or refresh from Yahoo Finance' : 'Add a single stock from Yahoo Finance' ?></p>
                </div>
                <a href="/admin/stocks" class="px-4 py-2 bg-page border border-gray-600 text-gray-300 rounded-lg hover:text-white transition">Back to Stocks</a>
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

            <?php
            $exchangeOptions = ['GLOBAL', 'NSE', 'BSE'];
            $inputClass = 'w-full px-3 py-2.5 bg-page border border-gray-600 rounded-lg text-white text-sm focus:outline-hidden focus:border-accent transition';
            ?>

            <form method="post" action="<?= $stock ? '/admin/stocks/update/' . (int) $stock['id'] : '/admin/stocks/save' ?>">
                <?= csrf_field() ?>

                <?php if ($stock): ?>
                    <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden mb-6">
                        <div class="px-6 py-4 flex flex-wrap items-center gap-4">
                            <div class="w-14 h-12 rounded-xl bg-accent/15 border border-accent/30 flex items-center justify-center shrink-0">
                                <span class="text-accent font-bold text-xl font-mono"><?= esc(strtoupper(substr($stock['symbol'], 0, 1))) ?></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h2 class="text-xl font-bold text-white font-mono"><?= esc($stock['symbol']) ?></h2>
                                    <span class="px-2 py-0.5 rounded bg-page border border-gray-600 text-xs text-gray-300"><?= esc($stock['exchange'] ?? 'GLOBAL') ?></span>
                                </div>
                                <p class="text-gray-400 text-sm truncate mt-0.5"><?= esc($stock['name']) ?></p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-2xl font-bold text-white font-mono"><?= number_format((float) $stock['current_price'], 2) ?></p>
                                <p class="text-xs text-gray-500 mt-0.5">Current price</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-700 flex items-center gap-2">
                            <span class="w-1 h-4 bg-accent rounded-full"></span>
                            <h3 class="text-white font-bold text-lg">Stock Details</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-400 mb-1.5" for="symbol">Symbol</label>
                                <input type="text" id="symbol" name="symbol" required value="<?= esc($stock['symbol']) ?>"
                                       class="<?= $inputClass ?> font-mono">
                                <p class="text-xs text-gray-500 mt-2">Refresh from Yahoo fetches using this symbol and the selected exchange.</p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1.5" for="name">Company Name</label>
                                <input type="text" id="name" name="name" required value="<?= esc($stock['name']) ?>"
                                       class="<?= $inputClass ?>">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1.5" for="sector">Sector</label>
                                <input type="text" id="sector" name="sector" value="<?= esc($stock['sector'] ?? '') ?>"
                                       class="<?= $inputClass ?>">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1.5" for="exchange">Exchange</label>
                                <select id="exchange" name="exchange" class="<?= $inputClass ?>">
                                    <?php $currentExchange = $stock['exchange'] ?? 'GLOBAL'; ?>
                                    <?php foreach ($exchangeOptions as $opt): ?>
                                        <option value="<?= $opt ?>" <?= $currentExchange === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1.5" for="current_price">Current Price</label>
                                <input type="number" id="current_price" name="current_price" step="0.01" min="0.01" required value="<?= esc($stock['current_price']) ?>"
                                       class="<?= $inputClass ?>">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1.5" for="previous_close">Previous Close</label>
                                <input type="number" id="previous_close" name="previous_close" step="0.01" min="0" value="<?= esc($stock['previous_close']) ?>"
                                       class="<?= $inputClass ?>">
                            </div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-500 mb-4">Save Changes writes the values above. Refresh from Yahoo overwrites them with live data and rebuilds price history and predictions.</p>

                    <div class="flex items-center gap-3">
                        <button type="submit" name="action" value="save" class="px-6 py-2 bg-accent text-on-accent font-medium rounded-lg hover:bg-accent-2 transition">Save Changes</button>
                        <button type="submit" name="action" value="refresh"
                                class="px-6 py-2 bg-page border border-gray-600 text-gray-300 rounded-lg hover:text-white transition">Refresh from Yahoo</button>
                        <a href="/admin/stocks" class="px-4 py-2 bg-page border border-gray-600 text-gray-300 rounded-lg hover:text-white transition">Cancel</a>
                    </div>
                <?php else: ?>
                    <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-700 flex items-center gap-2">
                            <span class="w-1 h-4 bg-accent rounded-full"></span>
                            <h3 class="text-white font-bold text-lg">Stock Symbol</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1.5" for="symbol">Symbol</label>
                                    <input type="text" id="symbol" name="symbol" required autofocus placeholder="e.g. RELIANCE, TCS, INFY"
                                           class="<?= $inputClass ?> font-mono">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1.5" for="exchange">Exchange</label>
                                    <select id="exchange" name="exchange" class="<?= $inputClass ?>">
                                        <?php foreach ($exchangeOptions as $opt): ?>
                                            <option value="<?= $opt ?>" <?= ($opt === 'GLOBAL') ? 'selected' : '' ?>><?= $opt ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-4">Name, sector, price and other details are fetched from Yahoo Finance for the selected exchange.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-6 py-2 bg-accent text-on-accent font-medium rounded-lg hover:bg-accent-2 transition">Add Stock</button>
                        <a href="/admin/stocks" class="px-4 py-2 bg-page border border-gray-600 text-gray-300 rounded-lg hover:text-white transition">Cancel</a>
                    </div>
                <?php endif; ?>
            </form>
