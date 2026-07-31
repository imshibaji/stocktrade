            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">Screeners</h1>
                    <p class="text-gray-400 mt-1">Manage saved stock screeners</p>
                </div>
                <a href="/admin/screeners/delete-all" class="px-4 py-2 bg-red-900/30 text-red-400 rounded-lg hover:bg-red-900/50 text-sm" data-confirm="Delete ALL saved screeners? This cannot be undone.">Delete All</a>
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
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Name</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">User</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Stocks</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Visibility</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($screeners as $screener): ?>
                            <tr class="border-b border-gray-700/50">
                                <td class="px-4 py-3 text-gray-400"><?= $screener['id'] ?></td>
                                <td class="px-4 py-3 text-white"><?= esc($screener['name']) ?></td>
                                <td class="px-4 py-3 text-gray-400">User #<?= $screener['user_id'] ?></td>
                                <td class="px-4 py-3 text-gray-400"><?= (int) $screener['stock_count'] ?></td>
                                <td class="px-4 py-3">
                                    <?php if (!empty($screener['is_public'])): ?>
                                        <span class="text-xs px-2 py-1 rounded bg-green-900/30 border border-green-700 text-green-300"><i class="fas fa-globe mr-1"></i>Public</span>
                                    <?php else: ?>
                                        <span class="text-xs px-2 py-1 rounded bg-page border border-gray-600 text-gray-400"><i class="fas fa-lock mr-1"></i>Private</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-sm"><?= esc($screener['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
