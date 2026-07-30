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
            <button onclick="toggleSavedLists()" class="border border-gray-600 text-gray-300 hover:border-gold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-save mr-2"></i>Saved Lists
                <span id="savedCount" class="ml-1 text-gold text-xs"><?= count($savedLists) ?></span>
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-sm">
            <div>
                <h3 class="text-gold font-semibold mb-3">Fundamental Fields</h3>
                <table class="w-full text-gray-300">
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">price</td><td class="py-1.5 text-gray-500">Current market price</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">previous_close</td><td class="py-1.5 text-gray-500">Previous day close</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">market_cap</td><td class="py-1.5 text-gray-500">Market capitalization</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">pe_ratio</td><td class="py-1.5 text-gray-500">Price-to-earnings ratio</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">dividend_yield</td><td class="py-1.5 text-gray-500">Dividend yield (0.02 = 2%)</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">beta</td><td class="py-1.5 text-gray-500">Beta (volatility)</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">avg_volume</td><td class="py-1.5 text-gray-500">Average daily volume</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">week_52_high</td><td class="py-1.5 text-gray-500">52-week high</td></tr>
                    <tr class="border-b border-gray-700"><td class="py-1.5 font-mono">week_52_low</td><td class="py-1.5 text-gray-500">52-week low</td></tr>
                </table>
                <h3 class="text-gold font-semibold mt-4 mb-2">Math Operators</h3>
                <p class="text-gray-400 text-xs">Transform a field value before comparing:</p>
                <code class="text-gray-300 text-xs">price × 2 > 5000</code><br>
                <code class="text-gray-300 text-xs">market_cap / 10000000 > 100</code>
            </div>
            <div>
                <h3 class="text-gold font-semibold mb-3">Technical Indicators</h3>
                <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Overlay & Trend</p><code class="text-gray-400 text-xs">sma_pct, ema_pct, vwap_ratio, macd, macd_signal, macd_histogram</code></div>
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Volatility & Channels</p><code class="text-gray-400 text-xs">atr, natr, bb_pct, bb_width, kc_pct, dc_pct</code></div>
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Momentum & Oscillators</p><code class="text-gray-400 text-xs">rsi, stoch_k, stoch_d, cci, roc, williams_r, rvi, coppock</code></div>
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Trend Strength</p><code class="text-gray-400 text-xs">supertrend, supertrend_dir, psar</code></div>
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Volume & Accumulation</p><code class="text-gray-400 text-xs">obv, cmf, vpt, mfi, volume_ratio, force_index, eom</code></div>
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Support / Resistance</p><code class="text-gray-400 text-xs">pivot, fib_61.8</code></div>
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Quantitative & Regime</p><code class="text-gray-400 text-xs">linreg_slope, linreg_rsq, zscore, efficiency_ratio, chop, hurst, dpo, ulcer_index</code></div>
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Adaptive / Microstructure</p><code class="text-gray-400 text-xs">kama, volume_delta</code></div>
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Tail Risk & Squeeze</p><code class="text-gray-400 text-xs">ttm_squeeze, ttm_momentum, sortino_ratio, cvar, historical_var, martin_ratio, downside_dev</code></div>
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Specialized Oscillators</p><code class="text-gray-400 text-xs">aroon_up, aroon_down, aroon_osc, tsi, vi_plus, vi_minus, cmo, mass_index, connors_rsi, rmi</code></div>
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Volatility Scaling</p><code class="text-gray-400 text-xs">klinger_osc, rainbow_sma1</code></div>
                    <div><p class="text-green-400 text-xs font-semibold mb-1">Volume Profile</p><code class="text-gray-400 text-xs">vp_poc, vp_vah, vp_val</code></div>
                </div>
            </div>
            <div>
                <h3 class="text-gold font-semibold mb-3">Match Mode</h3>
                <p class="text-gray-400 text-xs mb-2">Choose how conditions are combined:</p>
                <p class="text-gray-300 text-xs mb-3"><span class="text-green-400">All (AND)</span> &mdash; every condition must pass</p>
                <p class="text-gray-300 text-xs mb-3"><span class="text-green-400">Any (OR)</span> &mdash; at least one condition passes</p>
                <h3 class="text-gold font-semibold mb-3">Example Queries</h3>
                <div class="space-y-3">
                    <div class="bg-navy rounded-lg p-3 border border-gray-700">
                        <p class="text-green-400 text-xs mb-1">Oversold Bounce (AND)</p>
                        <code class="text-gray-300 text-xs">rsi &lt; 30 AND<br>stoch_k &lt; 20 AND<br>volume_ratio &gt; 1.5</code>
                    </div>
                    <div class="bg-navy rounded-lg p-3 border border-gray-700">
                        <p class="text-green-400 text-xs mb-1">Value OR Momentum (OR)</p>
                        <code class="text-gray-300 text-xs">pe_ratio &lt; 12 OR<br>rsi &gt; 60</code>
                    </div>
                    <div class="bg-navy rounded-lg p-3 border border-gray-700">
                        <p class="text-green-400 text-xs mb-1">Math Transform</p>
                        <code class="text-gray-300 text-xs">price &times; 2 &gt; 5000</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex gap-6">
        <div class="flex-1 min-w-0">
            <div class="bg-navy2 rounded-xl border border-gray-700 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-white font-bold text-lg">Filter Criteria</h2>
                    <div class="flex items-center gap-4">
                        <label class="text-gray-400 text-xs">Match:</label>
                        <select id="matchMode" class="bg-navy border border-gray-600 rounded px-3 py-1.5 text-sm text-white focus:border-gold focus:outline-none">
                            <option value="all">All (AND)</option>
                            <option value="any">Any (OR)</option>
                        </select>
                        <div class="flex gap-2">
                            <button onclick="addFundamentalFilter()" class="text-gold hover:text-gold2 text-sm transition">
                                <i class="fas fa-plus mr-1"></i>Fundamental
                            </button>
                            <button onclick="addTechnicalFilter()" class="text-gold hover:text-gold2 text-sm transition">
                                <i class="fas fa-chart-line mr-1"></i>Technical
                            </button>
                        </div>
                    </div>
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
                <div class="bg-navy2 rounded-xl border border-gray-700 p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-white font-bold text-lg">
                            Results <span id="resultCount2" class="text-gold text-base font-normal"></span>
                        </h2>
                        <div class="flex gap-2">
                            <button onclick="showSaveDialog()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                                <i class="fas fa-save mr-1"></i>Save as List
                            </button>
                        </div>
                    </div>
                </div>
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
        </div>

        <div id="savedListsPanel" class="hidden w-80 shrink-0">
            <div class="bg-navy2 rounded-xl border border-gray-700 p-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-white font-bold text-lg">Saved Lists</h2>
                    <button onclick="toggleSavedLists()" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
                </div>
                <div id="savedListsContainer"></div>
            </div>
        </div>
    </div>

    <div id="saveDialog" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60">
        <div class="bg-navy2 rounded-xl border border-gray-700 p-6 w-full max-w-md">
            <h3 class="text-white font-bold text-lg mb-4">Save Screener List</h3>
            <input type="text" id="listNameInput" placeholder="Enter list name..." class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white text-sm focus:border-gold focus:outline-none mb-4">
            <div class="flex justify-end gap-3">
                <button onclick="closeSaveDialog()" class="border border-gray-600 text-gray-300 px-4 py-2 rounded-lg text-sm">Cancel</button>
                <button onclick="saveList()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">Save</button>
            </div>
        </div>
    </div>
