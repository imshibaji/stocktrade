<?php
$heroTitle = home_setting('home_hero_title', 'Smart Stock Trading Tips');
$heroHighlight = home_setting('home_hero_title_highlight', 'Trading Tips');
$heroTitleHtml = esc($heroTitle);
if ($heroHighlight !== '' && str_contains($heroTitle, $heroHighlight)) {
    $heroTitleHtml = '';
    foreach (explode($heroHighlight, $heroTitle) as $i => $part) {
        if ($i > 0) {
            $heroTitleHtml .= '<span class="text-accent">' . esc($heroHighlight) . '</span>';
        }
        $heroTitleHtml .= esc($part);
    }
}
?>
<section class="text-center py-16">
    <h1 class="text-5xl font-bold text-white mb-4"><?= $heroTitleHtml ?></h1>
    <p class="text-xl text-gray-400 max-w-3xl mx-auto mb-8">
        <?= esc(home_setting('home_hero_subtitle', 'Analyze stocks, get future predictions, track your investments, and calculate net profit/loss after taxes — all in one place.')) ?>
    </p>
    <div class="flex justify-center space-x-4">
        <?php if (!is_logged_in()): ?>
        <a href="/register" class="bg-accent hover:bg-accent-2 text-on-accent font-bold px-8 py-3 rounded-lg text-lg transition">
            <?= esc(home_setting('home_hero_cta_primary', 'Get Started Free')) ?>
        </a>
        <a href="/about" class="border border-accent text-accent hover:bg-accent/10 px-8 py-3 rounded-lg text-lg transition">
            <?= esc(home_setting('home_hero_cta_secondary', 'Learn More')) ?>
        </a>
        <?php else: ?>
        <a href="/dashboard" class="bg-accent hover:bg-accent-2 text-on-accent font-bold px-8 py-3 rounded-lg text-lg transition">
            <?= esc(home_setting('home_hero_cta_primary_logged_in', 'Go to Dashboard')) ?>
        </a>
        <a href="/stocks" class="border border-accent text-accent hover:bg-accent/10 px-8 py-3 rounded-lg text-lg transition">
            <?= esc(home_setting('home_hero_cta_secondary_logged_in', 'Browse Stocks')) ?>
        </a>
        <?php endif; ?>
    </div>
</section>

<section class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16">
    <div class="bg-surface rounded-xl p-6 border border-gray-700 text-center hover:border-accent transition">
        <i class="fas fa-chart-bar text-4xl text-accent mb-3"></i>
        <h3 class="text-white font-semibold mb-2"><?= esc(home_setting('home_feature_1_title', 'Stock Analysis')) ?></h3>
        <p class="text-gray-400 text-sm"><?= esc(home_setting('home_feature_1_desc', 'Deep analysis with historical price trends, key metrics, and sector comparisons.')) ?></p>
    </div>
    <div class="bg-surface rounded-xl p-6 border border-gray-700 text-center hover:border-accent transition">
        <i class="fas fa-chart-line text-4xl text-accent mb-3"></i>
        <h3 class="text-white font-semibold mb-2"><?= esc(home_setting('home_feature_2_title', 'Future Predictions')) ?></h3>
        <p class="text-gray-400 text-sm"><?= esc(home_setting('home_feature_2_desc', 'AI-powered 30-day price predictions with confidence scores for every stock.')) ?></p>
    </div>
    <div class="bg-surface rounded-xl p-6 border border-gray-700 text-center hover:border-accent transition">
        <i class="fas fa-calculator text-4xl text-accent mb-3"></i>
        <h3 class="text-white font-semibold mb-2"><?= esc(home_setting('home_feature_3_title', 'P&L Calculator')) ?></h3>
        <p class="text-gray-400 text-sm"><?= esc(home_setting('home_feature_3_desc', 'Calculate gross profit/loss and net returns after STCG/LTCG tax deductions.')) ?></p>
    </div>
    <div class="bg-surface rounded-xl p-6 border border-gray-700 text-center hover:border-accent transition">
        <i class="fas fa-star text-4xl text-accent mb-3"></i>
        <h3 class="text-white font-semibold mb-2"><?= esc(home_setting('home_feature_4_title', 'Watchlist')) ?></h3>
        <p class="text-gray-400 text-sm"><?= esc(home_setting('home_feature_4_desc', 'Save your favorite stocks and track them daily with real-time analysis.')) ?></p>
    </div>
</section>

