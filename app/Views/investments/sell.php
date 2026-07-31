<section class="max-w-2xl mx-auto py-8">
    <div class="bg-surface rounded-xl p-8 border border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-white">Sell Investment</h1>
                <span id="marketBadge" class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>
                </span>
            </div>
            <a href="/investments" class="text-gray-400 hover:text-accent text-sm">
                <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
        </div>

        <div class="bg-page rounded-xl p-6 mb-6 border border-gray-700">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-white font-bold text-lg"><?= esc($investment['symbol']) ?> <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-surface border border-gray-600 text-gray-400 align-middle"><?= esc(exchange_display($investment['exchange'] ?? null, $investment['exchange_display'] ?? null)) ?></span></h3>
                    <p class="text-gray-400 text-sm"><?= esc($investment['name']) ?></p>
                </div>
                <span class="px-3 py-1 rounded bg-green-900/30 text-green-400 text-xs">Active</span>
            </div>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-gray-400 text-xs mb-1">Buy Price</p>
                    <p class="text-white font-semibold"><?= format_price($investment['buy_price'], $investment['currency']) ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Held Shares</p>
                    <p class="text-white font-semibold"><?= (int) $investment['shares'] ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Invested</p>
                    <p class="text-white font-semibold"><?= format_price($investment['total_invested'], $investment['currency']) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-page rounded-xl p-6 mb-6 border border-gray-700">
                            <h3 class="text-white font-semibold mb-4">Current Status</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-400 text-xs mb-1">Current Price</p>
                    <p id="sellLivePrice" class="text-white font-semibold"><?= format_price($investment['current_price'], $investment['currency']) ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Current Value</p>
                    <p id="sellLiveValue" class="text-white font-semibold"><?= format_price($pl['current_value'], $investment['currency']) ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Gross P/L</p>
                    <p id="sellLivePL" class="font-semibold <?= $pl['gross_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                        <?= $pl['gross_profit'] >= 0 ? '+' : '' ?><?= format_price($pl['gross_profit'], $investment['currency']) ?>
                        (<span id="sellLivePct"><?= $pl['gross_profit_pct'] >= 0 ? '+' : '' ?><?= $pl['gross_profit_pct'] ?></span>%)
                    </p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Est. Tax</p>
                    <p id="sellLiveTax" class="text-yellow-400 font-semibold"><?= format_price($pl['total_tax'], $investment['currency']) ?></p>
                </div>
            </div>
        </div>

        <form action="/investments/<?= $investment['id'] ?>/sell" method="post">
            <?= csrf_field() ?>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">Sell Price (per share)</label>
                    <input type="number" name="sell_price" id="sellPriceInput" required min="0.01" step="0.01"
                        value="<?= $investment['current_price'] ?>"
                        class="w-full bg-page border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-accent focus:outline-none text-lg">
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">Quantity to Sell</label>
                    <input type="number" name="quantity" id="sellQtyInput" required min="1" max="<?= (int) $investment['shares'] ?>" step="1"
                        value="<?= (int) $investment['shares'] ?>"
                        class="w-full bg-page border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-accent focus:outline-none text-lg">
                    <p class="text-gray-500 text-xs mt-1">Max <?= (int) $investment['shares'] ?> shares. Enter a lower number to sell partially.</p>
                </div>
            </div>
            <div class="flex space-x-4">
                <a href="/investments" class="flex-1 text-center border border-gray-600 text-gray-300 hover:border-accent py-3 rounded-lg transition">Cancel</a>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition">
                    <i class="fas fa-check mr-2"></i>Confirm Sale
                </button>
            </div>
        </form>
    </div>
</section>

<script>
(function() {
    var totalShares = <?= (int) $investment['shares'] ?>;

    document.getElementById('sellQtyInput').addEventListener('input', function() {
        var val = parseInt(this.value) || 0;
        if (val > totalShares) this.value = totalShares;
    });
})();
</script>
