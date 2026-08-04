<div class="space-y-3">
    <details class="bg-page rounded-xl border border-gray-700 overflow-hidden" open>
        <summary class="cursor-pointer select-none flex items-center justify-between px-5 py-3 text-white font-semibold text-sm">
            <span><i class="fas fa-terminal text-accent mr-2"></i>Query language</span>
            <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
        </summary>
        <div class="px-5 pb-5 space-y-4 text-xs text-gray-400">
            <div>
                <p class="font-mono text-gray-300 mb-1">field operator value</p>
                <p>Build a filter by comparing a fundamental field or a technical indicator against a number, another indicator, or a string.</p>
            </div>
            <div>
                <p class="text-gray-300 mb-1">Supported operators</p>
                <div class="flex flex-wrap gap-1.5 font-mono">
                    <span class="bg-surface rounded px-2 py-0.5">&gt;</span>
                    <span class="bg-surface rounded px-2 py-0.5">&gt;=</span>
                    <span class="bg-surface rounded px-2 py-0.5">&lt;</span>
                    <span class="bg-surface rounded px-2 py-0.5">&lt;=</span>
                    <span class="bg-surface rounded px-2 py-0.5">==</span>
                    <span class="bg-surface rounded px-2 py-0.5">!=</span>
                </div>
                <p class="text-gray-600 mt-1">Use <span class="font-mono">==</span> / <span class="font-mono">!=</span> for exact matches and strings.</p>
            </div>
            <div>
                <p class="text-gray-300 mb-1">Combine conditions</p>
                <p class="font-mono text-gray-300 mb-1">pe_ratio &lt; 15 AND market_cap &gt; 50000000000</p>
                <p>Join with <span class="font-mono text-gray-300">AND</span> (all must match) or <span class="font-mono text-gray-300">OR</span> (any match). Use the Match selector to choose the default mode.</p>
            </div>
        </div>
    </details>

    <details class="bg-page rounded-xl border border-gray-700 overflow-hidden">
        <summary class="cursor-pointer select-none flex items-center justify-between px-5 py-3 text-white font-semibold text-sm">
            <span><i class="fas fa-percent text-blue-400 mr-2"></i>Fundamental fields</span>
            <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
        </summary>
        <div class="px-5 pb-5 space-y-3 text-xs text-gray-400">
            <p>Company snapshot fields from the stock table:</p>
            <div class="space-y-1.5 font-mono text-gray-300">
                <p class="flex justify-between gap-2"><span>price</span><span class="text-gray-500">price &gt; 100</span></p>
                <p class="flex justify-between gap-2"><span>current_price</span><span class="text-gray-500">current_price &gt; 100</span></p>
                <p class="flex justify-between gap-2"><span>previous_close</span><span class="text-gray-500">previous_close &gt; 100</span></p>
                <p class="flex justify-between gap-2"><span>market_cap</span><span class="text-gray-500">market_cap &gt; 50000000000</span></p>
                <p class="flex justify-between gap-2"><span>avg_volume</span><span class="text-gray-500">avg_volume &gt; 1000000</span></p>
                <p class="flex justify-between gap-2"><span>pe_ratio</span><span class="text-gray-500">pe_ratio &lt; 15</span></p>
                <p class="flex justify-between gap-2"><span>forwardPE</span><span class="text-gray-500">forwardPE &lt; 20</span></p>
                <p class="flex justify-between gap-2"><span>dividend_yield</span><span class="text-gray-500">dividend_yield &gt; 0.02</span></p>
                <p class="flex justify-between gap-2"><span>priceToBook</span><span class="text-gray-500">priceToBook &lt; 3</span></p>
                <p class="flex justify-between gap-2"><span>week_52_high</span><span class="text-gray-500">week_52_high &gt; 500</span></p>
                <p class="flex justify-between gap-2"><span>week_52_low</span><span class="text-gray-500">current_price &lt; week_52_low * 1.1</span></p>
            </div>
            <p class="text-gray-600">Values are compared as numbers; use them on either side of a comparison.</p>
        </div>
    </details>

    <details class="bg-page rounded-xl border border-gray-700 overflow-hidden">
        <summary class="cursor-pointer select-none flex items-center justify-between px-5 py-3 text-white font-semibold text-sm">
            <span><i class="fas fa-chart-line text-green-400 mr-2"></i>Technical indicators</span>
            <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
        </summary>
        <div class="px-5 pb-5 space-y-3 text-xs text-gray-400">
            <p>Write <span class="font-mono text-gray-300">indicator(period)</span>. Period defaults to 14 when omitted.</p>
            <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 font-mono text-gray-300">
                <p>sma(50)</p>
                <p>ema(21)</p>
                <p>rsi(14)</p>
                <p>macd</p>
                <p>macd_signal</p>
                <p>macd_histogram</p>
                <p>stoch_k(14)</p>
                <p>stoch_d(14)</p>
                <p>atr(14)</p>
                <p>bb_pct(20)</p>
                <p>volume_ratio(20)</p>
                <p>cci(20)</p>
                <p>roc(12)</p>
                <p>williams_r(14)</p>
                <p>mfi(14)</p>
                <p>cmf(20)</p>
                <p>obv</p>
                <p>vpt</p>
                <p>psar</p>
                <p>dpo(20)</p>
                <p>zscore(20)</p>
                <p>chop(14)</p>
                <p>kama(10)</p>
                <p>tsi</p>
                <p>cmo(14)</p>
                <p>rmi(14)</p>
                <p>supertrend(10)</p>
                <p>supertrend_dir(10)</p>
            </div>
            <p class="text-gray-600">Common short-hands: <span class="font-mono">close</span>, <span class="font-mono">open</span>, <span class="font-mono">high</span>, <span class="font-mono">low</span>, <span class="font-mono">volume</span> — the latest bar value.</p>
        </div>
    </details>

    <details class="bg-page rounded-xl border border-gray-700 overflow-hidden">
        <summary class="cursor-pointer select-none flex items-center justify-between px-5 py-3 text-white font-semibold text-sm">
            <span><i class="fas fa-calculator text-amber-400 mr-2"></i>Math & indicator comparison</span>
            <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
        </summary>
        <div class="px-5 pb-5 space-y-3 text-xs text-gray-400">
            <p>Indicators can be compared to each other directly:</p>
            <ul class="space-y-1.5 font-mono text-gray-300">
                <li class="bg-surface rounded px-2 py-1">ema(9) &gt; ema(21) <span class="text-gray-500">- fast EMA above slow EMA</span></li>
                <li class="bg-surface rounded px-2 py-1">close &gt; sma(50) <span class="text-gray-500">- price above 50-day average</span></li>
                <li class="bg-surface rounded px-2 py-1">macd &gt; macd_signal <span class="text-gray-500">- bullish momentum</span></li>
                <li class="bg-surface rounded px-2 py-1">high &gt; sma(50) <span class="text-gray-500">- strong recent range</span></li>
            </ul>
            <p class="pt-1">Scale or shift the comparison value with math:</p>
            <ul class="space-y-1.5 font-mono text-gray-300">
                <li class="bg-surface rounded px-2 py-1">close &gt; sma(50) * 1.05 <span class="text-gray-500">- 5% above the average</span></li>
                <li class="bg-surface rounded px-2 py-1">current_price &lt; week_52_low * 1.1 <span class="text-gray-500">- near 52-week low</span></li>
                <li class="bg-surface rounded px-2 py-1">pe_ratio &lt; 15 + 2 <span class="text-gray-500">- same as pe_ratio &lt; 17</span></li>
                <li class="bg-surface rounded px-2 py-1">current_price &gt; 100 * 1.05 <span class="text-gray-500">- price above 105</span></li>
            </ul>
            <p class="text-gray-600">Supported math on the right-hand side: <span class="font-mono">+</span> <span class="font-mono">-</span> <span class="font-mono">*</span> <span class="font-mono">/</span> <span class="font-mono">%</span></p>
        </div>
    </details>

    <details class="bg-page rounded-xl border border-gray-700 overflow-hidden">
        <summary class="cursor-pointer select-none flex items-center justify-between px-5 py-3 text-white font-semibold text-sm">
            <span><i class="fas fa-font text-purple-400 mr-2"></i>String & sector filters</span>
            <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
        </summary>
        <div class="px-5 pb-5 space-y-3 text-xs text-gray-400">
            <p>Compare text fields with quoted strings using <span class="font-mono">==</span> / <span class="font-mono">!=</span>:</p>
            <ul class="space-y-1.5 font-mono text-gray-300">
                <li class="bg-surface rounded px-2 py-1">sector == 'Technology'</li>
                <li class="bg-surface rounded px-2 py-1">exchange == 'NSE'</li>
                <li class="bg-surface rounded px-2 py-1">currency == 'INR'</li>
                <li class="bg-surface rounded px-2 py-1">symbol == "RELIANCE"</li>
                <li class="bg-surface rounded px-2 py-1">sector != 'Financial Services'</li>
            </ul>
            <p class="text-gray-600">Single or double quotes both work; matching is case-insensitive.</p>
        </div>
    </details>

    <details class="bg-page rounded-xl border border-gray-700 overflow-hidden">
        <summary class="cursor-pointer select-none flex items-center justify-between px-5 py-3 text-white font-semibold text-sm">
            <span><i class="fas fa-lightbulb text-rose-400 mr-2"></i>Most usable samples</span>
            <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
        </summary>
        <div class="px-5 pb-5 space-y-2 text-xs">
            <div class="bg-surface rounded-lg p-2.5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-green-400/40 text-green-400">Value</span>
                    <span class="text-gray-500">Undervalued, sizeable, dividend-paying</span>
                </div>
                <p class="font-mono text-gray-300 text-[11px]">pe_ratio &lt; 15 AND market_cap &gt; 50000000000 AND dividend_yield &gt; 0.02</p>
            </div>
            <div class="bg-surface rounded-lg p-2.5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-blue-400/40 text-blue-400">Liquidity</span>
                    <span class="text-gray-500">Active large-caps</span>
                </div>
                <p class="font-mono text-gray-300 text-[11px]">price &gt; 100 AND avg_volume &gt; 1000000</p>
            </div>
            <div class="bg-surface rounded-lg p-2.5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-rose-400/40 text-rose-400">Oversold</span>
                    <span class="text-gray-500">RSI below 30</span>
                </div>
                <p class="font-mono text-gray-300 text-[11px]">rsi(14) &lt; 30</p>
            </div>
            <div class="bg-surface rounded-lg p-2.5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-green-400/40 text-green-400">Uptrend</span>
                    <span class="text-gray-500">Price above 50-day average with momentum</span>
                </div>
                <p class="font-mono text-gray-300 text-[11px]">close &gt; sma(50) AND rsi(14) &gt; 50</p>
            </div>
            <div class="bg-surface rounded-lg p-2.5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-amber-400/40 text-amber-400">MACD bullish</span>
                    <span class="text-gray-500">Momentum turning positive</span>
                </div>
                <p class="font-mono text-gray-300 text-[11px]">macd &gt; macd_signal AND macd_histogram &gt; 0</p>
            </div>
            <div class="bg-surface rounded-lg p-2.5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-blue-400/40 text-blue-400">Volume breakout</span>
                    <span class="text-gray-500">Above-average volume near resistance</span>
                </div>
                <p class="font-mono text-gray-300 text-[11px]">volume_ratio(20) &gt; 1.5 AND close &gt; sma(50)</p>
            </div>
            <div class="bg-surface rounded-lg p-2.5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-purple-400/40 text-purple-400">Sector play</span>
                    <span class="text-gray-500">Affordable tech names</span>
                </div>
                <p class="font-mono text-gray-300 text-[11px]">sector == 'Technology' AND pe_ratio &lt; 25</p>
            </div>
            <div class="bg-surface rounded-lg p-2.5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded border whitespace-nowrap border-cyan-400/40 text-cyan-400">Near 52-week low</span>
                    <span class="text-gray-500">Reversion candidates</span>
                </div>
                <p class="font-mono text-gray-300 text-[11px]">current_price &lt; week_52_low * 1.1 AND rsi(14) &lt; 40</p>
            </div>
        </div>
    </details>
</div>
