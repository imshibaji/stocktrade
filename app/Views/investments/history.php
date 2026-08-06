<section>
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white">Investment History</h1>
                <?= market_badge('NSE') ?>
            </div>
            <p class="text-gray-400 mt-1">Complete record of all your trades</p>
        </div>
        <a href="/investments" class="border border-accent text-accent hover:bg-accent/10 px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-plus mr-2"></i>New Investment
        </a>
    </div>

    <?php if (empty($investments)): ?>
    <div class="bg-surface rounded-xl border border-gray-700 p-12 text-center">
        <i class="fas fa-history text-6xl text-gray-600 mb-4"></i>
        <h3 class="text-white text-xl font-semibold mb-2">No investment history</h3>
        <p class="text-gray-400 mb-6">Start investing to build your trading history.</p>
        <a href="/stocks" class="bg-accent hover:bg-accent-2 text-on-accent font-semibold px-8 py-3 rounded-lg transition inline-block">
            Browse Stocks
        </a>
    </div>
    <?php else: ?>
    <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="text-left px-6 py-3">Stock</th>
                        <th class="text-left px-6 py-3">Buy Date</th>
                        <th class="text-right px-6 py-3">Buy Price</th>
                        <th class="text-right px-6 py-3">Shares</th>
                        <th class="text-right px-6 py-3">Invested</th>
                        <th class="text-right px-6 py-3">Current / Sell Price</th>
                        <th class="text-left px-6 py-3">Sell Date</th>
                        <th class="text-center px-6 py-3">Status</th>
                        <th class="text-right px-6 py-3">P/L</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($investments as $inv):
                        $isActive = $inv['status'] === 'active';
                        if ($isActive) {
                            $pl = $investmentPl[(int) $inv['id']] ?? [];
                            $actualPl = $pl['gross_profit'] ?? 0;
                            $actualPlPct = $pl['gross_profit_pct'] ?? 0;
                        } else {
                            $actualPl = ((float) $inv['sell_price'] - (float) $inv['buy_price']) * (float) $inv['shares'];
                            $actualPlPct = (float) $inv['buy_price'] > 0 ? (((float) $inv['sell_price'] - (float) $inv['buy_price']) / (float) $inv['buy_price']) * 100 : 0;
                        }
                        ?>
                    <tr class="border-b border-gray-700/50 hover:bg-page/50 cursor-pointer" onclick="location.href='/stocks/<?= $inv['stock_id'] ?>'">
                        <td class="px-6 py-4">
                            <a href="/stocks/<?= $inv['stock_id'] ?>" onclick="event.stopPropagation()" class="text-white font-semibold hover:text-accent"><?= esc($inv['symbol']) ?></a>
                            <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-surface border border-gray-600 text-gray-400 align-middle ml-1"><?= esc(exchange_display($inv['exchange'] ?? null, $inv['exchange_display'] ?? null)) ?></span>
                            <div class="text-gray-500 text-xs"><?= esc($inv['name']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-300"><?= date('M d, Y', strtotime($inv['buy_date'])) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= format_price($inv['buy_price'], $inv['currency']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= (int) $inv['shares'] ?></td>
                        <td class="px-6 py-4 text-right text-gray-300"><?= format_price($inv['total_invested'], $inv['currency']) ?></td>
                        <td class="px-6 py-4 text-right text-gray-300 hist-price">
                            <?php if ($isActive): ?>
                            <span class="hist-live"><?= format_price($inv['current_price'], $inv['currency']) ?></span>
                            <?php else: ?>
                            <?= format_price($inv['sell_price'], $inv['currency']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-300">
                            <?= $inv['sell_date'] ? date('M d, Y', strtotime($inv['sell_date'])) : '<span class="text-gray-600">-</span>' ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded text-xs hist-status <?= $isActive ? 'bg-green-900/30 text-green-400' : 'bg-gray-700 text-gray-400' ?>">
                                <?= ucfirst($inv['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right hist-pl font-semibold <?= $actualPl >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                            <span class="hist-pl-val"><?= $actualPl >= 0 ? '+' : '' ?><?= format_price($actualPl, $inv['currency']) ?></span>
                            <div class="text-xs hist-pl-pct <?= $actualPlPct >= 0 ? 'text-green-500' : 'text-red-500' ?>">
                                <?= $actualPlPct >= 0 ? '+' : '' ?><?= round($actualPlPct, 2) ?>%
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


