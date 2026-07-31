<section class="max-w-2xl mx-auto py-8">
    <div class="bg-surface rounded-xl p-8 border border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-white">Edit Investment</h1>
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
                <span class="px-3 py-1 rounded <?= $investment['status'] === 'active' ? 'bg-green-900/30 text-green-400' : 'bg-gray-700 text-gray-400' ?> text-xs">
                    <?= ucfirst($investment['status']) ?>
                </span>
            </div>
        </div>

        <form action="/investments/<?= $investment['id'] ?>/edit" method="post">
            <?= csrf_field() ?>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">Shares</label>
                    <input type="number" name="shares" required min="1" step="1"
                        value="<?= (int) $investment['shares'] ?>"
                        class="w-full bg-page border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-accent focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">Buy Price (Rs)</label>
                    <input type="number" name="buy_price" required min="0.01" step="0.01"
                        value="<?= $investment['buy_price'] ?>"
                        class="w-full bg-page border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-accent focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 text-sm">Buy Date</label>
                    <input type="date" name="buy_date" required
                        value="<?= $investment['buy_date'] ?>"
                        class="w-full bg-page border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-accent focus:outline-none">
                </div>
            </div>
            <div class="flex space-x-4">
                <a href="/investments" class="flex-1 text-center border border-gray-600 text-gray-300 hover:border-accent py-3 rounded-lg transition">Cancel</a>
                <button type="submit" class="flex-1 bg-accent hover:bg-accent-2 text-on-accent font-bold py-3 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</section>
