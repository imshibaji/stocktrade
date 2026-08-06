<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white">My Watchlist</h1>
                <?= market_badge('NSE') ?>
            </div>
            <p class="text-gray-400 mt-1">Track your favorite stocks with predictions</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">

            <a href="/stocks" class="bg-accent hover:bg-accent-2 text-on-accent font-semibold px-6 py-2 rounded-lg text-sm transition">
                <i class="fas fa-plus mr-2"></i>Add Stocks
            </a>
        </div>
    </div>

    <?php if (empty($stocks)): ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-12 text-center">
        <i class="far fa-star text-6xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-xl font-semibold mb-2">Your watchlist is empty</h3>
        <p class="text-gray-400 mb-6">Start adding stocks to monitor their performance and get predictions.</p>
        <a href="/stocks" class="bg-accent hover:bg-accent-2 text-on-accent font-semibold px-8 py-3 rounded-lg transition inline-block">
            Browse Stocks
        </a>
    </div>
    <?php else: ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="watchlistCards">
        <?php foreach ($stocks as $stock): echo renderWatchlistCard($stock, $predictions); endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<?php
function renderWatchlistCard($stock, $predictions)
{
    $change = get_price_change((float) $stock['current_price'], (float) $stock['previous_close']);
    $cur = stock_currency($stock['exchange'] ?? null);
    $sid = (int) $stock['stock_id'];
    $pred = $predictions[$sid] ?? null;
    $watchlistId = (int) $stock['id'];
    ob_start();
?>
<div class="wl-card bg-surface rounded-xl border border-gray-700 hover:border-accent transition cursor-pointer" onclick="location.href='/stocks/<?= $sid ?>'" data-sid="<?= $sid ?>" data-wlid="<?= $watchlistId ?>">
    <div class="p-6">
        <div class="flex justify-between items-start mb-3">
            <a href="/stocks/<?= $sid ?>" onclick="event.stopPropagation()" class="hover:text-accent">
                <h3 class="text-white font-bold text-lg"><?= esc($stock['symbol']) ?></h3>
                <p class="text-gray-400 text-sm"><?= esc($stock['name']) ?></p>
            </a>
        </div>

        <?php if ($pred): ?>
        <div class="flex items-center gap-2 mb-3 text-xs">
            <span class="text-gray-500">Prediction:</span>
            <span class="text-green-400 font-semibold"><?= format_price($pred['low'], $cur) ?></span>
            <span class="text-gray-500">–</span>
            <span class="text-red-400 font-semibold"><?= format_price($pred['high'], $cur) ?></span>
        </div>
        <?php
            $outlook = 'neutral';
            $outlookLabel = '30-Day Outlook: Neutral';
            $outlookClass = 'bg-gray-800/50 text-gray-400 border-gray-600';
            if ($pred['avg'] > 0 && (float) $stock['current_price'] > 0) {
                $outlookPct = (($pred['avg'] - (float) $stock['current_price']) / (float) $stock['current_price']) * 100;
                if ($outlookPct > 2) {
                    $outlook = 'bullish';
                    $outlookLabel = '30-Day Outlook: Bullish (+' . round($outlookPct, 1) . '%)';
                    $outlookClass = 'bg-green-900/30 text-green-400 border-green-700/50';
                } elseif ($outlookPct < -2) {
                    $outlook = 'bearish';
                    $outlookLabel = '30-Day Outlook: Bearish (' . round($outlookPct, 1) . '%)';
                    $outlookClass = 'bg-red-900/30 text-red-400 border-red-700/50';
                } else {
                    $outlookLabel = '30-Day Outlook: Sideways';
                }
            }
        ?>
        <div class="mb-3">
            <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded border <?= $outlookClass ?> font-semibold">
                <i class="fas fa-<?= $outlook === 'bullish' ? 'arrow-trend-up' : ($outlook === 'bearish' ? 'arrow-trend-down' : 'arrows-left-right') ?>"></i>
                <?= $outlookLabel ?>
            </span>
        </div>
        <?php endif; ?>

        <div class="flex justify-between items-end mt-2">
            <div>
                <p class="wl-price text-2xl font-bold text-white"><?= format_price($stock['current_price'], $cur) ?></p>
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
        <a href="/stocks/<?= $sid ?>" onclick="event.stopPropagation()" class="flex-1 text-center py-3 text-gray-400 hover:text-accent hover:bg-page text-sm transition border-r border-gray-700">
            <i class="fas fa-chart-line mr-1"></i>View
        </a>
        <a href="/stocks/<?= $sid ?>/predictions" onclick="event.stopPropagation()" class="flex-1 text-center py-3 text-gray-400 hover:text-accent hover:bg-page text-sm transition border-r border-gray-700">
            <i class="fas fa-chart-simple mr-1"></i>Predict
        </a>
        <button data-sid="<?= $sid ?>" data-symbol="<?= str_replace(['"', "'"], ['', '`'], $stock['symbol']) ?>" onclick="event.stopPropagation(); removeWatchlist(this.dataset.sid, this.dataset.symbol)" class="flex-1 text-center py-3 text-red-400 hover:text-red-300 hover:bg-red-900/10 text-sm transition">
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
})();
</script>
