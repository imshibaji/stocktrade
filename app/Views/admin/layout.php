<section class="min-h-screen">
    <div class="flex">
        <!-- Admin Sidebar -->
        <div class="w-64 bg-navy2 border-r border-gray-700 min-h-screen">
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-white font-bold text-lg">Admin Panel</h2>
            </div>
            <nav class="p-2">
                <a href="/admin" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition <?= $activePage === 'dashboard' ? 'bg-navy text-white' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
                <a href="/admin/users" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition <?= $activePage === 'users' ? 'bg-navy text-white' : '' ?>">
                    <i class="fas fa-users"></i>Users
                </a>
                <a href="/admin/stocks" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition <?= $activePage === 'stocks' ? 'bg-navy text-white' : '' ?>">
                    <i class="fas fa-chart-line"></i>Stocks
                </a>
                <a href="/admin/screeners" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition <?= $activePage === 'screeners' ? 'bg-navy text-white' : '' ?>">
                    <i class="fas fa-filter"></i>Screeners
                </a>
                <a href="/admin/pages" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition <?= $activePage === 'pages' ? 'bg-navy text-white' : '' ?>">
                    <i class="fas fa-file-alt"></i>Pages
                </a>
                <a href="/admin/settings" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition <?= $activePage === 'settings' ? 'bg-navy text-white' : '' ?>">
                    <i class="fas fa-cog"></i>Website Settings
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <div class="p-6 max-w-7xl mx-auto">
                <?= $content ?? '' ?>
            </div>
        </div>
    </div>
</section>
