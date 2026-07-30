<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error | StockTrade Tips</title>
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
<body class="bg-navy text-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-4xl">
        <div class="text-center mb-8">
            <i class="fas fa-<?= $icon ?? 'exclamation-circle' ?> text-<?= $color ?? 'yellow' ?>-500 text-8xl opacity-80"></i>
        </div>
        <h1 class="text-6xl font-bold text-white mb-4 text-center"><?= esc($code ?? 'Error') ?></h1>
        <h2 class="text-2xl text-gray-300 mb-6 text-center"><?= esc($title ?? 'An Error Occurred') ?></h2>
        <p class="text-gray-500 max-w-md mx-auto mb-8 text-center">
            <?= esc($message ?? 'Something went wrong. Please try again or contact support.') ?>
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <button onclick="window.location.reload()" class="bg-gold hover:bg-gold2 text-navy font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-sync-alt mr-2"></i>Retry
            </button>
            <a href="/dashboard" class="border border-gold text-gold hover:bg-gold/10 font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
            <a href="/" class="text-gray-400 hover:text-white text-sm self-center">
                <i class="fas fa-home mr-1"></i>Back to Home
            </a>
        </div>
        
        <?php if (ENVIRONMENT !== 'production' && isset($exception)): ?>
        <div class="text-left p-4 bg-navy2 rounded-lg border border-gray-700 overflow-hidden">
            <p class="text-red-400 text-xs mb-2">Debug Information (Development Only)</p>
            <pre class="text-xs text-gray-400 overflow-x-auto whitespace-pre-wrap"><?= esc((string)$exception) ?></pre>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>