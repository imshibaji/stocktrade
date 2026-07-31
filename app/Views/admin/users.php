            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">Users</h1>
                    <p class="text-gray-400 mt-1">Manage user accounts and permissions</p>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-900/30 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-4">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="bg-surface rounded-xl border border-gray-700 overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-page border-b border-gray-700">
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">ID</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Name</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Email</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Role</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Created</th>
                            <th class="text-right px-4 py-3 text-gray-300 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr class="border-b border-gray-700/50">
                                <td class="px-4 py-3 text-gray-400"><?= $user['id'] ?></td>
                                <td class="px-4 py-3 text-white"><?= esc($user['name']) ?></td>
                                <td class="px-4 py-3 text-gray-400"><?= esc($user['email']) ?></td>
                                <td class="px-4 py-3">
                                    <?php if ($user['is_admin']): ?>
                                        <span class="px-2 py-1 bg-accent/20 text-accent text-xs rounded-full">Admin</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-gray-700 text-gray-400 text-xs rounded-full">User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-sm"><?= esc($user['created_at']) ?></td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1">
                                        <?php if ($user['is_admin']): ?>
                                            <a href="/admin/users/remove-admin/<?= $user['id'] ?>" class="px-2 py-1 bg-gray-700 text-gray-300 text-xs rounded hover:bg-gray-600" title="Remove admin">Demote</a>
                                        <?php else: ?>
                                            <a href="/admin/users/make-admin/<?= $user['id'] ?>" class="px-2 py-1 bg-accent/20 text-accent text-xs rounded hover:bg-accent/30" title="Make admin">Promote</a>
                                        <?php endif; ?>
                                        <a href="/admin/users/delete/<?= $user['id'] ?>" class="px-2 py-1 bg-red-900/30 text-red-400 text-xs rounded hover:bg-red-900/50" title="Delete user" data-confirm="Delete this user?">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
