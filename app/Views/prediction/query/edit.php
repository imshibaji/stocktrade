<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Edit Prediction Query</h1>
            <p class="text-gray-400 mt-1">Adjust the strategy, screening rules, and method.</p>
        </div>
        <a href="/predictions/<?= (int) $query['id'] ?>" class="border border-gray-600 text-gray-300 hover:border-accent hover:text-white px-4 py-2 rounded-lg text-sm transition mt-4 md:mt-0">
            <i class="fas fa-arrow-left mr-1"></i>Back to Query
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-surface rounded-xl border border-gray-700 p-6">
            <form id="editQueryForm">
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-gray-400 text-sm mb-1.5">Query name</label>
                        <input type="text" id="name" name="name" required value="<?= esc($query['name']) ?>"
                            class="w-full bg-page border border-gray-600 rounded-lg px-3 py-2.5 text-white focus:border-accent focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-1.5">Prediction method</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <?php foreach ($methods as $method): ?>
                            <?php $meta = prediction_methods()[$method]; ?>
                            <label class="method-option flex items-start p-3 rounded-lg border cursor-pointer transition
                                bg-page border-gray-700 hover:border-accent
                                <?= $method === $query['method'] ? 'border-accent ring-1 ring-accent' : '' ?>">
                                <input type="radio" name="method" value="<?= esc($method) ?>" class="hidden"
                                    <?= $method === $query['method'] ? 'checked' : '' ?>>
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
                                <option value="7" <?= (int) $query['horizon_days'] === 7 ? 'selected' : '' ?>>7 days</option>
                                <option value="14" <?= (int) $query['horizon_days'] === 14 ? 'selected' : '' ?>>14 days</option>
                                <option value="30" <?= (int) $query['horizon_days'] === 30 ? 'selected' : '' ?>>30 days</option>
                            </select>
                        </div>
                        <div>
                            <label for="match_mode" class="block text-gray-400 text-sm mb-1.5">Match mode</label>
                            <select id="match_mode" name="match_mode" class="w-full bg-page border border-gray-600 rounded-lg px-3 py-2.5 text-white focus:border-accent focus:outline-hidden">
                                <option value="all" <?= ($query['match_mode'] ?? 'all') === 'all' ? 'selected' : '' ?>>All conditions must match</option>
                                <option value="any" <?= ($query['match_mode'] ?? '') === 'any' ? 'selected' : '' ?>>Any condition matches</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="query_text" class="block text-gray-400 text-sm mb-1.5">Screening criteria</label>
                        <input type="text" id="query_text" name="query_text"
                            value="<?= esc($query['query_text'] ?? prediction_criteria_to_query_text($query['criteria'] ?? null, $query['technical_criteria'] ?? null)) ?>"
                            class="w-full bg-page border border-gray-600 rounded-lg px-3 py-2.5 text-white focus:border-accent focus:outline-hidden font-mono"
                            placeholder="rsi < 30 AND close > sma(50)">
                        <p class="text-gray-600 text-xs mt-1.5">Leave blank to screen all stocks by method only.</p>
                    </div>

                    <label class="flex items-center space-x-2 cursor-pointer select-none">
                        <input type="checkbox" id="is_public" name="is_public" class="accent-accent w-4 h-4 rounded border-gray-600" <?= $query['is_public'] ? 'checked' : '' ?>>
                        <span class="text-sm text-gray-300">Make this query public</span>
                    </label>

                    <div class="flex justify-end space-x-3 pt-2">
                        <a href="/predictions/<?= (int) $query['id'] ?>" class="px-4 py-2.5 rounded-lg bg-page border border-gray-600 text-gray-300 hover:text-white transition text-sm">Cancel</a>
                        <button type="submit" id="editSubmitBtn" class="px-6 py-2.5 rounded-lg bg-accent hover:bg-accent-2 text-on-accent font-semibold transition text-sm">
                            <i class="fas fa-save mr-1"></i>Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="space-y-4">
            <div class="bg-surface rounded-xl border border-gray-700 p-5">
                <h3 class="text-white font-semibold text-sm mb-3"><i class="fas fa-info-circle text-accent mr-2"></i>About this query</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd class="text-gray-300"><?= ucfirst(esc($query['status'])) ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Last run</dt>
                        <dd class="text-gray-300"><?= $query['last_run_at'] ? esc(date('M j, Y H:i', strtotime($query['last_run_at']))) : 'Never' ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Created</dt>
                        <dd class="text-gray-300"><?= esc(date('M j, Y', strtotime($query['created_at']))) ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Visibility</dt>
                        <dd class="text-gray-300"><?= $query['is_public'] ? 'Public' : 'Private' ?></dd>
                    </div>
                </dl>
            </div>

            <?= view('prediction/query/_help_panel') ?>
        </div>
    </div>
</section>

<script>
(function() {
    var form = document.getElementById('editQueryForm');
    var submitBtn = document.getElementById('editSubmitBtn');

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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Saving...';

        var data = new URLSearchParams(new FormData(form));
        if (data.has('is_public')) { data.delete('is_public'); data.append('is_public', '1'); }

        fetch('/predictions/<?= (int) $query['id'] ?>/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: data.toString()
        })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.success) {
                    window.location.href = '/predictions/<?= (int) $query['id'] ?>';
                } else {
                    alert('Error: ' + (d.message || 'Could not update query'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-1"></i>Save Changes';
                }
            })
            .catch(function() {
                alert('Error updating query');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-1"></i>Save Changes';
            });
    });
})();
</script>
