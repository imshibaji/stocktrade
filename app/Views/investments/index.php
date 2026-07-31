<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white">My Investments</h1>
                <span id="marketBadge" class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>
                </span>
            </div>
            <p class="text-gray-400 mt-1">Track and manage your stock investments</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="/portfolio" class="border border-accent text-accent hover:bg-accent/10 px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-chart-pie mr-2"></i>Portfolio Summary
            </a>
            <a href="/investments/history" class="border border-gray-600 text-gray-300 hover:border-accent px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-history mr-2"></i>History
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2">
            <div class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-white font-bold text-lg mb-6">Add New Investment</h2>
                <form action="/investments/create" method="post" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-gray-300 mb-2 text-sm">Select Stock</label>
                        <select name="stock_id" id="invStockSelect" required class="w-full bg-page border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-accent focus:outline-none">
                            <option value="">Choose a stock...</option>
                            <?php foreach ($stocks as $s): ?>
                            <option value="<?= $s['id'] ?>" data-price="<?= $s['current_price'] ?>" data-currency="<?= stock_currency($s['exchange'] ?? null) ?>"><?= esc($s['symbol']) ?> - <?= esc($s['name']) ?> (<?= format_price($s['current_price'], stock_currency($s['exchange'] ?? null)) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <div id="selectedPrice" class="text-gray-500 text-xs mt-1 hidden">Price: <span id="liveStockPrice"></span></div>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex border border-gray-600 rounded-lg overflow-hidden mb-2">
                            <button type="button" id="inputTypeAmount" class="input-type-btn flex-1 py-2 px-3 text-sm font-semibold bg-accent text-on-accent transition">Amount</button>
                            <button type="button" id="inputTypeQuantity" class="input-type-btn flex-1 py-2 px-3 text-sm text-gray-400 hover:text-white transition">Quantity</button>
                        </div>
                        <input type="hidden" name="input_type" id="inputType" value="amount">
                        <div id="amountInput">
                            <label class="block text-gray-300 mb-2 text-sm">Investment Amount (Rs)</label>
                            <input type="number" name="amount" min="1" step="0.01" required
                                class="w-full bg-page border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-accent focus:outline-none"
                                placeholder="e.g. 50000">
                        </div>
                        <div id="quantityInput" class="hidden">
                            <label class="block text-gray-300 mb-2 text-sm">Number of Shares</label>
                            <input type="number" name="quantity" min="1" step="1"
                                class="w-full bg-page border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-accent focus:outline-none"
                                placeholder="e.g. 10">
                        </div>
                        <div id="calcResult" class="text-gray-400 text-xs mt-2 hidden"></div>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-accent hover:bg-accent-2 text-on-accent font-bold py-3 rounded-lg transition">
                            <i class="fas fa-plus mr-2"></i>Add Investment
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="bg-surface rounded-xl border border-gray-700 p-6">
            <h3 class="text-white font-semibold mb-4">Tax Information</h3>
            <div class="space-y-4">
                <div class="bg-yellow-900/20 border border-yellow-700/50 rounded-lg p-4">
                    <h4 class="text-yellow-400 font-semibold text-sm mb-1">STCG - <?= $taxInfo['stcg']['rate'] ?></h4>
                    <p class="text-gray-400 text-xs"><?= $taxInfo['stcg']['description'] ?></p>
                </div>
                <div class="bg-blue-900/20 border border-blue-700/50 rounded-lg p-4">
                    <h4 class="text-blue-400 font-semibold text-sm mb-1">LTCG - <?= $taxInfo['ltcg']['rate'] ?></h4>
                    <p class="text-gray-400 text-xs"><?= $taxInfo['ltcg']['description'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($investments)): ?>
    <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h2 class="text-white font-bold text-lg">All Investments</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="text-left px-6 py-3">Stock</th>
                        <th class="text-right px-6 py-3">Buy Price</th>
                        <th class="text-right px-6 py-3">Current</th>
                        <th class="text-right px-6 py-3">Shares</th>
                        <th class="text-right px-6 py-3">Invested</th>
                        <th class="text-right px-6 py-3">Value</th>
                        <th class="text-right px-6 py-3">P/L</th>
                        <th class="text-center px-6 py-3">Status</th>
                        <th class="text-center px-6 py-3">Sell</th>
                        <th class="text-right px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($investments as $inv): 
    $pl = $investmentPl[(int) $inv['id']] ?? [];
?>
                    <tr class="border-b border-gray-700/50 hover:bg-page/50 cursor-pointer" onclick="location.href='/stocks/<?= $inv['stock_id'] ?>'">
                        <td class="px-6 py-4">
                            <a href="/stocks/<?= $inv['stock_id'] ?>" onclick="event.stopPropagation()" class="text-white font-semibold hover:text-accent"><?= esc($inv['symbol']) ?></a>
                            <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-surface border border-gray-600 text-gray-400 align-middle ml-1"><?= esc(exchange_display($inv['exchange'] ?? null, $inv['exchange_display'] ?? null)) ?></span>
                            <div class="text-gray-500 text-xs"><?= esc($inv['name']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-300 inv-buy-price cursor-pointer" data-currency="<?= stock_currency($inv['exchange'] ?? null) ?>" data-price="<?= $inv['buy_price'] ?>"><?= format_price($inv['buy_price'], $inv['currency']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300 inv-current-price cursor-pointer" data-currency="<?= stock_currency($inv['exchange'] ?? null) ?>" data-price="<?= $inv['current_price'] ?>"><?= format_price($inv['current_price'], $inv['currency']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= (int) $inv['shares'] ?></td>
                        <td class="px-6 py-4 text-right text-gray-300 inv-invested cursor-pointer" data-currency="<?= stock_currency($inv['exchange'] ?? null) ?>" data-price="<?= $inv['total_invested'] ?>"><?= format_price($inv['total_invested'], $inv['currency']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300 inv-value"><?= format_price($pl['current_value'], $inv['currency']) ?></td>
                        <td class="px-6 py-4 text-right inv-pl <?= $pl['gross_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <span class="inv-gross"><?= $pl['gross_profit'] >= 0 ? '+' : '' ?><?= format_price($pl['gross_profit'], $inv['currency']) ?></span>
                            <div class="text-xs inv-gross-pct <?= $pl['gross_profit_pct'] >= 0 ? 'text-green-500' : 'text-red-500' ?>">
                                <?= $pl['gross_profit_pct'] >= 0 ? '+' : '' ?><?= $pl['gross_profit_pct'] ?>%
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded text-xs <?= $inv['status'] === 'active' ? 'bg-green-900/30 text-green-400' : 'bg-gray-700 text-gray-400' ?>">
                                <?= ucfirst($inv['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($inv['status'] === 'active'): ?>
                            <a href="/investments/<?= $inv['id'] ?>/sell" onclick="event.stopPropagation()" class="text-red-400 hover:text-red-300 text-xs font-semibold bg-red-900/20 px-3 py-1.5 rounded-lg border border-red-900/30 hover:bg-red-900/40 transition">
                                <i class="fas fa-dollar-sign mr-1"></i>Sell
                            </a>
                            <?php else: ?>
                            <span class="text-gray-600 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-3">
                                <a href="/investments/<?= $inv['id'] ?>/edit" onclick="event.stopPropagation()" class="text-gray-400 hover:text-accent text-xs" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/investments/<?= $inv['id'] ?>/delete" method="post" class="inline" onsubmit="return confirm('Delete this investment?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" onclick="event.stopPropagation()" class="text-red-400 hover:text-red-300 text-xs" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</section>

<script>
(function() {
    function getSelectedPrice() {
        var sel = document.getElementById('invStockSelect');
        var opt = sel.options[sel.selectedIndex];
        return opt && opt.value ? parseFloat(opt.dataset.price) : null;
    }

    function getSelectedCurrency() {
        var sel = document.getElementById('invStockSelect');
        var opt = sel.options[sel.selectedIndex];
        return opt && opt.dataset.currency ? opt.dataset.currency : 'INR';
    }

    function fmtr(v) { return v.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    function updateCalc() {
        var price = getSelectedPrice();
        var priceDisplay = document.getElementById('selectedPrice');
        var priceSpan = document.getElementById('liveStockPrice');
        var calcResult = document.getElementById('calcResult');
        if (!price) {
            priceDisplay.classList.add('hidden');
            calcResult.classList.add('hidden');
            return;
        }
        var cur = getSelectedCurrency();
        var sym = CURRENCY_SYMBOLS[cur] || (cur + ' ');
        priceSpan.textContent = sym + fmtr(price);
        priceDisplay.classList.remove('hidden');

        var type = document.getElementById('inputType').value;
        if (type === 'amount') {
            var amt = parseFloat(document.querySelector('input[name="amount"]').value) || 0;
            var shares = Math.floor(amt / price);
            if (shares > 0) {
                var total = shares * price;
                calcResult.textContent = 'You can buy ' + shares + ' shares @ ' + sym + price.toFixed(2) + ' = ' + sym + fmtr(total);
                calcResult.classList.remove('hidden');
            } else if (amt > 0) {
                calcResult.textContent = 'Amount too low. Need at least ' + sym + price.toFixed(2) + ' for 1 share.';
                calcResult.classList.remove('hidden');
            } else {
                calcResult.classList.add('hidden');
            }
        } else {
            var qty = parseInt(document.querySelector('input[name="quantity"]').value) || 0;
            if (qty > 0) {
                var total = qty * price;
                calcResult.textContent = qty + ' shares @ ' + sym + price.toFixed(2) + ' = ' + sym + fmtr(total);
                calcResult.classList.remove('hidden');
            } else {
                calcResult.classList.add('hidden');
            }
        }
    }

    function switchInputType(type) {
        document.getElementById('inputType').value = type;
        document.getElementById('amountInput').classList.toggle('hidden', type !== 'amount');
        document.getElementById('quantityInput').classList.toggle('hidden', type !== 'quantity');
        document.getElementById('inputTypeAmount').className = 'input-type-btn flex-1 py-2 px-3 text-sm font-semibold transition ' + (type === 'amount' ? 'bg-accent text-on-accent' : 'text-gray-400 hover:text-white');
        document.getElementById('inputTypeQuantity').className = 'input-type-btn flex-1 py-2 px-3 text-sm font-semibold transition ' + (type === 'quantity' ? 'bg-accent text-on-accent' : 'text-gray-400 hover:text-white');
        var amountInput = document.querySelector('input[name="amount"]');
        var qtyInput = document.querySelector('input[name="quantity"]');
        if (type === 'quantity') {
            amountInput.removeAttribute('required');
            qtyInput.setAttribute('required', 'required');
        } else {
            qtyInput.removeAttribute('required');
            amountInput.setAttribute('required', 'required');
        }
        updateCalc();
    }

    document.getElementById('inputTypeAmount').addEventListener('click', function() { switchInputType('amount'); });
    document.getElementById('inputTypeQuantity').addEventListener('click', function() { switchInputType('quantity'); });
    document.getElementById('invStockSelect').addEventListener('change', updateCalc);
    var amtInput = document.querySelector('input[name="amount"]');
    var qtyInput2 = document.querySelector('input[name="quantity"]');
    if (amtInput) amtInput.addEventListener('input', updateCalc);
    if (qtyInput2) qtyInput2.addEventListener('input', updateCalc);

    var CURRENCY_SYMBOLS = { 'INR': '\u20B9', 'USD': '\u0024', 'EUR': '\u20AC', 'GBP': '\u00A3', 'JPY': '\u00A5', 'AUD': 'A\u0024', 'CAD': 'C\u0024', 'CHF': 'CHF ', 'CNY': '\u00A5', 'SGD': 'S\u0024', 'HKD': 'HK\u0024', 'KRW': '\u20A9', 'MXN': 'Mex\u0024', 'BRL': 'R\u0024', 'NZD': 'NZ\u0024', 'ZAR': 'R', 'SEK': 'kr', 'NOK': 'kr', 'DKK': 'kr', 'PLN': 'z\u0142', 'CZK': 'K\u010D', 'HUF': 'Ft', 'RUB': '\u20BD', 'TRY': '\u20BA', 'ILS': '\u20AA', 'THB': '\u0E3F', 'MYR': 'RM', 'IDR': 'Rp', 'PHP': '\u20B1', 'TWD': 'NT\u0024', 'VND': '\u20AB', 'AED': '\u062F.\u0625', 'SAR': '\u0631.\u0639', 'QAR': 'QR', 'KWD': 'KD', 'OMR': '\u0631.\u0639', 'BHD': '.\u062F.\u0628' };
    function stockCurrency(exch) {
        var m = { 'NSE': 'INR', 'BSE': 'INR', 'NSI': 'INR',
                  'LSE': 'GBP', 'TSE': 'JPY', 'HKEX': 'HKD',
                  'KRX': 'KRW', 'TSX': 'CAD', 'ASX': 'AUD',
                  'SWX': 'CHF', 'FRA': 'EUR', 'ETR': 'EUR',
                  'Euronext': 'EUR', 'MEX': 'MXN', 'BVMF': 'BRL',
                  'NMS': 'USD', 'NYQ': 'USD', 'NGM': 'USD' };
        return m[exch] || 'USD';
    }
    function formatPrice(v, c) { c = c || 'INR'; var sym = CURRENCY_SYMBOLS[c] || (c + ' '); return sym + parseFloat(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    var exchangeRatesCache = {};

    function fetchExchangeRate(fromCurr, toCurr) {
        if (fromCurr === toCurr) return Promise.resolve(1);
        var cacheKey = fromCurr + '_' + toCurr;
        if (exchangeRatesCache[cacheKey]) return Promise.resolve(exchangeRatesCache[cacheKey]);
        return fetch('https://open.er-api.com/v6/latest/' + encodeURIComponent(fromCurr))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.result === 'success' && data.rates[toCurr]) {
                    var rate = data.rates[toCurr];
                    exchangeRatesCache[cacheKey] = rate;
                    return rate;
                }
                return null;
            })
            .catch(function() { return null; });
    }

    function showConversionTooltip(el, price, fromCurr) {
        var existing = document.getElementById('currency-convert-tooltip');
        if (existing) existing.remove();

        var toCurr = BASE_CURRENCY || 'INR';
        var rect = el.getBoundingClientRect();

        var tooltip = document.createElement('div');
        tooltip.id = 'currency-convert-tooltip';
        tooltip.style.cssText = 'position:fixed;z-index:9999;top:' + (rect.bottom + 8) + 'px;left:' + Math.max(8, rect.left) + 'px;background:#1e1e2e;border:1px solid #313244;border-radius:8px;padding:8px 12px;font-size:12px;color:#cdd6f4;box-shadow:0 4px 12px rgba(0,0,0,0.4);min-width:180px;';
        tooltip.innerHTML = '<div style="font-weight:600;margin-bottom:4px;">' + formatPrice(price, fromCurr) + '</div><div style="color:#a6adc8;">Loading conversion...</div>';
        document.body.appendChild(tooltip);

        fetchExchangeRate(fromCurr, toCurr).then(function(rate) {
            if (rate !== null) {
                var converted = price * rate;
                var sym = CURRENCY_SYMBOLS[toCurr] || (toCurr + ' ');
                tooltip.innerHTML = '<div style="font-weight:600;margin-bottom:4px;">' + formatPrice(price, fromCurr) + '</div><div style="color:#a6e3a1;">' + sym + converted.toLocaleString("en-IN", {minimumFractionDigits:2,maximumFractionDigits:2}) + ' (' + toCurr + ')</div><div style="color:#6c7086;font-size:11px;margin-top:2px;">1 ' + fromCurr + ' = ' + rate.toFixed(4) + ' ' + toCurr + '</div>';
            } else {
                tooltip.innerHTML = '<div style="font-weight:600;margin-bottom:4px;">' + formatPrice(price, fromCurr) + '</div><div style="color:#f38ba8;">Conversion unavailable</div>';
            }
        });

        setTimeout(function() { if (tooltip.parentNode) tooltip.remove(); }, 8000);
        tooltip.addEventListener('click', function() { tooltip.remove(); });
    }

    var conversionTooltipTimeout = null;

    function showConversionTooltip(el, price, fromCurr) {
        var existing = document.getElementById('currency-convert-tooltip');
        if (existing) existing.remove();
        if (conversionTooltipTimeout) clearTimeout(conversionTooltipTimeout);

        var toCurr = BASE_CURRENCY || 'INR';
        var rect = el.getBoundingClientRect();

        var tooltip = document.createElement('div');
        tooltip.id = 'currency-convert-tooltip';
        tooltip.style.cssText = 'position:fixed;z-index:9999;top:' + (rect.bottom + 8) + 'px;left:' + Math.max(8, rect.left) + 'px;background:#1e1e2e;border:1px solid #313244;border-radius:8px;padding:8px 12px;font-size:12px;color:#cdd6f4;box-shadow:0 4px 12px rgba(0,0,0,0.4);min-width:180px;';
        tooltip.innerHTML = '<div style="font-weight:600;margin-bottom:4px;">' + formatPrice(price, fromCurr) + '</div><div style="color:#a6adc8;">Loading conversion...</div>';
        document.body.appendChild(tooltip);

        fetchExchangeRate(fromCurr, toCurr).then(function(rate) {
            if (rate !== null) {
                var converted = price * rate;
                var sym = CURRENCY_SYMBOLS[toCurr] || (toCurr + ' ');
                tooltip.innerHTML = '<div style="font-weight:600;margin-bottom:4px;">' + formatPrice(price, fromCurr) + '</div><div style="color:#a6e3a1;">' + sym + converted.toLocaleString("en-IN", {minimumFractionDigits:2,maximumFractionDigits:2}) + ' (' + toCurr + ')</div><div style="color:#6c7086;font-size:11px;margin-top:2px;">1 ' + fromCurr + ' = ' + rate.toFixed(4) + ' ' + toCurr + '</div>';
            } else {
                tooltip.innerHTML = '<div style="font-weight:600;margin-bottom:4px;">' + formatPrice(price, fromCurr) + '</div><div style="color:#f38ba8;">Conversion unavailable</div>';
            }
        });

        tooltip.addEventListener('mouseleave', function() { tooltip.remove(); });
    }

    function hideConversionTooltip() {
        var existing = document.getElementById('currency-convert-tooltip');
        if (existing) existing.remove();
    }

    document.addEventListener('mouseenter', function(e) {
        var priceCell = e.target.closest('.inv-buy-price, .inv-current-price, .inv-invested');
        if (priceCell && !e.target.closest('a')) {
            var price = parseFloat(priceCell.dataset.price);
            var curr = priceCell.dataset.currency;
            if (price && curr) showConversionTooltip(priceCell, price, curr);
        }
    }, true);

    document.addEventListener('mouseleave', function(e) {
        var priceCell = e.target.closest('.inv-buy-price, .inv-current-price, .inv-invested');
        if (priceCell) hideConversionTooltip();
    }, true);

    var BASE_CURRENCY = '<?= esc($base_currency ?? 'INR') ?>';
    var urlParams = new URLSearchParams(window.location.search);
    var preselectedStockId = urlParams.get('stock_id');
    if (preselectedStockId) {
        var stockSelect = document.getElementById('invStockSelect');
        if (stockSelect) {
            stockSelect.value = preselectedStockId;
            stockSelect.dispatchEvent(new Event('change'));
        }
    }

})();
</script>