<section class="mb-16">
    <h2 class="text-3xl font-bold text-white text-center mb-4"><?= esc(home_setting('home_public_lists_title', 'Community Screener Lists')) ?></h2>
    <p class="text-center text-gray-400 mb-8 max-w-2xl mx-auto"><?= esc(home_setting('home_public_lists_subtitle', 'Discover stock lists shared by the community. Click a list to view its stocks.')) ?></p>
    <?php if (!empty($publicLists)): ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($publicLists as $list): ?>
        <div class="bg-surface rounded-xl p-5 border border-gray-700 hover:border-accent transition cursor-pointer public-list-card"
             data-list-id="<?= (int) $list['id'] ?>">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-white font-bold text-lg"><?= esc($list['name']) ?></h3>
                    <p class="text-gray-400 text-sm"><?= (int) $list['stock_count'] ?> stocks</p>
                </div>
                <span class="text-xs px-2 py-1 rounded bg-page border border-gray-600 text-gray-300"><i class="fas fa-globe mr-1"></i>Public</span>
            </div>
            <p class="text-xs text-gray-500">Shared by <?= esc($list['owner_name'] ?? 'Member') ?> &middot; <?= esc(date('M j, Y', strtotime($list['created_at']))) ?></p>
            <p class="mt-3 text-xs text-accent"><i class="fas fa-chevron-down mr-1"></i>Click to view stocks</p>
        </div>
        <?php endforeach; ?>
    </div>
    <div id="publicListDetail" class="hidden mt-6 bg-surface rounded-xl border border-gray-700 p-6"></div>
    <?php else: ?>
    <p class="text-center text-gray-500">No community lists yet.</p>
    <?php endif; ?>
</section>
<script>
(function() {
    function escHtml(s) { if (s == null) return ''; var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function curFor(ex) {
        var m = { 'NSE': 'INR', 'BSE': 'INR', 'NSI': 'INR',
                  'LSE': 'GBP', 'TSE': 'JPY', 'HKEX': 'HKD',
                  'KRX': 'KRW', 'TSX': 'CAD', 'ASX': 'AUD',
                  'SWX': 'CHF', 'FRA': 'EUR', 'ETR': 'EUR',
                  'Euronext': 'EUR', 'MEX': 'MXN', 'BVMF': 'BRL',
                  'NMS': 'USD', 'NYQ': 'USD', 'NGM': 'USD' };
        return m[ex] || 'USD';
    }
    function symFor(c) {
        var m = { 'INR': '\u20B9', 'USD': '$', 'EUR': '\u20AC', 'GBP': '\u00A3',
                  'JPY': '\u00A5', 'AUD': 'A$', 'CAD': 'C$', 'CHF': 'CHF ',
                  'CNY': '\u00A5', 'SGD': 'S$', 'HKD': 'HK$', 'KRW': '\u20A9',
                  'MXN': 'Mex$', 'BRL': 'R$', 'NZD': 'NZ$', 'ZAR': 'R',
                  'SEK': 'kr', 'NOK': 'kr', 'DKK': 'kr', 'PLN': 'z\u0142',
                  'CZK': 'K\u010D', 'HUF': 'Ft', 'RUB': '\u20BD', 'TRY': '\u20BA',
                  'ILS': '\u20AA', 'THB': '\u0E3F', 'MYR': 'RM', 'IDR': 'Rp',
                  'PHP': '\u20B1', 'TWD': 'NT$', 'VND': '\u20AB', 'AED': '\u062F.\u0625',
                  'SAR': '\u0631.\u0639', 'QAR': 'QR', 'KWD': 'KD', 'OMR': '\u0631.\u0639', 'BHD': '.\u062F.\u0628' };
        return m[c] || (c + ' ');
    }
    window.loadPublicList = function(id, card) {
        var detail = document.getElementById('publicListDetail');
        detail.classList.remove('hidden');
        detail.innerHTML = '<div class="text-gray-400 text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Loading...</div>';
        document.querySelectorAll('.public-list-card').forEach(function(c) { c.classList.remove('border-accent'); });
        if (card) card.classList.add('border-accent');
        fetch('/api/screener/public-list/' + id)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) { detail.innerHTML = '<p class="text-red-400">' + escHtml(data.message) + '</p>'; return; }
                var stocks = data.stocks || [];
                var html = '<div class="flex items-center justify-between mb-4">' +
                    '<h3 class="text-white font-bold text-lg">' + escHtml(data.list.name) + '</h3>' +
                    '<button onclick="document.getElementById(\'publicListDetail\').classList.add(\'hidden\')" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button></div>';
                if (stocks.length === 0) {
                    html += '<p class="text-gray-500 text-sm">This list has no stocks.</p>';
                } else {
                    html += '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
                    stocks.forEach(function(s) {
                        var cp = parseFloat(s.current_price) || 0, pc = parseFloat(s.previous_close) || 0;
                        var chg = cp - pc, pct = pc > 0 ? ((chg / pc) * 100).toFixed(2) : '0.00';
                        var cur = curFor(s.exchange);
                        html += '<div class="bg-page rounded-lg border border-gray-700 p-4 cursor-pointer hover:border-accent transition" onclick="window.location.href=\'/stocks/' + s.id + '\'">' +
                            '<div class="flex justify-between items-start mb-2"><p class="text-white font-bold">' + escHtml(s.symbol) + ' <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-surface border border-gray-600 text-gray-400 align-middle">' + escHtml(s.exchange || '') + '</span></p>' +
                            '<span class="text-xs px-1.5 py-0.5 rounded bg-surface border border-gray-600 text-gray-300">' + escHtml(s.sector || '') + '</span></div>' +
                            '<p class="text-gray-400 text-xs mb-2">' + escHtml(s.name) + '</p>' +
                            '<div class="flex justify-between items-end"><span class="text-white font-bold">' + symFor(cur) + cp.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>' +
                            '<span class="text-sm ' + (chg >= 0 ? 'text-green-400' : 'text-red-400') + '">' + (chg >= 0 ? '+' : '') + pct + '%</span></div></div>';
                    });
                    html += '</div>';
                }
                detail.innerHTML = html;
            })
            .catch(function() { detail.innerHTML = '<p class="text-red-400">Failed to load list.</p>'; });
    };
    document.querySelectorAll('.public-list-card').forEach(function(card) {
        card.addEventListener('click', function() {
            loadPublicList(card.getAttribute('data-list-id'), card);
        });
    });
})();
</script>

