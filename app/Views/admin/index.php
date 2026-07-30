<section class="min-h-screen">
    <div class="flex">
        <!-- Admin Sidebar -->
        <div class="w-64 bg-navy2 border-r border-gray-700 min-h-screen">
            <div class="p-4 border-b border-gray-700">
                <h2 class="text-white font-bold text-lg">Admin Panel</h2>
            </div>
            <nav class="p-2">
                <a href="/admin" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
                <a href="/admin/users" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">
                    <i class="fas fa-users"></i>Users
                </a>
                <a href="/admin/stocks" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">
                    <i class="fas fa-chart-line"></i>Stocks
                </a>
                <a href="/admin/screeners" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">
                    <i class="fas fa-filter"></i>Screeners
                </a>
                <a href="/admin/pages" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">
                    <i class="fas fa-file-alt"></i>Pages
                </a>
                <a href="/admin/settings" class="flex items-center gap-2 px-4 py-3 text-gray-300 hover:text-white hover:bg-navy rounded-lg transition">
                    <i class="fas fa-cog"></i>Website Settings
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
                <p class="text-gray-400 mt-1">Overview of your StockTrade Tips platform</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-navy2 rounded-xl p-6 border border-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Total Users</p>
                            <p class="text-3xl font-bold text-white"><?= $stats['users'] ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-400 text-xl"></i>
                        </div>
                    </div>
                    <a href="/admin/users" class="text-blue-400 text-sm hover:text-blue-300 mt-2 inline-block">Manage users</a>
                </div>
                <div class="bg-navy2 rounded-xl p-6 border border-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Total Stocks</p>
                            <p class="text-3xl font-bold text-white"><?= $stats['stocks'] ?></p>
                        </div>
                        <div class="w-12 h-12 bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-green-400 text-xl"></i>
                        </div>
                    </div>
                    <a href="/admin/stocks" class="text-green-400 text-sm hover:text-green-300 mt-2 inline-block">Manage stocks</a>
                </div>
                <div class="bg-navy2 rounded-xl p-6 border border-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Total Pages</p>
                            <p class="text-3xl font-bold text-white"><?= $stats['pages'] ?></p>
                        </div>
                        <div class="w-12 h-12 bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-purple-400 text-xl"></i>
                        </div>
                    </div>
                    <a href="/admin/pages" class="text-purple-400 text-sm hover:text-purple-300 mt-2 inline-block">Manage pages</a>
                </div>
                <div class="bg-navy2 rounded-xl p-6 border border-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Total Screeners</p>
                            <p class="text-3xl font-bold text-white"><?= $stats['screeners'] ?></p>
                        </div>
                        <div class="w-12 h-12 bg-gold/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-filter text-gold text-xl"></i>
                        </div>
                    </div>
                    <a href="/admin/screeners" class="text-gold text-sm hover:text-gold2 mt-2 inline-block">Manage screeners</a>
                </div>
            </div>

            <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
                <h2 class="text-white font-bold text-lg mb-4">Quick Actions</h2>
                <div class="flex flex-wrap gap-3">
                    <a href="/admin/users" class="bg-navy border border-gray-600 text-gray-300 hover:text-white hover:border-gold px-4 py-2 rounded-lg text-sm transition">Manage Users</a>
                    <a href="/admin/stocks" class="bg-navy border border-gray-600 text-gray-300 hover:text-white hover:border-gold px-4 py-2 rounded-lg text-sm transition">Manage Stocks</a>
                    <a href="/admin/pages/create" class="bg-navy border border-gray-600 text-gray-300 hover:text-white hover:border-gold px-4 py-2 rounded-lg text-sm transition">Add New Page</a>
                    <a href="/admin/settings" class="bg-navy border border-gray-600 text-gray-300 hover:text-white hover:border-gold px-4 py-2 rounded-lg text-sm transition">Website Settings</a>
                </div>
            </div>
        </div>
    </div>
</section>
