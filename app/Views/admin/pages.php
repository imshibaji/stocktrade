<section class="min-h-screen">
    <div class="flex">
        <!-- Admin Sidebar -->
        <div class="w-64 bg-navy2 border-r border-gray-700 min-h-screen">
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-white font-bold text-lg">Admin Panel</h2>
            </div>
            <nav class="p-2">
                <a href="/admin" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">Dashboard</a>
                <a href="/admin/users" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">Users</a>
                <a href="/admin/stocks" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">Stocks</a>
                <a href="/admin/screeners" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">Screeners</a>
                <a href="/admin/pages" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition text-white bg-navy">Pages</a>
                <a href="/admin/settings" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">Website Settings</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">Pages</h1>
                    <p class="text-gray-400 mt-1">Manage static pages (About, Contact, etc.)</p>
                </div>
                <a href="/admin/pages/create" class="px-4 py-2 bg-gold text-navy font-medium rounded-lg hover:bg-gold2 transition">Add New Page</a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-900/30 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-4">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="bg-navy2 rounded-xl border border-gray-700 overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-navy border-b border-gray-700">
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">ID</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Title</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Slug</th>
                            <th class="text-left px-4 py-3 text-gray-300 font-medium">Status</th>
                            <th class="text-right px-4 py-3 text-gray-300 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pages as $page): ?>
                            <tr class="border-b border-gray-700/50">
                                <td class="px-4 py-3 text-gray-400"><?= $page['id'] ?></td>
                                <td class="px-4 py-3 text-white"><?= esc($page['title']) ?></td>
                                <td class="px-4 py-3 text-gray-400 font-mono"><?= esc($page['slug']) ?></td>
                                <td class="px-4 py-3">
                                    <?php if ($page['is_active']): ?>
                                        <span class="px-2 py-1 bg-green-900/30 text-green-400 text-xs rounded-full">Active</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-gray-700 text-gray-400 text-xs rounded-full">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="/admin/pages/edit/<?= $page['id'] ?>" class="px-2 py-1 bg-navy border border-gray-600 text-gray-300 text-xs rounded hover:text-white">Edit</a>
                                    <a href="/admin/pages/delete/<?= $page['id'] ?>" class="px-2 py-1 bg-red-900/30 text-red-400 text-xs rounded hover:bg-red-900/50" data-confirm="Delete page '<?= esc($page['title']) ?>'?">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
