<section class="py-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-white mb-4">About <span class="text-accent"><?= esc(site_name()) ?></span></h1>
        <p class="text-gray-400 max-w-2xl mx-auto">Empowering investors with data-driven insights, AI-powered predictions, and comprehensive portfolio management tools.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
        <div>
            <h2 class="text-2xl font-bold text-white mb-4">Our Mission</h2>
            <p class="text-gray-400 leading-relaxed mb-4">
                At <?= esc(site_name()) ?>, we believe every investor deserves access to professional-grade analysis tools. Our platform combines real-time market data with advanced prediction algorithms to help you make informed investment decisions.
            </p>
            <p class="text-gray-400 leading-relaxed">
                Whether you're a seasoned trader or just starting, our intuitive dashboard, detailed stock analysis, and accurate profit/loss calculations give you the edge you need in today's dynamic market.
            </p>
        </div>
        <div class="bg-surface rounded-xl p-8 border border-gray-700">
            <h3 class="text-accent font-semibold text-lg mb-4">Platform Highlights</h3>
            <ul class="space-y-3">
                <li class="flex items-start text-gray-300">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                    <span>Real-time stock price tracking with 90-day historical data</span>
                </li>
                <li class="flex items-start text-gray-300">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                    <span>AI-driven 30-day future price predictions with confidence scores</span>
                </li>
                <li class="flex items-start text-gray-300">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                    <span>Comprehensive investment tracking with buy/sell records</span>
                </li>
                <li class="flex items-start text-gray-300">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                    <span>Automatic gross profit/loss calculations</span>
                </li>
                <li class="flex items-start text-gray-300">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                    <span>STCG (15%) and LTCG (10%) tax computation for net profit/loss</span>
                </li>
                <li class="flex items-start text-gray-300">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                    <span>Personalized watchlist for tracking favorite stocks</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        <div class="text-center bg-surface rounded-xl p-8 border border-gray-700">
            <div class="text-4xl text-accent mb-4">
                <i class="fas fa-chart-pie"></i>
            </div>
            <h3 class="text-white font-bold text-xl mb-2">15+ Stocks</h3>
            <p class="text-gray-400 text-sm">Tracked across multiple sectors with complete historical data</p>
        </div>
        <div class="text-center bg-surface rounded-xl p-8 border border-gray-700">
            <div class="text-4xl text-accent mb-4">
                <i class="fas fa-robot"></i>
            </div>
            <h3 class="text-white font-bold text-xl mb-2">30-Day Forecast</h3>
            <p class="text-gray-400 text-sm">AI-powered predictions with confidence scoring for every stock</p>
        </div>
        <div class="text-center bg-surface rounded-xl p-8 border border-gray-700">
            <div class="text-4xl text-accent mb-4">
                <i class="fas fa-file-invoice"></i>
            </div>
            <h3 class="text-white font-bold text-xl mb-2">Tax Ready</h3>
            <p class="text-gray-400 text-sm">Auto-calculates STCG and LTCG taxes on your profits</p>
        </div>
    </div>

    <div class="bg-surface rounded-xl p-8 border border-gray-700">
        <h2 class="text-2xl font-bold text-white mb-6">Understanding Tax Calculations</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="border border-yellow-700/50 rounded-lg p-6 bg-yellow-900/10">
                <h3 class="text-yellow-400 font-semibold text-lg mb-2">STCG - Short Term Capital Gains</h3>
                <p class="text-gray-400 text-sm mb-3">Applied when shares are held for <strong class="text-white">less than 1 year</strong>.</p>
                <div class="text-2xl font-bold text-yellow-400">15%</div>
                <p class="text-gray-500 text-xs mt-1">On all gains (no exemption)</p>
            </div>
            <div class="border border-blue-700/50 rounded-lg p-6 bg-blue-900/10">
                <h3 class="text-blue-400 font-semibold text-lg mb-2">LTCG - Long Term Capital Gains</h3>
                <p class="text-gray-400 text-sm mb-3">Applied when shares are held for <strong class="text-white">more than 1 year</strong>.</p>
                <div class="text-2xl font-bold text-blue-400">10%</div>
                <p class="text-gray-500 text-xs mt-1">On gains above &#8377;1,00,000 exemption</p>
            </div>
        </div>
    </div>
</section>
