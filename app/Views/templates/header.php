<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'StockTrade Tips') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#0a1929',
                        navy2: '#0f2744',
                        gold: '#d4a853',
                        gold2: '#f0c060',
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-navy text-gray-100 min-h-screen">

<?php if (is_logged_in()): ?>

    <nav class="bg-navy2 border-b border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <a href="/dashboard" class="flex items-center space-x-2">
                        <i class="fas fa-chart-line text-gold text-2xl"></i>
                        <span class="text-xl font-bold text-gold">StockTrade<span class="text-white">Tips</span></span>
                    </a>
                    <span id="navMarketBadge" class="hidden md:inline text-xs px-2 py-1 rounded-full border border-gray-600 text-gray-400">
                        <i class="fas fa-circle text-gray-500 text-[8px] mr-1"></i>
                    </span>
                    <div class="hidden md:flex space-x-1">
                        <a href="/dashboard" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-navy transition">
                            <i class="fas fa-tachometer-alt mr-1.5"></i>Dashboard
                        </a>
                        <a href="/stocks" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-navy transition">
                            <i class="fas fa-search mr-1.5"></i>Stocks
                        </a>
                        <a href="/watchlist" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-navy transition">
                            <i class="fas fa-star mr-1.5"></i>Watchlist
                        </a>
                        <a href="/screener" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-navy transition">
                            <i class="fas fa-filter mr-1.5"></i>Screener
                        </a>
                        <a href="/investments" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-navy transition">
                            <i class="fas fa-briefcase mr-1.5"></i>Investments
                        </a>
                        <a href="/portfolio" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-navy transition">
                            <i class="fas fa-chart-pie mr-1.5"></i>Portfolio
                        </a>
                        <a href="/investments/transactions" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-navy transition">
                            <i class="fas fa-exchange-alt mr-1.5"></i>Transactions
                        </a>
                        <div class="relative" id="apiDropdown">
                            <button onclick="toggleApiNav()" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-navy transition flex items-center">
                                <i class="fas fa-code mr-1.5"></i>API <i class="fas fa-chevron-down ml-1.5 text-[10px]"></i>
                            </button>
                            <div id="apiDropdownMenu" class="hidden absolute top-full left-0 mt-1 w-56 bg-navy2 border border-gray-600 rounded-lg shadow-xl z-50 py-1">
                                <a href="/api-playground" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                    <i class="fas fa-flask w-5 mr-2 text-gold"></i>API Playground
                                </a>
                                <a href="/api/live-prices" target="_blank" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                    <i class="fas fa-chart-line w-5 mr-2 text-green-400"></i>Live Prices
                                </a>
                                <a href="/api/quote/PFC" target="_blank" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                    <i class="fas fa-search-dollar w-5 mr-2 text-blue-400"></i>Quote Lookup
                                </a>
                                <a href="/api/sync-prices" target="_blank" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-navy transition">
                                    <i class="fas fa-sync w-5 mr-2 text-purple-400"></i>Sync Prices
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/settings" class="hidden md:flex items-center text-gray-300 text-sm hover:text-gold transition">
                        <i class="fas fa-user-circle text-gold mr-1.5"></i><?= esc(current_user()['name']) ?>
                    </a>
                    <a href="/logout" class="text-gray-400 hover:text-red-400 px-3 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-sign-out-alt mr-1"></i><span class="hidden md:inline">Logout</span>
                    </a>
                    <button class="md:hidden text-gray-300" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-700">
            <div class="px-4 py-3 space-y-1">
                <a href="/dashboard" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                    <i class="fas fa-tachometer-alt w-5 mr-2"></i>Dashboard
                </a>
                <a href="/stocks" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                    <i class="fas fa-search w-5 mr-2"></i>Stocks
                </a>
                <a href="/watchlist" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                    <i class="fas fa-star w-5 mr-2"></i>Watchlist
                </a>
                <a href="/screener" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                    <i class="fas fa-filter w-5 mr-2"></i>Screener
                </a>
                <a href="/investments" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                    <i class="fas fa-briefcase w-5 mr-2"></i>Investments
                </a>
                <a href="/portfolio" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                    <i class="fas fa-chart-pie w-5 mr-2"></i>Portfolio
                </a>
                <a href="/investments/transactions" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                    <i class="fas fa-exchange-alt w-5 mr-2"></i>Transactions
                </a>
                <a href="/api-playground" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                    <i class="fas fa-code w-5 mr-2 text-gold"></i>API Playground
                </a>
                <hr class="border-gray-700 my-2">
                <a href="/settings" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">
                    <i class="fas fa-cog w-5 mr-2"></i>Settings
                </a>
                <a href="/logout" class="flex items-center px-3 py-2 rounded-lg text-red-400 hover:text-red-300 hover:bg-navy">
                    <i class="fas fa-sign-out-alt w-5 mr-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

<?php else: ?>

    <nav class="bg-navy2 border-b border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <a href="/" class="flex items-center space-x-2">
                        <i class="fas fa-chart-line text-gold text-2xl"></i>
                        <span class="text-xl font-bold text-gold">StockTrade<span class="text-white">Tips</span></span>
                    </a>
                    <div class="hidden md:flex space-x-1">
                        <a href="/" class="px-3 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-navy transition">Home</a>
                        <a href="/about" class="px-3 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-navy transition">About</a>
                        <a href="/contact" class="px-3 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-navy transition">Contact</a>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/login" class="px-4 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-navy transition">Login</a>
                    <a href="/register" class="bg-gold hover:bg-gold2 text-navy font-semibold px-4 py-2 rounded-lg text-sm transition">Get Started</a>
                    <button class="md:hidden text-gray-300" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-700">
            <div class="px-4 py-3 space-y-1">
                <a href="/" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">Home</a>
                <a href="/about" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">About</a>
                <a href="/contact" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">Contact</a>
                <hr class="border-gray-700 my-2">
                <a href="/login" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-navy">Login</a>
                <a href="/register" class="block px-3 py-2 rounded-lg text-gold hover:text-gold2 hover:bg-navy font-semibold">Get Started</a>
            </div>
        </div>
    </nav>

<?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-green-900/50 border border-green-600 text-green-300 px-4 py-3 rounded-lg flex justify-between items-center">
            <span><i class="fas fa-check-circle mr-2"></i><?= esc(session()->getFlashdata('success')) ?></span>
            <button onclick="this.parentElement.remove()" class="text-green-300 hover:text-green-100">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-red-900/50 border border-red-600 text-red-300 px-4 py-3 rounded-lg flex justify-between items-center">
            <span><i class="fas fa-exclamation-circle mr-2"></i><?= esc(session()->getFlashdata('error')) ?></span>
            <button onclick="this.parentElement.remove()" class="text-red-300 hover:text-red-100">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-blue-900/50 border border-blue-600 text-blue-300 px-4 py-3 rounded-lg flex justify-between items-center">
            <span><i class="fas fa-info-circle mr-2"></i><?= esc(session()->getFlashdata('info')) ?></span>
            <button onclick="this.parentElement.remove()" class="text-blue-300 hover:text-blue-100">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <script>
    function toggleApiNav() {
        document.getElementById('apiDropdownMenu').classList.toggle('hidden');
    }
    document.addEventListener('click', function(e) {
        var dd = document.getElementById('apiDropdown');
        if (dd && !dd.contains(e.target)) {
            var menu = document.getElementById('apiDropdownMenu');
            if (menu) menu.classList.add('hidden');
        }
    });
    </script>

    <main class="max-w-7xl mx-auto px-4 py-8">
