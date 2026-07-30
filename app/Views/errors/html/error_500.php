<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | StockTrade Tips</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#0B1220',
                        navy2: '#111827',
                        gold: '#D4A853',
                        gold2: '#E8C56D',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-navy text-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <i class="fas fa-exclamation-triangle text-red-500 text-8xl opacity-80"></i>
        </div>
        <h1 class="text-6xl font-bold text-white mb-4">500</h1>
        <h2 class="text-2xl text-gray-300 mb-6">Server Error</h2>
        <p class="text-gray-500 max-w-md mx-auto mb-8">
            Something went wrong on our end. Our team's side. We've been notified and are looking into it.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="window.location.reload()" class="bg-gold hover:bg-gold2 text-navy font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-sync-alt mr-2"></i>Retry
            </button>
            <a href="/dashboard" class="border border-gold text-gold hover:bg-gold/10 font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
            <a href="/" class="text-gray-400 hover:text-white text-sm">
                <i class="fas fa-home mr-1"></i>Back to Home
            </a>
        </div>
        
        <?php if (ENVIRONMENT !== 'production'): ?>
        <div class="mt-12 text-left max-w-2xl mx-auto p-4 bg-navy2 rounded-lg border border-gray-700 overflow-hidden">
            <p class="text-red-400 text-xs mb-2">Debug Information (Development Only)</p>
            <pre class="text-xs text-gray-400 overflow-x-auto"><?= esc($message ?? 'Unknown error') ?></pre>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>