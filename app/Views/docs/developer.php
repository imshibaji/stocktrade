<section class="py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-8">
            <h1 class="text-3xl font-bold text-white">Developer Documentation</h1>
            <a href="/api-playground" class="text-sm text-accent hover:underline">Try the API Playground &rsaquo;</a>
        </div>

        <div class="space-y-8 text-gray-300">
            <div>
                <h2 class="text-xl font-semibold text-white mb-3">Overview</h2>
                <p>The <?= esc(site_name()) ?> API exposes market data, predictions, and portfolio helpers as JSON over HTTPS. The API is served from the same origin, e.g. <code class="bg-page px-2 py-1 rounded text-sm">https://app.stocktradetips.com/api</code>. Most endpoints are public; portfolio endpoints require authentication.</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white mb-3">Authentication</h2>
                <p>Authenticated endpoints use the session cookie established at <code class="bg-page px-1.5 py-0.5 rounded text-sm">/login</code>. No bearer token setup is needed when calling from a browser of a signed-in user; otherwise POST credentials to <code class="bg-page px-1.5 py-0.5 rounded text-sm">/login</code> and reuse the returned session cookie.</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white mb-3">Consolidated stock data (summary)</h2>
                <p>To avoid multiple round-trips, the stock detail page fetches all company/financial/trends data in <strong class="text-white">one</strong> request:
                </p>
                <pre class="bg-page border border-gray-700 rounded-lg p-4 overflow-x-auto text-sm"><span class="text-gray-400"># GET /api/summary/{symbol}/{exchange}?modules=...</span>
