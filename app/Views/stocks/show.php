<?php $isWatched = $isWatched ?? false; ?>
<?php $cur = stock_currency($stock['exchange'] ?? null); ?>
<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white"><?= esc($stock['symbol']) ?></h1>
                <span class="text-xs px-3 py-1 rounded bg-navy border border-gray-600 text-gray-300"><?= esc($stock['sector']) ?></span>
                <span id="marketBadge" class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>Checking...
                </span>
            </div>
            <p class="text-gray-400 mt-1"><?= esc($stock['name']) ?></p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <?php if (is_logged_in()): ?>
                <?php if ($isWatched): ?>
                <a href="/watchlist/remove/<?= $stock['id'] ?>" class="border border-red-500 text-red-400 hover:bg-red-900/20 px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-star mr-1"></i>Remove from Watchlist
                </a>
                <?php else: ?>
                <a href="/watchlist/add/<?= $stock['id'] ?>" class="border border-gold text-gold hover:bg-gold/10 px-4 py-2 rounded-lg text-sm transition">
                    <i class="far fa-star mr-1"></i>Add to Watchlist
                </a>
                <?php endif; ?>
            <?php endif; ?>
            <a href="/stocks/<?= $stock['id'] ?>/predictions" class="bg-gold hover:bg-gold2 text-navy font-semibold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-chart-line mr-1"></i> Predictions
            </a>
            <?php if (is_logged_in()): ?>
            <button type="button" onclick="refreshStock()" id="refreshBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
            <a href="/stocks/<?= $stock['id'] ?>/edit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <form action="/stocks/<?= $stock['id'] ?>/delete" method="post" class="inline" onsubmit="return confirm('Delete this stock and all related data?');">
                <?= csrf_field() ?>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-trash mr-1"></i> Remove
                </button>
            </form>
            <?php endif; ?>
            <a href="/stocks" class="border border-gray-600 text-gray-300 hover:bg-gray-800/50 px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 sd-panel py-6 px-2">
            <div class="px-5 pt-4 pb-1">
                <h3 class="sd-section-title" style="font-size:1rem;margin-bottom:0">Historical Data / Back-Tested Data</h3>
            </div>
            <div class="flex flex-col gap-3 px-5 pt-2">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-baseline gap-3">
                        <p id="livePrice" class="text-3xl font-bold text-white sd-num"><?= format_price($stock['current_price'], $cur) ?></p>
                        <p id="liveChange" class="<?= $priceChange['change'] >= 0 ? 'text-green-400' : 'text-red-400' ?> sd-num">
                            <?= $priceChange['change'] >= 0 ? '+' : '' ?><?= format_price($priceChange['change'], $cur) ?>
                            (<span id="livePct"><?= $priceChange['change'] >= 0 ? '+' : '' ?><?= $priceChange['percent'] ?></span>%)
                        </p>
                    </div>
                    <div class="sd-tabs" id="sdRangeTabs" role="tablist" aria-label="Chart range">
                        <button class="sd-tab" data-range="1 month" role="tab">1M</button>
                        <button class="sd-tab" data-range="3 months" role="tab">3M</button>
                        <button class="sd-tab active" data-range="6 months" role="tab" aria-selected="true">6M</button>
                        <button class="sd-tab" data-range="1 year" role="tab">1Y</button>
                        <button class="sd-tab" data-range="2 years" role="tab">2Y</button>
                        <button class="sd-tab" data-range="5 years" role="tab">5Y</button>
                    </div>
                </div>
                <div class="sd-tape sd-num" id="sdTape" aria-live="polite"></div>
            </div>
            <div class="sd-chart-wrap">
                <canvas id="sdChart" class="sd-chart-canvas" aria-label="Candlestick price chart"></canvas>
                <div id="sdChartState" class="absolute inset-0 hidden"></div>
            </div>
        </div>

        <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
            <h3 class="text-white font-semibold mb-4">Key Metrics</h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Market Cap</span>
                    <span class="text-white text-sm"><?= $stock['market_cap'] ? format_large_number($stock['market_cap']) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Avg Volume</span>
                    <span class="text-white text-sm" id="mktAvgVol"><?= ($stock['avg_volume'] ?? 0) ? format_large_number($stock['avg_volume']) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Open</span>
                    <span class="text-white text-sm" id="mktOpen"><?= isset($stock['open_price']) ? format_price($stock['open_price'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Day High</span>
                    <span class="text-green-400 text-sm" id="mktHigh"><?= isset($stock['day_high']) ? format_price($stock['day_high'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Day Low</span>
                    <span class="text-red-400 text-sm" id="mktLow"><?= isset($stock['day_low']) ? format_price($stock['day_low'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Volume</span>
                    <span class="text-white text-sm" id="mktVol"><?= isset($stock['volume']) ? format_large_number($stock['volume']) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">P/E Ratio</span>
                    <span class="text-white text-sm"><?= ($stock['pe_ratio'] ?? 0) > 0 ? round((float) $stock['pe_ratio'], 2) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">52-Week High</span>
                    <span class="text-green-400 text-sm"><?= ($stock['week_52_high'] ?? 0) ? format_price($stock['week_52_high'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">52-Week Low</span>
                    <span class="text-red-400 text-sm"><?= ($stock['week_52_low'] ?? 0) ? format_price($stock['week_52_low'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Dividend Yield</span>
                    <span class="text-white text-sm"><?= ($stock['dividend_yield'] ?? 0) > 0 ? round((float) $stock['dividend_yield'], 2) . '%' : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-700/50">
                    <span class="text-gray-400 text-sm">Bid / Ask</span>
                    <span class="text-white text-sm" id="mktBidAsk"><?= (($stock['bid'] ?? 0) > 0 && ($stock['ask'] ?? 0) > 0) ? format_price($stock['bid'], $cur) . ' / ' . format_price($stock['ask'], $cur) : 'N/A' ?></span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-400 text-sm">Beta</span>
                    <span class="text-white text-sm"><?= ($stock['beta'] ?? 0) ?: 'N/A' ?></span>
                </div>
            </div>
        </div>
    </div>

    <section class="sd-section">
        <div class="sd-section-head">
            <h2 class="sd-section-title">Company</h2>
            <span class="sd-eyebrow" id="sdSummaryEyebrow">Loading profile&hellip;</span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 sd-panel sd-panel-pad" id="sdProfile"></div>
            <div class="sd-panel sd-panel-pad" id="sdSnapshot"></div>
        </div>
    </section>

    <section class="sd-section">
        <div class="sd-section-head">
            <h2 class="sd-section-title">Corporate events</h2>
            <span class="sd-eyebrow">Last 5 years</span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="sd-panel" id="sdDividendsPanel">
                <div class="sd-ledger-head">
                    <h3 class="sd-section-title">Dividends</h3>
                    <span class="sd-ledger-count"><b id="sdDivCount">&ndash;</b> payouts</span>
                </div>
                <div class="sd-ledger" id="sdDivRows"></div>
            </div>
            <div class="sd-panel" id="sdSplitsPanel">
                <div class="sd-ledger-head">
                    <h3 class="sd-section-title">Splits</h3>
                    <span class="sd-ledger-count"><b id="sdSplitCount">&ndash;</b> events</span>
                </div>
                <div class="sd-ledger" id="sdSplitRows"></div>
            </div>
        </div>
    </section>

    <section class="sd-section" id="sdOptionsSection">
        <div class="sd-section-head">
            <h2 class="sd-section-title">Options chain</h2>
            <span class="sd-eyebrow" id="sdOptEyebrow">Loading&hellip;</span>
        </div>
        <div class="sd-panel">
            <div class="sd-opt-expiries" id="sdOptExpiries"></div>
            <div class="overflow-x-auto" id="sdOptScroll"></div>
            <div id="sdOptState"></div>
        </div>
    </section>

    <?php if (!empty($stock['predictions'])): ?>
    <div class="bg-navy2 rounded-xl border border-gray-700 p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-white font-bold text-lg">30-Day Price Predictions</h2>
            <a href="/stocks/<?= $stock['id'] ?>/predictions" class="text-gold text-sm hover:text-gold2 transition">View Details</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="text-left px-4 py-2">Date</th>
                        <th class="text-right px-4 py-2">Predicted Price</th>
                        <th class="text-right px-4 py-2">Confidence</th>
                        <th class="text-right px-4 py-2">Change from Current</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 0;
                    foreach ($stock['predictions'] as $p): 
                        if ($count++ >= 7) break;
                        $predChange = (float) $p['predicted_price'] - (float) $stock['current_price'];
                        $predChangePct = (float) $stock['current_price'] > 0 ? ($predChange / (float) $stock['current_price']) * 100 : 0;
                    ?>
                    <tr class="border-b border-gray-700/50 hover:bg-navy/50">
                        <td class="px-4 py-3 text-gray-300"><?= date('M d, Y', strtotime($p['predicted_date'])) ?></td>
                        <td class="px-4 py-3 text-right text-white"><?= format_price($p['predicted_price'], $cur) ?></td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <div class="w-16 h-2 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full <?= $p['confidence_score'] >= 80 ? 'bg-green-500' : ($p['confidence_score'] >= 65 ? 'bg-yellow-500' : 'bg-red-500') ?>" 
                                         style="width: <?= $p['confidence_score'] ?>%"></div>
                                </div>
                                <span class="text-gray-400 text-xs"><?= $p['confidence_score'] ?>%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right <?= $predChange >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <?= $predChange >= 0 ? '+' : '' ?><?= format_price($predChange, $cur)?> (<?= $predChangePct >= 0 ? '+' : '' ?><?= round($predChangePct, 2) ?>%)
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if (is_logged_in()): ?>
    <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
        <h2 class="text-white font-bold text-lg mb-4">Quick Investment Calculator</h2>
        <form action="/investments/create" method="post" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <?= csrf_field() ?>
            <input type="hidden" name="stock_id" value="<?= $stock['id'] ?>">
            <div>
                <label class="block text-gray-300 mb-2 text-sm">Current Price</label>
                <input type="text" value="<?= format_price($stock['current_price'], $cur) ?>" disabled
                    class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-gray-400">
            </div>
            <div>
                <label class="block text-gray-300 mb-2 text-sm">Investment Amount (Rs)</label>
                <input type="number" name="amount" required min="1" step="0.01"
                    class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none"
                    placeholder="e.g. 10000">
            </div>
            <div>
                <label class="block text-gray-300 mb-2 text-sm">Buy Date</label>
                <input type="date" name="buy_date" value="<?= date('Y-m-d') ?>"
                    class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
            </div>
            <div>
                <button type="submit" class="w-full bg-gold hover:bg-gold2 text-navy font-bold py-3 rounded-lg transition">
                    <i class="fas fa-plus mr-1"></i> Add Investment
                </button>
            </div>
        </form>
        <p class="text-gray-500 text-xs mt-3" id="sharePreview">
            Estimated shares: Enter amount above to see
        </p>
    </div>
    <script>
        document.querySelector('input[name="amount"]').addEventListener('input', function() {
            var amount = parseFloat(this.value);
            var priceEl = document.getElementById('livePrice');
            var price = priceEl ? parseFloat(priceEl.textContent.replace(/[^0-9.-]/g, '')) || 0 : 0;
            if (amount > 0 && price > 0) {
                var shares = amount / price;
                document.getElementById('sharePreview').textContent = 'Estimated shares: ' + shares.toFixed(4) + ' at ' + price.toFixed(2) + ' each';
            }
        });
    </script>
    <?php endif; ?>
</section>

<script>
(function () {
    'use strict';

    var CONF = {
        symbol: <?= json_encode($stock['symbol']) ?>,
        exchange: <?= json_encode($stock['exchange'] ?? 'NSE') ?>,
        currency: <?= json_encode($cur) ?>,
        spot: <?= (float) $stock['current_price'] ?: 0 ?>,
        dividendYield: <?= (float) ($stock['dividend_yield'] ?? 0) ?>,
    };

    var SYMS = { 'INR': '\u20B9', 'USD': '\u0024', 'EUR': '\u20AC', 'GBP': '\u00A3', 'JPY': '\u00A5', 'AUD': 'A\u0024', 'CAD': 'C\u0024', 'CHF': 'CHF ', 'CNY': '\u00A5', 'SGD': 'S\u0024' };

    var curSym = SYMS[CONF.currency] || (CONF.currency + ' ');

    function el(id) { return document.getElementById(id); }
    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function raw(v) {
        if (v && typeof v === 'object' && 'raw' in v) return v.raw;
        return v;
    }
    function fmtPrice(v) {
        if (v === null || v === undefined || isNaN(v)) return '\u2014';
        return curSym + (+v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function fmtAxis(v) {
        if (v === null || v === undefined) return '\u2014';
        return curSym + (+v).toLocaleString('en-IN', { maximumFractionDigits: v >= 1000 ? 0 : 2 });
    }
    function fmtInt(v) {
        return (+v).toLocaleString('en-IN');
    }
    function fmtCompact(v) {
        v = +v || 0;
        if (v >= 1e9) return (v / 1e9).toFixed(1) + 'B';
        if (v >= 1e6) return (v / 1e6).toFixed(1) + 'M';
        if (v >= 1e3) return (v / 1e3).toFixed(1) + 'K';
        return String(v);
    }
    function fmtLarge(v) {
        v = +v || 0;
        if (v >= 1e13) return (v / 1e13).toFixed(2) + ' Lakh Cr';
        if (v >= 1e7) return (v / 1e7).toFixed(2) + ' Cr';
        if (v >= 1e5) return (v / 1e5).toFixed(2) + ' L';
        return v.toLocaleString('en-IN');
    }
    function pct(v, d) {
        if (v === null || v === undefined || isNaN(v)) return null;
        return (v * 100).toFixed(d || 1) + '%';
    }
    function num(v, d) {
        if (v === null || v === undefined || isNaN(v)) return null;
        return (+v).toLocaleString('en-IN', { maximumFractionDigits: d || 2 });
    }
    function fetchJSON(url, ok, err) {
        fetch(url, { headers: { Accept: 'application/json' } })
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function (d) { if (d && d.error) throw new Error(d.error); ok(d); })
            .catch(function (e) { (err || function () {})(e); });
    }
    function sdLoading(label) {
        return '<div class="sd-loading"><span class="sd-spin" aria-hidden="true"></span>' + esc(label) + '</div>';
    }
    function sdNote(title, body) {
        return '<div class="sd-note"><span class="sd-note-strong">' + esc(title) + '</span>' + esc(body) + '</div>';
    }
    function sdError(body, retryKey) {
        var btn = retryKey ? '<button type="button" class="sd-btn" data-retry="' + esc(retryKey) + '"><i class="fas fa-sync-alt" aria-hidden="true"></i> Try again</button>' : '';
        return '<div class="sd-note"><span class="sd-note-strong">Couldn\u2019t reach the data feed</span>' + esc(body) + btn + '</div>';
    }
    function apiUrl(path) {
        return '/api/' + path.replace('{sym}', encodeURIComponent(CONF.symbol)).replace('{exch}', encodeURIComponent(CONF.exchange));
    }

    var retryHandlers = {};
    document.addEventListener('click', function (e) {
        var btn = e.target.closest ? e.target.closest('[data-retry]') : null;
        if (btn && retryHandlers[btn.getAttribute('data-retry')]) {
            retryHandlers[btn.getAttribute('data-retry')]();
        }
    });

    /* ================= PRICE TAPE + CANDLESTICK CHART ================= */

    var tape = el('sdTape');
    var canvas = el('sdChart');
    var stateEl = el('sdChartState');
    var ctx = canvas.getContext('2d');
    var chart = { rows: [], range: '6 months', hover: -1, mouseY: 0, lo: 0, hi: 0 };
    var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function cssVar(name) {
        return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || '0, 0, 0';
    }
    function col(name, alpha) {
        return alpha === undefined ? 'rgb(' + cssVar(name) + ')' : 'rgb(' + cssVar(name) + ' / ' + alpha + ')';
    }
    function roundRectPath(g, x, y, w, h, r) {
        r = Math.min(r, w / 2, h / 2);
        g.beginPath();
        g.moveTo(x + r, y);
        g.arcTo(x + w, y, x + w, y + h, r);
        g.arcTo(x + w, y + h, x, y + h, r);
        g.arcTo(x, y + h, x, y, r);
        g.arcTo(x, y, x + w, y, r);
        g.closePath();
    }
    function niceTicks(lo, hi) {
        var span = hi - lo;
        if (span <= 0) return [lo];
        var pow = Math.pow(10, Math.floor(Math.log10(span)));
        var f = span / pow;
        var nf;
        if (f < 1.5) nf = 1; else if (f < 3) nf = 2; else if (f < 7) nf = 5; else nf = 10;
        var step = nf * pow;
        var start = Math.ceil(lo / step) * step;
        var ticks = [];
        for (var t = start; t <= hi + step * 0.001; t += step) ticks.push(t);
        return ticks;
    }
    function parseDateStr(s) { return new Date(s + 'T00:00:00Z'); }
    function axisDateLabel(d, longRange) {
        var mon = d.toLocaleDateString('en-GB', { month: 'short', timeZone: 'UTC' });
        if (longRange) return mon + ' \u2019' + String(d.getUTCFullYear()).slice(2);
        return d.getUTCDate() + ' ' + mon;
    }
    function tapeDateLabel(d) {
        return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', timeZone: 'UTC' });
    }

    function setTape(c, rows) {
        if (!c) return;
        var prev = null;
        if (rows) {
            var idx = rows.indexOf(c);
            if (idx > 0) prev = rows[idx - 1];
        }
        var up = prev ? c.close >= prev.close : c.close >= c.open;
        var cls = up ? 'tape-close' : 'tape-close down';
        tape.innerHTML =
            '<span class="sd-tape-date">' + tapeDateLabel(c.d) + '</span>' +
            '<span class="sd-tape-item"><b>O</b><span>' + fmtPrice(c.open) + '</span></span>' +
            '<span class="sd-tape-item"><b>H</b><span>' + fmtPrice(c.high) + '</span></span>' +
            '<span class="sd-tape-item"><b>L</b><span>' + fmtPrice(c.low) + '</span></span>' +
            '<span class="sd-tape-item ' + cls + '"><b>C</b><span>' + fmtPrice(c.close) + '</span></span>' +
            '<span class="sd-tape-item"><b>V</b><span>' + fmtCompact(c.volume) + '</span></span>';
    }

    function chartLayout() {
        var rect = canvas.getBoundingClientRect();
        var pad = { t: 20, r: 74, b: 10, l: 14 };
        var avail = rect.height - pad.t - pad.b;
        var volH = Math.max(34, Math.round(avail * 0.18));
        var dateBand = 24;
        var priceH = Math.max(120, avail - volH - dateBand);
        var priceY = pad.t;
        var volY = priceY + priceH + dateBand;
        return {
            w: rect.width, h: rect.height,
            price: { x: pad.l, y: priceY, w: rect.width - pad.l - pad.r, h: priceH },
            vol: { x: pad.l, y: volY, w: rect.width - pad.l - pad.r, h: volH },
            dateY: priceY + priceH + 5
        };
    }

    function drawChart(animPct) {
        var rows = chart.rows;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        var L = chartLayout();
        if (!rows.length || L.w <= 0 || L.h <= 0) return;

        var min = Infinity, max = -Infinity;
        rows.forEach(function (r) { if (r.low < min) min = r.low; if (r.high > max) max = r.high; });
        var span = (max - min) || 1;
        var lo = min - span * 0.06, hi = max + span * 0.06;
        chart.lo = lo; chart.hi = hi;
        function py(v) { return L.price.y + (1 - (v - lo) / (hi - lo)) * L.price.h; }

        var n = rows.length;
        var step = L.price.w / n;
        var bw = Math.max(1, Math.min(step * 0.7, 13));
        var clipW = L.price.w * (animPct == null ? 1 : animPct);
        var longRange = chart.range.indexOf('year') >= 0;

        /* grid + y-axis */
        var ticks = niceTicks(lo, hi);
        ctx.font = '11px ui-monospace, Menlo, Monaco, Consolas, monospace';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        ticks.forEach(function (t) {
            var yy = py(t);
            ctx.strokeStyle = col('--border', 0.35);
            ctx.setLineDash([3, 4]);
            ctx.beginPath(); ctx.moveTo(L.price.x, yy); ctx.lineTo(L.price.x + L.price.w, yy); ctx.stroke();
            ctx.setLineDash([]);
            ctx.fillStyle = col('--ink-5');
            ctx.fillText(fmtAxis(t), L.price.x + L.price.w + 8, yy);
        });

        /* candles */
        ctx.save();
        ctx.beginPath();
        ctx.rect(L.price.x, L.price.y, clipW, L.price.h);
        ctx.clip();
        for (var i = 0; i < n; i++) {
            var r = rows[i];
            var x = L.price.x + (i + 0.5) * step;
            if (x > L.price.x + clipW) break;
            var up = r.close >= r.open;
            var c = up ? col('--pos') : col('--neg');
            ctx.strokeStyle = c;
            ctx.fillStyle = c;
            ctx.beginPath(); ctx.moveTo(x, py(r.high)); ctx.lineTo(x, py(r.low)); ctx.stroke();
            var yo = py(r.open), yc = py(r.close);
            var top = Math.min(yo, yc);
            var bh = Math.abs(yc - yo);
            if (bh < 1) { top = up ? yo - 1 : yo; bh = 1; }
            ctx.fillRect(x - bw / 2, top, bw, bh);
        }
        ctx.restore();

        /* volume pane */
        var vmax = 1;
        rows.forEach(function (r) { if (r.volume > vmax) vmax = r.volume; });
        var vBase = L.vol.y + L.vol.h;
        ctx.strokeStyle = col('--border', 0.5);
        ctx.beginPath(); ctx.moveTo(L.price.x, L.vol.y - 1); ctx.lineTo(L.price.x + L.price.w, L.vol.y - 1); ctx.stroke();
        ctx.save();
        ctx.beginPath();
        ctx.rect(L.price.x, L.vol.y, clipW, L.vol.h);
        ctx.clip();
        for (i = 0; i < n; i++) {
            r = rows[i];
            x = L.price.x + (i + 0.5) * step;
            if (x > L.price.x + clipW) break;
            var vh = Math.max(1, (r.volume / vmax) * L.vol.h);
            ctx.fillStyle = r.close >= r.open ? col('--pos', 0.32) : col('--neg', 0.32);
            ctx.fillRect(x - bw / 2, vBase - vh, bw, vh);
        }
        ctx.restore();

        /* date axis */
        ctx.font = '10px ui-monospace, Menlo, Monaco, Consolas, monospace';
        ctx.fillStyle = col('--ink-5');
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        var labelN = Math.min(6, n);
        var idxStep = Math.max(1, Math.round((n - 1) / (labelN - 1)));
        for (i = 0; i < n; i += idxStep) {
            var lbl = axisDateLabel(rows[i].d, longRange);
            ctx.fillText(lbl, L.price.x + (i + 0.5) * step, L.dateY);
        }
        ctx.fillText(axisDateLabel(rows[n - 1].d, longRange), L.price.x + (n - 0.5) * step, L.dateY);

        /* crosshair */
        if (chart.hover >= 0 && chart.hover < n) {
            var hx = L.price.x + (chart.hover + 0.5) * step;
            var myy = Math.min(L.price.y + L.price.h, Math.max(L.price.y, chart.mouseY));
            ctx.strokeStyle = col('--gold-text', 0.6);
            ctx.setLineDash([3, 3]);
            ctx.beginPath(); ctx.moveTo(hx, L.price.y); ctx.lineTo(hx, L.vol.y - 1); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(L.price.x, myy); ctx.lineTo(L.price.x + L.price.w, myy); ctx.stroke();
            ctx.setLineDash([]);
            var pval = lo + (1 - (myy - L.price.y) / L.price.h) * (hi - lo);
            var plabel = fmtAxis(pval);
            ctx.font = 'bold 10px ui-monospace, Menlo, Monaco, Consolas, monospace';
            ctx.textAlign = 'left';
            var tw = ctx.measureText(plabel).width;
            var chipY = myy - 9;
            ctx.fillStyle = col('--gold-text');
            roundRectPath(ctx, L.price.x + L.price.w + 4, chipY, tw + 10, 17, 4);
            ctx.fill();
            ctx.fillStyle = col('--gold-active-ink');
            ctx.fillText(plabel, L.price.x + L.price.w + 9, chipY + 8.5);
        }
    }

    function animateChart() {
        if (reducedMotion) { drawChart(1); return; }
        var start = performance.now();
        var dur = 420;
        function frame(now) {
            var p = Math.min(1, (now - start) / dur);
            drawChart(1 - Math.pow(1 - p, 3));
            if (p < 1) requestAnimationFrame(frame);
        }
        requestAnimationFrame(frame);
    }

    function fitCanvas() {
        var rect = canvas.getBoundingClientRect();
        var dpr = window.devicePixelRatio || 1;
        var w = Math.round(rect.width * dpr);
        var h = Math.round(rect.height * dpr);
        if (canvas.width !== w || canvas.height !== h) {
            canvas.width = w;
            canvas.height = h;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            drawChart(1);
        }
    }

    function showChartState(html) {
        if (html) {
            stateEl.innerHTML = html;
            stateEl.classList.remove('hidden');
        } else {
            stateEl.classList.add('hidden');
            stateEl.innerHTML = '';
        }
    }

    function loadChartRange(range, quiet) {
        chart.range = range;
        if (!quiet) showChartState(sdLoading('Fetching price data\u2026'));
        fetchJSON(apiUrl('historical/{sym}/{exch}/' + encodeURIComponent(range)), function (rows) {
            var data = (rows || []).filter(function (r) { return r.close != null && r.high != null && r.low != null; });
            data.forEach(function (r) { r.d = parseDateStr(r.date); });
            if (!data.length) {
                showChartState(sdNote('No price history', 'No trading data came back for ' + CONF.symbol + ' over the last ' + range + '.'), 'chart');
                return;
            }
            chart.rows = data;
            showChartState(null);
            setTape(data[data.length - 1], data);
            animateChart();
        }, function () {
            showChartState(sdError('The price feed for ' + CONF.symbol + ' didn\u2019t respond. Check the symbol and try again.', 'chart'));
        });
    }
    retryHandlers.chart = function () { loadChartRange(chart.range, true); };

    var tabs = document.querySelectorAll('#sdRangeTabs .sd-tab');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');
            loadChartRange(tab.getAttribute('data-range'));
        });
    });

    canvas.addEventListener('mousemove', function (e) {
        if (!chart.rows.length) return;
        var rect = canvas.getBoundingClientRect();
        var mx = e.clientX - rect.left;
        var my = e.clientY - rect.top;
        var L = chartLayout();
        var step = L.price.w / chart.rows.length;
        var i = Math.floor((mx - L.price.x) / step);
        if (i < 0 || i >= chart.rows.length) {
            chart.hover = -1;
        } else {
            chart.hover = i;
            chart.mouseY = my;
            setTape(chart.rows[i], chart.rows);
        }
        drawChart(1);
    });
    canvas.addEventListener('mouseleave', function () {
        chart.hover = -1;
        if (chart.rows.length) setTape(chart.rows[chart.rows.length - 1], chart.rows);
        drawChart(1);
    });

    if (window.ResizeObserver) {
        new ResizeObserver(function () { fitCanvas(); }).observe(canvas.parentElement);
    } else {
        window.addEventListener('resize', function () { fitCanvas(); });
    }
    document.documentElement.addEventListener('themechange', function () { fitCanvas(); });
    if (canvas.parentElement.getBoundingClientRect().width) {
        fitCanvas();
        loadChartRange(chart.range);
    }

    /* ================= COMPANY DOSSIER ================= */

    var profileEl = el('sdProfile');
    var snapEl = el('sdSnapshot');
    var summaryEyebrow = el('sdSummaryEyebrow');

    function chipHtml(label, primary) {
        return '<span class="sd-chip">' + (primary ? '<span class="sd-dot" aria-hidden="true"></span>' : '') + esc(label) + '</span>';
    }
    function metaItem(label, valueHtml) {
        return '<div class="sd-meta"><span class="sd-meta-label">' + esc(label) + '</span>' + valueHtml + '</div>';
    }

    function renderProfile(prof) {
        var chips = '<div class="flex flex-wrap gap-2 mb-4">' +
            (prof.sector ? chipHtml(prof.sector, true) : '') +
            (prof.industry ? chipHtml(prof.industry) : '') +
            '</div>';
        var desc = prof.longBusinessSummary
            ? '<p class="sd-business">' + esc(prof.longBusinessSummary) + '</p>'
            : sdNote('No company description', 'This data feed didn\u2019t return a business summary for ' + CONF.symbol + '.');
        var meta = [];
        if (prof.website) {
            var host = '';
            try { host = new URL(prof.website).hostname.replace(/^www\./, ''); } catch (e) { host = prof.website; }
            meta.push(metaItem('Website', '<a class="sd-meta-value" href="' + esc(prof.website) + '" target="_blank" rel="noopener">' + esc(host) + '</a>'));
        }
        if (prof.city || prof.country) {
            meta.push(metaItem('Headquarters', '<span class="sd-meta-value">' + esc([prof.address1, prof.city, prof.country].filter(Boolean).join(', ')) + '</span>'));
        }
        if (raw(prof.fullTimeEmployees)) {
            meta.push(metaItem('Employees', '<span class="sd-meta-value sd-num">' + fmtInt(raw(prof.fullTimeEmployees)) + '</span>'));
        }
        if (prof.phone) {
            meta.push(metaItem('Phone', '<span class="sd-meta-value sd-num">' + esc(prof.phone) + '</span>'));
        }
        var officers = (prof.officers || prof.companyOfficers || []).slice(0, 4);
        var officersHtml = '';
        if (officers.length) {
            officersHtml = '<div class="sd-officers">' + officers.map(function (o) {
                return '<div class="sd-officer"><span class="sd-officer-name">' + esc(o.name || '') + '</span><span class="sd-officer-title">' + esc(o.title || '') + '</span></div>';
            }).join('') + '</div>';
        }
        profileEl.innerHTML = '<div class="sd-fade">' + chips + desc +
            (meta.length ? '<div class="sd-meta-grid">' + meta.join('') + '</div>' : '') +
            officersHtml + '</div>';
    }

    function renderSnapshot(fd, dk) {
        var spot = raw(fd.currentPrice) || CONF.spot || 0;
        var shares = raw(dk.sharesOutstanding);
        var target = raw(fd.targetMeanPrice);
        var rec = raw(fd.recommendationKey);
        var analysts = raw(fd.numberOfAnalystOpinions);
        var fwdPe = raw(dk.forwardPE);
        var p2b = raw(dk.priceToBook);
        var beta = raw(dk.beta);
        var roe = raw(fd.returnOnEquity);
        var pm = raw(fd.profitMargins);
        var rev = raw(fd.totalRevenue);
        var revG = raw(fd.revenueGrowth);
        var earnG = raw(fd.earningsGrowth);
        var d2e = raw(fd.debtToEquity);
        var cash = raw(fd.totalCash);
        var debt = raw(fd.totalDebt);
        var fcf = raw(fd.freeCashflow);
        var ebitda = raw(fd.ebitda);
        var rows = [];
        function add(label, value, cls) {
            if (value === null || value === undefined || value === '') return;
            rows.push('<div class="sd-spec-row"><span class="sd-spec-label">' + esc(label) + '</span><span class="sd-spec-value sd-num ' + (cls || '') + '">' + value + '</span></div>');
        }
        if (spot && shares) add('Market cap', fmtLarge(spot * shares));
        if (target > 0) add('Target price', fmtPrice(target));
        if (rec) {
            var recCls = rec === 'buy' ? 'sd-pos' : (rec === 'sell' ? 'sd-neg' : '');
            add('Recommendation', esc(String(rec).replace(/_/g, ' ')), recCls);
        }
        if (analysts) add('Analyst coverage', fmtInt(analysts) + ' analysts');
        if (fwdPe) add('Forward P/E', num(fwdPe));
        if (p2b) add('Price / book', num(p2b, 2));
        if (beta) add('Beta', num(beta, 2));
        var roeStr = pct(roe, 1);
        if (roeStr) add('Return on equity', roeStr);
        var pmStr = pct(pm, 1);
        if (pmStr) add('Profit margin', pmStr);
        if (rev) add('Revenue (TTM)', fmtLarge(rev));
        var rg = pct(revG, 1);
        if (rg) add('Revenue growth', rg, revG >= 0 ? 'sd-pos' : 'sd-neg');
        var eg = pct(earnG, 1);
        if (eg) add('Earnings growth', eg, earnG >= 0 ? 'sd-pos' : 'sd-neg');
        if (d2e !== null && d2e !== undefined) add('Debt / equity', num(d2e, 2));
        if (cash) add('Total cash', fmtLarge(cash));
        if (debt) add('Total debt', fmtLarge(debt));
        if (fcf) add('Free cash flow', fmtLarge(fcf));
        if (ebitda) add('EBITDA', fmtLarge(ebitda));
        if (CONF.dividendYield > 0) add('Dividend yield', (CONF.dividendYield * 100).toFixed(2) + '%');

        if (!rows.length) {
            snapEl.innerHTML = sdNote('No financial data', 'The fundamentals feed didn\u2019t return values for ' + CONF.symbol + '.');
            return;
        }
        snapEl.innerHTML = '<div class="sd-fade sd-spec">' + rows.join('') + '</div>';
    }

    function loadSummary() {
        profileEl.innerHTML = sdLoading('Reading company profile\u2026');
        snapEl.innerHTML = sdLoading('Pulling the numbers\u2026');
        summaryEyebrow.textContent = 'Fetching from the data feed\u2026';
        fetchJSON(apiUrl('summary/{sym}/{exch}') + '?modules=summaryProfile,assetProfile,financialData,defaultKeyStatistics', function (data) {
            var prof = data.summaryProfile || data.assetProfile || {};
            renderProfile(prof);
            renderSnapshot(data.financialData || {}, data.defaultKeyStatistics || {});
            summaryEyebrow.textContent = CONF.symbol + ' \u00B7 ' + (prof.sector || CONF.exchange);
        }, function () {
            profileEl.innerHTML = sdError('The profile feed didn\u2019t respond for ' + CONF.symbol + '.', 'summary');
            snapEl.innerHTML = '';
            summaryEyebrow.textContent = 'Profile unavailable';
        });
    }
    retryHandlers.summary = loadSummary;

    /* ================= CORPORATE EVENTS ================= */

    var divCount = el('sdDivCount');
    var splitCount = el('sdSplitCount');

    function renderDividendRows(rows) {
        var list = rows.slice().sort(function (a, b) { return b.date < a.date ? -1 : 1; });
        divCount.textContent = String(list.length);
        if (!list.length) {
            el('sdDivRows').innerHTML = sdNote('No dividends', 'No dividend payouts for ' + CONF.symbol + ' in the last 5 years.');
            return;
        }
        var html = list.map(function (r) {
            var d = parseDateStr(r.date);
            return '<div class="sd-ledger-row"><span class="sd-ledger-date sd-num">' + tapeDateLabel(d) + '</span><span class="sd-ledger-amt sd-amt-gold sd-num">' + fmtPrice(r.dividend) + '</span></div>';
        }).join('');
        el('sdDivRows').innerHTML = '<div class="sd-fade">' + html + '</div>';
    }

    function renderSplitRows(rows) {
        var list = rows.slice().sort(function (a, b) { return b.date < a.date ? -1 : 1; });
        splitCount.textContent = String(list.length);
        if (!list.length) {
            el('sdSplitsPanel').style.display = 'none';
            el('sdDividendsPanel').classList.add('lg:col-span-2');
            el('sdSplitRows').innerHTML = '';
            return;
        }
        el('sdSplitsPanel').style.display = '';
        el('sdDividendsPanel').classList.remove('lg:col-span-2');
        var html = list.map(function (r) {
            var d = parseDateStr(r.date);
            var ratio = String(r.split || '').replace(':', '-for-');
            return '<div class="sd-ledger-row"><span class="sd-ledger-date sd-num">' + tapeDateLabel(d) + '</span><span class="sd-ledger-amt sd-num">' + esc(ratio) + '</span></div>';
        }).join('');
        el('sdSplitRows').innerHTML = '<div class="sd-fade">' + html + '</div>';
    }

    function loadEvents() {
        el('sdDivRows').innerHTML = sdLoading('Reading dividend ledger\u2026');
        el('sdSplitRows').innerHTML = sdLoading('Reading split ledger\u2026');
        fetchJSON(apiUrl('dividends/{sym}/{exch}/5 years'), renderDividendRows, function () {
            el('sdDivRows').innerHTML = sdError('Dividend history unavailable for ' + CONF.symbol + '.', 'dividends');
            divCount.textContent = '\u2013';
        });
        fetchJSON(apiUrl('splits/{sym}/{exch}/5 years'), renderSplitRows, function () {
            el('sdSplitRows').innerHTML = sdError('Split history unavailable for ' + CONF.symbol + '.', 'splits');
            splitCount.textContent = '\u2013';
        });
    }
    retryHandlers.dividends = loadEvents;
    retryHandlers.splits = loadEvents;

    /* ================= OPTIONS CHAIN ================= */

    var optEyebrow = el('sdOptEyebrow');
    var optExpiries = el('sdOptExpiries');
    var optScroll = el('sdOptScroll');
    var optState = el('sdOptState');
    var optSection = el('sdOptionsSection');

    function fmtExpiry(d) {
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: '2-digit', timeZone: 'UTC' }).replace(/ /g, ' ');
    }
    function optCell(v, cls) {
        return '<td class="sd-num ' + (cls || '') + '">' + (v === null || v === undefined || v === '' || isNaN(v) ? '\u2014' : v) + '</td>';
    }

    function renderOptionsTable(opt, spot) {
        if (!opt) {
            optState.innerHTML = sdNote('No contracts', 'No contracts were returned for this expiry.');
            optScroll.innerHTML = '';
            return;
        }
        var callsByStrike = {};
        var putsByStrike = {};
        (opt.calls || []).forEach(function (c) { callsByStrike[c.strike] = c; });
        (opt.puts || []).forEach(function (p) { putsByStrike[p.strike] = p; });
        var strikes = Array.from(new Set(Object.keys(callsByStrike).concat(Object.keys(putsByStrike)).map(Number)))
            .sort(function (a, b) { return b - a; });

        if (!strikes.length) {
            optState.innerHTML = sdNote('No contracts', 'No contracts were returned for this expiry.');
            optScroll.innerHTML = '';
            return;
        }

        var nearest = strikes.reduce(function (acc, s) { return Math.abs(s - spot) < Math.abs(acc - spot) ? s : acc; }, strikes[0]);

        var head =
            '<table class="sd-opt-table"><thead>' +
            '<tr class="sd-opt-group-head">' +
            '<th rowspan="2" class="text-left" style="min-width:70px">Strike</th>' +
            '<th colspan="6" class="sd-opt-side-calls">Calls</th>' +
            '<th colspan="6" class="sd-opt-side-puts">Puts</th>' +
            '</tr>' +
            '<tr class="sd-opt-col-head">' +
            '<th>Last</th><th>Chg</th><th>Bid/Ask</th><th>Vol</th><th>OI</th><th>IV</th>' +
            '<th>IV</th><th>OI</th><th>Vol</th><th>Bid/Ask</th><th>Chg</th><th>Last</th>' +
            '</tr></thead><tbody>';

        var body = strikes.map(function (s) {
            var call = callsByStrike[s];
            var put = putsByStrike[s];
            var strikeCls = s === nearest ? 'sd-strike-atm' : (s < spot ? 'sd-strike-pos' : 'sd-strike-neg');
            function lastCell(c, sideCls) {
                if (!c) return optCell('\u2014', 'sd-opt-cell-dim');
                var cls = c.inTheMoney ? sideCls : '';
                return optCell(fmtPrice(c.lastPrice), cls);
            }
            function chgCell(c) {
                if (!c || c.percentChange === null || c.percentChange === undefined) return optCell('\u2014', 'sd-opt-cell-dim');
                var cls = c.percentChange >= 0 ? 'sd-opt-cell-pos' : 'sd-opt-cell-neg';
                return optCell((c.percentChange >= 0 ? '+' : '') + (+c.percentChange).toFixed(1) + '%', cls);
            }
            function bidAskCell(c) {
                if (!c) return optCell('\u2014', 'sd-opt-cell-dim');
                if ((!c.bid || !c.ask)) return optCell('\u2014', 'sd-opt-cell-dim');
                return '<td class="sd-opt-bidask sd-num">' + num(c.bid, 2) + ' / ' + num(c.ask, 2) + '</td>';
            }
            function volCell(c) {
                if (!c) return optCell('\u2014', 'sd-opt-cell-dim');
                return optCell(fmtCompact(c.volume), 'sd-opt-cell-dim');
            }
            function oiCell(c) {
                if (!c) return optCell('\u2014', 'sd-opt-cell-dim');
                return optCell(fmtCompact(c.openInterest), 'sd-opt-cell-dim');
            }
            function ivCell(c) {
                if (!c || c.impliedVolatility === null || c.impliedVolatility === undefined) return optCell('\u2014', 'sd-opt-cell-dim');
                return optCell((+c.impliedVolatility * 100).toFixed(1) + '%', 'sd-opt-cell-dim');
            }
            return '<tr>' +
                '<td class="sd-opt-strike ' + strikeCls + ' sd-num">' + (+s).toLocaleString('en-IN', { maximumFractionDigits: 0 }) + '</td>' +
                lastCell(call, 'sd-opt-cell-pos') +
                chgCell(call) +
                bidAskCell(call) +
                volCell(call) +
                oiCell(call) +
                ivCell(call) +
                ivCell(put) +
                oiCell(put) +
                volCell(put) +
                bidAskCell(put) +
                chgCell(put) +
                lastCell(put, 'sd-opt-cell-neg') +
                '</tr>';
        }).join('');

        optScroll.innerHTML = head + body + '</tbody></table>';
        optState.innerHTML = '';
    }

    function loadOptions() {
        optExpiries.innerHTML = '';
        optScroll.innerHTML = '';
        optState.innerHTML = sdLoading('Loading the option chain\u2026');
        optEyebrow.textContent = 'Fetching\u2026';
        fetchJSON(apiUrl('options/{sym}/{exch}'), function (chains) {
            var chain = (chains || [])[0];
            if (!chain || !chain.options || !chain.options.length) {
                optSection.style.display = 'none';
                return;
            }
            optSection.style.display = '';
            optEyebrow.textContent = 'Underlying \u00B7 ' + esc(chain.underlyingSymbol || CONF.symbol);
            var dates = chain.expirationDates || [];
            var optionsByDate = {};
            (chain.options || []).forEach(function (o) { if (o.expirationDate) optionsByDate[o.expirationDate] = o; });

            var active = null;
            function pick(dateStr) {
                active = dateStr;
                optExpiries.querySelectorAll('.sd-tab').forEach(function (t) {
                    var on = t.getAttribute('data-date') === dateStr;
                    t.classList.toggle('active', on);
                    t.setAttribute('aria-selected', on ? 'true' : 'false');
                });
                renderOptionsTable(optionsByDate[dateStr] || null, CONF.spot || 0);
            }
            if (dates.length) {
                var pills = dates.slice(0, 12).map(function (ds) {
                    return '<button type="button" class="sd-tab" data-date="' + esc(ds) + '" role="tab">' + fmtExpiry(parseDateStr(ds)) + '</button>';
                }).join('');
                optExpiries.innerHTML = pills;
                optExpiries.querySelectorAll('.sd-tab').forEach(function (t) {
                    t.addEventListener('click', function () { pick(t.getAttribute('data-date')); });
                });
                pick(dates[0]);
            } else {
                optState.innerHTML = sdNote('No expiries', 'The option chain for ' + CONF.symbol + ' has no expiration dates.');
            }
        }, function () {
            optEyebrow.textContent = 'Chain unavailable';
            optState.innerHTML = sdError('The option chain feed didn\u2019t respond for ' + CONF.symbol + '.', 'options');
        });
    }
    retryHandlers.options = loadOptions;

    /* ================= GO ================= */

    loadSummary();
    loadEvents();
    loadOptions();
})();
</script>
<script>
function refreshStock() {
    var btn = document.getElementById('refreshBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Refreshing...';
    var csrf = document.querySelector('input[name="csrf_test_name"]') || document.querySelector('[name="csrf_test_name"]');
    var token = csrf ? csrf.value : '';
    fetch('/api/stocks/refresh', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'csrf_test_name=' + encodeURIComponent(token) + '&id=<?= $stock["id"] ?>'
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) { window.location.reload(); }
        else { alert(d.message); btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> Refresh'; }
    })
    .catch(function() { alert('Refresh failed.'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> Refresh'; });
}
</script>