</section>

<script>
(function() {
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

    var TECH_GROUPS = [
        { label: 'Overlay & Trend', options: [
            { value: 'sma_pct', label: 'SMA % (Price/SMA*100)', period: 50 },
            { value: 'ema_pct', label: 'EMA % (Price/EMA*100)', period: 50 },
            { value: 'vwap_ratio', label: 'VWAP Ratio', period: 0 },
            { value: 'macd', label: 'MACD Line', period: 12 },
            { value: 'macd_signal', label: 'MACD Signal', period: 12 },
            { value: 'macd_histogram', label: 'MACD Histogram', period: 12 },
        ]},
        { label: 'Volatility & Channels', options: [
            { value: 'atr', label: 'ATR', period: 14 },
            { value: 'natr', label: 'NATR (%)', period: 14 },
            { value: 'bb_pct', label: 'BB %B (0-100)', period: 20 },
            { value: 'bb_width', label: 'BB Width (%)', period: 20 },
            { value: 'kc_pct', label: 'Keltner %B', period: 20 },
            { value: 'dc_pct', label: 'Donchian %B', period: 20 },
        ]},
        { label: 'Momentum & Oscillators', options: [
            { value: 'rsi', label: 'RSI', period: 14 },
            { value: 'stoch_k', label: 'Stochastic %K', period: 14 },
            { value: 'stoch_d', label: 'Stochastic %D', period: 14 },
            { value: 'cci', label: 'CCI', period: 20 },
            { value: 'roc', label: 'ROC (%)', period: 12 },
            { value: 'williams_r', label: 'Williams %R', period: 14 },
            { value: 'rvi', label: 'RVI', period: 14 },
            { value: 'coppock', label: 'Coppock Curve', period: 10 },
        ]},
        { label: 'Trend Strength', options: [
            { value: 'supertrend', label: 'Supertrend Value', period: 10 },
            { value: 'supertrend_dir', label: 'Supertrend Direction', period: 10 },
            { value: 'psar', label: 'Parabolic SAR', period: 0 },
        ]},
        { label: 'Volume & Accumulation', options: [
            { value: 'obv', label: 'OBV', period: 0 },
            { value: 'cmf', label: 'CMF', period: 20 },
            { value: 'vpt', label: 'VPT', period: 0 },
            { value: 'mfi', label: 'MFI', period: 14 },
            { value: 'volume_ratio', label: 'Volume Ratio', period: 20 },
            { value: 'force_index', label: 'Force Index', period: 13 },
            { value: 'eom', label: 'Ease of Movement', period: 14 },
        ]},
        { label: 'S/R & Pivots', options: [
            { value: 'pivot', label: 'Pivot Point', period: 0 },
            { value: 'fib_61.8', label: 'Fib 61.8% Ratio', period: 0 },
        ]},
        { label: 'Quantitative / Regime', options: [
            { value: 'linreg_slope', label: 'Lin Reg Slope', period: 20 },
            { value: 'linreg_rsq', label: 'Lin Reg R\u00B2', period: 20 },
            { value: 'zscore', label: 'Z-Score', period: 20 },
            { value: 'efficiency_ratio', label: 'Efficiency Ratio', period: 10 },
            { value: 'chop', label: 'Choppiness Index', period: 14 },
            { value: 'hurst', label: 'Hurst Exponent', period: 20 },
            { value: 'dpo', label: 'DPO', period: 20 },
            { value: 'ulcer_index', label: 'Ulcer Index', period: 14 },
        ]},
        { label: 'Adaptive / Micro', options: [
            { value: 'kama', label: 'KAMA', period: 10 },
            { value: 'volume_delta', label: 'Volume Delta', period: 0 },
        ]},
        { label: 'Tail Risk & Squeeze', options: [
            { value: 'ttm_squeeze', label: 'TTM Squeeze (0/1)', period: 20 },
            { value: 'ttm_momentum', label: 'TTM Momentum', period: 20 },
            { value: 'sortino_ratio', label: 'Sortino Ratio', period: 0 },
            { value: 'cvar', label: 'CVaR', period: 0 },
            { value: 'historical_var', label: 'Hist VaR', period: 0 },
            { value: 'martin_ratio', label: 'Martin Ratio', period: 0 },
            { value: 'downside_dev', label: 'Downside Dev', period: 0 },
        ]},
        { label: 'Specialized Oscillators', options: [
            { value: 'aroon_up', label: 'Aroon Up', period: 25 },
            { value: 'aroon_down', label: 'Aroon Down', period: 25 },
            { value: 'aroon_osc', label: 'Aroon Oscillator', period: 25 },
            { value: 'tsi', label: 'TSI', period: 25 },
            { value: 'vi_plus', label: 'Vortex VI+', period: 14 },
            { value: 'vi_minus', label: 'Vortex VI-', period: 14 },
            { value: 'cmo', label: 'CMO', period: 14 },
            { value: 'mass_index', label: 'Mass Index', period: 9 },
            { value: 'connors_rsi', label: 'Connors RSI', period: 3 },
            { value: 'rmi', label: 'RMI', period: 14 },
        ]},
        { label: 'Volatility Scaling', options: [
            { value: 'klinger_osc', label: 'Klinger Osc', period: 34 },
            { value: 'rainbow_sma1', label: 'Rainbow SMA %', period: 2 },
        ]},
        { label: 'Volume Profile', options: [
            { value: 'vp_poc', label: 'VP POC', period: 30 },
            { value: 'vp_vah', label: 'VP VAH', period: 30 },
            { value: 'vp_val', label: 'VP VAL', period: 30 },
        ]},
    ];

    var OPS = ['>', '>=', '<', '<=', '=='];
    var MATH_OPS = [
        { value: '=', label: '=' },
        { value: '+', label: '+' },
        { value: '-', label: '-' },
        { value: '*', label: '\u00D7' },
        { value: '/', label: '\u00F7' },
        { value: '%', label: '%' },
    ];
    var filterId = 0;
    var lastResults = [];

    function formatPrice(v) { return '\u20B9' + parseFloat(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function escHtml(s) { if (!s) return ''; var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    window.toggleGuide = function() {
        document.getElementById('screenerGuide').classList.toggle('hidden');
    };
    window.toggleSavedLists = function() {
        var panel = document.getElementById('savedListsPanel');
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) loadSavedLists();
    };

    window.toggleMathValue = function(sel) {
        var row = sel.closest('.filter-row');
        if (!row) return;
        var inp = row.querySelector('.math-value');
        if (!inp) return;
        if (sel.value === '=') { inp.style.display = 'none'; inp.value = ''; }
        else { inp.style.display = 'block'; }
    };

    window.addFundamentalFilter = function() {
        var list = document.getElementById('filterList');
        var id = ++filterId;
        var div = document.createElement('div');
        div.className = 'filter-row flex items-center gap-1.5 bg-navy rounded-lg p-3 border border-gray-700';
        div.id = 'filter-' + id;
        div.innerHTML =
            '<span class="text-xs text-gold font-semibold shrink-0 w-14">FUND</span>' +
            '<select class="bg-navy border border-gray-600 rounded px-3 py-2 text-sm text-white focus:border-gold focus:outline-none" style="min-width:120px">' +
                FILTER_FIELDS.map(function(f) { return '<option value="' + f.value + '">' + f.label + '</option>'; }).join('') +
            '</select>' +
            '<select class="math-op bg-navy border border-gray-600 rounded px-2 py-2 text-sm text-white focus:border-gold focus:outline-none" style="width:44px" onchange="toggleMathValue(this)">' +
                MATH_OPS.map(function(m) { return '<option value="' + m.value + '">' + m.label + '</option>'; }).join('') +
            '</select>' +
            '<input type="number" step="any" class="math-value bg-navy border border-gray-600 rounded px-2 py-2 text-sm text-white focus:border-gold focus:outline-none" placeholder="N" style="width:65px;display:none">' +
            '<select class="bg-navy border border-gray-600 rounded px-2 py-2 text-sm text-white focus:border-gold focus:outline-none" style="width:52px">' +
                OPS.map(function(o) { return '<option value="' + o + '">' + o + '</option>'; }).join('') +
            '</select>' +
            '<input type="number" step="any" placeholder="Value" class="flex-1 bg-navy border border-gray-600 rounded px-3 py-2 text-sm text-white focus:border-gold focus:outline-none" style="min-width:100px">' +
            '<input type="hidden" class="filter-type" value="fundamental">' +
            '<button onclick="this.closest(\'.filter-row\').remove()" class="text-red-400 hover:text-red-300 text-sm px-2 shrink-0"><i class="fas fa-times"></i></button>';
        list.appendChild(div);
    };

    window.addTechnicalFilter = function() {
        var list = document.getElementById('filterList');
        var id = ++filterId;
        var div = document.createElement('div');
        div.className = 'filter-row flex items-center gap-1.5 bg-navy rounded-lg p-3 border border-gray-700';
        div.id = 'filter-' + id;
        div.innerHTML =
            '<span class="text-xs text-blue-400 font-semibold shrink-0 w-14">TECH</span>' +
            '<select class="indicator-select bg-navy border border-gray-600 rounded px-3 py-2 text-sm text-white focus:border-gold focus:outline-none" onchange="updatePeriod(this)" style="min-width:170px">' +
                buildIndicatorOptions() +
            '</select>' +
            '<input type="number" class="period-input bg-navy border border-gray-600 rounded px-2 py-2 text-sm text-white focus:border-gold focus:outline-none" placeholder="P" style="width:58px" value="14">' +
            '<select class="math-op bg-navy border border-gray-600 rounded px-2 py-2 text-sm text-white focus:border-gold focus:outline-none" style="width:44px" onchange="toggleMathValue(this)">' +
                MATH_OPS.map(function(m) { return '<option value="' + m.value + '">' + m.label + '</option>'; }).join('') +
            '</select>' +
            '<input type="number" step="any" class="math-value bg-navy border border-gray-600 rounded px-2 py-2 text-sm text-white focus:border-gold focus:outline-none" placeholder="N" style="width:65px;display:none">' +
            '<select class="bg-navy border border-gray-600 rounded px-2 py-2 text-sm text-white focus:border-gold focus:outline-none" style="width:52px">' +
                OPS.map(function(o) { return '<option value="' + o + '">' + o + '</option>'; }).join('') +
            '</select>' +
            '<input type="number" step="any" placeholder="Value" class="flex-1 bg-navy border border-gray-600 rounded px-3 py-2 text-sm text-white focus:border-gold focus:outline-none" style="min-width:100px">' +
            '<input type="hidden" class="filter-type" value="technical">' +
            '<button onclick="this.closest(\'.filter-row\').remove()" class="text-red-400 hover:text-red-300 text-sm px-2 shrink-0"><i class="fas fa-times"></i></button>';
        list.appendChild(div);
        updatePeriod(div.querySelector('.indicator-select'));
    };

    window.updatePeriod = function(sel) {
        var opt = sel.options[sel.selectedIndex];
        var p = opt.getAttribute('data-period');
        var inp = sel.closest('.filter-row').querySelector('.period-input');
        if (p && p !== '0') inp.value = p;
    };

    window.clearAll = function() {
        document.getElementById('filterList').innerHTML = '';
        var rc = document.getElementById('resultsContainer');
        if (rc) rc.classList.add('hidden');
        document.getElementById('resultCount').textContent = '';
        lastResults = [];
    };

    function collectFilters() {
        var filters = [], techFilters = [];
        document.querySelectorAll('.filter-row').forEach(function(r) {
            var type = (r.querySelector('.filter-type') || {}).value || 'fundamental';
            var inputs = r.querySelectorAll('input:not([type=hidden]):not(.math-value)');
            var mathInputs = r.querySelectorAll('.math-value');
            var selects = r.querySelectorAll('select');
            var mathOpSelect = r.querySelector('.math-op');

            var mathOp = mathOpSelect ? mathOpSelect.value : '=';
            var mathValue = mathInputs.length ? mathInputs[0].value : '';

            if (type === 'technical') {
                var indicatorSelect = r.querySelector('.indicator-select');
                var periodInput = r.querySelector('.period-input');
                if (indicatorSelect && selects.length >= 2) {
                    var opSelect = selects[selects.length - 1];
                    var valueInput = inputs[inputs.length - 1];
                    var val = valueInput ? valueInput.value.trim() : '';
                    if (val !== '') techFilters.push({
                        indicator: indicatorSelect.value,
                        period: parseInt(periodInput ? periodInput.value : 14) || 14,
                        math_op: mathOp,
                        math_value: mathOp !== '=' ? mathValue : '',
                        op: opSelect.value,
                        value: val
                    });
                }
            } else {
                var fieldSelect = selects[0], opSelect = selects[selects.length - 1], valueInput = inputs[inputs.length - 1];
                if (fieldSelect && opSelect && valueInput) {
                    var val = valueInput.value.trim();
                    if (val !== '') filters.push({
                        field: fieldSelect.value,
                        math_op: mathOp,
                        math_value: mathOp !== '=' ? mathValue : '',
                        op: opSelect.value,
                        value: val
                    });
                }
            }
        });
        return { filters: filters, techFilters: techFilters };
    }

    window.runScreener = function() {
        var cf = collectFilters();
        if (cf.filters.length === 0 && cf.techFilters.length === 0) { alert('Add at least one filter condition.'); return; }

        var matchMode = document.getElementById('matchMode').value;
        var params = new URLSearchParams();
        params.set('match_mode', matchMode);
        if (cf.filters.length > 0) params.set('filters', JSON.stringify(cf.filters));
        if (cf.techFilters.length > 0) params.set('tech_filters', JSON.stringify(cf.techFilters));

        var url = '/api/screener/run?' + params.toString();
        var btn = document.querySelector('button[onclick="runScreener()"]');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Searching...'; }

        fetch(url)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-search mr-2"></i>Run Screener'; }
                lastResults = data.stocks || [];
                var total = data.total ?? data.stocks?.length ?? 0;
                document.getElementById('resultCount').textContent = total + ' stocks found';
                document.getElementById('resultCount2').textContent = '(' + total + ')';
                var tbody = document.getElementById('resultsBody');
                tbody.innerHTML = '';
                if (data.stocks.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-8 text-center text-gray-500">No stocks match your criteria.</td></tr>';
                } else {
                    data.stocks.forEach(function(s) {
                        var cp = parseFloat(s.current_price) || 0, pc = parseFloat(s.previous_close) || 0;
                        var chg = cp - pc, pct = pc > 0 ? ((chg / pc) * 100).toFixed(2) : '0.00';
                        var tr = document.createElement('tr');
                        tr.className = 'border-b border-gray-700/50 hover:bg-navy/50';
                        tr.innerHTML =
                            '<td class="px-6 py-4"><a href="/stocks/' + s.id + '" class="text-white font-semibold hover:text-gold">' + escHtml(s.symbol) + '</a></td>' +
                            '<td class="px-6 py-4 text-gray-400 text-xs">' + escHtml(s.name) + '</td>' +
                            '<td class="px-6 py-4 text-right text-white font-semibold">' + formatPrice(cp) + '</td>' +
                            '<td class="px-6 py-4 text-right ' + (chg >= 0 ? 'text-green-400' : 'text-red-400') + '">' + (chg >= 0 ? '+' : '') + pct + '%</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.pe_ratio || '\u2014') + '</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.market_cap ? formatMCap(s.market_cap) : '\u2014') + '</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.dividend_yield ? (parseFloat(s.dividend_yield || 0) * 100).toFixed(2) + '%' : '\u2014') + '</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.beta || '\u2014') + '</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.avg_volume ? formatVol(s.avg_volume) : '\u2014') + '</td>';
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

    window.showSaveDialog = function() {
        if (lastResults.length === 0) { alert('No results to save.'); return; }
        document.getElementById('saveDialog').classList.remove('hidden');
        document.getElementById('listNameInput').value = '';
        document.getElementById('listNameInput').focus();
    };
    window.closeSaveDialog = function() { document.getElementById('saveDialog').classList.add('hidden'); };

    window.saveList = function() {
        var name = document.getElementById('listNameInput').value.trim();
        if (!name) { alert('Enter a list name.'); return; }
        var cf = collectFilters();
        var stockIds = lastResults.map(function(s) { return s.id; });
        var stockSymbols = lastResults.map(function(s) { return s.symbol; });
        var matchMode = document.getElementById('matchMode').value;
        var formData = new FormData();
        formData.append('name', name);
        formData.append('match_mode', matchMode);
        formData.append('criteria', JSON.stringify(cf.filters));
        formData.append('technical_criteria', JSON.stringify(cf.techFilters));
        formData.append('stock_ids', JSON.stringify(stockIds));
        formData.append('stock_symbols', JSON.stringify(stockSymbols));
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        fetch('/api/screener/save', { method: 'POST', body: formData })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) { closeSaveDialog(); var c = document.getElementById('savedCount'); c.textContent = parseInt(c.textContent || '0', 10) + 1; }
                else { alert(data.message || 'Save failed'); }
            })
            .catch(function() { alert('Save failed'); });
    };

    function loadSavedLists() {
        var container = document.getElementById('savedListsContainer');
        container.innerHTML = '<div class="text-gray-400 text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Loading...</div>';
        fetch('/api/screener/lists').then(function(r) { return r.json(); }).then(function(lists) {
            if (lists.length === 0) { container.innerHTML = '<div class="text-gray-500 text-center py-4 text-sm">No saved lists yet.</div>'; return; }
            container.innerHTML = '';
            lists.forEach(function(l) {
                var div = document.createElement('div');
                div.className = 'border-b border-gray-700/50 py-3';
                div.innerHTML =
                    '<div class="flex items-center justify-between">' +
                        '<div class="flex-1 min-w-0">' +
                            '<p class="text-white text-sm font-semibold truncate">' + escHtml(l.name) + '</p>' +
                            '<p class="text-gray-500 text-xs">' + l.stock_count + ' stocks \u00B7 ' + new Date(l.created_at).toLocaleDateString() + '</p>' +
                        '</div>' +
                        '<div class="flex gap-1 ml-2">' +
                            '<button onclick="loadList(' + l.id + ')" class="text-gold hover:text-gold2 text-xs px-2 py-1 rounded border border-gold/30 hover:border-gold transition" title="Load"><i class="fas fa-upload"></i></button>' +
                            '<button onclick="deleteList(' + l.id + ')" class="text-red-400 hover:text-red-300 text-xs px-2 py-1 rounded border border-red-400/30 hover:border-red-400 transition" title="Delete"><i class="fas fa-trash"></i></button>' +
                        '</div>' +
                    '</div>';
                container.appendChild(div);
            });
        }).catch(function() { container.innerHTML = '<div class="text-red-400 text-center py-4 text-sm">Failed to load lists.</div>'; });
    }

    window.loadList = function(id) {
        fetch('/api/screener/load-list?id=' + id).then(function(r) { return r.json(); }).then(function(data) {
            if (!data.success) { alert(data.message); return; }
            document.getElementById('filterList').innerHTML = '';
            filterId = 0;
            if (data.match_mode) document.getElementById('matchMode').value = data.match_mode;

            (data.criteria || []).forEach(function(c) {
                addFundamentalFilter();
                var rows = document.querySelectorAll('.filter-row');
                var lr = rows[rows.length - 1];
                var sels = lr.querySelectorAll('select');
                var inps = lr.querySelectorAll('input:not([type=hidden]):not(.math-value)');
                var mathOpSel = lr.querySelector('.math-op');
                var mathInp = lr.querySelector('.math-value');
                if (sels.length >= 3) { sels[0].value = c.field || 'market_cap'; }
                if (mathOpSel) {
                    mathOpSel.value = c.math_op || '=';
                    if (c.math_op && c.math_op !== '=' && mathInp) {
                        mathInp.style.display = 'block';
                        mathInp.value = c.math_value || '';
                    }
                }
                if (sels.length >= 3) { sels[sels.length - 1].value = c.op || '>'; }
                if (inps.length) { inps[inps.length - 1].value = c.value || ''; }
            });

            (data.technical_criteria || []).forEach(function(tc) {
                addTechnicalFilter();
                var rows = document.querySelectorAll('.filter-row');
                var lr = rows[rows.length - 1];
                var isel = lr.querySelector('.indicator-select');
                var pInput = lr.querySelector('.period-input');
                var mathOpSel = lr.querySelector('.math-op');
                var mathInp = lr.querySelector('.math-value');
                var sels = lr.querySelectorAll('select');
                var inps = lr.querySelectorAll('input:not([type=hidden]):not(.math-value)');
                if (isel) isel.value = tc.indicator || 'rsi';
                if (pInput) pInput.value = tc.period || 14;
                if (mathOpSel) {
                    mathOpSel.value = tc.math_op || '=';
                    if (tc.math_op && tc.math_op !== '=' && mathInp) {
                        mathInp.style.display = 'block';
                        mathInp.value = tc.math_value || '';
                    }
                }
                if (sels.length) sels[sels.length - 1].value = tc.op || '>';
                if (inps.length) inps[inps.length - 1].value = tc.value || '';
            });

            if (data.stocks && data.stocks.length > 0) {
                lastResults = data.stocks;
                document.getElementById('resultCount').textContent = data.stocks.length + ' stocks found';
                document.getElementById('resultCount2').textContent = '(' + data.stocks.length + ')';
                var tbody = document.getElementById('resultsBody');
                tbody.innerHTML = '';
                data.stocks.forEach(function(s) {
                    var cp = parseFloat(s.current_price) || 0, pc = parseFloat(s.previous_close) || 0;
                    var chg = cp - pc, pct = pc > 0 ? ((chg / pc) * 100).toFixed(2) : '0.00';
                    var tr = document.createElement('tr');
                    tr.className = 'border-b border-gray-700/50 hover:bg-navy/50';
                    tr.innerHTML =
                        '<td class="px-6 py-4"><a href="/stocks/' + s.id + '" class="text-white font-semibold hover:text-gold">' + escHtml(s.symbol) + '</a></td>' +
                        '<td class="px-6 py-4 text-gray-400 text-xs">' + escHtml(s.name) + '</td>' +
                        '<td class="px-6 py-4 text-right text-white font-semibold">' + formatPrice(cp) + '</td>' +
                        '<td class="px-6 py-4 text-right ' + (chg >= 0 ? 'text-green-400' : 'text-red-400') + '">' + (chg >= 0 ? '+' : '') + pct + '%</td>' +
                        '<td class="px-6 py-4 text-right text-gray-300">' + (s.pe_ratio || '\u2014') + '</td>' +
                        '<td class="px-6 py-4 text-right text-gray-300">' + (s.market_cap ? formatMCap(s.market_cap) : '\u2014') + '</td>' +
'<td class="px-6 py-4 text-right text-gray-300">' + (s.dividend_yield ? (parseFloat(s.dividend_yield || 0) * 100).toFixed(2) + '%' : '\u2014') + '</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.beta || '\u2014') + '</td>' +
                            '<td class="px-6 py-4 text-right text-gray-300">' + (s.avg_volume ? formatVol(s.avg_volume) : '\u2014') + '</td>';
                    tbody.appendChild(tr);
                });
                document.getElementById('resultsContainer').classList.remove('hidden');
            }
            document.getElementById('savedListsPanel').classList.add('hidden');
        }).catch(function() { alert('Failed to load list.'); });
    };

    window.deleteList = function(id) {
        if (!confirm('Delete this saved list?')) return;
        var formData = new FormData();
        formData.append('id', id);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        fetch('/api/screener/delete-list', { method: 'POST', body: formData })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) { loadSavedLists(); var c = document.getElementById('savedCount'); c.textContent = Math.max(0, parseInt(c.textContent || '0', 10) - 1); }
                else { alert(data.message); }
            }).catch(function() { alert('Delete failed'); });
    };

    function buildIndicatorOptions() {
        var html = '';
        TECH_GROUPS.forEach(function(g) {
            html += '<optgroup label="' + g.label + '">';
            g.options.forEach(function(o) { html += '<option value="' + o.value + '" data-period="' + o.period + '">' + o.label + '</option>'; });
            html += '</optgroup>';
        });
        return html;
    }

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

    addFundamentalFilter();
})();
</script>
