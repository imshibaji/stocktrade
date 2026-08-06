    </main>

    <?php if (!is_logged_in()): ?>
    <footer class="bg-surface border-t border-gray-700 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-accent font-bold text-lg mb-3"><?= esc(site_name()) ?></h3>
                    <p class="text-gray-400 text-sm">Your trusted platform for stock analysis, predictions, and investment tracking.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/" class="hover:text-accent transition">Home</a></li>
                        <li><a href="/about" class="hover:text-accent transition">About Us</a></li>
                        <li><a href="/pricing" class="hover:text-accent transition">Pricing</a></li>
                        <li><a href="/stocks" class="hover:text-accent transition">Stocks</a></li>
                        <li><a href="/contact" class="hover:text-accent transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Documentation</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/docs/user" class="hover:text-accent transition">User Guide</a></li>
                        <li><a href="/docs/developer" class="hover:text-accent transition">Developer Docs</a></li>
                        <li><a href="/api-playground" class="hover:text-accent transition">API Playground</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Legal</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/terms" class="hover:text-accent transition">Terms and Conditions</a></li>
                        <li><a href="/privacy" class="hover:text-accent transition">Privacy Policy</a></li>
                        <li><a href="/faq" class="hover:text-accent transition">FAQ</a></li>
                        <li><a href="/pricing" class="hover:text-accent transition">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Features</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><i class="fas fa-check text-green-500 mr-1"></i> Stock Analysis</li>
                        <li><i class="fas fa-check text-green-500 mr-1"></i> Future Predictions</li>
                        <li><i class="fas fa-check text-green-500 mr-1"></i> Portfolio Tracking</li>
                        <li><i class="fas fa-check text-green-500 mr-1"></i> Tax Calculations</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Disclaimer</h4>
                    <p class="text-gray-500 text-xs">Stock market investments are subject to market risks. Past performance is not indicative of future results. Predictions are AI-generated and should not be considered as financial advice.</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-500 text-sm">
                &copy; <?= date('Y') ?> <?= esc(site_name()) ?>. All rights reserved. Built with CodeIgniter 4. Developed by <a href="https://www.shibajidebnath.com" class="text-accent hover:underline" target="_blank" rel="noopener">Shibaji Debnath</a>.
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <?php if (is_logged_in()): ?>
    <footer class="bg-surface border-t border-gray-700 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-4 text-center text-gray-500 text-xs">
            &copy; <?= date('Y') ?> <?= esc(site_name()) ?>. Market data powered by Yahoo Finance. Not financial advice. Developed by <a href="https://www.shibajidebnath.com" class="text-accent hover:underline" target="_blank" rel="noopener">Shibaji Debnath</a>.
        </div>
    </footer>
    <?php endif; ?>

    <script>
    (function () {
        'use strict';
        var badge = document.getElementById('marketBadge');
        if (!badge || !badge.getAttribute('data-exchange')) return;

        var stockId = badge.getAttribute('data-stock-id');
        var ZONES = {
            NSE: 'Asia/Kolkata', BSE: 'Asia/Kolkata', NSI: 'Asia/Kolkata',
            NMS: 'America/New_York', NYQ: 'America/New_York', NGM: 'America/New_York',
            NASDAQ: 'America/New_York', NYSE: 'America/New_York', AMEX: 'America/New_York',
            TSX: 'America/Toronto', LSE: 'Europe/London', FRA: 'Europe/Berlin',
            ETR: 'Europe/Berlin', XETRA: 'Europe/Berlin', EURONEXT: 'Europe/Paris',
            ASX: 'Australia/Sydney', TSE: 'Asia/Tokyo', TYO: 'Asia/Tokyo',
            HKEX: 'Asia/Hong_Kong', HKG: 'Asia/Hong_Kong', KRX: 'Asia/Seoul',
            SES: 'Asia/Singapore', SGX: 'Asia/Singapore'
        };
        var US = ['NMS', 'NYQ', 'NGM', 'NASDAQ', 'NYSE', 'AMEX'];

        var CLOSED = { label: 'Closed', dot: 'text-gray-500', text: 'text-gray-400', border: 'border-gray-600' };

        function timeStatus(ex) {
            var isUs = US.indexOf(ex) >= 0;
            var preStart = isUs ? 240 : 540;
            var preEnd = isUs ? 570 : 555;
            var regStart = 570;
            var regEnd = isUs ? 960 : 930;
            var postStart = isUs ? 960 : 930;
            var postEnd = isUs ? 1200 : 930;

            var day = '', h = 0, m = 0;
            try {
                var parts = new Intl.DateTimeFormat('en-US', {
                    timeZone: ZONES[ex] || 'Asia/Kolkata',
                    hour12: false, weekday: 'short', hour: '2-digit', minute: '2-digit'
                }).formatToParts(new Date());
                parts.forEach(function (p) {
                    if (p.type === 'weekday') day = p.value;
                    if (p.type === 'hour') h = parseInt(p.value, 10) % 24;
                    if (p.type === 'minute') m = parseInt(p.value, 10);
                });
            } catch (e) {
                return CLOSED;
            }
            var mins = h * 60 + m;
            if (day === 'Sat' || day === 'Sun') return CLOSED;
            if (mins >= preStart && mins < preEnd) return { label: 'Pre-Market', dot: 'text-yellow-400', text: 'text-yellow-300', border: 'border-yellow-600/60' };
            if (mins >= regStart && mins < regEnd) return { label: 'Open', dot: 'text-green-400', text: 'text-green-300', border: 'border-green-600/60' };
            if (mins >= postStart && mins < postEnd) return { label: 'Post-Market', dot: 'text-yellow-400', text: 'text-yellow-300', border: 'border-yellow-600/60' };
            return CLOSED;
        }

        function apply(s, marketRegion) {
            var ex = (badge.getAttribute('data-exchange') || 'NSE').toUpperCase();
            badge.className = 'text-xs px-3 py-1 rounded-full border ' + s.border + ' ' + s.text;
            badge.innerHTML = '<i class="fas fa-circle ' + s.dot + ' text-[8px] mr-1"></i>' + s.label;
            var title = ex + ' market \u00B7 ' + s.label;
            if (marketRegion) title = ex + ' (' + marketRegion + ') \u00B7 ' + s.label;
            badge.setAttribute('title', title);
            if (s.state) badge.setAttribute('data-market-state', s.state);
        }

        function runTimer() {
            apply(timeStatus(badge.getAttribute('data-exchange') || 'NSE'));
            setInterval(function () {
                apply(timeStatus(badge.getAttribute('data-exchange') || 'NSE'));
            }, 60000);
        }

        if (stockId) {
            fetch('/api/market-status/' + encodeURIComponent(stockId), { headers: { Accept: 'application/json' } })
                .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(function (d) {
                    if (d && d.status) {
                        apply(d.status, d.market);
                        badge.setAttribute('data-market-state', d.marketState || d.status.state || '');
                    }
                })
                .catch(function () { runTimer(); });
        } else {
            runTimer();
        }
    })();
    </script>

</body>
</html>
