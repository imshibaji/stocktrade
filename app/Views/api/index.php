<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">API Playground</h1>
            <p class="text-gray-400 mt-1">Test all available API endpoints</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6" id="apiEndpoints">
        <?php
        $endpoints = [
            'search' => [
                'method' => 'GET',
                'path' => '/api/search/{query}',
                'desc' => 'Yahoo Finance symbol search',
                'params' => [
                    ['name' => 'query', 'label' => 'Query', 'default' => 'RECLTD', 'in' => 'path']
                ]
            ],
            'quote' => [
                'method' => 'GET',
                'path' => '/api/quote/{symbol}/{exchange}',
                'desc' => 'Single stock quote with all fields',
                'params' => [
                    ['name' => 'symbol', 'label' => 'Symbol', 'default' => 'PFC', 'in' => 'path'],
                    ['name' => 'exchange', 'label' => 'Exchange', 'default' => 'NSE', 'in' => 'path']
                ]
            ],
            'quotes' => [
                'method' => 'GET',
                'path' => '/api/quotes/{symbols}/{exchange}',
                'desc' => 'Multiple stock quotes',
                'params' => [
                    ['name' => 'symbols', 'label' => 'Symbols (comma-sep)', 'default' => 'PFC,PNB,RECLTD', 'in' => 'path'],
                    ['name' => 'exchange', 'label' => 'Exchange', 'default' => 'NSE', 'in' => 'path']
                ]
            ],
            'historical' => [
                'method' => 'GET',
                'path' => '/api/historical/{symbol}/{exchange}/{time}',
                'desc' => 'Historical price data',
                'params' => [
                    ['name' => 'symbol', 'label' => 'Symbol', 'default' => 'PFC', 'in' => 'path'],
                    ['name' => 'exchange', 'label' => 'Exchange', 'default' => 'NSE', 'in' => 'path'],
                    ['name' => 'time', 'label' => 'Time period', 'default' => '14 days', 'in' => 'path']
                ]
            ],
            'dividends' => [
                'method' => 'GET',
                'path' => '/api/dividends/{symbol}/{exchange}/{time}',
                'desc' => 'Historical dividend data',
                'params' => [
                    ['name' => 'symbol', 'label' => 'Symbol', 'default' => 'PFC', 'in' => 'path'],
                    ['name' => 'exchange', 'label' => 'Exchange', 'default' => 'NSE', 'in' => 'path'],
                    ['name' => 'time', 'label' => 'Time period', 'default' => '5 years', 'in' => 'path']
                ]
            ],
            'splits' => [
                'method' => 'GET',
                'path' => '/api/splits/{symbol}/{exchange}/{time}',
                'desc' => 'Historical stock split data',
                'params' => [
                    ['name' => 'symbol', 'label' => 'Symbol', 'default' => 'PFC', 'in' => 'path'],
                    ['name' => 'exchange', 'label' => 'Exchange', 'default' => 'NSE', 'in' => 'path'],
                    ['name' => 'time', 'label' => 'Time period', 'default' => '5 years', 'in' => 'path']
                ]
            ],
            'exchange' => [
                'method' => 'GET',
                'path' => '/api/exchange/{from}/{to}',
                'desc' => 'Currency exchange rate',
                'params' => [
                    ['name' => 'from', 'label' => 'From currency', 'default' => 'USD', 'in' => 'path'],
                    ['name' => 'to', 'label' => 'To currency', 'default' => 'INR', 'in' => 'path']
                ]
            ],
            'options' => [
                'method' => 'GET',
                'path' => '/api/options/{symbol}/{exchange}',
                'desc' => 'Options chain data',
                'params' => [
                    ['name' => 'symbol', 'label' => 'Symbol', 'default' => 'PFC', 'in' => 'path'],
                    ['name' => 'exchange', 'label' => 'Exchange', 'default' => 'NSE', 'in' => 'path']
                ]
            ],
            'summary' => [
                'method' => 'GET',
                'path' => '/api/summary/{symbol}/{exchange}',
                'desc' => 'Quote summary with modules',
                'params' => [
                    ['name' => 'symbol', 'label' => 'Symbol', 'default' => 'PFC', 'in' => 'path'],
                    ['name' => 'exchange', 'label' => 'Exchange', 'default' => 'NSE', 'in' => 'path'],
                    ['name' => 'modules', 'label' => 'Modules (comma-sep)', 'default' => 'summaryProfile,financialData,price', 'in' => 'query']
                ]
            ],
            'news' => [
                'method' => 'GET',
                'path' => '/api/news/{symbol}',
                'desc' => 'Latest news for a symbol',
                'params' => [
                    ['name' => 'symbol', 'label' => 'Symbol', 'default' => 'PFC', 'in' => 'path']
                ]
            ],
            'app_search' => [
                'method' => 'GET',
                'path' => '/api/search?q={query}',
                'desc' => 'App stock search (local DB + Yahoo)',
                'params' => [
                    ['name' => 'q', 'label' => 'Query', 'default' => 'PFC', 'in' => 'query']
                ],
                'auth' => true
            ],
            'quote_lookup' => [
                'method' => 'GET',
                'path' => '/api/quote/{symbol}',
                'desc' => 'Extended lookup (quote + summary merged)',
                'params' => [
                    ['name' => 'symbol', 'label' => 'Symbol', 'default' => 'PFC', 'in' => 'path']
                ],
                'auth' => true
            ],
        ];
        ?>

        <?php foreach ($endpoints as $key => $ep): ?>
        <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden api-card" data-key="<?= $key ?>">
            <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="px-2 py-0.5 rounded text-xs font-mono font-bold
                        <?= $ep['method'] === 'GET' ? 'bg-green-900/50 text-green-400' : 'bg-yellow-900/50 text-yellow-400' ?>">
                        <?= $ep['method'] ?>
                    </span>
                    <code class="text-sm text-gray-200 font-mono"><?= esc($ep['path']) ?></code>
                    <?php if (!empty($ep['auth'])): ?>
                    <span class="px-1.5 py-0.5 rounded text-[10px] bg-blue-900/50 text-blue-400 font-mono">AUTH</span>
                    <?php endif; ?>
                </div>
                <button onclick="toggleApiCard('<?= $key ?>')" class="text-gray-500 hover:text-accent transition">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="px-5 py-3 bg-page/30">
                <p class="text-gray-400 text-sm"><?= esc($ep['desc']) ?></p>
            </div>
            <div id="apiBody_<?= $key ?>" class="hidden">
                <div class="px-5 py-4 space-y-3">
                    <?php foreach ($ep['params'] as $param): ?>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">
                            <?= esc($param['label']) ?>
                            <?php if (($param['in'] ?? '') === 'query'): ?>
                            <span class="text-blue-400">(query)</span>
                            <?php endif; ?>
                        </label>
                        <input type="text" id="apiParam_<?= $key ?>_<?= $param['name'] ?>"
                            value="<?= esc($param['default'] ?? '') ?>"
                            data-in="<?= $param['in'] ?? 'path' ?>"
                            data-name="<?= $param['name'] ?>"
                            class="api-param w-full bg-page border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:border-accent focus:outline-hidden font-mono">
                    </div>
                    <?php endforeach; ?>
                    <button onclick="tryEndpoint('<?= $key ?>')"
                        class="w-full bg-accent hover:bg-accent-2 text-on-accent font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center justify-center space-x-2">
                        <i class="fas fa-play"></i>
                        <span>Try It</span>
                    </button>
                </div>
                <div id="apiResult_<?= $key ?>" class="hidden border-t border-gray-700">
                    <div class="flex items-center justify-between px-5 py-2 bg-gray-900/50">
                        <span class="text-xs text-gray-400 font-mono" id="apiUrl_<?= $key ?>"></span>
                        <span id="apiStatus_<?= $key ?>" class="text-xs font-mono"></span>
                    </div>
                    <pre id="apiOutput_<?= $key ?>" class="p-4 text-xs font-mono text-gray-300 overflow-x-auto max-h-80 overflow-y-auto"></pre>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
