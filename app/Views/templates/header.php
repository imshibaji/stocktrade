<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'StockTrade Tips') ?></title>
    <link rel="stylesheet" href="/css/tailwind.css">
    <?php if (!empty($showChartJs)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-navy text-gray-100 min-h-screen">

<?php if (is_logged_in()): ?>

    <nav class="bg-navy2 border-b border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">

                <!-- Left: Logo + market badge -->
                <div class="flex items-center space-x-4 shrink-0">
                    <a href="/dashboard" class="flex items-center space-x-2">
                        <i class="fas fa-chart-line text-gold text-2xl"></i>
                        <span class="text-xl font-bold text-gold hidden sm:inline">StockTrade<span class="text-white">Tips</span></span>
                    </a>
                    <span id="navMarketBadge" class="hidden md:inline text-xs px-2 py-1 rounded-full border border-gray-600 text-gray-400">
                        <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>
                    </span>
                </div>

                <!-- Center: Search bar (desktop) -->
                <div class="hidden md:block flex-1 max-w-lg mx-4">
                    <div class="relative">
                        <input type="text" id="navSearch" placeholder="Search symbol, name or exchange..."
                            autocomplete="off"
                            class="w-full bg-navy border border-gray-600 rounded-lg pl-10 pr-3 py-2 text-sm text-white placeholder-gray-500 focus:border-gold focus:outline-none">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <div id="navSearchDropdown" class="hidden absolute top-full left-0 mt-1 w-full bg-navy2 border border-gray-600 rounded-lg shadow-xl z-50 max-h-80 overflow-y-auto"></div>
                    </div>
                </div>

                <!-- Right: Dashboard + User dropdown + hamburger -->
                <div class="flex items-center space-x-2">
                    <a href="/dashboard" class="hidden md:flex px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-navy transition items-center">
                        <i class="fas fa-tachometer-alt mr-1.5"></i>Dashboard
                    </a>
                    <div class="relative hidden md:block" id="userDropdown">
                        <button onclick="toggleUserNav()" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-navy transition">
                            <i class="fas fa-user-circle text-gold"></i>
                            <span class="max-w-[100px] truncate"><?= esc(current_user()['name']) ?></span>
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </button>
                        <div id="userDropdownMenu" class="hidden absolute top-full right-0 mt-1 w-52 bg-navy2 border border-gray-600 rounded-lg shadow-xl z-50 py-1">
                            <a href="/watchlist" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                <i class="fas fa-star w-5 mr-2 text-yellow-400"></i>Watchlist
                            </a>
                            <a href="/investments" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                <i class="fas fa-briefcase w-5 mr-2 text-blue-400"></i>Investments
                            </a>
                            <a href="/portfolio" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                <i class="fas fa-chart-pie w-5 mr-2 text-purple-400"></i>Portfolio
                            </a>
                            <a href="/investments/transactions" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                <i class="fas fa-exchange-alt w-5 mr-2 text-teal-400"></i>Transactions
                            </a>
                            <a href="/screener" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                <i class="fas fa-filter w-5 mr-2 text-indigo-400"></i>Screener
                            </a>
                            <a href="/stocks" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                <i class="fas fa-search w-5 mr-2 text-green-400"></i>Stocks
                            </a>
                            <hr class="border-gray-700 my-1">
                            <a href="/api-playground" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                <i class="fas fa-code w-5 mr-2 text-gold"></i>API Docs
                            </a>
                            <hr class="border-gray-700 my-1">
                            <a href="/settings" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                <i class="fas fa-cog w-5 mr-2 text-gray-400"></i>Settings
                            </a>
                            <a href="/logout" class="flex items-center px-4 py-2.5 text-sm text-red-400 hover:text-red-300 hover:bg-navy transition">
                                <i class="fas fa-sign-out-alt w-5 mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                    <button class="md:hidden text-gray-300" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-700">
            <div class="px-4 py-3">
                <div class="flex items-center space-x-3 mb-3">
                    <i class="fas fa-chart-line text-gold text-xl"></i>
                    <span class="text-lg font-bold text-gold">StockTrade<span class="text-white">Tips</span></span>
                </div>
                <div class="relative mb-3">
                    <input type="text" id="mobileSearch" placeholder="Search symbol, name or exchange..."
                        autocomplete="off"
                        class="w-full bg-navy border border-gray-600 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-gray-500 focus:border-gold focus:outline-none">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <div id="mobileSearchDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-navy2 border border-gray-600 rounded-lg shadow-xl z-50 max-h-60 overflow-y-auto"></div>
                </div>
                <hr class="border-gray-700 my-2">
                <div class="space-y-1">
                    <a href="/dashboard" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                        <i class="fas fa-tachometer-alt w-5 mr-2"></i>Dashboard
                    </a>
                    <a href="/watchlist" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                        <i class="fas fa-star w-5 mr-2 text-yellow-400"></i>Watchlist
                    </a>
                    <a href="/investments" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                        <i class="fas fa-briefcase w-5 mr-2 text-blue-400"></i>Investments
                    </a>
                    <a href="/portfolio" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                        <i class="fas fa-chart-pie w-5 mr-2 text-purple-400"></i>Portfolio
                    </a>
                    <a href="/investments/transactions" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                        <i class="fas fa-exchange-alt w-5 mr-2 text-teal-400"></i>Transactions
                    </a>
                    <a href="/screener" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                        <i class="fas fa-filter w-5 mr-2 text-indigo-400"></i>Screener
                    </a>
                    <a href="/stocks" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                        <i class="fas fa-search w-5 mr-2 text-green-400"></i>Stocks
                    </a>
                    <hr class="border-gray-700 my-2">
                    <a href="/api-playground" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                        <i class="fas fa-code w-5 mr-2 text-gold"></i>API Docs
                    </a>
                    <hr class="border-gray-700 my-2">
                    <a href="/settings" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                        <i class="fas fa-cog w-5 mr-2"></i>Settings
                    </a>
                    <a href="/logout" class="flex items-center px-3 py-2 rounded-lg text-red-400 hover:text-red-300 hover:bg-navy">
                        <i class="fas fa-sign-out-alt w-5 mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>

        <script>
        function toggleUserNav() {
            document.getElementById('userDropdownMenu').classList.toggle('hidden');
        }
        document.addEventListener('click', function(e) {
            var dd = document.getElementById('userDropdown');
            if (dd && !dd.contains(e.target)) {
                var menu = document.getElementById('userDropdownMenu');
                if (menu) menu.classList.add('hidden');
            }
        });

        (function() {
            var si = document.getElementById('mobileSearch');
            var sd = document.getElementById('mobileSearchDropdown');
            var t = null;
            if (!si || !sd) return;

            function eh(s) {
                if (!s) return '';
                var d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            }

            si.addEventListener('input', function() {
                var v = this.value.trim();
                if (t) clearTimeout(t);
                if (v.length < 2) { sd.classList.add('hidden'); return; }
                t = setTimeout(function() {
                    fetch('/api/search?q=' + encodeURIComponent(v))
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            if (!data.results || data.results.length === 0) {
                                sd.innerHTML = '<div class="p-3 text-gray-400 text-sm text-center">No results</div>';
                                sd.classList.remove('hidden');
                                return;
                            }
                            var h = '';
                            data.results.forEach(function(s) {
                                var ps = s.price ? '\u20B9' + parseFloat(s.price).toLocaleString('en-IN', { minimumFractionDigits: 2 }) : '\u2014';
                                var cs = '';
                                if (s.change_percent !== null) {
                                    var cl = s.change_percent >= 0 ? 'text-green-400' : 'text-red-400';
                                    cs = '<span class="' + cl + ' text-xs">' + (s.change_percent >= 0 ? '+' : '') + s.change_percent + '%</span>';
                                }
                                h += '<div class="flex justify-between items-center px-4 py-3 hover:bg-navy border-b border-gray-700/50 last:border-0">' +
                                    '<div class="min-w-0 flex-1"><span class="text-white text-sm font-semibold">' + eh(s.symbol) + '</span> <span class="text-gray-500 text-xs truncate">' + eh(s.name) + '</span></div>' +
                                    '<div class="text-right ml-3"><span class="text-white text-sm">' + ps + '</span> ' + cs + '</div></div>';
                            });
                            sd.innerHTML = h;
                            sd.classList.remove('hidden');
                        })
                        .catch(function() { sd.classList.add('hidden'); });
                }, 250);
            });

            si.addEventListener('blur', function() {
                setTimeout(function() { sd.classList.add('hidden'); }, 200);
            });
        })();
        </script>

    </nav>