<section class="mb-16">
    <h2 class="text-3xl font-bold text-white text-center mb-8"><?= esc(home_setting('home_topstocks_title', 'Top Stocks by Market Cap')) ?></h2>
    <?php if (!empty($topPerformer) || !empty($topLoser)): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <?php if (!empty($topPerformer)): ?>
        <?php $pStock = $topPerformer['stock']; $pChange = $topPerformer['change']; $pCur = stock_currency($pStock['exchange'] ?? null); ?>
        <?php $pred = $predictionsMap[$pStock['id']] ?? null; ?>
        <?php $bullish = $pred !== null && $pred['avg'] >= (float) $pStock['current_price']; ?>
        <div class="bg-surface rounded-xl p-6 border border-green-700/60 hover:border-green-500 transition cursor-pointer flex flex-col"
             onclick="<?= is_logged_in() ? "window.location.href='/stocks/{$pStock['id']}'" : "window.location.href='/login'" ?>">
            <div class="flex items-center gap-3 mb-3">
                <span class="w-10 h-10 rounded-lg bg-green-500/15 text-green-400 flex items-center justify-center"><i class="fas fa-arrow-trend-up"></i></span>
                <div>
                    <p class="text-xs text-green-400 font-semibold uppercase tracking-wider">Top Performer</p>
                    <p class="text-white font-bold text-lg leading-tight"><?= esc($pStock['symbol']) ?> <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-surface border border-gray-600 text-gray-400 align-middle"><?= esc(exchange_display($pStock['exchange'] ?? null, $pStock['exchange_display'] ?? null)) ?></span></p>
                </div>
            </div>
            <p class="text-gray-400 text-sm mb-3"><?= esc($pStock['name']) ?></p>
            <?php if ($pred !== null): ?>
            <div class="mt-3 pt-3 border-t border-gray-700/50">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-gray-500 uppercase tracking-wider">30-Day Outlook</span>
                    <span class="text-xs font-semibold <?= $bullish ? 'text-green-400' : 'text-red-400' ?>">
                        <i class="fas fa-<?= $bullish ? 'arrow-trend-up' : 'arrow-trend-down' ?> mr-1"></i><?= $bullish ? 'Bullish' : 'Bearish' ?>
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-gray-500">Target</span>
                    <span class="text-white font-semibold"><?= format_price($pred['avg'], $pCur) ?></span>
                </div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="text-gray-500">Range</span>
                    <span class="text-gray-300"><?= format_price($pred['low'], $pCur) ?> &ndash; <?= format_price($pred['high'], $pCur) ?></span>
                </div>
                <?php if (!empty($pred['prices'])): ?>
                <svg viewBox="0 0 100 28" preserveAspectRatio="none" class="w-full h-7">
                    <polyline fill="none" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"
                              stroke="<?= $bullish ? '#22c55e' : '#ef4444' ?>"
                              points="<?= sparkline_points($pred['prices']) ?>" />
                </svg>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="flex justify-between items-end mt-auto pt-3">
                <span class="text-2xl font-bold text-white"><?= format_price($pStock['current_price'], $pCur) ?></span>
                <span class="text-green-400 text-sm font-semibold">+<?= $pChange['percent'] ?>%</span>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($topLoser)): ?>
        <?php $lStock = $topLoser['stock']; $lChange = $topLoser['change']; $lCur = stock_currency($lStock['exchange'] ?? null); ?>
        <?php $pred = $predictionsMap[$lStock['id']] ?? null; ?>
        <?php $bullish = $pred !== null && $pred['avg'] >= (float) $lStock['current_price']; ?>
        <div class="bg-surface rounded-xl p-6 border border-red-700/60 hover:border-red-500 transition cursor-pointer flex flex-col"
             onclick="<?= is_logged_in() ? "window.location.href='/stocks/{$lStock['id']}'" : "window.location.href='/login'" ?>">
            <div class="flex items-center gap-3 mb-3">
                <span class="w-10 h-10 rounded-lg bg-red-500/15 text-red-400 flex items-center justify-center"><i class="fas fa-arrow-trend-down"></i></span>
                <div>
                    <p class="text-xs text-red-400 font-semibold uppercase tracking-wider">Top Loser</p>
                    <p class="text-white font-bold text-lg leading-tight"><?= esc($lStock['symbol']) ?> <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-surface border border-gray-600 text-gray-400 align-middle"><?= esc(exchange_display($lStock['exchange'] ?? null, $lStock['exchange_display'] ?? null)) ?></span></p>
                </div>
            </div>
            <p class="text-gray-400 text-sm mb-3"><?= esc($lStock['name']) ?></p>
            <?php if ($pred !== null): ?>
            <div class="mt-3 pt-3 border-t border-gray-700/50">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-gray-500 uppercase tracking-wider">30-Day Outlook</span>
                    <span class="text-xs font-semibold <?= $bullish ? 'text-green-400' : 'text-red-400' ?>">
                        <i class="fas fa-<?= $bullish ? 'arrow-trend-up' : 'arrow-trend-down' ?> mr-1"></i><?= $bullish ? 'Bullish' : 'Bearish' ?>
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-gray-500">Target</span>
                    <span class="text-white font-semibold"><?= format_price($pred['avg'], $lCur) ?></span>
                </div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="text-gray-500">Range</span>
                    <span class="text-gray-300"><?= format_price($pred['low'], $lCur) ?> &ndash; <?= format_price($pred['high'], $lCur) ?></span>
                </div>
                <?php if (!empty($pred['prices'])): ?>
                <svg viewBox="0 0 100 28" preserveAspectRatio="none" class="w-full h-7">
                    <polyline fill="none" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"
                              stroke="<?= $bullish ? '#22c55e' : '#ef4444' ?>"
                              points="<?= sparkline_points($pred['prices']) ?>" />
                </svg>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="flex justify-between items-end mt-auto pt-3">
                <span class="text-2xl font-bold text-white"><?= format_price($lStock['current_price'], $lCur) ?></span>
                <span class="text-red-400 text-sm font-semibold"><?= $lChange['percent'] ?>%</span>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($topStocks as $stock): ?>
        <?php $change = get_price_change((float) $stock['current_price'], (float) $stock['previous_close']); ?>
        <?php $cur = stock_currency($stock['exchange'] ?? null); ?>
        <?php $pred = $predictionsMap[$stock['id']] ?? null; ?>
        <?php $bullish = $pred !== null && $pred['avg'] >= (float) $stock['current_price']; ?>
        <div class="bg-surface rounded-xl p-6 border border-gray-700 hover:border-accent transition cursor-pointer flex flex-col"
             onclick="<?= is_logged_in() ? "window.location.href='/stocks/{$stock['id']}'" : "window.location.href='/login'" ?>">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-white font-bold text-lg"><?= esc($stock['symbol']) ?> <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-surface border border-gray-600 text-gray-400 align-middle"><?= esc(exchange_display($stock['exchange'] ?? null, $stock['exchange_display'] ?? null)) ?></span></h3>
                    <p class="text-gray-400 text-sm"><?= esc($stock['name']) ?></p>
                </div>
                <span class="text-xs px-2 py-1 rounded bg-page border border-gray-600 text-gray-300"><?= esc($stock['sector']) ?></span>
            </div>
            <?php if ($pred !== null): ?>
            <div class="mt-3 pt-3 border-t border-gray-700/50">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-gray-500 uppercase tracking-wider">30-Day Outlook</span>
                    <span class="text-xs font-semibold <?= $bullish ? 'text-green-400' : 'text-red-400' ?>">
                        <i class="fas fa-<?= $bullish ? 'arrow-trend-up' : 'arrow-trend-down' ?> mr-1"></i><?= $bullish ? 'Bullish' : 'Bearish' ?>
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-gray-500">Target</span>
                    <span class="text-white font-semibold"><?= format_price($pred['avg'], $cur) ?></span>
                </div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="text-gray-500">Range</span>
                    <span class="text-gray-300"><?= format_price($pred['low'], $cur) ?> &ndash; <?= format_price($pred['high'], $cur) ?></span>
                </div>
                <?php if (!empty($pred['prices'])): ?>
                <svg viewBox="0 0 100 28" preserveAspectRatio="none" class="w-full h-7">
                    <polyline fill="none" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"
                              stroke="<?= $bullish ? '#22c55e' : '#ef4444' ?>"
                              points="<?= sparkline_points($pred['prices']) ?>" />
                </svg>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="flex justify-between items-end mt-auto pt-3">
                <span class="text-2xl font-bold text-white"><?= format_price($stock['current_price'], $cur) ?></span>
                <span class="<?= $change['change'] >= 0 ? 'text-green-400' : 'text-red-400' ?> text-sm">
                    <?= $change['change'] >= 0 ? '+' : '' ?><?= $change['percent'] ?>%
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="mb-16">
    <h2 class="text-3xl font-bold text-white text-center mb-8"><?= esc(home_setting('home_how_title', 'How It Works')) ?></h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="text-center">
            <div class="w-16 h-16 bg-accent/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl font-bold text-accent">1</span>
            </div>
            <h3 class="text-white font-semibold mb-2"><?= esc(home_setting('home_how_1_title', 'Build Watchlist')) ?></h3>
            <p class="text-gray-400 text-sm"><?= esc(home_setting('home_how_1_desc', 'Add stocks to your watchlist for quick access and daily monitoring.')) ?></p>
        </div>
        <div class="text-center">
            <div class="w-16 h-16 bg-accent/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl font-bold text-accent">2</span>
            </div>
            <h3 class="text-white font-semibold mb-2"><?= esc(home_setting('home_how_2_title', 'Analyze & Invest')) ?></h3>
            <p class="text-gray-400 text-sm"><?= esc(home_setting('home_how_2_desc', 'View detailed analysis, predictions, and record your investments with amounts.')) ?></p>
        </div>
        <div class="text-center">
            <div class="w-16 h-16 bg-accent/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl font-bold text-accent">3</span>
            </div>
            <h3 class="text-white font-semibold mb-2"><?= esc(home_setting('home_how_3_title', 'Track Profits')) ?></h3>
            <p class="text-gray-400 text-sm"><?= esc(home_setting('home_how_3_desc', 'Track gross profit/loss and net returns after calculating STCG/LTCG taxes.')) ?></p>
        </div>
    </div>
</section>

<section class="text-center py-12 bg-surface rounded-2xl border border-gray-700 mb-16">
    <h2 class="text-3xl font-bold text-white mb-4"><?= esc(home_setting('home_cta_title', 'Ready to Start Trading Smarter?')) ?></h2>
    <p class="text-gray-400 mb-6"><?= esc(home_setting('home_cta_subtitle', 'Join now and get access to stock analysis, predictions, and profit tracking.')) ?></p>
    <?php if (!is_logged_in()): ?>
    <a href="/register" class="bg-accent hover:bg-accent-2 text-on-accent font-bold px-10 py-3 rounded-lg text-lg transition">
        <?= esc(home_setting('home_cta_button_primary', 'Create Free Account')) ?>
    </a>
    <?php else: ?>
    <a href="/stocks" class="bg-accent hover:bg-accent-2 text-on-accent font-bold px-10 py-3 rounded-lg text-lg transition">
        <?= esc(home_setting('home_cta_button_logged_in', 'Explore Stocks')) ?>
    </a>
    <?php endif; ?>
</section>
