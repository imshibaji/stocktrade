<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Create Prediction Query</h1>
            <p class="text-gray-400 mt-1">Name a strategy, choose how it screens, and pick the forecast method.</p>
        </div>
        <a href="/predictions" class="border border-gray-600 text-gray-300 hover:border-accent hover:text-white px-4 py-2 rounded-lg text-sm transition mt-4 md:mt-0">
            <i class="fas fa-arrow-left mr-1"></i>Back to Queries
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-surface rounded-xl border border-gray-700 p-6">
            <form id="createQueryForm">
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-gray-400 text-sm mb-1.5">Query name</label>
                        <input type="text" id="name" name="name" required placeholder="e.g. Oversold large-caps"
                            class="w-full bg-page border border-gray-600 rounded-lg px-3 py-2.5 text-white focus:border-accent focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-1.5">Prediction method</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <?php foreach ($methods as $method): ?>
                            <?php $meta = prediction_methods()[$method]; ?>
                            <label class="method-option flex items-start p-3 rounded-lg border cursor-pointer transition
                                bg-page border-gray-700 hover:border-accent
                                <?= $method === 'linear_regression' ? 'border-accent ring-1 ring-accent' : '' ?>">
                                <input type="radio" name="method" value="<?= esc($method) ?>" class="hidden"
                                    <?= $method === 'linear_regression' ? 'checked' : '' ?>>
                                <span class="text-sm font-semibold px-2 py-0.5 rounded border mr-3 whitespace-nowrap <?= $meta['chip'] ?>">
                                    <?= esc($meta['label']) ?>
                                </span>
                                <span class="text-xs text-gray-500 leading-snug"><?= esc($meta['description']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="horizon_days" class="block text-gray-400 text-sm mb-1.5">Horizon</label>
                            <select id="horizon_days" name="horizon_days" class="w-full bg-page border border-gray-600 rounded-lg px-3 py-2.5 text-white focus:border-accent focus:outline-hidden">
                                <option value="7">7 days</option>
                                <option value="14">14 days</option>
                                <option value="30">30 days</option>
                            </select>
                        </div>
                        <div>
                            <label for="match_mode" class="block text-gray-400 text-sm mb-1.5">Match mode</label>
                            <select id="match_mode" name="match_mode" class="w-full bg-page border border-gray-600 rounded-lg px-3 py-2.5 text-white focus:border-accent focus:outline-hidden">
                                <option value="all">All conditions must match</option>
                                <option value="any">Any condition matches</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="query_text" class="block text-gray-400 text-sm mb-1.5">Screening criteria</label>
                        <input type="text" id="query_text" name="query_text" class="w-full bg-page border border-gray-600 rounded-lg px-3 py-2.5 text-white focus:border-accent focus:outline-hidden font-mono" placeholder="rsi < 30 AND close > sma(50)">
                        <p class="text-gray-600 text-xs mt-1.5">Use the screener query language. Leave blank to screen all stocks by method only.</p>
                    </div>

                    <label class="flex items-center space-x-2 cursor-pointer select-none">
                        <input type="checkbox" id="is_public" name="is_public" class="accent-accent w-4 h-4 rounded border-gray-600">
                        <span class="text-sm text-gray-300">Make this query public</span>
                    </label>

                    <div class="flex justify-end space-x-3 pt-2">
                        <a href="/predictions" class="px-4 py-2.5 rounded-lg bg-page border border-gray-600 text-gray-300 hover:text-white transition text-sm">Cancel</a>
                        <button type="submit" id="createSubmitBtn" class="px-6 py-2.5 rounded-lg bg-accent hover:bg-accent-2 text-on-accent font-semibold transition text-sm">
                            <i class="fas fa-plus mr-1"></i>Create Query
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="space-y-4">
            <div class="bg-surface rounded-xl border border-gray-700 p-5">
                <h3 class="text-white font-semibold text-sm mb-3"><i class="fas fa-flask text-accent mr-2"></i>How it works</h3>
                <ol class="space-y-3 text-sm text-gray-400">
                    <li class="flex"><span class="text-accent font-bold mr-2">1</span> The query screens every stock in your watchlist.</li>
                    <li class="flex"><span class="text-accent font-bold mr-2">2</span> Matching stocks get a <span class="text-white"><?= (int) 30 ?>-day history</span> loaded for forecasting.</li>
                    <li class="flex"><span class="text-accent font-bold mr-2">3</span> The chosen method produces a price, change % and confidence.</li>
                    <li class="flex"><span class="text-accent font-bold mr-2">4</span> Run anytime — results are saved for later review.</li>
                </ol>
            </div>

            <?= view('prediction/query/_help_panel') ?>
        </div>
    </div>
</section>

<script>
(function() {
    var form = document.getElementById('createQueryForm');
    var submitBtn = document.getElementById('createSubmitBtn');

    function selectMethodOption(opt) {
        document.querySelectorAll('.method-option').forEach(function(o) {
            o.classList.remove('border-accent', 'ring-1', 'ring-accent');
        });
        opt.classList.add('border-accent', 'ring-1', 'ring-accent');
        opt.querySelector('input').checked = true;
    }

    document.querySelectorAll('.method-option').forEach(function(opt) {
        opt.addEventListener('click', function() { selectMethodOption(opt); });
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Creating...';

        var data = new URLSearchParams(new FormData(form));
        if (data.has('is_public')) { data.delete('is_public'); data.append('is_public', '1'); }

        fetch('/predictions/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: data.toString()
        })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.success) {
                    window.location.href = '/predictions';
                } else {
                    alert('Error: ' + (d.message || 'Could not create query'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-plus mr-1"></i>Create Query';
                }
            })
            .catch(function() {
                alert('Error creating query');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-plus mr-1"></i>Create Query';
            });
    });
})();
</script>
