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
            <a href="/portfolio" class="border border-gold text-gold hover:bg-gold/10 px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-chart-pie mr-2"></i>Portfolio Summary
            </a>
            <a href="/investments/history" class="border border-gray-600 text-gray-300 hover:border-gold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-history mr-2"></i>History
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2">
            <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
                <h2 class="text-white font-bold text-lg mb-6">Add New Investment</h2>
                <form action="/investments/create" method="post" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-gray-300 mb-2 text-sm">Select Stock</label>
                        <select name="stock_id" id="invStockSelect" required class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none">
                            <option value="">Choose a stock...</option>
                            <?php foreach ($stocks as $s): ?>
                            <option value="<?= $s['id'] ?>" data-price="<?= $s['current_price'] ?>" data-currency="<?= stock_currency($s['exchange'] ?? null) ?>"><?= esc($s['symbol']) ?> - <?= esc($s['name']) ?> (<?= format_price($s['current_price'], stock_currency($s['exchange'] ?? null)) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <div id="selectedPrice" class="text-gray-500 text-xs mt-1 hidden">Price: <span id="liveStockPrice"></span></div>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex border border-gray-600 rounded-lg overflow-hidden mb-2">
                            <button type="button" id="inputTypeAmount" class="input-type-btn flex-1 py-2 px-3 text-sm font-semibold bg-gold text-navy transition">Amount</button>
                            <button type="button" id="inputTypeQuantity" class="input-type-btn flex-1 py-2 px-3 text-sm text-gray-400 hover:text-white transition">Quantity</button>
                        </div>
                        <input type="hidden" name="input_type" id="inputType" value="amount">
                        <div id="amountInput">
                            <label class="block text-gray-300 mb-2 text-sm">Investment Amount (Rs)</label>
                            <input type="number" name="amount" min="1" step="0.01" required
                                class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none"
                                placeholder="e.g. 50000">
                        </div>
                        <div id="quantityInput" class="hidden">
                            <label class="block text-gray-300 mb-2 text-sm">Number of Shares</label>
                            <input type="number" name="quantity" min="1" step="1"
                                class="w-full bg-navy border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-gold focus:outline-none"
                                placeholder="e.g. 10">
                        </div>
                        <div id="calcResult" class="text-gray-400 text-xs mt-2 hidden"></div>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-gold hover:bg-gold2 text-navy font-bold py-3 rounded-lg transition">
                            <i class="fas fa-plus mr-2"></i>Add Investment
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
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
    <div class="bg-navy2 rounded-xl border border-gray-700 overflow-hidden">
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
                    <tr class="border-b border-gray-700/50 hover:bg-navy/50 cursor-pointer" onclick="location.href='/stocks/<?= $inv['stock_id'] ?>'">
                        <td class="px-6 py-4">
                            <a href="/stocks/<?= $inv['stock_id'] ?>" onclick="event.stopPropagation()" class="text-white font-semibold hover:text-gold"><?= esc($inv['symbol']) ?></a>
                            <div class="text-gray-500 text-xs"><?= esc($inv['name']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-300 inv-buy-price"><?= format_price($inv['buy_price']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300 inv-current-price"><?= format_price($inv['current_price']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= (int) $inv['shares'] ?></td>
                        <td class="px-6 py-4 text-right text-gray-300 inv-invested"><?= format_price($inv['total_invested']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300 inv-value"><?= format_price($pl['current_value']) ?></td>
                        <td class="px-6 py-4 text-right inv-pl <?= $pl['gross_profit'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <span class="inv-gross"><?= $pl['gross_profit'] >= 0 ? '+' : '' ?><?= format_price($pl['gross_profit']) ?></span>
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
                                <a href="/investments/<?= $inv['id'] ?>/edit" onclick="event.stopPropagation()" class="text-gray-400 hover:text-gold text-xs" title="Edit">
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
        document.getElementById('inputTypeAmount').className = 'input-type-btn flex-1 py-2 px-3 text-sm font-semibold transition ' + (type === 'amount' ? 'bg-gold text-navy' : 'text-gray-400 hover:text-white');
        document.getElementById('inputTypeQuantity').className = 'input-type-btn flex-1 py-2 px-3 text-sm font-semibold transition ' + (type === 'quantity' ? 'bg-gold text-navy' : 'text-gray-400 hover:text-white');
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

    var CURRENCY_SYMBOLS = { 'INR': '\u20B9', 'USD': '\u0024', 'EUR': '\u20AC', 'GBP': '\u00A3', 'JPY': '\u00A5', 'AUD': 'A\u0024', 'CAD': 'C\u0024', 'CHF': 'CHF ', 'CNY': '\u00A5', 'SGD': 'S\u0024' };
    function stockCurrency(exch) { return (exch && (exch.indexOf('NS')>=0||exch.indexOf('BSE')>=0)) ? 'INR' : 'USD'; }
    function formatPrice(v, c) { c = c || 'INR'; var sym = CURRENCY_SYMBOLS[c] || (c + ' '); return sym + parseFloat(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

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
