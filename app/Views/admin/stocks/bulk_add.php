            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">Bulk Add Stocks</h1>
                    <p class="text-gray-400 mt-1">Add many stocks at once. One symbol per line — details are fetched from Yahoo Finance (GLOBAL by default).</p>
                </div>
                <a href="/admin/stocks" class="px-4 py-2 bg-page border border-gray-600 text-gray-300 rounded-lg hover:text-white transition">Back to Stocks</a>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-900/30 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-4">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/admin/stocks/bulk-add" id="bulkAddForm">
                <?= csrf_field() ?>
                <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-700">
                        <h3 class="text-white font-bold text-lg">Stock Symbols</h3>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm text-gray-400 mb-2" for="exchange">Exchange</label>
                        <select id="exchange" name="exchange"
                                class="w-full px-3 py-2 bg-page border border-gray-600 rounded-lg text-white text-sm focus:outline-hidden focus:border-accent mb-4">
                            <?php foreach (['GLOBAL', 'NSE', 'BSE', 'NYSE', 'NASDAQ', 'LSE'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($opt === 'GLOBAL') ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label class="block text-sm text-gray-400 mb-2" for="rows">Paste symbols below</label>
                        <textarea id="rows" name="rows" rows="12" spellcheck="false" required
                                  class="w-full px-3 py-2 bg-page border border-gray-600 rounded-lg text-white text-sm font-mono focus:outline-hidden focus:border-accent"
                                  placeholder="RELIANCE&#10;TCS&#10;INFY&#10;HDFCBANK"></textarea>
                        <p class="text-xs text-gray-500 mt-2">Existing symbols are skipped automatically. Symbols that cannot be fetched from Yahoo Finance are reported in the result message.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-2 bg-accent text-on-accent font-medium rounded-lg hover:bg-accent-2 transition">Bulk Add</button>
                    <a href="/admin/stocks" class="px-4 py-2 bg-page border border-gray-600 text-gray-300 rounded-lg hover:text-white transition">Cancel</a>
                </div>
            </form>

            <script>
            document.getElementById('bulkAddForm').addEventListener('submit', function(e) {
                var text = document.getElementById('rows').value.trim();
                if (text === '') {
                    e.preventDefault();
                    alert('Paste at least one stock symbol.');
                    return;
                }
                var symbols = [];
                text.split(/\r?\n/).forEach(function(line) {
                    line = line.trim();
                    if (line === '') return;
                    symbols.push(line);
                });
                if (symbols.length === 0) {
                    e.preventDefault();
                    alert('No symbols found.');
                    return;
                }
                var form = e.target;
                symbols.forEach(function(v) {
                    var i = document.createElement('input');
                    i.type = 'hidden';
                    i.name = 'symbols[]';
                    i.value = v;
                    form.appendChild(i);
                });
            });
            </script>
