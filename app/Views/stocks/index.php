<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white">Stocks</h1>
                <span id="marketBadge" class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>Checking...
                </span>
            </div>
        </div>
        <div class="flex items-center space-x-2 mt-4 md:mt-0">

            <div class="relative" id="searchContainer">
                <input type="text" id="stockSearch" value="<?= esc($search) ?>" placeholder="Search by symbol or name..."
                    autocomplete="off"
                    class="bg-navy2 border border-gray-600 rounded-lg pl-10 pr-4 py-2 text-white focus:border-gold focus:outline-none w-40 md:w-72">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                <button id="searchClear" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white text-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <button onclick="toggleAddForm()" class="bg-navy2 border border-gold text-gold hover:bg-gold hover:text-navy font-semibold px-4 py-2 rounded-lg transition text-sm">
                <i class="fas fa-plus mr-1"></i> New
            </button>
        </div>
    </div>

    <div id="addStockForm" class="hidden bg-navy2 rounded-xl border border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4">Add New Stock</h3>
        <form id="addStockFormEl" onsubmit="return false;">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Symbol *</label>
                    <input type="text" id="addSymbol" placeholder="e.g. RELIANCE"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-gold focus:outline-none uppercase">
                    <p id="addSymbolStatus" class="text-xs mt-1 hidden"></p>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Exchange</label>
                    <select id="addExchange"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-3 py-2 text-white focus:border-gold focus:outline-none">
                        <option value="NSE" selected>NSE</option>
                        <option value="BSE">BSE</option>
                        <option value="GLOBAL">GLOBAL</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Name</label>
                    <input type="text" id="addName" placeholder="Auto-filled from Yahoo"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-3 py-2 text-white" readonly>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Sector</label>
                    <input type="text" id="addSector" placeholder="Auto-filled from Yahoo"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-3 py-2 text-white" readonly>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Current Price (₹)</label>
                    <input type="text" id="addPrice" placeholder="Auto-filled from Yahoo"
                        class="w-full bg-navy border border-gray-600 rounded-lg px-3 py-2 text-white" readonly>
                </div>
            </div>
            <div class="flex justify-end mt-4 space-x-3">
                <button type="button" onclick="toggleAddForm()" class="px-4 py-2 rounded-lg bg-navy border border-gray-600 text-gray-300 hover:text-white transition">
                    Cancel
                </button>
                <button type="button" id="addSubmitBtn" class="px-6 py-2 rounded-lg bg-gold hover:bg-gold2 text-navy font-semibold transition" disabled onclick="window.importStockFromForm()">
                    <i class="fas fa-plus mr-1"></i> Add Stock
                </button>
            </div>
        </form>
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="/stocks" class="sector-filter px-4 py-2 rounded-lg text-sm transition <?= !$sector ? 'bg-gold text-navy font-semibold' : 'bg-navy2 text-gray-300 border border-gray-600 hover:border-gold' ?>" data-sector="">
            All
        </a>
        <?php foreach ($sectors as $s): ?>
        <a href="/stocks?sector=<?= urlencode($s['sector']) ?>"
           class="sector-filter px-4 py-2 rounded-lg text-sm transition <?= $sector === $s['sector'] ? 'bg-gold text-navy font-semibold' : 'bg-navy2 text-gray-300 border border-gray-600 hover:border-gold' ?>" data-sector="<?= esc($s['sector']) ?>">
            <?= esc($s['sector']) ?>
        </a>
        <?php endforeach; ?>
    </div>

    <div id="searchStatus" class="hidden text-center text-gray-400 text-sm py-2 mb-4">
        <i class="fas fa-spinner fa-spin mr-2"></i> Searching...
    </div>

    <div id="searchResults">
        <?php if (empty($stocks)): ?>
        <div id="emptyState" class="bg-navy2 rounded-xl border border-gray-700 p-12 text-center">
            <i class="fas fa-search text-5xl text-gray-600 mb-4"></i>
            <h3 class="text-white text-lg font-semibold mb-2">No stocks found</h3>
            <p class="text-gray-400">Try adjusting your search or filter criteria.</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="stockCards">
            <?php foreach ($stocks as $stock): ?>
            <?php $change = get_price_change((float) $stock['current_price'], (float) $stock['previous_close']); ?>
            <?php $isWatched = isset($watchlistMap[(int) $stock['id']]); ?>
            <?php $cur = stock_currency($stock['exchange'] ?? null); ?>
            <div class="stock-card bg-navy2 rounded-xl border border-gray-700 hover:border-gold transition relative group" data-sid="<?= $stock['id'] ?>">
                <div class="p-6 cursor-pointer" onclick="goToStock(<?= $stock['id'] ?>)">
                    <button class="watch-btn absolute top-3 right-3 text-xl transition"
                        data-sid="<?= $stock['id'] ?>"
                        data-watched="<?= $isWatched ? '1' : '0' ?>"
                        onclick="event.stopPropagation(); toggleWatch(this)"
                        title="<?= $isWatched ? 'Remove from watchlist' : 'Add to watchlist' ?>">
                        <i class="<?= $isWatched ? 'fas fa-star text-gold' : 'far fa-star text-gray-500 hover:text-gold' ?>"></i>
                    </button>
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-white font-bold text-lg"><?= esc($stock['symbol']) ?></h3>
                            <p class="text-gray-400 text-sm"><?= esc($stock['name']) ?></p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded bg-navy border border-gray-600 text-gray-300 mr-8"><?= esc($stock['sector']) ?></span>
                    </div>
                    <div class="flex justify-between items-end mt-4">
                        <div>
                            <p class="price-value text-2xl font-bold text-white"><?= format_price($stock['current_price'], $cur) ?></p>
                            <p class="text-gray-500 text-xs mt-1 prev-close">
                                Prev Close: <?= format_price($stock['previous_close'], $cur) ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="change-badge px-3 py-1 rounded text-sm font-semibold <?= $change['change'] >= 0 ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400' ?>">
                                <?= $change['change'] >= 0 ? '+' : '' ?><?= $change['percent'] ?>%
                            </span>
                            <?php if ($stock['pe_ratio']): ?>
                            <p class="text-gray-500 text-xs mt-1">P/E: <?= $stock['pe_ratio'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-700 flex">
                    <a href="/stocks/<?= $stock['id'] ?>/edit" onclick="event.stopPropagation()" class="flex-1 text-center py-3 text-gray-400 hover:text-gold hover:bg-navy text-sm transition border-r border-gray-700">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <form action="/stocks/<?= $stock['id'] ?>/delete" method="post" class="flex-1" onsubmit="return confirm('Delete <?= esc($stock['symbol']) ?>? This will remove all related data.');">
                        <?= csrf_field() ?>
                        <button type="submit" class="w-full text-center py-3 text-red-400 hover:text-red-300 hover:bg-red-900/10 text-sm transition" onclick="event.stopPropagation()">
                            <i class="fas fa-trash mr-1"></i>Remove
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
(function() {
    var CSRF_NAME = '<?= csrf_token() ?>';
    var CSRF_HASH = '<?= csrf_hash() ?>';
    var searchInput = document.getElementById('stockSearch');
    var searchClear = document.getElementById('searchClear');
    var searchStatus = document.getElementById('searchStatus');
    var searchResults = document.getElementById('searchResults');
    var searchTimer = null;
    var lastQuery = '';

    function getCSRF() {
        return document.querySelector('input[name="' + CSRF_NAME + '"]')
            || document.querySelector('input[name^="csrf_"]');
    }

    var CURRENCY_SYMBOLS = { 'INR': '\u20B9', 'USD': '\u0024', 'EUR': '\u20AC', 'GBP': '\u00A3', 'JPY': '\u00A5', 'AUD': 'A\u0024', 'CAD': 'C\u0024', 'CHF': 'CHF ', 'CNY': '\u00A5', 'SGD': 'S\u0024' };
    function stockCurrency(exch) { return (exch && (exch.indexOf('NS')>=0||exch.indexOf('BSE')>=0)) ? 'INR' : 'USD'; }
    function formatPrice(v, c) { c = c || 'INR'; var sym = CURRENCY_SYMBOLS[c] || (c + ' '); return sym + parseFloat(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function buildCardHtml(s) {
        var isPos = s.change_percent !== null && s.change_percent >= 0;
        var changePct = s.change_percent !== null ? (s.change_percent >= 0 ? '+' + s.change_percent : s.change_percent) + '%' : 'N/A';
        var sc = s.currency || stockCurrency(s.exchange);
        var priceHtml = s.price ? formatPrice(s.price, sc) : '<span class="text-gray-500">—</span>';
        var prevCloseHtml = s.price ? 'Prev Close: ' + formatPrice(s.price - (s.change || 0), sc) : '';
        var changeClass = isPos ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400';
        var href = s.id ? '/stocks/' + s.id : '#';

        if (s.from_yahoo) {
            var yahooPriceHtml = '';
            var yahooChangeHtml = '';
            if (s.price) {
                var yahooIsPos = s.change_percent >= 0;
                var yahooChangeClass = yahooIsPos ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400';
                var yahooChangePct = (s.change_percent >= 0 ? '+' + s.change_percent : s.change_percent) + '%';
                yahooPriceHtml = '<p class="price-value text-2xl font-bold text-white">' + formatPrice(s.price, sc) + '</p>';
                yahooChangeHtml = '<span class="change-badge px-3 py-1 rounded text-sm font-semibold ' + yahooChangeClass + '">' + yahooChangePct + '</span>';
            }
            return '<div class="stock-card bg-navy2 rounded-xl border border-gray-700 hover:border-gold transition relative group yahoo-result" data-sym="' + (s.symbol || '') + '" data-exch="' + (s.exchange || 'NSE') + '">' +
                '<div class="p-6 cursor-pointer" onclick="importStock(this.parentElement.dataset.sym, this.parentElement.dataset.exch)">' +
                '<div class="flex justify-between items-start mb-3">' +
                '<div>' +
                '<h3 class="text-white font-bold text-lg">' + escHtml(s.symbol) + '</h3>' +
                '<p class="text-gray-400 text-sm">' + escHtml(s.name || '') + '</p>' +
                '</div>' +
                '<span class="text-xs px-2 py-1 rounded bg-navy border border-gray-600 text-gray-300">' + escHtml(s.sector || '') + '</span>' +
                '</div>' +
                (yahooPriceHtml ? '<div class="flex justify-between items-end mt-4"><div>' + yahooPriceHtml + '</div><div class="text-right">' + yahooChangeHtml + '</div></div>' : '') +
                '<div class="text-center py-4 import-msg">' +
                '<span class="inline-flex items-center gap-1 bg-yellow-900/30 text-yellow-400 border border-yellow-700/50 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-yellow-800/40 transition cursor-pointer">' +
                '<i class="fas fa-cloud-download-alt"></i> Import from Yahoo Finance</span>' +
                '</div>' +
                '</div>' +
                '</div>';
        }

        var sym = escHtml(s.symbol);
        return '<div class="stock-card bg-navy2 rounded-xl border border-gray-700 hover:border-gold transition relative group" data-sid="' + s.id + '">' +
            '<div class="p-6 cursor-pointer" onclick="goToStock(' + s.id + ')">' +
            '<button class="watch-btn absolute top-3 right-3 text-xl transition z-10" data-sid="' + s.id + '" data-watched="0" onclick="event.stopPropagation(); toggleWatch(this)" title="Add to watchlist">' +
            '<i class="far fa-star text-gray-500 hover:text-gold"></i>' +
            '</button>' +
            '<div class="flex justify-between items-start mb-3">' +
            '<div>' +
            '<h3 class="text-white font-bold text-lg">' + sym + '</h3>' +
            '<p class="text-gray-400 text-sm">' + escHtml(s.name) + '</p>' +
            '</div>' +
            '<span class="text-xs px-2 py-1 rounded bg-navy border border-gray-600 text-gray-300 mr-8">' + escHtml(s.sector) + '</span>' +
            '</div>' +
            '<div class="flex justify-between items-end mt-4">' +
            '<div>' +
            '<p class="price-value text-2xl font-bold text-white">' + priceHtml + '</p>' +
            '<p class="text-gray-500 text-xs mt-1 prev-close">' + prevCloseHtml + '</p>' +
            '</div>' +
            '<div class="text-right">' +
            '<span class="change-badge px-3 py-1 rounded text-sm font-semibold ' + changeClass + '">' + changePct + '</span>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="border-t border-gray-700 flex">' +
            '<a href="/stocks/' + s.id + '/edit" onclick="event.stopPropagation()" class="flex-1 text-center py-3 text-gray-400 hover:text-gold hover:bg-navy text-sm transition border-r border-gray-700">' +
            '<i class="fas fa-edit mr-1"></i>Edit</a>' +
            '<form action="/stocks/' + s.id + '/delete" method="post" class="flex-1" onsubmit="return confirm(\'Delete ' + sym + '? This will remove all related data.\');">' +
            '<input type="hidden" name="' + CSRF_NAME + '" value="' + CSRF_HASH + '" />' +
            '<button type="submit" class="w-full text-center py-3 text-red-400 hover:text-red-300 hover:bg-red-900/10 text-sm transition" onclick="event.stopPropagation()">' +
            '<i class="fas fa-trash mr-1"></i>Remove</button></form>' +
            '</div>' +
            '</div>';
    }

    function escHtml(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    window.importStock = function(sym, exchange) {
        var cards = document.querySelectorAll('.stock-card');
        cards.forEach(function(c) {
            if (c.textContent.indexOf(sym) >= 0) {
                c.style.opacity = '0.5';
                c.onclick = null;
                var msg = c.querySelector('.import-msg');
                if (msg) msg.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Importing...';
            }
        });
        var body = CSRF_NAME + '=' + encodeURIComponent(CSRF_HASH) + '&symbol=' + encodeURIComponent(sym) + '&exchange=' + encodeURIComponent(exchange);
        fetch('/api/stocks/import', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
            .then(function(r){ return r.json(); })
            .then(function(d){
                if (d.success) {
                    window.location.reload();
                } else {
                    alert(d.message);
                    window.location.reload();
                }
            })
            .catch(function(){
                alert('Import failed.');
                window.location.reload();
            });
    };

    function renderResults(data) {
        if (data.results.length === 0) {
            searchResults.innerHTML = '<div class="bg-navy2 rounded-xl border border-gray-700 p-12 text-center">' +
                '<i class="fas fa-search text-5xl text-gray-600 mb-4"></i>' +
                '<h3 class="text-white text-lg font-semibold mb-2">No stocks found</h3>' +
                '<p class="text-gray-400">No results for "' + escHtml(data.query) + '". Try a different symbol or name.</p>' +
                '</div>';
            return;
        }
        var html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="stockCards">';
        data.results.forEach(function(s) { html += buildCardHtml(s); });
        html += '</div>';
        searchResults.innerHTML = html;
    }

    function doSearch(query) {
        if (query === lastQuery) return;
        lastQuery = query;

        if (query.length < 2) {
            if (query === '') {
                searchClear.classList.add('hidden');
                window.location.href = '/stocks';
            }
            return;
        }

        searchClear.classList.remove('hidden');
        searchStatus.classList.remove('hidden');

        fetch('/api/search?q=' + encodeURIComponent(query))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                searchStatus.classList.add('hidden');
                renderResults(data);
            })
            .catch(function() {
                searchStatus.classList.add('hidden');
            });
    }

    searchInput.addEventListener('input', function() {
        var val = this.value.trim();
        if (searchTimer) clearTimeout(searchTimer);
        searchTimer = setTimeout(function() { doSearch(val); }, 300);
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (searchTimer) clearTimeout(searchTimer);
            doSearch(this.value.trim());
        }
    });

    searchClear.addEventListener('click', function() {
        searchInput.value = '';
        searchClear.classList.add('hidden');
        if (searchTimer) clearTimeout(searchTimer);
        window.location.href = '/stocks';
    });

    window.toggleWatch = function(btn) {
        var sid = btn.getAttribute('data-sid');
        var csrfInput = getCSRF();
        var bodyStr = (CSRF_NAME || 'csrf_test_name') + '=' + encodeURIComponent(csrfInput ? csrfInput.value : '');
        fetch('/watchlist/toggle/' + sid, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: bodyStr
        }).then(function(r) { return r.json(); })
          .then(function(data) {
              var icon = btn.querySelector('i');
              if (data.watched) {
                  btn.setAttribute('data-watched', '1');
                  btn.title = 'Remove from watchlist';
                  icon.className = 'fas fa-star text-gold';
              } else {
                  btn.setAttribute('data-watched', '0');
                  btn.title = 'Add to watchlist';
                  icon.className = 'far fa-star text-gray-500 hover:text-gold';
              }
          });
    };

    window.goToStock = function(id) {
        window.location.href = '/stocks/' + id;
    };

    var addTimer = null;

    function disableBtn(btn) {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    }
    function enableBtn(btn) {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    window.lookupSymbol = function() {
        var sym = document.getElementById('addSymbol').value.trim().toUpperCase();
        var exchange = document.getElementById('addExchange').value;
        var status = document.getElementById('addSymbolStatus');
        var nameEl = document.getElementById('addName');
        var sectorEl = document.getElementById('addSector');
        var priceEl = document.getElementById('addPrice');
        var btn = document.getElementById('addSubmitBtn');

        if (sym.length < 2) {
            status.className = 'text-xs mt-1 hidden';
            nameEl.value = ''; sectorEl.value = ''; priceEl.value = '';
            disableBtn(btn);
            return;
        }

        status.className = 'text-xs mt-1 text-gray-400';
        status.textContent = 'Looking up...';
        status.classList.remove('hidden');

        fetch('/api/search?q=' + encodeURIComponent(sym) + '&exchange=' + encodeURIComponent(exchange))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var match = null;
                data.results.forEach(function(s) {
                    if (s.symbol.toUpperCase() === sym && !match) match = s;
                });
                if (!match) match = data.results[0];
                if (!match) {
                    status.className = 'text-xs mt-1 text-red-400';
                    status.textContent = 'Symbol not found on Yahoo Finance.';
                    nameEl.value = ''; sectorEl.value = ''; priceEl.value = '';
                    disableBtn(btn);
                    return;
                }
                if (match.id) {
                    status.className = 'text-xs mt-1 text-yellow-400';
                    status.innerHTML = 'Already in DB — <a href="/stocks/' + match.id + '" class="underline">View stock</a>';
                    nameEl.value = match.name || ''; sectorEl.value = match.sector || ''; priceEl.value = match.price || '';
                    disableBtn(btn);
                    return;
                }
                nameEl.value = match.name || '';
                sectorEl.value = match.sector || '';
                if (match.price) {
                    priceEl.value = '\u20B9' + parseFloat(match.price).toLocaleString('en-IN');
                    status.className = 'text-xs mt-1 text-green-400';
                    status.textContent = 'Ready to add — all data will come from Yahoo live.';
                    enableBtn(btn);
                } else {
                    status.className = 'text-xs mt-1 text-gray-400';
                    status.textContent = 'Fetching live price...';
                    fetch('/api/quote/' + encodeURIComponent(sym) + '/' + encodeURIComponent(exchange))
                        .then(function(r) { return r.json(); })
                        .then(function(q) {
                            var price = q.regularMarketPrice || q.price || q.current_price;
                            if (price && price > 0) {
                                priceEl.value = '\u20B9' + parseFloat(price).toLocaleString('en-IN');
                                if (!nameEl.value) nameEl.value = q.longName || q.shortName || q.name || '';
                                if (!sectorEl.value) sectorEl.value = q.sector || q.fullExchangeName || '';
                                status.className = 'text-xs mt-1 text-green-400';
                                status.textContent = 'Ready to add — all data from Yahoo live.';
                                enableBtn(btn);
                            } else {
                                status.className = 'text-xs mt-1 text-red-400';
                                status.textContent = 'No price data available for ' + sym;
                                disableBtn(btn);
                            }
                        })
                        .catch(function() {
                            status.className = 'text-xs mt-1 text-red-400';
                            status.textContent = 'Failed to fetch price.';
                            disableBtn(btn);
                        });
                }
            })
            .catch(function() {
                status.className = 'text-xs mt-1 text-red-400';
                status.textContent = 'Lookup failed.';
                disableBtn(btn);
            });
    };

    window.importStockFromForm = function() {
        var sym = document.getElementById('addSymbol').value.trim().toUpperCase();
        var exchange = document.getElementById('addExchange').value;
        var btn = document.getElementById('addSubmitBtn');
        var status = document.getElementById('addSymbolStatus');
            if (!sym) return;
        disableBtn(btn);
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Importing...';
        status.className = 'text-xs mt-1 text-gray-400';
        status.textContent = 'Fetching all data from Yahoo Finance live API...';
        status.classList.remove('hidden');
        var body = CSRF_NAME + '=' + encodeURIComponent(CSRF_HASH) + '&symbol=' + encodeURIComponent(sym) + '&exchange=' + encodeURIComponent(exchange);
        fetch('/api/stocks/import', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.success) {
                    window.location.reload();
                } else if (d.id) {
                    window.location.href = '/stocks/' + d.id;
                } else {
                    status.className = 'text-xs mt-1 text-red-400';
                    status.textContent = d.message;
                    enableBtn(btn);
                    btn.innerHTML = '<i class="fas fa-plus mr-1"></i> Add Stock';
                }
            })
            .catch(function() {
                status.className = 'text-xs mt-1 text-red-400';
                status.textContent = 'Import failed.';
                enableBtn(btn);
                btn.innerHTML = '<i class="fas fa-plus mr-1"></i> Add Stock';
            });
    };

    document.getElementById('addSymbol').addEventListener('input', function() {
        if (addTimer) clearTimeout(addTimer);
        addTimer = setTimeout(window.lookupSymbol, 500);
    });

    document.getElementById('addSymbol').addEventListener('blur', function() {
        if (addTimer) clearTimeout(addTimer);
        window.lookupSymbol();
    });

    window.toggleAddForm = function() {
        var form = document.getElementById('addStockForm');
        form.classList.toggle('hidden');
    };
})();
</script>
