<?php
$errorSiteName = 'StockTrade Tips';
try {
    helper('setting');
    $errorSiteName = site_name();
} catch (\Throwable $e) {
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | <?= esc($errorSiteName) ?></title>
    <script>
    (function () {
        try {
            var mq = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)');
            var apply = function () { document.documentElement.setAttribute('data-theme', mq.matches ? 'night' : 'day'); };
            apply();
            if (mq && mq.addEventListener) mq.addEventListener('change', apply);
            else if (mq && mq.addListener) mq.addListener(apply);
        } catch (e) {
            document.documentElement.setAttribute('data-theme', 'night');
        }
    })();
    </script>
    <style>
        :root, [data-theme="night"] {
            color-scheme: dark;
            --bg-page: 20 20 19;
            --bg-surface: 28 28 27;
            --ink: 250 249 245;
            --ink-2: 240 239 233;
            --ink-3: 232 230 220;
            --ink-4: 196 195 189;
            --edge: 88 87 82;
            --accent: 217 119 87;
            --accent-2: 226 147 118;
            --on-accent: 20 20 19;
        }
        [data-theme="day"] {
            color-scheme: light;
            --bg-page: 250 249 245;
            --bg-surface: 255 255 255;
            --ink: 20 20 19;
            --ink-2: 42 41 39;
            --ink-3: 64 63 60;
            --ink-4: 92 91 86;
            --edge: 199 198 192;
            --accent: 201 96 63;
            --accent-2: 226 147 118;
            --on-accent: 250 249 245;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        page: 'rgb(var(--bg-page))',
                        surface: 'rgb(var(--bg-surface))',
                        ink: 'rgb(var(--ink))',
                        'ink-2': 'rgb(var(--ink-2))',
                        'ink-3': 'rgb(var(--ink-3))',
                        'ink-4': 'rgb(var(--ink-4))',
                        edge: 'rgb(var(--edge))',
                        accent: 'rgb(var(--accent))',
                        'accent-2': 'rgb(var(--accent-2))',
                        'on-accent': 'rgb(var(--on-accent))',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-page text-ink min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <i class="fas fa-exclamation-triangle text-red-500 text-8xl opacity-80"></i>
        </div>
        <h1 class="text-6xl font-bold text-ink mb-4">500</h1>
        <h2 class="text-2xl text-ink-2 mb-6">Server Error</h2>
        <p class="text-ink-4 max-w-md mx-auto mb-8">
            Something went wrong on our end. Our team's side. We've been notified and are looking into it.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="window.location.reload()" class="bg-accent hover:bg-accent-2 text-on-accent font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-sync-alt mr-2"></i>Retry
            </button>
            <a href="/dashboard" class="border border-accent text-accent hover:bg-surface font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
            <a href="/" class="border border-edge text-ink-4 hover:text-ink font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-home mr-1"></i>Back to Home
            </a>
        </div>
        
        <?php if (ENVIRONMENT !== 'production'): ?>
        <div class="mt-12 text-left max-w-2xl mx-auto p-4 bg-surface rounded-lg border border-edge overflow-hidden">
            <p class="text-red-400 text-xs mb-2">Debug Information (Development Only)</p>
            <pre class="text-xs text-ink-3 overflow-x-auto"><?= esc($message ?? 'Unknown error') ?></pre>
            <?php if (isset($exception)): ?>
            <div class="mt-4 text-ink-4 text-xs">
                <p><strong>Type:</strong> <?= get_class($exception) ?></p>
                <p><strong>Message:</strong> <?= esc($exception->getMessage()) ?></p>
                <p><strong>File:</strong> <?= esc($exception->getFile()) ?></p>
                <p><strong>Line:</strong> <?= esc($exception->getLine()) ?></p>
                <p><strong>Trace:</strong></p>
                <pre class="text-xs text-ink-3 overflow-x-auto"><?= esc($exception->getTraceAsString()) ?></pre>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>