            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">Predictions</h1>
                    <p class="text-gray-400 mt-1">Manage saved prediction queries</p>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-900/30 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-4">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-900/30 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-4">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="bg-surface rounded-xl border border-gray-700 overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-page border-b border-gray-700">
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">ID</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Name</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">User</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Method</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Status</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Horizon</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Results</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Visibility</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Created</th>
                            <th class="text-right px-4 py-3 text-gray-300 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($queries as $query): ?>
                            <?php $status = prediction_status_meta($query['status']); ?>
                            <tr class="border-b border-gray-700/50">
                                <td class="px-4 py-3 text-gray-400"><?= $query['id'] ?></td>
                                <td class="px-4 py-3">
                                    <a href="/predictions/public/<?= $query['id'] ?>" class="text-white hover:text-accent" title="View public page">
                                        <?= esc($query['name']) ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-400">
                                    <?= esc($query['creator_name'] ?? 'User #' . $query['user_id']) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-1 rounded bg-page border border-gray-600 text-gray-300">
                                        <?= esc(prediction_method_label($query['method'])) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-1 rounded border <?= $status['class'] ?>"><?= $status['label'] ?></span>
                                </td>
                                <td class="px-4 py-3 text-gray-400"><?= (int) $query['horizon_days'] ?> days</td>
                                <td class="px-4 py-3 text-gray-400"><?= (int) $query['results_count'] ?></td>
                                <td class="px-4 py-3">
                                    <?php if (!empty($query['is_public'])): ?>
                                        <span class="text-xs px-2 py-1 rounded bg-green-900/30 border border-green-700 text-green-300"><i class="fas fa-globe mr-1"></i>Public</span>
                                    <?php else: ?>
                                        <span class="text-xs px-2 py-1 rounded bg-page border border-gray-600 text-gray-400"><i class="fas fa-lock mr-1"></i>Private</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-sm"><?= esc($query['created_at']) ?></td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1">
                                        <?php if (!empty($query['is_public'])): ?>
                                            <a href="/admin/predictions/toggle-public/<?= $query['id'] ?>" class="px-2 py-1 bg-gray-700 text-gray-300 text-xs rounded hover:bg-gray-600" title="Make private">Make private</a>
                                        <?php else: ?>
                                            <a href="/admin/predictions/toggle-public/<?= $query['id'] ?>" class="px-2 py-1 bg-accent/20 text-accent text-xs rounded hover:bg-accent/30" title="Make public">Make public</a>
                                        <?php endif; ?>
                                        <a href="/admin/predictions/delete/<?= $query['id'] ?>" class="px-2 py-1 bg-red-900/30 text-red-400 text-xs rounded hover:bg-red-900/50" title="Delete prediction" data-confirm="Delete this prediction query and all its results?">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
