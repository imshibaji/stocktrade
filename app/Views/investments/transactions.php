<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold text-white">Transaction History</h1>
            <p class="text-gray-400 mt-1">All completed sell transactions with booked profit/loss</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="/investments" class="border border-accent text-accent hover:bg-accent/10 px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-plus mr-2"></i>New Investment
            </a>
            <a href="/investments/history" class="border border-gray-600 text-gray-300 hover:border-accent px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-history mr-2"></i>Portfolio History
            </a>
        </div>
    </div>

    <?php if (!empty($transactions)): ?>
    <div class="grid grid-cols-5 gap-4 mb-6">
        <div class="bg-surface rounded-xl border border-gray-700 p-4 text-center">
            <p class="text-gray-400 text-xs mb-1">Total Sells</p>
            <p class="text-white font-bold text-xl"><?= count($transactions) ?></p>
        </div>
        <div class="bg-surface rounded-xl border border-gray-700 p-4 text-center">
            <p class="text-gray-400 text-xs mb-1">Total Sale Value</p>
            <p class="text-white font-bold text-xl"><?= format_price($totalSellValue) ?></p>
        </div>
        <div class="bg-surface rounded-xl border border-gray-700 p-4 text-center">
            <p class="text-gray-400 text-xs mb-1">Gross P/L</p>
            <p class="font-bold text-xl <?= $totalGross >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                <?= $totalGross >= 0 ? '+' : '' ?><?= format_price($totalGross) ?>
            </p>
        </div>
        <div class="bg-surface rounded-xl border border-gray-700 p-4 text-center">
            <p class="text-gray-400 text-xs mb-1">Fees + Tax</p>
            <p class="font-bold text-xl text-yellow-400"><?= format_price($totalFees + $totalTax) ?></p>
        </div>
        <div class="bg-surface rounded-xl border border-gray-700 p-4 text-center">
            <p class="text-gray-400 text-xs mb-1">Net P/L</p>
            <p class="font-bold text-xl <?= $totalNet >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                <?= $totalNet >= 0 ? '+' : '' ?><?= format_price($totalNet) ?>
            </p>
        </div>
    </div>

    <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="text-left px-6 py-3">Stock</th>
                        <th class="text-right px-6 py-3">Shares</th>
                        <th class="text-right px-6 py-3">Buy Price</th>
                        <th class="text-right px-6 py-3">Sell Price</th>
                        <th class="text-right px-6 py-3">Cost Basis</th>
                        <th class="text-right px-6 py-3">Sale Value</th>
                        <th class="text-right px-6 py-3">Gross P/L</th>
                        <th class="text-right px-6 py-3">Fees + Tax</th>
                        <th class="text-right px-6 py-3">Net P/L</th>
                        <th class="text-right px-6 py-3">Return</th>
                        <th class="text-left px-6 py-3">Buy Date</th>
                        <th class="text-left px-6 py-3">Sell Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): 
                        $gross = (float) $tx['profit_loss'];
                        $fees = (float) ($tx['total_fees'] ?? 0);
                        $tax = (float) ($tx['total_tax'] ?? 0);
                        $net = (float) ($tx['net_profit_loss'] ?? 0);
                        $ret = (float) $tx['cost_basis'] > 0 ? ($net / (float) $tx['cost_basis']) * 100 : 0;
                    ?>
                    <tr class="border-b border-gray-700/50 hover:bg-page/50 cursor-pointer" onclick="location.href='/stocks/<?= $tx['stock_id'] ?>'">
                        <td class="px-6 py-4">
                            <span class="text-white font-semibold"><?= esc($tx['symbol']) ?></span>
                            <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-surface border border-gray-600 text-gray-400 align-middle ml-1"><?= esc(exchange_display($tx['exchange'] ?? null, $tx['exchange_display'] ?? null)) ?></span>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= (int) $tx['shares'] ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= format_price($tx['buy_price'], stock_currency($tx['exchange'] ?? null)) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= format_price($tx['sell_price'], stock_currency($tx['exchange'] ?? null)) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= format_price($tx['cost_basis'], stock_currency($tx['exchange'] ?? null)) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= format_price($tx['sale_value'], stock_currency($tx['exchange'] ?? null)) ?></td>
                        <td class="px-6 py-4 text-right font-semibold <?= $gross >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <?= $gross >= 0 ? '+' : '' ?><?= format_price($gross, stock_currency($tx['exchange'] ?? null)) ?>
                        </td>
                        <td class="px-6 py-4 text-right text-yellow-400"><?= format_price($fees + $tax, stock_currency($tx['exchange'] ?? null)) ?></td>
                        <td class="px-6 py-4 text-right font-semibold <?= $net >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <?= $net >= 0 ? '+' : '' ?><?= format_price($net, stock_currency($tx['exchange'] ?? null)) ?>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold <?= $ret >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <?= $ret >= 0 ? '+' : '' ?><?= number_format($ret, 2) ?>%
                        </td>
                        <td class="px-6 py-4 text-gray-300"><?= date('M d, Y', strtotime($tx['buy_date'])) ?></td>
                        <td class="px-6 py-4 text-gray-300"><?= date('M d, Y', strtotime($tx['sell_date'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-12 text-center">
        <i class="fas fa-exchange-alt text-6xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-xl font-semibold mb-2">No transactions yet</h3>
        <p class="text-gray-400 mb-6">When you sell investments, the completed transactions will appear here.</p>
        <a href="/investments" class="bg-accent hover:bg-accent-2 text-on-accent font-semibold px-8 py-3 rounded-lg transition inline-block">
            View Investments
        </a>
    </div>
    <?php endif; ?>
</section>
