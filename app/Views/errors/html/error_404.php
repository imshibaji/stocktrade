<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | StockTrade Tips</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        page: '#141413',
                        surface: '#1c1c1b',
                        accent: '#D97757',
                        'accent-2': '#E29376',
                        'on-accent': '#141413',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-page text-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <i class="fas fa-search text-accent text-8xl opacity-80"></i>
        </div>
        <h1 class="text-6xl font-bold text-white mb-4">404</h1>
        <h2 class="text-2xl text-gray-300 mb-6">Page Not Found</h2>
        <p class="text-gray-500 max-w-md mx-auto mb-8">
            The page you're looking for doesn't exist or has been moved. 
            It might have been a bad link or the stock you're looking for was delisted.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/dashboard" class="bg-accent hover:bg-accent-2 text-on-accent font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
            </a>
            <a href="/stocks" class="border border-accent text-accent hover:bg-accent/10 font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-search mr-2"></i>Browse Stocks
            </a>
            <a href="/" class="border border-gray-600 text-gray-400 hover:text-white font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-home mr-1"></i>Back to Home
            </a>
        </div>
    </div>
</body>
</html>