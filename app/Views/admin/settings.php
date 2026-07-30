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
                <a href="/admin/pages" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">Pages</a>
                <a href="/admin/settings" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition text-white bg-navy">Website Settings</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">Website Settings</h1>
                    <p class="text-gray-400 mt-1">Configure website-wide settings</p>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-900/30 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-4">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/admin/settings/update">
                <?= csrf_field() ?>
                <div class="space-y-6">
                    <?php foreach ($settings as $group => $groupSettings): ?>
                        <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
                            <h2 class="text-xl font-bold text-white mb-4"><?= ucfirst(str_replace('_', ' ', $group)) ?> Settings</h2>
                            <div class="space-y-4">
                                <?php foreach ($groupSettings as $setting): ?>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1"><?= esc($setting['label']) ?></label>
                                        <?php if ($setting['type'] === 'boolean'): ?>
                                            <select name="values[]" class="w-full px-3 py-2 bg-navy border border-gray-600 rounded-lg text-white focus:outline-none focus:border-gold">
                                                <option value="1" <?= $setting['value'] == '1' ? 'selected' : '' ?>>Enabled</option>
                                                <option value="0" <?= $setting['value'] == '0' ? 'selected' : '' ?>>Disabled</option>
                                            </select>
                                        <?php elseif ($setting['type'] === 'textarea'): ?>
                                            <textarea name="values[]" rows="3" class="w-full px-3 py-2 bg-navy border border-gray-600 rounded-lg text-white focus:outline-none focus:border-gold"><?= esc($setting['value']) ?></textarea>
                                        <?php else: ?>
                                            <input type="text" name="values[]" value="<?= esc($setting['value']) ?>" class="w-full px-3 py-2 bg-navy border border-gray-600 rounded-lg text-white focus:outline-none focus:border-gold">
                                        <?php endif; ?>
                                        <input type="hidden" name="keys[]" value="<?= esc($setting['key']) ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6">
                    <button type="submit" class="px-6 py-2 bg-gold text-navy font-medium rounded-lg hover:bg-gold2 transition">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</section>