GET /api/summary/PFC/NSE?modules=summaryProfile,financialData,earnings,netSharePurchaseActivity,indexTrend</pre>
                <p>The response is a single object keyed by module name. Key modules:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li><code class="bg-page px-1.5 py-0.5 rounded text-sm">summaryProfile</code> — sector, industry, business summary, officers.</li>
                    <li><code class="bg-page px-1.5 py-0.5 rounded text-sm">financialData</code> — currentPrice, marketCap, profitMargins, earningsGrowth, etc.</li>
                    <li><code class="bg-page px-1.5 py-0.5 rounded text-sm">defaultKeyStatistics</code> — forwardPE, priceToBook, sharesOutstanding, beta.</li>
                    <li><code class="bg-page px-1.5 py-0.5 rounded text-sm">earnings</code> — next earnings date (<code class="bg-page px-1.5 py-0.5 rounded text-sm">earningsChart.earningsDate</code>) and quarterly/annual financials (<code class="bg-page px-1.5 py-0.5 rounded text-sm">financialsChart.quarterly</code> / <code class="bg-page px-1.5 py-0.5 rounded text-sm">financialsChart.yearly</code>).</li>
                    <li><code class="bg-page px-1.5 py-0.5 rounded text-sm">indexTrend</code> — analyst growth estimates per period (<code class="bg-page px-1.5 py-0.5 rounded text-sm">estimates[].{period,growth}</code>).</li>
                    <li><code class="bg-page px-1.5 py-0.5 rounded text-sm">netSharePurchaseActivity</code> — institutional buying, net buying %, total insider shares (<code class="bg-page px-1.5 py-0.5 rounded text-sm">netInstSharesBuying</code>, <code class="bg-page px-1.5 py-0.5 rounded text-sm">netInstBuyingPercent</code>, <code class="bg-page px-1.5 py-0.5 rounded text-sm">totalInsiderShares</code>).</li>
                </ul>
                <p>Fetch only the modules you need: <code class="bg-page px-1.5 py-0.5 rounded text-sm">?modules=summaryProfile,earnings</code>. If omitted, defaults to <code class="bg-page px-1.5 py-0.5 rounded text-sm">summaryProfile,assetProfile</code>.</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white mb-3">Key API endpoints</h2>
                <div class="space-y-3">
                    <div>
                        <code class="bg-page px-1.5 py-0.5 rounded text-sm text-green-400">GET</code> <code class="bg-page px-1.5 py-0.5 rounded text-sm">/api/quote/{symbol}/{exchange}</code>
                        <p class="text-sm text-gray-400 mt-1">Single stock real-time quote.</p>
                    </div>
                    <div>
                        <code class="bg-page px-1.5 py-0.5 rounded text-sm text-green-400">GET</code> <code class="bg-page px-1.5 py-0.5 rounded text-sm">/api/summary/{symbol}/{exchange}?modules=...</code>
                        <p class="text-sm text-gray-400 mt-1">Consolidated quote + summary modules (profile, financials, earnings, trends, activity).</p>
                    </div>
                    <div>
                        <code class="bg-page px-1.5 py-0.5 rounded text-sm text-green-400">GET</code> <code class="bg-page px-1.5 py-0.5 rounded text-sm">/api/historical/{symbol}/{exchange}/{time}</code>
                        <p class="text-sm text-gray-400 mt-1">Historical OHLC prices (e.g. <code class="bg-page px-1.5 py-0.5 rounded text-sm">14 days</code>, <code class="bg-page px-1.5 py-0.5 rounded text-sm">1 year</code>).</p>
                    </div>
                    <div>
                        <code class="bg-page px-1.5 py-0.5 rounded text-sm text-green-400">GET</code> <code class="bg-page px-1.5 py-0.5 rounded text-sm">/api/dividends/{symbol}/{exchange}/{time}</code> &nbsp;
                        <code class="bg-page px-1.5 py-0.5 rounded text-sm text-green-400">GET</code> <code class="bg-page px-1.5 py-0.5 rounded text-sm">/api/splits/{symbol}/{exchange}/{time}</code>
                        <p class="text-sm text-gray-400 mt-1">Historical dividend and stock-split ledgers.</p>
                    </div>
                    <div>
                        <code class="bg-page px-1.5 py-0.5 rounded text-sm text-green-400">GET</code> <code class="bg-page px-1.5 py-0.5 rounded text-sm">/api/search?q={query}</code> &nbsp;
                        <code class="bg-page px-1.5 py-0.5 rounded text-sm text-green-400">GET</code> <code class="bg-page px-1.5 py-0.5 rounded text-sm">/api/news/{symbol}</code>
                        <p class="text-sm text-gray-400 mt-1">Symbol search and latest news.</p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white mb-3">Caching</h2>
                <p>Summary responses are cached for 60 seconds; quotes for 15 seconds. Re-requesting the consolidated summary within that window reuses the cache — this is why combining modules into one call is also cheaper than several.</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white mb-3">Rate limits</h2>
                <p>Anonymous callers are limited to 60 requests/minute. Authenticated users get 120 requests/minute. Exceeding a limit returns <code class="bg-page px-1.5 py-0.5 rounded text-sm">429 Too Many Requests</code>; retry after the <code class="bg-page px-1.5 py-0.5 rounded text-sm">Retry-After</code> header.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white mb-3">Errors</h2>
                <p>Errors return JSON: <code class="bg-page px-1.5 py-0.5 rounded text-sm">{"error": "..."}</code> with the appropriate HTTP status (<code class="bg-page px-1.5 py-0.5 rounded text-sm">400</code>, <code class="bg-page px-1.5 py-0.5 rounded text-sm">404</code>, <code class="bg-page px-1.5 py-0.5 rounded text-sm">429</code>, <code class="bg-page px-1.5 py-0.5 rounded text-sm">500</code>). Check <code class="bg-page px-1.5 py-0.5 rounded text-sm">error</code> for a human-readable message.</p>
            </div>

            <div class="border-t border-gray-700 pt-6">
                <p class="text-gray-400 text-sm">Questions? Visit <a href="/api-playground" class="text-accent hover:underline">the API Playground</a> or email <a href="mailto:support@stocktradetips.com" class="text-accent hover:underline">support@stocktradetips.com</a>.</p>
            </div>
        </div>
    </div>
</section>
