<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bad Request | StockTrade Tips</title>
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
            <i class="fas fa-exclamation-circle text-orange-500 text-8xl opacity-80"></i>
        </div>
        <h1 class="text-6xl font-bold text-white mb-4">400</h1>
        <h2 class="text-2xl text-gray-300 mb-6">Bad Request</h2>
        <p class="text-gray-500 max-w-md mx-auto mb-8">
            The request was invalid or cannot be served. This might be due to malformed data or missing parameters.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="window.history.back()" class="bg-accent hover:bg-accent-2 text-on-accent font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Go Back
            </button>
            <a href="/dashboard" class="border border-accent text-accent hover:bg-accent/10 font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
            <a href="/" class="text-gray-400 hover:text-white text-sm">
                <i class="fas fa-home mr-1"></i>Home
            </a>
        </div>
        
        <?php if (ENVIRONMENT !== 'production'): ?>
        <div class="mt-12 text-left max-w-2xl mx-auto p-4 bg-surface rounded-lg border border-gray-700 overflow-hidden">
            <p class="text-red-400 text-xs mb-2">Debug Information (Development Only)</p>
            <pre class="text-xs text-gray-400 overflow-x-auto"><?= esc($message) ?></pre>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>