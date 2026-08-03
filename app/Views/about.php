<section class="py-8" aria-labelledby="about-heading">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 id="about-heading" class="text-4xl font-bold text-white mb-4">About <span class="text-accent"><?= esc(site_name()) ?></span></h1>
            <p class="text-gray-400 max-w-2xl mx-auto">Empowering investors with data-driven insights, AI-powered predictions, and comprehensive portfolio management tools.</p>
        </div>

        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "<?= esc(site_name()) ?>",
            "url": "<?= esc(site_url('/')) ?>",
            "description": "A free stock analysis and prediction platform offering real-time data, AI-driven forecasts, portfolio tracking, and automated tax calculations.",
            "author": {
                "@type": "Person",
                "name": "Shibaji Debnath",
                "url": "https://www.shibajidebnath.com"
            },
            "sameAs": [
                "https://www.shibajidebnath.com"
            ]
        }
        </script>

        <article class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16" aria-labelledby="mission-heading">
            <section>
                <h2 id="mission-heading" class="text-2xl font-bold text-white mb-4">Our Mission</h2>
                <p class="text-gray-400 leading-relaxed mb-4">
                    At <?= esc(site_name()) ?>, we believe every investor deserves access to professional-grade analysis tools. Our platform combines real-time market data with advanced prediction algorithms to help you make informed investment decisions.
                </p>
                <p class="text-gray-400 leading-relaxed">
                    Whether you're a seasoned trader or just starting, our intuitive dashboard, detailed stock analysis, and accurate profit/loss calculations give you the edge you need in today's dynamic market.
                </p>
            </section>
            <section aria-labelledby="highlights-heading">
                <h3 id="highlights-heading" class="text-accent font-semibold text-lg mb-4">Platform Highlights</h3>
                <ul class="space-y-3" role="list">
                    <li class="flex items-start text-gray-300">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3" aria-hidden="true"></i>
                        <span>Consolidated summary endpoint — one API call fetches profile, snapshot, earnings, growth, and institutional activity</span>
                    </li>
                    <li class="flex items-start text-gray-300">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3" aria-hidden="true"></i>
                        <span>Real-time stock price tracking with 90-day historical data</span>
                    </li>
                    <li class="flex items-start text-gray-300">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3" aria-hidden="true"></i>
                        <span>AI-driven 30-day future price predictions with confidence scores</span>
                    </li>
                    <li class="flex items-start text-gray-300">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3" aria-hidden="true"></i>
                        <span>Comprehensive investment tracking with buy/sell records</span>
                    </li>
                    <li class="flex items-start text-gray-300">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3" aria-hidden="true"></i>
                        <span>Automatic gross profit/loss and STCG (15%) / LTCG (10%) tax computation</span>
                    </li>
                    <li class="flex items-start text-gray-300">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3" aria-hidden="true"></i>
                        <span>Personalized watchlist for tracking favorite stocks</span>
                    </li>
                    <li class="flex items-start text-gray-300">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3" aria-hidden="true"></i>
                        <span>Responsive mobile navigation with slide-over menu, search, and theme switching</span>
                    </li>
                </ul>
            </section>
        </article>

        <section aria-labelledby="features-heading" class="mb-16">
            <h2 id="features-heading" class="text-2xl font-bold text-white mb-8 text-center">Key Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center bg-surface rounded-xl p-8 border border-gray-700" aria-labelledby="feat-stocks">
                    <div class="text-4xl text-accent mb-4" aria-hidden="true"><i class="fas fa-chart-pie"></i></div>
                    <h3 id="feat-stocks" class="text-white font-bold text-xl mb-2">15+ Stocks</h3>
                    <p class="text-gray-400 text-sm">Tracked across multiple sectors with complete historical data and consolidated summaries</p>
                </div>
                <div class="text-center bg-surface rounded-xl p-8 border border-gray-700" aria-labelledby="feat-forecast">
                    <div class="text-4xl text-accent mb-4" aria-hidden="true"><i class="fas fa-robot"></i></div>
                    <h3 id="feat-forecast" class="text-white font-bold text-xl mb-2">30-Day Forecast</h3>
                    <p class="text-gray-400 text-sm">AI-powered predictions with confidence scoring for every stock</p>
                </div>
                <div class="text-center bg-surface rounded-xl p-8 border border-gray-700" aria-labelledby="feat-tax">
                    <div class="text-4xl text-accent mb-4" aria-hidden="true"><i class="fas fa-file-invoice"></i></div>
                    <h3 id="feat-tax" class="text-white font-bold text-xl mb-2">Tax Ready</h3>
                    <p class="text-gray-400 text-sm">Auto-calculates STCG and LTCG taxes on your profits</p>
                </div>
            </div>
        </section>

        <section aria-labelledby="pricing-heading" class="mb-16">
            <h2 id="pricing-heading" class="text-2xl font-bold text-white mb-8 text-center">Pricing Plans</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="bg-surface rounded-xl p-8 border border-gray-700" aria-labelledby="plan-free">
                    <h3 id="plan-free" class="text-white font-bold text-xl mb-2">Free</h3>
                    <div class="text-3xl font-bold text-accent mb-4">$0<span class="text-gray-400 text-sm font-normal">/month</span></div>
                    <ul class="space-y-2 text-gray-400 text-sm" role="list">
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>Basic stock analysis</li>
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>30-day price predictions</li>
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>Portfolio tracking</li>
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>STCG/LTCG tax calc</li>
                    </ul>
                    <a href="/pricing" class="mt-6 block text-center px-4 py-2 rounded-lg bg-accent text-on-accent font-semibold hover:bg-accent-2 transition">Get Started</a>
                </div>
                <div class="bg-surface rounded-xl p-8 border-2 border-accent relative" aria-labelledby="plan-pro">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-accent text-on-accent text-xs font-bold px-3 py-1 rounded-full">Popular</span>
                    <h3 id="plan-pro" class="text-white font-bold text-xl mb-2">Pro</h3>
                    <div class="text-3xl font-bold text-accent mb-4">$9<span class="text-gray-400 text-sm font-normal">/month</span></div>
                    <ul class="space-y-2 text-gray-400 text-sm" role="list">
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>Everything in Free</li>
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>Advanced technical indicators</li>
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>Earnings &amp; institutional activity</li>
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>Priority data refresh</li>
                    </ul>
                    <a href="/pricing" class="mt-6 block text-center px-4 py-2 rounded-lg bg-accent text-on-accent font-semibold hover:bg-accent-2 transition">Upgrade Now</a>
                </div>
                <div class="bg-surface rounded-xl p-8 border border-gray-700" aria-labelledby="plan-enterprise">
                    <h3 id="plan-enterprise" class="text-white font-bold text-xl mb-2">Enterprise</h3>
                    <div class="text-3xl font-bold text-accent mb-4">$29<span class="text-gray-400 text-sm font-normal">/month</span></div>
                    <ul class="space-y-2 text-gray-400 text-sm" role="list">
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>Everything in Pro</li>
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>API access with higher limits</li>
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>Custom data exports</li>
                        <li><i class="fas fa-check text-green-500 mr-2" aria-hidden="true"></i>Dedicated support</li>
                    </ul>
                    <a href="/pricing" class="mt-6 block text-center px-4 py-2 rounded-lg bg-accent text-on-accent font-semibold hover:bg-accent-2 transition">Contact Us</a>
                </div>
            </div>
        </section>

        <section aria-labelledby="tax-heading" class="bg-surface rounded-xl p-8 border border-gray-700 mb-16">
            <h2 id="tax-heading" class="text-2xl font-bold text-white mb-6">Understanding Tax Calculations</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="border border-yellow-700/50 rounded-lg p-6 bg-yellow-900/10" aria-labelledby="stcg-heading">
                    <h3 id="stcg-heading" class="text-yellow-400 font-semibold text-lg mb-2">STCG — Short Term Capital Gains</h3>
                    <p class="text-gray-400 text-sm mb-3">Applied when shares are held for <strong class="text-white">less than 1 year</strong>.</p>
                    <div class="text-2xl font-bold text-yellow-400">15%</div>
                    <p class="text-gray-500 text-xs mt-1">On all gains (no exemption)</p>
                </div>
                <div class="border border-blue-700/50 rounded-lg p-6 bg-blue-900/10" aria-labelledby="ltcg-heading">
                    <h3 id="ltcg-heading" class="text-blue-400 font-semibold text-lg mb-2">LTCG — Long Term Capital Gains</h3>
                    <p class="text-gray-400 text-sm mb-3">Applied when shares are held for <strong class="text-white">more than 1 year</strong>.</p>
                    <div class="text-2xl font-bold text-blue-400">10%</div>
                    <p class="text-gray-500 text-xs mt-1">On gains above &#8377;1,00,000 exemption</p>
                </div>
            </div>
        </section>

        <section aria-labelledby="faq-about-heading" class="mb-16">
            <h2 id="faq-about-heading" class="text-2xl font-bold text-white mb-6 text-center">Frequently Asked Questions</h2>
            <div class="max-w-3xl mx-auto space-y-4">
                <details class="bg-surface rounded-lg p-6 border border-gray-700">
                    <summary class="text-white font-semibold cursor-pointer hover:text-accent transition">Is StockTrade Tips really free?</summary>
                    <p class="text-gray-400 text-sm mt-3">Yes. The core platform — stock analysis, predictions, portfolio tracking, and tax calculations — is completely free. We offer Pro and Enterprise plans for advanced features and higher API limits.</p>
                </details>
                <details class="bg-surface rounded-lg p-6 border border-gray-700">
                    <summary class="text-white font-semibold cursor-pointer hover:text-accent transition">How accurate are the AI predictions?</summary>
                    <p class="text-gray-400 text-sm mt-3">Our 30-day predictions use ensemble models trained on historical price data, technical indicators, and volume patterns. Confidence scores are provided for every forecast — treat them as one input among many, not as financial advice.</p>
                </details>
                <details class="bg-surface rounded-lg p-6 border border-gray-700">
                    <summary class="text-white font-semibold cursor-pointer hover:text-accent transition">How often is data refreshed?</summary>
                    <p class="text-gray-400 text-sm mt-3">Free-tier data refreshes on a 15-minute interval. Pro and Enterprise plans get priority refresh rates. All data is sourced from Yahoo Finance.</p>
                </details>
                <details class="bg-surface rounded-lg p-6 border border-gray-700">
                    <summary class="text-white font-semibold cursor-pointer hover:text-accent transition">Can I export my portfolio data?</summary>
                    <p class="text-gray-400 text-sm mt-3">Yes. You can export your portfolio, watchlist, and transaction history as CSV from the dashboard. Enterprise plans also support custom data exports via API.</p>
                </details>
            </div>
        </section>

        <section aria-labelledby="cta-heading" class="text-center bg-surface rounded-xl p-8 border border-gray-700">
            <h2 id="cta-heading" class="text-2xl font-bold text-white mb-4">Ready to Get Started?</h2>
            <p class="text-gray-400 mb-6">Join thousands of investors using <?= esc(site_name()) ?> to track, analyze, and optimize their portfolios.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/register" class="px-6 py-3 rounded-lg bg-accent text-on-accent font-semibold hover:bg-accent-2 transition">Create Free Account</a>
                <a href="/pricing" class="px-6 py-3 rounded-lg bg-page border border-gray-600 text-gray-300 hover:text-white hover:border-accent transition">View Plans</a>
                <a href="/docs/user" class="px-6 py-3 rounded-lg bg-page border border-gray-600 text-gray-300 hover:text-white hover:border-accent transition">Read the Docs</a>
            </div>
        </section>
    </div>
</section>