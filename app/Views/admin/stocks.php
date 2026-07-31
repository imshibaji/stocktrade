            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">Stocks</h1>
                    <p class="text-gray-400 mt-1">Manage all stocks in the system</p>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-900/30 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-4">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="bg-surface rounded-xl border border-gray-700 overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-page border-b border-gray-700">
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">ID</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Symbol</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Name</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Exchange</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Sector</th>
                            <th class="text-right px-4 py-3 text-gray-300 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stocks as $stock): ?>
                            <tr class="border-b border-gray-700/50">
                                <td class="px-4 py-3 text-gray-400"><?= $stock['id'] ?></td>
                                <td class="px-4 py-3 text-white font-mono"><?= esc($stock['symbol']) ?></td>
                                <td class="px-4 py-3 text-gray-300"><?= esc($stock['name']) ?></td>
                                <td class="px-4 py-3 text-gray-400"><?= esc($stock['exchange']) ?></td>
                                <td class="px-4 py-3 text-gray-400"><?= esc($stock['sector'] ?? 'N/A') ?></td>
                                <td class="px-4 py-3 text-right">
                                    <a href="/stocks/<?= $stock['id'] ?>" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white">View</a>
                                    <a href="/admin/stocks/delete/<?= $stock['id'] ?>" class="px-2 py-1 bg-red-900/30 text-red-400 text-xs rounded hover:bg-red-900/50" data-confirm="Delete stock <?= esc($stock['symbol']) ?>?">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
