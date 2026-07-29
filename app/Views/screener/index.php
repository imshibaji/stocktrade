<section>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Stock Screener</h1>
            <p class="text-gray-400 mt-1">Filter stocks by fundamental and technical criteria</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="toggleGuide()" class="border border-gray-600 text-gray-300 hover:border-gold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-book mr-2"></i>Guide
            </button>
            <a href="/stocks" class="border border-gold text-gold hover:bg-gold/10 px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-list mr-2"></i>All Stocks
            </a>
        </div>
    </div>

    <div id="screenerGuide" class="hidden bg-navy2 rounded-xl border border-gray-700 p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-white font-bold text-lg">Screener Guide</h2>
            <button onclick="toggleGuide()" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
                <h3 class="text-gold font-semibold mb-3">Available Fields</h3>
                <table class="w-full text-gray-300">
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">price</td><td class="py-1.5 text-gray-500">Current market price</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">previous_close</td><td class="py-1.5 text-gray-500">Previous day close</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">market_cap</td><td class="py-1.5 text-gray-500">Market capitalization (in Rs)</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">pe_ratio</td><td class="py-1.5 text-gray-500">Price-to-earnings ratio</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">dividend_yield</td><td class="py-1.5 text-gray-500">Dividend yield (decimal, e.g. 0.02 = 2%)</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">beta</td><td class="py-1.5 text-gray-500">Beta (volatility relative to market)</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">avg_volume</td><td class="py-1.5 text-gray-500">Average daily volume</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">week_52_high</td><td class="py-1.5 text-gray-500">52-week high price</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">week_52_low</td><td class="py-1.5 text-gray-500">52-week low price</td></tr>
                </table>
            </div>
            <div>
                <h3 class="text-gold font-semibold mb-3">Example Queries</h3>
                <div class="space-y-3">
                    <div class="bg-navy rounded-lg p-3 border border-gray-700">
                        <p class="text-green-400 text-xs mb-1">Large Cap Value</p>
                        <code class="text-gray-300 text-xs">market_cap > 500000000000 AND<br>pe_ratio < 15 AND<br>dividend_yield > 0.02</code>
                    </div>
                    <div class="bg-navy rounded-lg p-3 border border-gray-700">
                        <p class="text-green-400 text-xs mb-1">High Growth</p>
                        <code class="text-gray-300 text-xs">pe_ratio > 20 AND<br>beta > 1 AND<br>market_cap < 500000000000</code>
                    </div>
                    <div class="bg-navy rounded-lg p-3 border border-gray-700">
                        <p class="text-green-400 text-xs mb-1">Low Volatility</p>
                        <code class="text-gray-300 text-xs">beta < 0.8 AND<br>dividend_yield > 0.015 AND<br>pe_ratio < 20</code>
                    </div>
                    <p class="text-gray-500 text-xs mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Note: Advanced metrics like Debt/Equity, ROCE, and earnings data require subscription-based financial APIs. The screener uses available Yahoo Finance data.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-navy2 rounded-xl border border-gray-700 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-white font-bold text-lg">Filter Criteria</h2>
            <button onclick="addFilter()" class="text-gold hover:text-gold2 text-sm transition">
                <i class="fas fa-plus mr-1"></i>Add Condition
            </button>
        </div>
        <div id="filterList" class="space-y-3 mb-4"></div>
        <div class="flex items-center gap-3">
            <button onclick="runScreener()" class="bg-gold hover:bg-gold2 text-navy font-bold px-6 py-3 rounded-lg transition">
                <i class="fas fa-search mr-2"></i>Run Screener
            </button>
            <button onclick="clearAll()" class="border border-gray-600 text-gray-300 hover:border-gold px-4 py-3 rounded-lg text-sm transition">
                <i class="fas fa-undo mr-1"></i>Clear All
            </button>
            <span id="resultCount" class="text-gray-400 text-sm ml-auto"></span>
        </div>
    </div>

    <div id="resultsContainer" class="hidden">
        <div class="bg-navy2 rounded-xl border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-700">
                            <th class="text-left px-6 py-3">Symbol</th>
                            <th class="text-left px-6 py-3">Name</th>
                            <th class="text-right px-6 py-3">Price</th>
                            <th class="text-right px-6 py-3">Change</th>
                            <th class="text-right px-6 py-3">P/E</th>
                            <th class="text-right px-6 py-3">M Cap</th>
                            <th class="text-right px-6 py-3">Div Yield</th>
                            <th class="text-right px-6 py-3">Beta</th>
                            <th class="text-right px-6 py-3">Volume</th>
                        </tr>
                    </thead>
                    <tbody id="resultsBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
