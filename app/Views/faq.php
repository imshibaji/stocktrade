<section class="py-8">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-4">Frequently Asked Questions</h1>
            <p class="text-gray-400 max-w-2xl mx-auto">Find answers to common questions about <?= esc(site_name()) ?> — pricing, data, predictions, taxes, and more.</p>
        </div>

        <div class="space-y-4">
            <div class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-2">Is <?= esc(site_name()) ?> free to use?</h2>
                <p class="text-gray-400">Yes. There is a free tier that includes real-time stock tracking, 30-day AI predictions, watchlists, and investment portfolio tracking. A Pro tier (see <a href="/pricing" class="text-accent hover:underline">Pricing</a>) adds advanced screening, priority support, and extended data.</p>
            </div>

            <div class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-2">Is the price data real-time?</h2>
                <p class="text-gray-400">Live quote and chart data are sourced from Yahoo Finance and refreshed during market hours. As with all third-party feeds, real-time coverage depends on the exchange and your data provider — some markets (especially after-hours or smaller regional exchanges) may be delayed. We recommend checking the underlying exchange for mission-critical trades.</p>
            </div>

            <div class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-2">How accurate are the predictions?</h2>
                <p class="text-gray-400">Our predictions are AI-generated forecasts, not financial advice. They are refreshed daily and each forecast includes a confidence score. Predictions are a tool to inform your research, not a recommendation to buy or sell — always do your own research before trading.</p>
            </div>

            <div class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-2">How often are predictions updated?</h2>
                <p class="text-gray-400">Predictions are regenerated once per day using the latest available price history and fundamentals. The forecast you see covers a 30-day horizon; the confidence score reflects historical model error for similar past periods.</p>
            </div>

            <div class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-2">How are taxes calculated?</h2>
                <p class="text-gray-400">We automatically compute your gains and apply Indian tax rules: STCG (Short Term Capital Gains) at 15% on holdings sold within 1 year, and LTCG (Long Term Capital Gains) at 10% on gains above the &#8377;1,00,000 annual exemption. This is shown in your Portfolio summary and on each investment's detail page.</p>
            </div>

            <div class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-2">Can I export my data?</h2>
                <p class="text-gray-400">Yes. From your Portfolio and Watchlist you can export transactions and holdings as a CSV file at any time. API access for developers is available in the Developer Documentation (<a href="/docs/developer" class="text-accent hover:underline">Pro tier</a> feature) — see the rate limits there.</p>
            </div>

            <div class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-2">How do I contact support?</h2>
                <p class="text-gray-400">Email us at <a href="mailto:support@stocktradetips.com" class="text-accent hover:underline">support@stocktradetips.com</a> or fill out the <a href="/contact" class="text-accent hover:underline">contact form</a>. Support is available Monday–Saturday (IST). Pro users get priority response.</p>
            </div>

            <div class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-2">Can I cancel my subscription?</h2>
                <p class="text-gray-400">Yes. You can downgrade or cancel your Pro subscription at any time from your account settings. There are no contracts or penalties; your benefits continue through the end of the paid period.</p>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-10 pt-6 text-center">
            <p class="text-gray-400 text-sm">Still have a question? <a href="/contact" class="text-accent hover:underline">Contact us</a> — we'll get back as soon as possible.</p>
        </div>
    </div>
</section>
