<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white">My Watchlist</h1>
                <span id="marketBadge" class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>Checking...
                </span>
            </div>
            <p class="text-gray-400 mt-1">Track your favorite stocks with predictions &amp; organized buckets</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="syncPrices()" class="bg-navy2 border border-gray-600 text-gray-300 hover:text-gold hover:border-gold px-4 py-2 rounded-lg transition text-sm">
                <i class="fas fa-sync mr-1"></i> Sync
            </button>
            <a href="/stocks" class="bg-gold hover:bg-gold2 text-navy font-semibold px-6 py-2 rounded-lg text-sm transition">
                <i class="fas fa-plus mr-2"></i>Add Stocks
            </a>
        </div>
    </div>

    <?php if (empty($stocks)): ?>
    <div class="bg-navy2 rounded-xl border border-gray-700 p-12 text-center">
        <i class="far fa-star text-6xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-xl font-semibold mb-2">Your watchlist is empty</h3>
        <p class="text-gray-400 mb-6">Start adding stocks to monitor their performance and get predictions.</p>
        <a href="/stocks" class="bg-gold hover:bg-gold2 text-navy font-semibold px-8 py-3 rounded-lg transition inline-block">
            Browse Stocks
        </a>
    </div>
    <?php else: ?>

    <div class="flex flex-wrap items-center gap-2 mb-6">
        <span class="text-gray-400 text-sm mr-2">Buckets:</span>
        <a href="/watchlist" class="px-3 py-1.5 rounded-lg text-sm <?= empty($selectedBucket) ? 'bg-gold text-navy font-semibold' : 'bg-navy border border-gray-600 text-gray-300 hover:border-gold' ?> transition">
            <i class="fas fa-layer-group mr-1"></i>All
        </a>
        <?php foreach ($buckets as $b): ?>
        <a href="/watchlist?bucket=<?= $b['id'] ?>" class="px-3 py-1.5 rounded-lg text-sm <?= (!empty($selectedBucket) && $selectedBucket == $b['id']) ? 'bg-gold text-navy font-semibold' : 'bg-navy border border-gray-600 text-gray-300 hover:border-gold' ?> transition">
            <i class="fas fa-folder mr-1"></i><?= esc($b['name']) ?>
        </a>
        <?php endforeach; ?>
        <button onclick="document.getElementById('bucketForm').classList.toggle('hidden')" class="px-3 py-1.5 rounded-lg text-sm bg-navy border border-dashed border-gray-600 text-gray-400 hover:text-gold hover:border-gold transition">
            <i class="fas fa-plus mr-1"></i>New Bucket
        </button>
    </div>

    <form id="bucketForm" action="/watchlist/bucket/create" method="post" class="hidden flex items-center gap-2 mb-6 p-3 bg-navy2 rounded-lg border border-gray-700">
        <?= csrf_field() ?>
        <input type="text" name="name" placeholder="Bucket name (e.g. Large Cap, Growth)" required
            class="flex-1 bg-navy border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:border-gold focus:outline-none">
        <button type="submit" class="bg-gold hover:bg-gold2 text-navy font-semibold px-4 py-2 rounded-lg text-sm transition">Create</button>
        <button type="button" onclick="this.closest('form').classList.add('hidden')" class="text-gray-400 hover:text-white text-sm px-2">Cancel</button>
    </form>

    <div class="space-y-8" id="watchlistCards">
        <?php foreach ($stocksByBucket as $g): $bName = $g['bucket']['name']; $bId = $g['bucket']['id']; ?>
        <?php if (!empty($g['stocks'])): ?>
        <div class="bucket-group" data-bucket="<?= $bId ?>">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-white font-bold text-lg">
                    <i class="fas fa-folder text-gold mr-2"></i><?= esc($bName) ?>
                    <span class="text-gray-500 text-sm font-normal">(<?= count($g['stocks']) ?>)</span>
                </h2>
                <form action="/watchlist/bucket/<?= $bId ?>/delete" method="post" class="inline" onsubmit="return confirm('Delete bucket "<?= esc($bName) ?>"? Stocks will move to Uncategorized.')">
                    <?= csrf_field() ?>
                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs"><i class="fas fa-trash mr-1"></i>Delete Bucket</button>
                </form>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($g['stocks'] as $stock): echo renderWatchlistCard($stock, $predictions, $buckets); endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>

        <?php if (!empty($uncategorized)): ?>
        <div class="bucket-group" data-bucket="">
            <div class="flex items-center mb-3">
                <h2 class="text-white font-bold text-lg">
                    <i class="fas fa-inbox text-gray-500 mr-2"></i>Uncategorized
                    <span class="text-gray-500 text-sm font-normal">(<?= count($uncategorized) ?>)</span>
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($uncategorized as $stock): echo renderWatchlistCard($stock, $predictions, $buckets); endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>

