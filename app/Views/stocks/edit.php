<section class="max-w-2xl mx-auto py-4">
    <div class="flex items-center space-x-3 mb-8">
        <a href="/stocks/<?= $stock['id'] ?>" class="text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-white">Edit <?= esc($stock['symbol']) ?></h1>
    </div>

    <div class="bg-navy2 rounded-xl border border-gray-700 p-8">
        <form action="/stocks/<?= $stock['id'] ?>/edit" method="post">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Symbol</label>
                    <input type="text" value="<?= esc($stock['symbol']) ?>" disabled
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-gray-400">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Name</label>
                    <input type="text" name="name" value="<?= old('name', $stock['name']) ?>" required
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Sector</label>
                    <input type="text" name="sector" value="<?= old('sector', $stock['sector']) ?>" required
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Current Price</label>
                    <input type="number" name="current_price" step="0.01" value="<?= old('current_price', $stock['current_price']) ?>" required
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Previous Close</label>
                    <input type="number" name="previous_close" step="0.01" value="<?= old('previous_close', $stock['previous_close']) ?>"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Market Cap</label>
                    <input type="number" name="market_cap" step="0.01" value="<?= old('market_cap', $stock['market_cap']) ?>"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Avg Volume</label>
                    <input type="number" name="avg_volume" step="1" value="<?= old('avg_volume', $stock['avg_volume']) ?>"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">P/E Ratio</label>
                    <input type="number" name="pe_ratio" step="0.01" value="<?= old('pe_ratio', $stock['pe_ratio']) ?>"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">52 Week High</label>
                    <input type="number" name="week_52_high" step="0.01" value="<?= old('week_52_high', $stock['week_52_high']) ?>"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">52 Week Low</label>
                    <input type="number" name="week_52_low" step="0.01" value="<?= old('week_52_low', $stock['week_52_low']) ?>"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Dividend Yield</label>
                    <input type="number" name="dividend_yield" step="0.0001" value="<?= old('dividend_yield', $stock['dividend_yield']) ?>"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Beta</label>
                    <input type="number" name="beta" step="0.01" value="<?= old('beta', $stock['beta']) ?>"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <a href="/stocks/<?= $stock['id'] ?>" class="px-6 py-3 rounded-lg bg-navy border border-gray-600 text-gray-300 hover:text-white transition text-sm">
                    Cancel
                </a>
                <button type="button" id="autoFillBtn" class="px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition text-sm">
                    <i class="fas fa-cloud-download-alt mr-2"></i>Auto-fill from Yahoo
                </button>
                <button type="submit" class="px-6 py-3 rounded-lg bg-gold hover:bg-gold2 text-navy font-semibold transition text-sm">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</section>

<script>
document.getElementById('autoFillBtn').addEventListener('click', function() {
    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Fetching...';
    fetch('/api/quote/<?= $stock['symbol'] ?>')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            var fields = {
                'name': data.name,
                'sector': data.sector,
                'current_price': data.current_price,
                'previous_close': data.previous_close,
                'market_cap': data.market_cap,
                'avg_volume': data.avg_volume,
                'pe_ratio': data.pe_ratio,
                'week_52_high': data.week_52_high,
                'week_52_low': data.week_52_low,
                'dividend_yield': data.dividend_yield,
                'beta': data.beta
            };
            for (var name in fields) {
                var input = document.querySelector('input[name="' + name + '"]');
                if (input && fields[name] !== null && fields[name] !== undefined) {
                    input.value = fields[name];
                    input.classList.add('border-yellow-500');
                }
            }
        })
        .catch(function(err) {
            alert('Failed to fetch data: ' + err.message);
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-cloud-download-alt mr-2"></i>Auto-fill from Yahoo';
        });
});
</script>