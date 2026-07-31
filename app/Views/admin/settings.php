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
                        <div class="bg-surface rounded-xl border border-gray-700 p-6">
                            <h2 class="text-xl font-bold text-white mb-4"><?= $group === 'seo' ? 'SEO' : ucfirst(str_replace('_', ' ', $group)) ?> Settings</h2>
                            <div class="space-y-4">
                                <?php foreach ($groupSettings as $setting): ?>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1"><?= esc($setting['label']) ?></label>
                                        <?php if ($setting['type'] === 'boolean'): ?>
                                            <select name="values[]" class="w-full px-3 py-2 bg-page border border-gray-600 rounded-lg text-white focus:outline-none focus:border-accent">
                                                <option value="1" <?= $setting['value'] == '1' ? 'selected' : '' ?>>Enabled</option>
                                                <option value="0" <?= $setting['value'] == '0' ? 'selected' : '' ?>>Disabled</option>
                                            </select>
                                        <?php elseif ($setting['type'] === 'textarea'): ?>
                                            <textarea name="values[]" rows="3" class="w-full px-3 py-2 bg-page border border-gray-600 rounded-lg text-white focus:outline-none focus:border-accent"><?= esc($setting['value']) ?></textarea>
                                        <?php else: ?>
                                            <input type="text" name="values[]" value="<?= esc($setting['value']) ?>" class="w-full px-3 py-2 bg-page border border-gray-600 rounded-lg text-white focus:outline-none focus:border-accent">
                                        <?php endif; ?>
                                        <input type="hidden" name="keys[]" value="<?= esc($setting['key']) ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6">
                    <button type="submit" class="px-6 py-2 bg-accent text-on-accent font-medium rounded-lg hover:bg-accent-2 transition">Save Settings</button>
                </div>
            </form>