<?php
function renderWatchlistCard($stock, $predictions, $buckets)
{
    $change = get_price_change((float) $stock['current_price'], (float) $stock['previous_close']);
    $sid = (int) $stock['stock_id'];
    $pred = $predictions[$sid] ?? null;
    $watchlistId = (int) $stock['id'];
    ob_start();
?>
<div class="wl-card bg-navy2 rounded-xl border border-gray-700 hover:border-gold transition" data-sid="<?= $sid ?>" data-wlid="<?= $watchlistId ?>">
    <div class="p-6">
        <div class="flex justify-between items-start mb-3">
            <a href="/stocks/<?= $sid ?>" class="hover:text-gold">
                <h3 class="text-white font-bold text-lg"><?= esc($stock['symbol']) ?></h3>
                <p class="text-gray-400 text-sm"><?= esc($stock['name']) ?></p>
            </a>
            <div class="flex flex-col items-end gap-1">
                <span class="text-xs px-2 py-1 rounded bg-navy border border-gray-600 text-gray-300"><?= esc($stock['sector']) ?></span>
                <select onchange="moveToBucket(<?= $watchlistId ?>, this.value)" class="text-xs bg-navy border border-gray-700 rounded px-1 py-0.5 text-gray-400 cursor-pointer hover:border-gold">
                    <option value="">Move...</option>
                    <option value="">Uncategorized</option>
                    <?php foreach ($buckets as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= esc($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?php if ($pred): ?>
        <div class="flex items-center gap-2 mb-3 text-xs">
            <span class="text-gray-500">Prediction:</span>
            <span class="text-green-400 font-semibold"><?= format_price($pred['low']) ?></span>
            <span class="text-gray-500">–</span>
            <span class="text-red-400 font-semibold"><?= format_price($pred['high']) ?></span>
        </div>
        <?php endif; ?>

        <div class="flex justify-between items-end mt-2">
            <div>
                <p class="wl-price text-2xl font-bold text-white"><?= format_price($stock['current_price']) ?></p>
            </div>
            <div class="text-right">
                <span class="wl-change px-3 py-1 rounded text-sm font-semibold <?= $change['change'] >= 0 ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400' ?>">
                    <?= $change['change'] >= 0 ? '+' : '' ?><?= $change['percent'] ?>%
                </span>
                <div class="flex gap-3 mt-1 text-xs text-gray-500">
                    <?php if ($stock['pe_ratio']): ?><span>P/E: <?= $stock['pe_ratio'] ?></span><?php endif; ?>
                    <?php if ($stock['market_cap']): ?><span>MCap: <?= format_mcap((float) $stock['market_cap']) ?></span><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="border-t border-gray-700 flex">
        <a href="/stocks/<?= $sid ?>" class="flex-1 text-center py-3 text-gray-400 hover:text-gold hover:bg-navy text-sm transition border-r border-gray-700">
            <i class="fas fa-chart-line mr-1"></i>View
        </a>
        <a href="/stocks/<?= $sid ?>/predictions" class="flex-1 text-center py-3 text-gray-400 hover:text-gold hover:bg-navy text-sm transition border-r border-gray-700">
            <i class="fas fa-chart-simple mr-1"></i>Predict
        </a>
        <button onclick="removeWatchlist(<?= $sid ?>, '<?= esc($stock['symbol']) ?>')" class="flex-1 text-center py-3 text-red-400 hover:text-red-300 hover:bg-red-900/10 text-sm transition">
            <i class="fas fa-trash mr-1"></i>Remove
        </button>
    </div>
</div>
<?php
    return ob_get_clean();
}
?>

<script>
(function() {
    var CSRF_NAME = '<?= csrf_token() ?>';
    var CSRF_HASH = '<?= csrf_hash() ?>';

    function getCSRF() {
        return document.querySelector('input[name="' + CSRF_NAME + '"]')
            || document.querySelector('input[name^="csrf_"]');
    }

    function formatPrice(v) { return '\u20B9' + parseFloat(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function updateBadge(market) {
        var badge = document.getElementById('marketBadge');
        if (!badge) return;
        if (market.open) {
            badge.className = 'text-xs px-3 py-1 rounded-full border border-green-600 text-green-400';
            badge.innerHTML = '<i class="fas fa-circle text-green-400 text-[8px] mr-1 animate-pulse"></i>' + market.label;
        } else {
            badge.className = 'text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400';
            badge.innerHTML = '<i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>' + market.label;
        }
    }

    function updateCards(stocks) {
        stocks.forEach(function(s) {
            var card = document.querySelector('.wl-card[data-sid="' + s.id + '"]');
            if (!card) return;
            var priceEl = card.querySelector('.wl-price');
            var changeEl = card.querySelector('.wl-change');
            if (priceEl) priceEl.textContent = formatPrice(s.current_price);
            if (changeEl) {
                var pct = s.change_percent >= 0 ? '+' + s.change_percent : s.change_percent;
                changeEl.textContent = pct + '%';
                changeEl.className = 'wl-change px-3 py-1 rounded text-sm font-semibold ' + (s.change_percent >= 0 ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400');
            }
            if (s.change_percent > 0) {
                card.style.borderColor = 'rgba(74, 222, 128, 0.3)';
                setTimeout(function() { card.style.borderColor = ''; }, 1500);
            } else if (s.change_percent < 0) {
                card.style.borderColor = 'rgba(248, 113, 113, 0.3)';
                setTimeout(function() { card.style.borderColor = ''; }, 1500);
            }
        });
    }

    function poll() {
        fetch('/api/live-prices')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                updateBadge(data.market);
                updateCards(data.stocks);
            })
            .catch(function() {});
    }

    poll();
    setInterval(poll, 5000);

    window.syncPrices = function() {
        var btn = document.querySelector('button[onclick="syncPrices()"]');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>'; }
        fetch('/api/sync-prices')
            .then(function(r) { return r.json(); })
            .then(function() {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync mr-1"></i> Sync'; }
                poll();
            })
            .catch(function() {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync mr-1"></i> Sync'; }
            });
    };

    window.removeWatchlist = function(id, symbol) {
        if (!confirm('Remove ' + symbol + ' from watchlist?')) return;
        var csrfInput = getCSRF();
        var bodyStr = (CSRF_NAME || 'csrf_test_name') + '=' + encodeURIComponent(csrfInput ? csrfInput.value : '');
        fetch('/watchlist/toggle/' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: bodyStr
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.watched) {
                var card = document.querySelector('.wl-card[data-sid="' + id + '"]');
                if (card) {
                    card.style.transition = 'opacity 0.3s';
                    card.style.opacity = '0';
                    setTimeout(function() { card.remove(); }, 300);
                }
            }
        });
    };

    window.moveToBucket = function(watchlistId, bucketId) {
        var csrfInput = getCSRF();
        var bodyStr = (CSRF_NAME || 'csrf_test_name') + '=' + encodeURIComponent(csrfInput ? csrfInput.value : '')
            + '&watchlist_id=' + watchlistId + '&bucket_id=' + bucketId;
        fetch('/watchlist/move-to-bucket', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: bodyStr
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.success) location.reload();
        });
    };
})();
</script>
