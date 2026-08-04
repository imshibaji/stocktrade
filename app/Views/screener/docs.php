<section>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-white">Screener Query Documentation</h1>
                <span class="text-xs px-3 py-1 rounded-full border border-gray-600 text-gray-400">
                    <i class="fas fa-file-alt text-accent mr-1"></i>Reference
                </span>
            </div>
            <p class="text-gray-400 mt-1">Complete guide to the screener query language — fields, indicators, math, and ready-to-use examples.</p>
        </div>
        <a href="/screener" class="bg-surface hover:bg-page border border-gray-600 text-gray-300 hover:text-white px-4 py-2.5 rounded-lg transition text-sm font-semibold mt-4 md:mt-0">
            <i class="fas fa-arrow-left mr-1"></i>Back to Screener
        </a>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        <div class="lg:w-56 shrink-0">
            <nav class="bg-surface rounded-xl border border-gray-700 p-4 sticky top-24 text-sm">
                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-3">On this page</p>
                <ul class="space-y-2">
                    <li><a href="#syntax" class="text-gray-300 hover:text-accent">Syntax & operators</a></li>
                    <li><a href="#fundamental" class="text-gray-300 hover:text-accent">Fundamental fields</a></li>
                    <li><a href="#technical" class="text-gray-300 hover:text-accent">Technical indicators</a></li>
                    <li><a href="#math" class="text-gray-300 hover:text-accent">Math & comparisons</a></li>
                    <li><a href="#strings" class="text-gray-300 hover:text-accent">String filters</a></li>
                    <li><a href="#samples" class="text-gray-300 hover:text-accent">Most usable samples</a></li>
                </ul>
            </nav>
        </div>

        <div class="flex-1 min-w-0 space-y-6">
            <div id="syntax" class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-white font-bold text-lg mb-4">Query syntax</h2>
                <p class="text-gray-400 text-sm mb-4">A query is a list of conditions joined by <span class="font-mono text-gray-300">AND</span> or <span class="font-mono text-gray-300">OR</span>. Each condition compares a field or indicator against a number, another indicator, or a string.</p>
                <div class="bg-page rounded-lg p-3 border border-gray-700 font-mono text-sm text-white mb-4">field operator value [AND|OR field operator value ...]</div>
                <h3 class="text-accent font-semibold text-sm mb-2">Operators</h3>
                <div class="flex flex-wrap gap-1.5 font-mono text-sm mb-4">
                    <span class="bg-page rounded px-2 py-0.5 text-gray-300">&gt;</span>
                    <span class="bg-page rounded px-2 py-0.5 text-gray-300">&gt;=</span>
                    <span class="bg-page rounded px-2 py-0.5 text-gray-300">&lt;</span>
                    <span class="bg-page rounded px-2 py-0.5 text-gray-300">&lt;=</span>
                    <span class="bg-page rounded px-2 py-0.5 text-gray-300">==</span>
                    <span class="bg-page rounded px-2 py-0.5 text-gray-300">!=</span>
                </div>
                <p class="text-gray-400 text-sm">Use <span class="font-mono text-gray-300">==</span> / <span class="font-mono text-gray-300">!=</span> for exact numeric matches and for quoted strings. The <span class="font-mono text-gray-300">Match</span> selector in the screener sets the default mode for combining conditions (all = AND, any = OR).</p>
            </div>

            <div id="fundamental" class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-white font-bold text-lg mb-4">Fundamental fields</h2>
                <p class="text-gray-400 text-sm mb-4">Company snapshot fields stored on the stock record. Compare them numerically, e.g. <span class="font-mono text-gray-300">pe_ratio &lt; 15</span>.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6">
                    <div>
                        <h3 class="text-accent font-semibold text-sm mb-2">Price & valuation</h3>
                        <ul class="space-y-1.5 font-mono text-gray-300 text-xs">
                            <li class="flex justify-between gap-2"><span>price</span><span class="text-gray-500">current market price</span></li>
                            <li class="flex justify-between gap-2"><span>current_price</span><span class="text-gray-500">alias of price</span></li>
                            <li class="flex justify-between gap-2"><span>previous_close</span><span class="text-gray-500">previous day close</span></li>
                            <li class="flex justify-between gap-2"><span>regularMarketOpen</span><span class="text-gray-500">today's open</span></li>
                            <li class="flex justify-between gap-2"><span>regularMarketDayHigh</span><span class="text-gray-500">day high</span></li>
                            <li class="flex justify-between gap-2"><span>regularMarketDayLow</span><span class="text-gray-500">day low</span></li>
                            <li class="flex justify-between gap-2"><span>regularMarketChange</span><span class="text-gray-500">change amount</span></li>
                            <li class="flex justify-between gap-2"><span>regularMarketChangePercent</span><span class="text-gray-500">change %</span></li>
                            <li class="flex justify-between gap-2"><span>week_52_high</span><span class="text-gray-500">52-week high</span></li>
                            <li class="flex justify-between gap-2"><span>week_52_low</span><span class="text-gray-500">52-week low</span></li>
                            <li class="flex justify-between gap-2"><span>fiftyDayAverage</span><span class="text-gray-500">50-day average</span></li>
                            <li class="flex justify-between gap-2"><span>twoHundredDayAverage</span><span class="text-gray-500">200-day average</span></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-accent font-semibold text-sm mb-2">Ratios, size & yield</h3>
                        <ul class="space-y-1.5 font-mono text-gray-300 text-xs">
                            <li class="flex justify-between gap-2"><span>market_cap</span><span class="text-gray-500">market capitalization</span></li>
                            <li class="flex justify-between gap-2"><span>pe_ratio</span><span class="text-gray-500">trailing P/E</span></li>
                            <li class="flex justify-between gap-2"><span>forwardPE</span><span class="text-gray-500">forward P/E</span></li>
                            <li class="flex justify-between gap-2"><span>trailingPE</span><span class="text-gray-500">trailing P/E (Yahoo)</span></li>
                            <li class="flex justify-between gap-2"><span>priceToBook</span><span class="text-gray-500">price to book</span></li>
                            <li class="flex justify-between gap-2"><span>bookValue</span><span class="text-gray-500">book value / share</span></li>
                            <li class="flex justify-between gap-2"><span>epsTrailingTwelveMonths</span><span class="text-gray-500">EPS (TTM)</span></li>
                            <li class="flex justify-between gap-2"><span>epsForward</span><span class="text-gray-500">EPS forward</span></li>
                            <li class="flex justify-between gap-2"><span>dividend_yield</span><span class="text-gray-500">0.02 = 2%</span></li>
                            <li class="flex justify-between gap-2"><span>avg_volume</span><span class="text-gray-500">average daily volume</span></li>
                            <li class="flex justify-between gap-2"><span>regularMarketVolume</span><span class="text-gray-500">latest session volume</span></li>
                            <li class="flex justify-between gap-2"><span>sharesOutstanding</span><span class="text-gray-500">shares outstanding</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="technical" class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-white font-bold text-lg mb-4">Technical indicators</h2>
                <p class="text-gray-400 text-sm mb-4">Write <span class="font-mono text-gray-300">indicator(period)</span>. Period defaults to 14 when omitted. Short-hands <span class="font-mono text-gray-300">close</span>, <span class="font-mono text-gray-300">open</span>, <span class="font-mono text-gray-300">high</span>, <span class="font-mono text-gray-300">low</span>, <span class="font-mono text-gray-300">volume</span> return the latest bar value.</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-1.5 font-mono text-gray-300 text-xs">
                    <p>sma(50)</p>
                    <p>ema(21)</p>
                    <p>rsi(14)</p>
                    <p>macd</p>
                    <p>macd_signal</p>
                    <p>macd_histogram</p>
                    <p>stoch_k(14)</p>
                    <p>stoch_d(14)</p>
                    <p>atr(14)</p>
                    <p>natr(14)</p>
                    <p>bb_pct(20)</p>
                    <p>bb_width(20)</p>
                    <p>kc_pct(20)</p>
                    <p>dc_pct(20)</p>
                    <p>vwap_ratio</p>
                    <p>volume_ratio(20)</p>
                    <p>mfi(14)</p>
                    <p>cmf(20)</p>
                    <p>cci(20)</p>
                    <p>roc(12)</p>
                    <p>williams_r(14)</p>
                    <p>obv</p>
                    <p>vpt</p>
                    <p>psar</p>
                    <p>supertrend(10)</p>
                    <p>supertrend_dir(10)</p>
                    <p>linreg_slope(20)</p>
                    <p>linreg_rsq(20)</p>
                    <p>zscore(20)</p>
                    <p>dpo(20)</p>
                    <p>chop(14)</p>
                    <p>hurst(20)</p>
                    <p>kama(10)</p>
                    <p>tsi</p>
                    <p>cmo(14)</p>
                    <p>rmi(14)</p>
                    <p>aroon_osc(25)</p>
                    <p>vi_plus(14)</p>
                    <p>ttm_squeeze(20)</p>
                    <p>efficiency_ratio(10)</p>
                </div>
                <p class="text-gray-500 text-xs mt-3">A full list is available in the <a href="/screener" class="text-accent hover:underline">Screener Guide</a> on the screener page.</p>
            </div>

            <div id="math" class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-white font-bold text-lg mb-4">Math & indicator comparison</h2>
                <p class="text-gray-400 text-sm mb-3">Compare indicators directly to each other:</p>
                <ul class="space-y-1.5 font-mono text-gray-300 text-xs mb-4">
                    <li class="bg-page rounded px-2 py-1">ema(9) &gt; ema(21) <span class="text-gray-500">- fast EMA above slow EMA</span></li>
                    <li class="bg-page rounded px-2 py-1">close &gt; sma(50) <span class="text-gray-500">- price above 50-day average</span></li>
                    <li class="bg-page rounded px-2 py-1">macd &gt; macd_signal <span class="text-gray-500">- bullish momentum</span></li>
                    <li class="bg-page rounded px-2 py-1">high &gt; sma(50) <span class="text-gray-500">- strong recent range</span></li>
                </ul>
                <p class="text-gray-400 text-sm mb-3">Scale or shift the comparison value with math on the right-hand side:</p>
                <ul class="space-y-1.5 font-mono text-gray-300 text-xs">
                    <li class="bg-page rounded px-2 py-1">close &gt; sma(50) * 1.05 <span class="text-gray-500">- 5% above the average</span></li>
                    <li class="bg-page rounded px-2 py-1">current_price &lt; week_52_low * 1.1 <span class="text-gray-500">- near 52-week low</span></li>
                    <li class="bg-page rounded px-2 py-1">pe_ratio &lt; 15 + 2 <span class="text-gray-500">- same as pe_ratio &lt; 17</span></li>
                    <li class="bg-page rounded px-2 py-1">current_price &gt; 100 * 1.05 <span class="text-gray-500">- price above 105</span></li>
                </ul>
                <p class="text-gray-500 text-xs mt-3">Supported math: <span class="font-mono">+</span> <span class="font-mono">-</span> <span class="font-mono">*</span> <span class="font-mono">/</span> <span class="font-mono">%</span></p>
            </div>

            <div id="strings" class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-white font-bold text-lg mb-4">String filters</h2>
                <p class="text-gray-400 text-sm mb-4">Compare text fields with quoted strings using <span class="font-mono text-gray-300">==</span> / <span class="font-mono text-gray-300">!=</span>. Single or double quotes work; matching is case-insensitive.</p>
                <ul class="space-y-1.5 font-mono text-gray-300 text-xs">
                    <li class="bg-page rounded px-2 py-1">sector == 'Technology'</li>
                    <li class="bg-page rounded px-2 py-1">exchange == 'NSE'</li>
                    <li class="bg-page rounded px-2 py-1">currency == 'INR'</li>
                    <li class="bg-page rounded px-2 py-1">symbol == "RELIANCE"</li>
                    <li class="bg-page rounded px-2 py-1">name == 'Tata Motors Limited'</li>
                    <li class="bg-page rounded px-2 py-1">sector != 'Financial Services'</li>
                </ul>
            </div>

            <div id="samples" class="bg-surface rounded-xl border border-gray-700 p-6">
                <h2 class="text-white font-bold text-lg mb-4">Most usable samples</h2>
                <div class="space-y-2 text-xs">
                    <div class="bg-page rounded-lg p-2.5">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-green-400/40 text-green-400">Value</span>
                            <span class="text-gray-500">Undervalued, sizeable, dividend-paying</span>
                        </div>
                        <p class="font-mono text-gray-300">pe_ratio &lt; 15 AND market_cap &gt; 50000000000 AND dividend_yield &gt; 0.02</p>
                    </div>
                    <div class="bg-page rounded-lg p-2.5">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-blue-400/40 text-blue-400">Liquidity</span>
                            <span class="text-gray-500">Active large-caps</span>
                        </div>
                        <p class="font-mono text-gray-300">price &gt; 100 AND avg_volume &gt; 1000000</p>
                    </div>
                    <div class="bg-page rounded-lg p-2.5">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-rose-400/40 text-rose-400">Oversold</span>
                            <span class="text-gray-500">RSI below 30</span>
                        </div>
                        <p class="font-mono text-gray-300">rsi(14) &lt; 30</p>
                    </div>
                    <div class="bg-page rounded-lg p-2.5">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-green-400/40 text-green-400">Uptrend</span>
                            <span class="text-gray-500">Price above 50-day average with momentum</span>
                        </div>
                        <p class="font-mono text-gray-300">close &gt; sma(50) AND rsi(14) &gt; 50</p>
                    </div>
                    <div class="bg-page rounded-lg p-2.5">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-amber-400/40 text-amber-400">MACD bullish</span>
                            <span class="text-gray-500">Momentum turning positive</span>
                        </div>
                        <p class="font-mono text-gray-300">macd &gt; macd_signal AND macd_histogram &gt; 0</p>
                    </div>
                    <div class="bg-page rounded-lg p-2.5">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-blue-400/40 text-blue-400">Volume breakout</span>
                            <span class="text-gray-500">Above-average volume near resistance</span>
                        </div>
                        <p class="font-mono text-gray-300">volume_ratio(20) &gt; 1.5 AND close &gt; sma(50)</p>
                    </div>
                    <div class="bg-page rounded-lg p-2.5">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-purple-400/40 text-purple-400">Sector play</span>
                            <span class="text-gray-500">Affordable tech names</span>
                        </div>
                        <p class="font-mono text-gray-300">sector == 'Technology' AND pe_ratio &lt; 25</p>
                    </div>
                    <div class="bg-page rounded-lg p-2.5">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-cyan-400/40 text-cyan-400">Near 52-week low</span>
                            <span class="text-gray-500">Reversion candidates</span>
                        </div>
                        <p class="font-mono text-gray-300">current_price &lt; week_52_low * 1.1 AND rsi(14) &lt; 40</p>
                    </div>
                </div>
            </div>

            <div class="bg-surface rounded-xl border border-gray-700 p-6 text-center">
                <p class="text-gray-400 text-sm mb-4">Ready to build your own screen?</p>
                <a href="/screener" class="inline-flex items-center bg-accent hover:bg-accent-2 text-on-accent font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                    <i class="fas fa-filter mr-1"></i>Open the Screener
                </a>
            </div>
        </div>
    </div>
</section>