(function() {
    var CSRF_NAME = '<?= csrf_token() ?>';
    var FILTER_FIELDS = [
        { value: 'market_cap', label: 'Market Cap' },
        { value: 'pe_ratio', label: 'P/E Ratio' },
        { value: 'dividend_yield', label: 'Dividend Yield' },
        { value: 'beta', label: 'Beta' },
        { value: 'price', label: 'Current Price' },
        { value: 'previous_close', label: 'Previous Close' },
        { value: 'avg_volume', label: 'Avg Volume' },
        { value: 'week_52_high', label: '52-Week High' },
        { value: 'week_52_low', label: '52-Week Low' },
    ];
    var OPS = ['>', '>=', '<', '<=', '=='];
    var filterId = 0;

    function formatPrice(v) { return '\u20B9' + parseFloat(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function escHtml(s) { if (!s) return ''; var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    window.toggleGuide = function() {
        document.getElementById('screenerGuide').classList.toggle('hidden');
    };

    window.addFilter = function() {
        var list = document.getElementById('filterList');
        var id = ++filterId;
        var div = document.createElement('div');
        div.className = 'filter-row flex items-center gap-2 bg-navy rounded-lg p-3 border border-gray-700';
        div.id = 'filter-' + id;
        div.innerHTML =
            '<select class="bg-navy border border-gray-600 rounded px-3 py-2 text-sm text-white focus:border-gold focus:outline-none">' +
                FILTER_FIELDS.map(function(f) { return '<option value="' + f.value + '">' + f.label + '</option>'; }).join('') +
            '</select>' +
            '<select class="bg-navy border border-gray-600 rounded px-3 py-2 text-sm text-white focus:border-gold focus:outline-none">' +
                OPS.map(function(o) { return '<option value="' + o + '">' + o + '</option>'; }).join('') +
            '</select>' +
            '<input type="number" step="any" placeholder="Value" class="flex-1 bg-navy border border-gray-600 rounded px-3 py-2 text-sm text-white focus:border-gold focus:outline-none" style="min-width:120px">' +
            '<button onclick="this.closest(\'.filter-row\').remove()" class="text-red-400 hover:text-red-300 text-sm px-2"><i class="fas fa-times"></i></button>';
        list.appendChild(div);
    };

    window.clearAll = function() {
        document.getElementById('filterList').innerHTML = '';
        document.getElementById('resultsContainer').classList.add('hidden');
        document.getElementById('resultCount').textContent = '';
    };

    window.runScreener = function() {
        var rows = document.querySelectorAll('.filter-row');
        var filters = [];
        rows.forEach(function(r) {
            var selects = r.querySelectorAll('select');
            var input = r.querySelector('input');
            if (selects.length === 2 && input) {
                var val = input.value.trim();
                if (val !== '') {
                    filters.push({ field: selects[0].value, op: selects[1].value, value: val });
                }
            }
        });

        if (filters.length === 0) {
            alert('Add at least one filter condition.');
            return;
        }

        var url = '/api/screener/run?filters=' + encodeURIComponent(JSON.stringify(filters));
        var btn = document.querySelector('button[onclick="runScreener()"]');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Searching...'; }

        fetch(url)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-search mr-2"></i>Run Screener'; }
                document.getElementById('resultCount').textContent = data.total + ' stocks found';
                var tbody = document.getElementById('resultsBody');
                tbody.innerHTML = '';
                if (data.stocks.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-8 text-center text-gray-500">No stocks match your criteria.</td></tr>';
                } else {
                    data.stocks.forEach(function(s) {
                        var cp = parseFloat(s.current_price) || 0;
                        var pc = parseFloat(s.previous_close) || 0;
                        var chg = cp - pc;
                        var pct = pc > 0 ? ((chg / pc) * 100).toFixed(2) : '0.00';
                        var tr = document.createElement('tr');
                        tr.className = 'border-b border-gray-700/50 hover:bg-navy/50';
                        tr.innerHTML =
                            '<td class="px-6 py-4"><a href="/stocks/' + s.id + '" class="text-white font-semibold hover:text-gold">' + escHtml(s.symbol) + '</a></td>' +
                            '<td class="px-6 py-4 text-gray-400 text-xs">' + escHtml(s.name) + '</td>' +
                            '<td class="px-6 py-4 text-right text-white font-semibold">' + formatPrice(cp) + '</td>' +
                            '<td class="px-6 py-4 text-right ' + (chg >= 0 ? 'text-green-400' : 'text-red-400') + '">' + (chg >= 0 ? '+' : '') + pct + '%</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.pe_ratio || '—') + '</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.market_cap ? formatMCap(s.market_cap) : '—') + '</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.dividend_yield ? (parseFloat(s.dividend_yield) * 100).toFixed(2) + '%' : '—') + '</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.beta || '—') + '</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.avg_volume ? formatVol(s.avg_volume) : '—') + '</td>';
                        tbody.appendChild(tr);
                    });
                }
                document.getElementById('resultsContainer').classList.remove('hidden');
            })
            .catch(function() {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-search mr-2"></i>Run Screener'; }
                alert('Screener failed. Check console for details.');
            });
    };

    function formatMCap(v) {
        v = parseFloat(v);
        if (v >= 10000000000000) return (v / 10000000000000).toFixed(2) + 'L Cr';
        if (v >= 10000000) return (v / 10000000).toFixed(2) + ' Cr';
        if (v >= 100000) return (v / 100000).toFixed(2) + ' L';
        return v.toLocaleString('en-IN');
    }

    function formatVol(v) {
        v = parseFloat(v);
        if (v >= 10000000) return (v / 10000000).toFixed(2) + ' Cr';
        if (v >= 100000) return (v / 100000).toFixed(2) + ' L';
        return v.toLocaleString('en-IN');
    }

    addFilter();
})();
</script>