function toggleApiCard(key) {
    var body = document.getElementById('apiBody_' + key);
    body.classList.toggle('hidden');
    var icon = body.closest('.api-card').querySelector('.fa-chevron-down');
    if (icon) icon.classList.toggle('rotate-180');
}

function buildUrl(key) {
    var card = document.querySelector('.api-card[data-key="' + key + '"]');
    var path = card.querySelector('code').textContent.trim();
    var params = card.querySelectorAll('.api-param');
    var url = path;
    var qs = [];
    params.forEach(function(p) {
        var val = encodeURIComponent(p.value.trim());
        var loc = p.getAttribute('data-in');
        var name = p.getAttribute('data-name');
        if (loc === 'query') {
            qs.push(name + '=' + val);
        } else {
            url = url.replace('{' + name + '}', val);
        }
    });
    if (qs.length > 0) url += '?' + qs.join('&');
    return url;
}

function tryEndpoint(key) {
    var url = buildUrl(key);
    var resultEl = document.getElementById('apiResult_' + key);
    var outputEl = document.getElementById('apiOutput_' + key);
    var urlEl = document.getElementById('apiUrl_' + key);
    var statusEl = document.getElementById('apiStatus_' + key);
    var btn = resultEl.closest('.api-card').querySelector('button[onclick*="tryEndpoint"]');
    var orig = btn.innerHTML;

    resultEl.classList.remove('hidden');
    outputEl.textContent = 'Loading...';
    urlEl.textContent = url;
    statusEl.textContent = '';
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span> Loading...</span>';

    fetch(url)
        .then(function(r) {
            statusEl.textContent = r.status + ' ' + r.statusText;
            statusEl.className = 'text-xs font-mono ' + (r.ok ? 'text-green-400' : 'text-red-400');
            return r.text();
        })
        .then(function(text) {
            try {
                var json = JSON.parse(text);
                outputEl.textContent = JSON.stringify(json, null, 2);
            } catch (e) {
                outputEl.textContent = text;
            }
        })
        .catch(function(err) {
            outputEl.textContent = 'Error: ' + err.message;
            statusEl.textContent = 'NETWORK ERROR';
            statusEl.className = 'text-xs font-mono text-red-400';
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = orig;
        });
}
</script>

<style>
.rotate-180 { transform: rotate(180deg); }
.api-card .fa-chevron-down { transition: transform 0.2s; }
</style>
