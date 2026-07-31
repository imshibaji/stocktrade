<section class="min-h-screen">
    <div class="flex">
        <!-- Admin Sidebar -->
        <aside class="w-60 bg-surface border-r border-gray-700 min-h-screen flex flex-col shrink-0">
            <div class="px-5 py-4 border-b border-gray-700 flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-accent/15 text-accent flex items-center justify-center text-base shrink-0">
                    <i class="fas fa-chart-line"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-white font-semibold text-sm leading-tight truncate"><?= esc(site_name()) ?></p>
                    <p class="text-gray-500 text-[11px] uppercase tracking-widest mt-0.5">Admin Console</p>
                </div>
            </div>

            <?php
            $adminNav = [
                'dashboard' => ['/admin', 'fa-tachometer-alt', 'Dashboard'],
                'users'     => ['/admin/users', 'fa-users', 'Users'],
                'stocks'    => ['/admin/stocks', 'fa-chart-line', 'Stocks'],
                'screeners' => ['/admin/screeners', 'fa-filter', 'Screeners'],
                'pages'     => ['/admin/pages', 'fa-file-alt', 'Pages'],
                'featured'  => ['/admin/featured-stocks', 'fa-star', 'Home Stocks'],
                'settings'  => ['/admin/settings', 'fa-cog', 'Settings'],
            ];
            $activePage = $activePage ?? 'dashboard';
            ?>
            <nav class="p-2.5 space-y-1 flex-1">
                <?php foreach ($adminNav as $navKey => [$navHref, $navIcon, $navLabel]): ?>
                <?php $isActive = $navKey === $activePage; ?>
                <a href="<?= $navHref ?>"
                   class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors duration-150 <?= $isActive ? 'bg-page text-white' : 'text-gray-400 hover:text-white hover:bg-page/60' ?>">
                    <?php if ($isActive): ?>
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-full bg-accent"></span>
                    <?php endif; ?>
                    <i class="fas <?= $navIcon ?> w-4 text-center text-sm <?= $isActive ? 'text-accent' : 'text-gray-500 group-hover:text-gray-300' ?>"></i>
                    <?= $navLabel ?>
                </a>
                <?php endforeach; ?>
            </nav>

            <div class="p-3 border-t border-gray-700">
                <?php $adminUser = current_user(); ?>
                <div class="flex items-center gap-3 px-2.5 py-2.5 rounded-lg bg-page/60">
                    <span class="w-8 h-8 rounded-full bg-accent/20 text-accent flex items-center justify-center text-xs font-semibold shrink-0"><?= esc(strtoupper(substr(($adminUser['name'] ?? 'A'), 0, 1))) ?></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-white font-medium truncate"><?= esc($adminUser['name'] ?? 'Admin') ?></p>
                        <a href="/" class="text-[11px] text-gray-500 hover:text-accent transition-colors">View site &rarr;</a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <div class="p-6 lg:p-8 max-w-7xl mx-auto">
                <?= $content ?? '' ?>
            </div>
        </div>
    </div>
</section>
