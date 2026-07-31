<section class="text-center py-16">
    <h1 class="text-5xl font-bold text-white mb-4">
        Smart Stock <span class="text-accent">Trading Tips</span>
    </h1>
    <p class="text-xl text-gray-400 max-w-3xl mx-auto mb-8">
        Analyze stocks, get future predictions, track your investments, and calculate net profit/loss after taxes — all in one place.
    </p>
    <div class="flex justify-center space-x-4">
        <?php if (!is_logged_in()): ?>
        <a href="/register" class="bg-accent hover:bg-accent-2 text-on-accent font-bold px-8 py-3 rounded-lg text-lg transition">
            Get Started Free
        </a>
        <a href="/about" class="border border-accent text-accent hover:bg-accent/10 px-8 py-3 rounded-lg text-lg transition">
            Learn More
        </a>
        <?php else: ?>
        <a href="/dashboard" class="bg-accent hover:bg-accent-2 text-on-accent font-bold px-8 py-3 rounded-lg text-lg transition">
            Go to Dashboard
        </a>
        <a href="/stocks" class="border border-accent text-accent hover:bg-accent/10 px-8 py-3 rounded-lg text-lg transition">
            Browse Stocks
        </a>
        <?php endif; ?>
    </div>
</section>

<section class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16">
    <div class="bg-surface rounded-xl p-6 border border-gray-700 text-center hover:border-accent transition">
        <i class="fas fa-chart-bar text-4xl text-accent mb-3"></i>
        <h3 class="text-white font-semibold mb-2">Stock Analysis</h3>
        <p class="text-gray-400 text-sm">Deep analysis with historical price trends, key metrics, and sector comparisons.</p>
    </div>
    <div class="bg-surface rounded-xl p-6 border border-gray-700 text-center hover:border-accent transition">
        <i class="fas fa-chart-line text-4xl text-accent mb-3"></i>
        <h3 class="text-white font-semibold mb-2">Future Predictions</h3>
        <p class="text-gray-400 text-sm">AI-powered 30-day price predictions with confidence scores for every stock.</p>
    </div>
    <div class="bg-surface rounded-xl p-6 border border-gray-700 text-center hover:border-accent transition">
        <i class="fas fa-calculator text-4xl text-accent mb-3"></i>
        <h3 class="text-white font-semibold mb-2">P&L Calculator</h3>
        <p class="text-gray-400 text-sm">Calculate gross profit/loss and net returns after STCG/LTCG tax deductions.</p>
    </div>
    <div class="bg-surface rounded-xl p-6 border border-gray-700 text-center hover:border-accent transition">
        <i class="fas fa-star text-4xl text-accent mb-3"></i>
        <h3 class="text-white font-semibold mb-2">Watchlist</h3>
        <p class="text-gray-400 text-sm">Save your favorite stocks and track them daily with real-time analysis.</p>
    </div>
</section>

<section class="mb-16">
    <h2 class="text-3xl font-bold text-white text-center mb-8">Top Stocks by Market Cap</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($topStocks as $stock): ?>
        <?php $change = get_price_change((float) $stock['current_price'], (float) $stock['previous_close']); ?>
        <?php $cur = stock_currency($stock['exchange'] ?? null); ?>
        <div class="bg-surface rounded-xl p-6 border border-gray-700 hover:border-accent transition cursor-pointer"
             onclick="<?= is_logged_in() ? "window.location.href='/stocks/{$stock['id']}'" : "window.location.href='/login'" ?>">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-white font-bold text-lg"><?= esc($stock['symbol']) ?></h3>
                    <p class="text-gray-400 text-sm"><?= esc($stock['name']) ?></p>
                </div>
                <span class="text-xs px-2 py-1 rounded bg-page border border-gray-600 text-gray-300"><?= esc($stock['sector']) ?></span>
            </div>
            <div class="flex justify-between items-end">
                <span class="text-2xl font-bold text-white"><?= format_price($stock['current_price'], $cur) ?></span>
                <span class="<?= $change['change'] >= 0 ? 'text-green-400' : 'text-red-400' ?> text-sm">
                    <?= $change['change'] >= 0 ? '+' : '' ?><?= $change['percent'] ?>%
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="mb-16">
    <h2 class="text-3xl font-bold text-white text-center mb-8">How It Works</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="text-center">
            <div class="w-16 h-16 bg-accent/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl font-bold text-accent">1</span>
            </div>
            <h3 class="text-white font-semibold mb-2">Build Watchlist</h3>
            <p class="text-gray-400 text-sm">Add stocks to your watchlist for quick access and daily monitoring.</p>
        </div>
        <div class="text-center">
            <div class="w-16 h-16 bg-accent/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl font-bold text-accent">2</span>
            </div>
            <h3 class="text-white font-semibold mb-2">Analyze & Invest</h3>
            <p class="text-gray-400 text-sm">View detailed analysis, predictions, and record your investments with amounts.</p>
        </div>
        <div class="text-center">
            <div class="w-16 h-16 bg-accent/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl font-bold text-accent">3</span>
            </div>
            <h3 class="text-white font-semibold mb-2">Track Profits</h3>
            <p class="text-gray-400 text-sm">Track gross profit/loss and net returns after calculating STCG/LTCG taxes.</p>
        </div>
    </div>
</section>

<section class="text-center py-12 bg-surface rounded-2xl border border-gray-700">
    <h2 class="text-3xl font-bold text-white mb-4">Ready to Start Trading Smarter?</h2>
    <p class="text-gray-400 mb-6">Join now and get access to stock analysis, predictions, and profit tracking.</p>
    <?php if (!is_logged_in()): ?>
    <a href="/register" class="bg-accent hover:bg-accent-2 text-on-accent font-bold px-10 py-3 rounded-lg text-lg transition">
        Create Free Account
    </a>
    <?php else: ?>
    <a href="/stocks" class="bg-accent hover:bg-accent-2 text-on-accent font-bold px-10 py-3 rounded-lg text-lg transition">
        Explore Stocks
    </a>
    <?php endif; ?>
</section>