<?php else: ?>

    <nav class="bg-navy2 border-b border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <a href="/" class="flex items-center space-x-2">
                        <i class="fas fa-chart-line text-gold text-2xl"></i>
                        <span class="text-xl font-bold text-gold">StockTrade<span class="text-white">Tips</span></span>
                    </a>
                    <div class="hidden md:flex space-x-1">
                        <a href="/" class="px-3 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-navy transition">Home</a>
                        <a href="/about" class="px-3 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-navy transition">About</a>
                        <a href="/contact" class="px-3 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-navy transition">Contact</a>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/login" class="px-4 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-navy transition">Login</a>
                    <a href="/register" class="bg-gold hover:bg-gold2 text-navy font-semibold px-4 py-2 rounded-lg text-sm transition">Get Started</a>
                    <button class="md:hidden text-gray-300" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-700">
            <div class="px-4 py-3 space-y-1">
                <a href="/" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">Home</a>
                <a href="/about" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">About</a>
                <a href="/contact" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">Contact</a>
                <hr class="border-gray-700 my-2">
                <a href="/login" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">Login</a>
                <a href="/register" class="block px-3 py-2 rounded-lg text-gold hover:text-gold2 hover:bg-navy font-semibold">Get Started</a>
            </div>
        </div>
    </nav>

