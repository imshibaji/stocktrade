<div class="drawer lg:drawer-open">
    <input id="admin-drawer" type="checkbox" class="drawer-toggle" />

    <div class="drawer-content flex flex-col min-h-screen">
        <!-- Top navbar: brand on mobile, hamburger toggles sidebar -->
        <div class="navbar bg-base-100 border-b border-base-300 px-4 lg:px-6 h-14">
            <div class="navbar-start">
                <label for="admin-drawer" class="btn btn-ghost btn-circle lg:hidden">
                    <i class="fas fa-bars"></i>
                </label>
                <span class="font-semibold text-base-content hidden sm:inline-block">Admin Console</span>
            </div>
            <div class="navbar-end">
                <a href="/admin" class="btn btn-ghost btn-sm text-base-content/70">Dashboard</a>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-4 lg:p-8 max-w-7xl mx-auto w-full">
                <?= $content ?? '' ?>
            </div>
        </div>

        <footer class="border-t border-base-300 bg-base-200">
            <div class="max-w-7xl mx-auto px-4 lg:px-8 py-3 text-xs text-base-content/50">
                StockTrade &middot; Admin Console
            </div>
        </footer>
    </div>

    <!-- Sidebar: visible on desktop, toggles on mobile -->
    <div class="drawer-side">
        <label for="admin-drawer" class="drawer-overlay"></label>

        <div class="bg-base-200 flex flex-col min-h-screen w-60">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-base-300">
                <span class="w-9 h-9 rounded-xl bg-primary/15 text-primary flex items-center justify-center shrink-0">
                    <i class="fas fa-chart-line"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-base-content font-semibold text-sm truncate"><?= esc(site_name()) ?></p>
                    <p class="text-base-content/50 text-[10px] uppercase tracking-widest mt-0.5">Admin Console</p>
                </div>
            </div>

            <?php
            $adminNav = [
                'dashboard' => ['/admin', 'fa-tachometer-alt', 'Dashboard'],
                'users'     => ['/admin/users', 'fa-users', 'Users'],
                'stocks'    => ['/admin/stocks', 'fa-chart-line', 'Stocks'],
                'screeners' => ['/admin/screeners', 'fa-filter', 'Screeners'],
                'predictions' => ['/admin/predictions', 'fa-chart-line', 'Predictions'],
                'pages'     => ['/admin/pages', 'fa-file-alt', 'Pages'],
                'featured'  => ['/admin/featured-stocks', 'fa-star', 'Home Stocks'],
                'settings'  => ['/admin/settings', 'fa-cog', 'Settings'],
            ];
            $activePage = $activePage ?? 'dashboard';
            ?>

            <nav class="menu menu-vertical p-3 space-y-1 overflow-y-auto flex-1">
                <?php foreach ($adminNav as $navKey => [$navHref, $navIcon, $navLabel]): ?>
                    <?php $isActive = $navKey === $activePage; ?>
                    <li>
                        <a href="<?= $navHref ?>"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors duration-150 <?= $isActive
                                ? 'bg-primary text-primary-content font-medium'
                                : 'text-base-content/70 hover:text-base-content hover:bg-base-300/50' ?>">
                            <i class="fas <?= $navIcon ?> w-4 text-center"></i>
                            <?= $navLabel ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </nav>

            <div class="p-3 border-t border-base-300">
                <?php $adminUser = current_user(); ?>
                <div class="flex items-center gap-3 px-2.5 py-2.5 rounded-lg bg-base-300/30">
                    <span class="w-8 h-8 rounded-full bg-primary/20 text-primary flex items-center justify-center text-xs font-semibold shrink-0">
                        <?= esc(strtoupper(substr((string) ($adminUser['name'] ?? 'A'), 0, 1))) ?>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-base-content font-medium truncate"><?= esc($adminUser['name'] ?? 'Admin') ?></p>
                        <a href="/" class="text-[11px] text-base-content/50 hover:text-primary transition-colors">View site &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