<?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-green-900/50 border border-green-600 text-green-300 px-4 py-3 rounded-lg flex justify-between items-center">
            <span><i class="fas fa-check-circle mr-2"></i><?= esc(session()->getFlashdata('success')) ?></span>
            <button onclick="this.parentElement.remove()" class="text-green-300 hover:text-green-100">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-red-900/50 border border-red-600 text-red-300 px-4 py-3 rounded-lg flex justify-between items-center">
            <span><i class="fas fa-exclamation-circle mr-2"></i><?= esc(session()->getFlashdata('error')) ?></span>
            <button onclick="this.parentElement.remove()" class="text-red-300 hover:text-red-100">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-blue-900/50 border border-blue-600 text-blue-300 px-4 py-3 rounded-lg flex justify-between items-center">
            <span><i class="fas fa-info-circle mr-2"></i><?= esc(session()->getFlashdata('info')) ?></span>
            <button onclick="this.parentElement.remove()" class="text-blue-300 hover:text-blue-100">&times;</button>
        </div>
    </div>
    <?php endif; ?>

<script>
(function() {
    var searchInput = document.getElementById('navSearch');
    var searchDropdown = document.getElementById('navSearchDropdown');
    var timer = null;
    if (!searchInput) return;

    function escHtml(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function importStock(sym) {
        var body = '<?= csrf_token() ?>=' + encodeURIComponent('<?= csrf_hash() ?>') + '&symbol=' + encodeURIComponent(sym);
        var btn = document.querySelector('[data-import="' + sym + '"]');
        if (!btn) return;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        fetch('/api/stocks/import', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
            .then(function(r){ return r.json(); })
            .then(function(d){
                if (d.success) { window.location.href = '/watchlist'; }
                else { alert(d.message); btn.disabled = false; btn.innerHTML = '+ Add'; }
            })
            .catch(function(){ alert('Failed to import.'); if(btn){btn.disabled=false;btn.innerHTML='+ Add';} });
    }

    searchInput.addEventListener('input', function() {
        var val = this.value.trim();
        if (timer) clearTimeout(timer);
        if (val.length < 2) { searchDropdown.classList.add('hidden'); return; }
        timer = setTimeout(function() {
            fetch('/api/search?q=' + encodeURIComponent(val))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (!data.results || data.results.length === 0) {
                        searchDropdown.innerHTML = '<div class="p-3 text-gray-400 text-sm text-center">No results</div>';
                        searchDropdown.classList.remove('hidden');
                        return;
                    }
                    var html = '';
                    data.results.forEach(function(s) {
                        var priceStr = s.price ? '\u20B9' + parseFloat(s.price).toLocaleString('en-IN', { minimumFractionDigits: 2 }) : '\u2014';
                        var changeStr = '';
                        if (s.change_percent !== null) {
                            var cls = s.change_percent >= 0 ? 'text-green-400' : 'text-red-400';
                            changeStr = '<span class="' + cls + ' text-xs">' + (s.change_percent >= 0 ? '+' : '') + s.change_percent + '%</span>';
                        }
                        html += '<div class="flex justify-between items-center px-4 py-3 hover:bg-navy border-b border-gray-700/50 last:border-0">' +
                            '<div class="min-w-0 flex-1">' +
                            '<span class="text-white text-sm font-semibold">' + escHtml(s.symbol) + '</span> ' +
                            '<span class="text-gray-500 text-xs truncate">' + escHtml(s.name) + '</span>' +
                            '</div>' +
                            '<div class="text-right ml-3"><span class="text-white text-sm">' + priceStr + '</span> ' + changeStr + '</div>' +
                            '</div>';
                    });
                    searchDropdown.innerHTML = html;
                    searchDropdown.classList.remove('hidden');
                })
                .catch(function() { searchDropdown.classList.add('hidden'); });
        }, 250);
    });

    searchInput.addEventListener('blur', function() {
        setTimeout(function() { searchDropdown.classList.add('hidden'); }, 200);
    });

    searchInput.addEventListener('focus', function() {
        if (searchDropdown.children.length > 0) {
            searchDropdown.classList.remove('hidden');
        }
    });
})();
</script>

    <main class="max-w-7xl mx-auto px-4 py-8">
