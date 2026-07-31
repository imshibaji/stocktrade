<section>
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Stock Trade Screener Query Documentation</h1>
            <p class="text-gray-400 text-lg">Complete reference guide for building powerful stock screening queries using the Stock Trade Screener.</p>
            <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                <span><i class="fas fa-clock mr-1"></i> Last Updated: July 31, 2026</span>
                <span><i class="fas fa-book mr-1"></i> 50+ Query Examples</span>
                <span><i class="fas fa-layer-group mr-1"></i> 8 Main Categories</span>
            </div>
        </div>

        <!-- Navigation -->
        <div class="bg-navy2 rounded-xl border border-gray-700 p-6 mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">&#x1F4CB; Quick Navigation</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="#getting-started" class="block p-3 bg-navy border border-gray-600 rounded-lg hover:border-gold/30 transition-colors">
                    <div class="text-gold font-semibold mb-1">&#x1F680; Getting Started</div>
                    <div class="text-gray-400 text-sm">Basic query syntax and examples</div>
                </a>
                <a href="#fundamental-fields" class="block p-3 bg-navy border border-gray-600 rounded-lg hover:border-gold/30 transition-colors">
                    <div class="text-blue-400 font-semibold mb-1">&#x1F4B0; Fundamental Fields</div>
                    <div class="text-gray-400 text-sm">Financial metrics and ratios</div>
                </a>
                <a href="#technical-analysis" class="block p-3 bg-navy border border-gray-600 rounded-lg hover:border-gold/30 transition-colors">
                    <div class="text-green-400 font-semibold mb-1">&#x1F4C8; Technical Analysis</div>
                    <div class="text-gray-400 text-sm">Moving averages, indicators, momentum</div>
                </a>
                <a href="#advanced-queries" class="block p-3 bg-navy border border-gray-600 rounded-lg hover:border-gold/30 transition-colors">
                    <div class="text-purple-400 font-semibold mb-1">&#x1F9E0; Advanced Queries</div>
                    <div class="text-gray-400 text-sm">Complex nested logic and expressions</div>
                </a>
                <a href="#query-builder" class="block p-3 bg-navy border border-gray-600 rounded-lg hover:border-gold/30 transition-colors">
                    <div class="text-cyan-400 font-semibold mb-1">&#x1F6E0;&#xFE0F; Interactive Builder</div>
                    <div class="text-gray-400 text-sm">Drag-and-drop query construction</div>
                </a>
                <a href="#quick-reference" class="block p-3 bg-navy border border-gray-600 rounded-lg hover:border-gold/30 transition-colors">
                    <div class="text-yellow-400 font-semibold mb-1">&#x26A1; Quick Reference</div>
                    <div class="text-gray-400 text-sm">Common queries and shortcuts</div>
                </a>
            </div>
        </div>

        <!-- Getting Started Section -->
        <section id="getting-started" class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-2">
                <span class="text-gold">&#x1F680;</span> Getting Started with Screener Queries
            </h2>
            
            <div class="bg-navy2 rounded-xl border border-gray-700 p-6 mb-6">
                <h3 class="text-xl font-semibold text-white mb-4">What Are Screener Queries?</h3>
                <p class="text-gray-300 mb-4">
                    Stock screener queries are powerful search expressions that filter stocks based on your investment criteria. 
                    Think of them as "search filters" for the stock universe, allowing you to find exactly the stocks that match your strategy.
                </p>
                
                <div class="bg-green-900/20 border border-green-500/30 rounded-lg p-4 mb-4">
                    <h4 class="text-green-400 font-semibold mb-2">✨ Why Use Query Syntax</h4>
                    <ul class="text-gray-300 text-sm space-y-1">
                        <li>• More precise than basic filters</li>
                        <li>• Supports complex logical operations (AND/OR)</li>
                        <li>• Handles mathematical expressions and calculations</li>
                        <li>• Supports string comparisons for sector, exchange, etc.</li>
                        <li>• Combines multiple conditions elegantly</li>
                        <li>• Faster than selecting individual checkboxes</li>
                    </ul>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-navy border border-gray-600 rounded-lg p-5">
                    <h4 class="text-gold font-semibold mb-3">📝 Basic Syntax</h4>
                    <div class="bg-gray-900 rounded p-3 font-mono text-sm">
                        <div class="text-gray-400 mb-2">Field Operator Value</div>
                        <div class="text-white">market_cap > 1000000000000</div>
                        <div class="text-gray-400 mt-2 mb-1">Supported Operators:</div>
                        <div class="text-white text-xs"> >, >=, <, <=, ==, !=</div>
                    </div>
                </div>
                
                <div class="bg-navy border border-gray-600 rounded-lg p-5">
                    <h4 class="text-blue-400 font-semibold mb-3">🔗 Logical Connectors</h4>
                    <div class="bg-gray-900 rounded p-3 font-mono text-sm">
                        <div class="text-gray-400 mb-2">AND / OR Logic</div>
                        <div class="text-white">field1 > value1 AND field2 < value2</div>
                        <div class="text-white mt-1">field1 > value1 OR field2 < value2</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fundamental Fields Section -->
        <section id="fundamental-fields" class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-2">
                <span class="text-blue-400">&#x1F4B0;</span> Fundamental Fields Reference
            </h2>
            
            <div class="grid lg:grid-cols-2 gap-6">
                <!-- Pricing & Valuation -->
                <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
                    <h3 class="text-xl font-semibold text-white mb-4 border-b border-gray-700 pb-2">
                        <span class="text-green-400">&#x1F48E;</span> Pricing & Valuation
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-navy rounded-lg border border-gray-600">
                            <div>
                                <div class="text-white font-medium">Current Price</div>
                                <div class="text-gray-400 text-sm">market_cap, pe_ratio, dividend_yield</div>
                            </div>
                            <div class="text-right">
                                <div class="text-green-400 font-mono text-sm">price > 100</div>
                                <div class="text-gray-500 text-xs">>=, <=, ==, !=</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-navy rounded-lg border border-gray-600">
                            <div>
                                <div class="text-white font-medium">P/E Ratio</div>
                                <div class="text-gray-400 text-sm">trailingPE, forwardPE</div>
                            </div>
                            <div class="text-right">
                                <div class="text-green-400 font-mono text-sm">pe_ratio < 20</div>
                                <div class="text-gray-500 text-xs">>, <, ==, !=</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-navy rounded-lg border border-gray-600">
                            <div>
                                <div class="text-white font-medium">Market Cap</div>
                                <div class="text-gray-400 text-sm">market_cap, marketCap</div>
                            </div>
                            <div class="text-right">
                                <div class="text-green-400 font-mono text-sm">market_cap > 1T</div>
                                <div class="text-gray-500 text-xs">>=, <, ==, !=</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-navy rounded-lg border border-gray-600">
                            <div>
                                <div class="text-white font-medium">Dividend Yield</div>
                                <div class="text-gray-400 text-sm">dividend_yield, trailingAnnualDividendYield</div>
                            </div>
                            <div class="text-right">
                                <div class="text-green-400 font-mono text-sm">dividend_yield > 0.02</div>
                                <div class="text-gray-500 text-xs">>, >=, <, <=, ==, !=</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-navy rounded-lg border border-gray-600">
                            <div>
                                <div class="text-white font-medium">Price to Book</div>
                                <div class="text-gray-400 text-sm">priceToBook, bookValue</div>
                            </div>
                            <div class="text-right">
                                <div class="text-green-400 font-mono text-sm">priceToBook < 3</div>
                                <div class="text-gray-500 text-xs">>, <, ==, !=</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Profitability & Returns -->
                <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
                    <h3 class="text-xl font-semibold text-white mb-4 border-b border-gray-700 pb-2">
                        <span class="text-yellow-400">&#x1F4C8;</span> Profitability & Returns
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-navy rounded-lg border border-gray-600">
                            <div>
                                <div class="text-white font-medium">EPS (TTM)</div>
                                <div class="text-gray-400 text-sm">epsTrailingTwelveMonths</div>
                            </div>
                            <div class="text-right">
                                <div class="text-green-400 font-mono text-sm">epsTrailingTwelveMonths > 5</div>
                                <div class="text-gray-500 text-xs">>, <, ==, !=</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-navy rounded-lg border border-gray-600">
                            <div>
                                <div class="text-white font-medium">Revenue Growth (3Y)</div>
                                <div class="text-gray-400 text-sm">revenue_growth_3y</div>
                            </div>
                            <div class="text-right">
                                <div class="text-green-400 font-mono text-sm">revenue_growth_3y > 0.15</div>
                                <div class="text-gray-500 text-xs">>, >=, <, ==, !=</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-navy rounded-lg border border-gray-600">
                            <div>
                                <div class="text-white font-medium">Profit Margin</div>
                                <div class="text-gray-400 text-sm">profit_margin</div>
                            </div>
                            <div class="text-right">
                                <div class="text-green-400 font-mono text-sm">profit_margin > 0.15</div>
                                <div class="text-gray-500 text-xs">>, >=, <, ==, !=</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-navy rounded-lg border border-gray-600">
                            <div>
                                <div class="text-white font-medium">Beta</div>
                                <div class="text-gray-400 text-sm">beta</div>
                            </div>
                            <div class="text-right">
                                <div class="text-green-400 font-mono text-sm">beta < 1.5</div>
                                <div class="text-gray-500 text-xs">>, <, ==, !=</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-navy rounded-lg border border-gray-600">
                            <div>
                                <div class="text-white font-medium">Average Earnings (10Y)</div>
                                <div class="text-gray-400 text-sm">average_earnings_10y</div>
                            </div>
                            <div class="text-right">
                                <div class="text-green-400 font-mono text-sm">average_earnings_10y > 10</div>
                                <div class="text-gray-500 text-xs">>, <, ==, !=</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Technical Analysis Section -->
        <section id="technical-analysis" class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-2">
                <span class="text-purple-400">&#x1F4C9;</span> Technical Analysis Fields
            </h2>
            
            <div class="bg-navy2 rounded-xl border border-gray-700 p-6 mb-6">
                <h3 class="text-xl font-semibold text-white mb-4">Moving Averages & Momentum</h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="p-4 bg-navy rounded-lg border border-gray-600">
                        <div class="text-white font-medium mb-2">50-Day SMA %</div>
                        <div class="text-gray-400 text-sm mb-2">sma_pct(50) - Price vs 50-day SMA</div>
                        <div class="text-green-400 font-mono text-sm">sma_pct(50) > 100</div>
                        <div class="text-gray-500 text-xs mt-1">50-period simple moving average</div>
                    </div>
                    
                    <div class="p-4 bg-navy rounded-lg border border-gray-600">
                        <div class="text-white font-medium mb-2">200-Day SMA %</div>
                        <div class="text-gray-400 text-sm mb-2">sma_pct(200) - Price vs 200-day SMA</div>
                        <div class="text-green-400 font-mono text-sm">sma_pct(200) > 100</div>
                        <div class="text-gray-500 text-xs mt-1">Long-term trend indicator</div>
                    </div>
                    
                    <div class="p-4 bg-navy rounded-lg border border-gray-600">
                        <div class="text-white font-medium mb-2">EMA(20) %</div>
                        <div class="text-gray-400 text-sm mb-2">ema_pct(20) - Price vs 20-day EMA</div>
                        <div class="text-green-400 font-mono text-sm">ema_pct(20) > 100</div>
                        <div class="text-gray-500 text-xs mt-1">Exponential moving average</div>
                    </div>
                    
                    <div class="p-4 bg-navy rounded-lg border border-gray-600">
                        <div class="text-white font-medium mb-2">MACD Line</div>
                        <div class="text-gray-400 text-sm mb-2">macd(12) - MACD line value</div>
                        <div class="text-green-400 font-mono text-sm">macd(12) > 0</div>
                        <div class="text-gray-500 text-xs mt-1">Moving average convergence divergence</div>
                    </div>
                    
                    <div class="p-4 bg-navy rounded-lg border border-gray-600">
                        <div class="text-white font-medium mb-2">RSI(14)</div>
                        <div class="text-gray-400 text-sm mb-2">Relative Strength Index</div>
                        <div class="text-green-400 font-mono text-sm">rsi(14) < 30</div>
                        <div class="text-gray-500 text-xs mt-1">Oversold condition</div>
                    </div>
                    	ha
                    <div class="p-4 bg-navy rounded-lg border border-gray-600">
                        <div class="text-white font-medium mb-2">Stochastic %K</div>
                        <div class="text-gray-400 text-sm mb-2">stoch_k(14) - Stochastic oscillator</div>
                        <div class="text-green-400 font-mono text-sm">stoch_k(14) < 20</div>
                        <div class="text-gray-500 text-xs mt-1">Momentum oscillator</div>
                    </div>
                    <a href="/Users/shibaji/Desktop/project/Learnings/stock trade/stocktrade/app/Views/screener/index.php" style="text-decoration: none; color: inherit;"></a>
                </div>
            </div>
            
            <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Volume & Accumulation</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="p-4 bg-navy rounded-lg border border-gray-600">
                        <div class="text-white font-medium mb-2">Volume Ratio</div>
                        <div class="text-gray-400 text-sm mb-2">volume_ratio(20) - Volume relative to average</div>
                        <div class="text-green-400 font-mono text-sm">volume_ratio(20) > 1.5</div>
                        <div class="text-gray-500 text-xs mt-1">Above average volume confirmation</div>
                    </div>
                    
                    <div class="p-4 bg-navy rounded-lg border border-gray-600">
                        <div class="text-white font-medium mb-2">OBV</div>
                        <div class="text-gray-400 text-sm mb-2">On Balance Volume</div>
                        <div class="text-green-400 font-mono text-sm">obv > obv(20)</div>
                        <div class="text-gray-500 text-xs mt-1">Volume-based momentum indicator</div>
                    </div>
                    
                    <div class="p-4 bg-navy rounded-lg border border-gray-600">
                        <div class="text-white font-medium mb-2">MFI(14)</div>
                        <div class="text-gray-400 text-sm mb-2">Money Flow Index</div>
                        <div class="text-green-400 font-mono text-sm">mfi(14) < 20</div>
                        <div class="text-gray-500 text-xs mt-1">Money flow exhaustion indicator</div>
                    </div>
                    
                    <div class="p-4 bg-navy rounded-lg border border-gray-600">
                        <div class="text-white font-medium mb-2">CMF(20)</div>
                        <div class="text-gray-400 text-sm mb-2">Chaikin Money Flow</div>
                        <div class="text-green-400 font-mono text-sm">cmf(20) > 0.05</div>
                        <div class="text-gray-500 text-xs mt-1">Capital flow momentum indicator</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Advanced Queries Section -->
        <section id="advanced-queries" class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-2">
                <span class="text-red-400">&#x1F9E0;</span> Advanced Query Examples
            </h2>
            
            <div class="space-y-8">
                <!-- Complex Nested Logic -->
                <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">🔗 Complex Nested Logic (Parentheses)</h3>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-900 rounded-lg p-4 border border-gray-600">
                            <div class="text-purple-400 font-mono text-sm mb-2">(condition1 AND condition2) OR (condition3 AND condition4)</div>
                            <div class="grid md:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <div class="text-gray-400 text-xs mb-1">Left Group (AND):</div>
                                    <div class="text-green-400 text-sm">sales_growth_3y > 0.12</div>
                                    <div class="text-green-400 text-sm">net_block > net_block_3y_back * 2</div>
                                </div>
                                <div>
                                    <div class="text-gray-400 text-xs mb-1">Right Group (AND):</div>
                                    <div class="text-green-400 text-sm">capital_work_in_progress > 500M</div>
                                    <div class="text-green-400 text-sm">debt_to_equity < 3</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-900 rounded-lg p-4 border border-gray-600">
                            <div class="text-purple-400 font-mono text-sm mb-2">(Technical OR Fundamental) AND High Volume</div>
                            <div class="grid md:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <div class="text-gray-400 text-xs mb-1">Technical Group:</div>
                                    <div class="text-green-400 text-sm">rsi(14) < 30</div>
                                    <div class="text-green-400 text-sm">macd(12) > macd_signal(12)</div>
                                </div>
                                <div>
                                    <div class="text-gray-400 text-xs mb-1">Fundamental + Volume:</div>
                                    <div class="text-green-400 text-sm">pe_ratio < 20</div>
                                    <div class="text-green-400 text-sm">volume_ratio(20) > 1.5</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mathematical Expressions -->
                <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">&#x1F9EE; Mathematical Expressions</h3>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-900 rounded-lg p-4 border border-gray-600">
                            <div class="text-purple-400 font-mono text-sm mb-2">Combined Field Calculations</div>
                            <div class="text-green-400 text-sm mb-2">(net_block + capital_work_in_progress) > 1.5 * (net_block_3y_back + capital_work_in_progress_1y_back)</div>
                            <div class="text-gray-400 text-sm mt-2">
                                <strong>Meaning:</strong> Current asset value (net block + capital work in progress) > 1.5 × Previous year asset value<br>
                                <strong>Use Case:</strong> Identify rapidly expanding companies with significant asset growth
                            </div>
                        </div>
                        
                        <div class="bg-gray-900 rounded-lg p-4 border border-gray-600">
                            <div class="text-purple-400 font-mono text-sm mb-2">PEG Ratio Calculation</div>
                            <div class="text-green-400 text-sm mb-2">pe_ratio / (earnings_growth_rate * 100)</div>
                            <div class="text-gray-400 text-sm mt-2">
                                <strong>Meaning:</strong> PEG ratio using earnings growth rate<br>
                                <strong>Use Case:</strong> Relative valuation adjusting for growth expectations
                            </div>
                        </div>
                        
                        <div class="bg-gray-900 rounded-lg p-4 border border-gray-600">
                            <div class="text-purple-400 font-mono text-sm mb-2">Percentage Change Calculations</div>
                            <div class="text-green-400 text-sm mb-2">((current_price - sma_50) / sma_50) * 100 > 5</div>
                            <div class="text-gray-400 text-sm mt-2">
                                <strong>Meaning:</strong> Price is more than 5% above 50-day SMA<br>
                                <strong>Use Case:</strong> Technical strength measurement
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Real-world Examples -->
                <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">&#x1F4BC; Real-World Query Examples</h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-green-400 font-semibold mb-3">&#x1F4CA; Value Investing</h4>
                            <div class="space-y-2 text-sm">
                                <div class="bg-gray-900 rounded p-3 border border-gray-600">
                                    <div class="text-gray-300 font-mono text-xs">pe_ratio < 15 AND market_cap > 500M AND dividend_yield > 0.02</div>
                                    <div class="text-gray-500 text-xs mt-1">Traditional value metrics: low P/E, substantial market cap, dividend yield</div>
                                </div>
                                
                                <div class="bg-gray-900 rounded p-3 border border-gray-600">
                                    <div class="text-gray-300 font-mono text-xs">price < book_value AND pe_ratio < 20 AND debt_to_equity < 2</div>
                                    <div class="text-gray-500 text-xs mt-1">Strong fundamentals with manageable leverage</div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-blue-400 font-semibold mb-3">&#x1F680; Growth Investing</h4>
                            <div class="space-y-2 text-sm">
                                <div class="bg-gray-900 rounded p-3 border border-gray-600">
                                    <div class="text-gray-300 font-mono text-xs">sales_growth_3y > 0.25 AND pe_ratio < 30 AND revenue_growth_4q > 0.15</div>
                                    <div class="text-gray-500 text-xs mt-1">High growth with reasonable valuation</div>
                                </div>
                                
                                <div class="bg-gray-900 rounded p-3 border border-gray-600">
                                    <div class="text-gray-300 font-mono text-xs">(rsi(14) > 50 AND volume_ratio(20) > 1.5) OR (sales_growth_3y > 0.30 AND pe_ratio < 25)</div>
                                    <div class="text-gray-500 text-xs mt-1">Technical or fundamental growth leaders</div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-purple-400 font-semibold mb-3">&#x2699;&#xFE0F; Technical Trading</h4>
                            <div class="space-y-2 text-sm">
                                <div class="bg-gray-900 rounded p-3 border border-gray-600">
                                    <div class="text-gray-300 font-mono text-xs">Golden Cross: sma_pct(50) > sma_pct(200) AND rsi(14) > 50</div>
                                    <div class="text-gray-500 text-xs mt-1">50-day above 200-day + momentum</div>
                                </div>
                                
                                <div class="bg-gray-900 rounded p-3 border border-gray-600">
                                    <div class="text-gray-300 font-mono text-xs">MACD Bullish: macd(12) > macd_signal(12) AND macd_histogram > 0</div>
                                    <div class="text-gray-500 text-xs mt-1"> MACD momentum turning positive</div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-orange-400 font-semibold mb-3">&#x1F3E2; Sector-Specific</h4>
                            <div class="space-y-2 text-sm">
                                <div class="bg-gray-900 rounded p-3 border border-gray-600">
                                    <div class="text-gray-300 font-mono text-xs">industry == 'Technology' AND pe_ratio < 25 AND sales_growth_3y > 0.20</div>
                                    <div class="text-gray-500 text-xs mt-1">Technology companies with strong growth</div>
                                </div>
                                
                                <div class="bg-gray-900 rounded p-3 border border-gray-600">
                                    <div class="text-gray-300 font-mono text-xs">sector == 'Financial Services' AND debt_to_equity < 3 AND ROE > 15%</div>
                                    <div class="text-gray-500 text-xs mt-1">Healthy financial sector with good capital efficiency</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Query Builder Section -->
        <section id="query-builder" class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-2">
                <span class="text-cyan-400">&#x1F6E0;&#xFE0F;</span> Interactive Query Builder
            </h2>
            
            <div class="bg-navy2 rounded-xl border border-gray-700 p-6">
                <div class="grid lg:grid-cols-3 gap-6">
                    <!-- Left Panel - Field Categories -->
                    <div class="lg:col-span-1">
                        <h3 class="text-lg font-semibold text-white mb-4">&#x1F4CB; Field Categories</h3>
                        
                        <div class="space-y-3">
                            <div class="bg-navy rounded-lg border border-gray-600 p-4">
                                <h4 class="text-green-400 font-semibold mb-3">&#x1F4CA; Fundamental</h4>
                                <div class="space-y-2">
                                    <button onclick="addField('market_cap')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-green-400">market_cap</span> - Market Capitalization
                                    </button>
                                    <button onclick="addField('pe_ratio')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-green-400">pe_ratio</span> - P/E Ratio
                                    </button>
                                    <button onclick="addField('dividend_yield')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-green-400">dividend_yield</span> - Dividend Yield
                                    </button>
                                    <button onclick="addField('eps_forward')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-green-400">eps_forward</span> - EPS Forward
                                    </button>
                                    <button onclick="addField('sales_growth_3y')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-green-400">sales_growth_3y</span> - Sales Growth 3Y
                                    </button>
                                </div>
                            </div>
                            
                            <div class="bg-navy rounded-lg border border-gray-600 p-4">
                                <h4 class="text-blue-400 font-semibold mb-3">&#x1F4C9; Technical Analysis</h4>
                                <div class="space-y-2">
                                    <button onclick="addField('sma_pct(50)')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-blue-400">sma_pct(50)</span> - 50-Day SMA %
                                    </button>
                                    <button onclick="addField('rsi(14)')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-blue-400">rsi(14)</span> - RSI
                                    </button>
                                    <button onclick="addField('macd(12)')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-blue-400">macd(12)</span> - MACD Line
                                    </button>
                                    <button onclick="addField('volume_ratio(20)')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-blue-400">volume_ratio(20)</span> - Volume Ratio
                                    </button>
                                </div>
                            </div>
                            
                            <div class="bg-navy rounded-lg border border-gray-600 p-4">
                                <h4 class="text-purple-400 font-semibold mb-3">&#x1F3E2; Company Profile</h4>
                                <div class="space-y-2">
                                    <button onclick="addField('industry')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-purple-400">industry</span> - Industry
                                    </button>
                                    <button onclick="addField('sector')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-purple-400">sector</span> - Sector
                                    </button>
                                    <button onclick="addField('debt_to_equity')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-purple-400">debt_to_equity</span> - Debt to Equity
                                    </button>
                                    <button onclick="addField('return_capital_employed_7y')" class="w-full text-left p-2 rounded bg-gray-800 hover:bg-gray-700 transition-colors text-gray-300 text-sm">
                                        <span class="text-purple-400">return_capital_employed_7y</span> - ROCE
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Center Panel - Query Builder -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-semibold text-white mb-4">&#x26A1; Query Builder Interface</h3>
                        
                        <div class="bg-gray-900 rounded-lg p-4 mb-4">
                            <div class="flex flex-wrap gap-2 mb-4">
                                <button onclick="addCondition('field1 > value1')" class="px-3 py-2 bg-green-600 hover:bg-green-700 rounded text-white text-sm transition-colors">Add Condition</button>
                                <button onclick="addLogicalOperator('AND')" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 rounded text-white text-sm transition-colors">AND</button>
                                <button onclick="addLogicalOperator('OR')" class="px-3 py-2 bg-purple-600 hover:bg-purple-700 rounded text-white text-sm transition-colors">OR</button>
                                <button onclick="addParentheses()" class="px-3 py-2 bg-gray-600 hover:bg-gray-500 rounded text-white text-sm transition-colors">(</button>
                                <button onclick="addParentheses()" class="px-3 py-2 bg-gray-600 hover:bg-gray-500 rounded text-white text-sm transition-colors">)</button>
                            </div>
                            
                            <div class="bg-navy border border-gray-600 rounded-lg p-4 min-h-32 mb-4">
                                <div id="query-builder-canvas" class="flex flex-wrap gap-2 items-center">
                                    <div class="text-gray-500 text-sm">Drop fields here to build your query...</div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <button onclick="generateQuery()" class="px-4 py-2 bg-gold hover:bg-gold2 text-navy font-semibold rounded-lg transition-colors">
                                    Generate Query
                                </button>
                                <button onclick="clearBuilder()" class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded text-white text-sm transition-colors">
                                    Clear Builder
                                </button>
                            </div>
                        </div>
                        
                        <div class="bg-blue-900/20 border border-blue-500/30 rounded-lg p-4">
                            <h4 class="text-blue-400 font-semibold mb-2">📝 Example Generated Query</h4>
                            <div class="text-gray-300 font-mono text-sm" id="example-query">
                                (market_cap > 1000000000000 AND pe_ratio < 20) OR (sales_growth_3y > 0.15 AND volume_ratio(20) > 1.5)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Reference -->
        <section id="quick-reference" class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-700 pb-2">
                <span class="text-yellow-400">&#x26A1;</span> Quick Reference Guide
            </h2>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-navy2 rounded-lg border border-gray-700 p-4">
                    <div class="text-green-400 font-semibold mb-2">🔥 Most Used</div>
                    <div class="space-y-2 text-sm">
                        <div class="text-gray-300 font-mono">market_cap > 1T</div>
                        <div class="text-gray-300 font-mono">pe_ratio < 20</div>
                        <div class="text-gray-300 font-mono">sma_pct(50) > 100</div>
                        <div class="text-gray-300 font-mono">volume_ratio(20) > 1.5</div>
                    </div>
                </div>
                
                <div class="bg-navy2 rounded-lg border border-gray-700 p-4">
                    <div class="text-blue-400 font-semibold mb-2">&#x1F4CA; Technical</div>
                    <div class="space-y-2 text-sm">
                        <div class="text-gray-300 font-mono">rsi(14) < 30</div>
                        <div class="text-gray-300 font-mono">macd(12) > macd_signal(12)</div>
                        <div class="text-gray-300 font-mono">stoch_k(14) < 20</div>
                        <div class="text-gray-300 font-mono">atr(14) > 1.5</div>
                    </div>
                </div>
                
                <div class="bg-navy2 rounded-lg border border-gray-700 p-4">
                    <div class="text-purple-400 font-semibold mb-2">&#x1F4C8; Growth</div>
                    <div class="space-y-2 text-sm">
                        <div class="text-gray-300 font-mono">sales_growth_3y > 0.25</div>
                        <div class="text-gray-300 font-mono">eps_forward > 10</div>
                        <div class="text-gray-300 font-mono">return_capital_employed_7y > 20%</div>
                        <div class="text-gray-300 font-mono">revenue_growth_4q > 0.15</div>
                    </div>
                </div>
                
                <div class="bg-navy2 rounded-lg border border-gray-700 p-4">
                    <div class="text-orange-400 font-semibold mb-2">&#x1F3E2; Sector</div>
                    <div class="space-y-2 text-sm">
                        <div class="text-gray-300 font-mono">industry == 'Technology'</div>
                        <div class="text-gray-300 font-mono">sector == 'Financial Services'</div>
                        <div class="text-gray-300 font-mono">country == 'India'</div>
                        <div class="text-gray-300 font-mono">exchange == 'NSE'</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="text-center py-12">
            <div class="bg-gradient-to-r from-blue-900/50 to-purple-900/50 rounded-xl p-8 border border-gray-700">
                <h2 class="text-3xl font-bold text-white mb-4">&#x1F680; Ready to Build Your Screener Queries?</h2>
                <p class="text-gray-300 mb-6">Use the interactive query builder above to create complex stock screening expressions, or refer to the documentation for syntax examples.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <button onclick="scrollToSection('query-builder')" class="px-6 py-3 bg-gold hover:bg-gold2 text-navy font-semibold rounded-lg transition-colors">
                        Start Building Queries
                    </button>
                    <button onclick="scrollToSection('getting-started')" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        View Getting Started
                    </button>
                </div>
            </div>
        </section>
    </div>

    <style>
    /* Custom styles for query builder */
    .field-chip {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 6px 12px;
        border-radius: 20px;
        color: white;
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .operator-chip {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        padding: 6px 12px;
        border-radius: 20px;
        color: white;
        font-size: 12px;
        font-weight: 500;
    }

    .logical-chip {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        padding: 6px 12px;
        border-radius: 20px;
        color: white;
        font-size: 12px;
        font-weight: 500;
    }

    .expression-chain {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 12px;
        margin: 8px 0;
    }

    .expression-arrow {
        color: #fbbf24;
        font-weight: bold;
        text-align: center;
        padding: 4px 0;
    }

    .field-select {
        background: rgba(59, 130, 246, 0.1);
        border-color: rgba(59, 130, 246, 0.3);
    }

    .field-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .duration-input {
        background: rgba(139, 92, 246, 0.1);
        border-color: rgba(139, 92, 246, 0.3);
    }

    .duration-input:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }

    /* Scroll to section function */
    .scroll-to-section {
        scroll-margin-top: 2rem;
    }

    @media (max-width: 768px) {
        .grid-cols-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    </style>

    <script>
    // Query Builder Functionality
    let currentExpression = [];

    function addField(field) {
        const fieldChip = document.createElement('div');
        fieldChip.className = 'field-chip';
        fieldChip.innerHTML = `<span>${field}</span><button onclick="removeElement(this)" class="ml-2 text-white hover:text-red-300">&times;</button>`;
        document.getElementById('query-builder-canvas').appendChild(fieldChip);
        currentExpression.push({ type: 'field', value: field });
        updateExampleQuery();
    }

    function addOperator(operator) {
        const operatorChip = document.createElement('div');
        operatorChip.className = 'operator-chip';
        operatorChip.textContent = operator;
        document.getElementById('query-builder-canvas').appendChild(operatorChip);
        currentExpression.push({ type: 'operator', value: operator });
        updateExampleQuery();
    }

    function addLogicalOperator(logical) {
        const logicalChip = document.createElement('div');
        logicalChip.className = 'logical-chip';
        logicalChip.textContent = logical;
        document.getElementById('query-builder-canvas').appendChild(logicalChip);
        currentExpression.push({ type: 'logical', value: logical });
        updateExampleQuery();
    }

    function addParentheses() {
        const parentheses = document.createElement('div');
        parentheses.className = 'px-3 py-1 bg-gray-600 rounded text-white text-sm font-mono';
        parentheses.textContent = '()';
        document.getElementById('query-builder-canvas').appendChild(parentheses);
        currentExpression.push({ type: 'parentheses', value: '()' });
        updateExampleQuery();
    }

    function removeElement(button) {
        button.parentElement.remove();
        // TODO: Remove from currentExpression
    }

    function updateExampleQuery() {
        const exampleQuery = document.getElementById('example-query');
        if (exampleQuery) {
            const expressionStr = currentExpression.map(item => item.value).join(' ');
            exampleQuery.textContent = expressionStr || 'No expression built yet...';
        }
    }

    function generateQuery() {
        alert('Query generated: ' + document.getElementById('example-query').textContent);
    }

    function clearBuilder() {
        document.getElementById('query-builder-canvas').innerHTML = '';
        currentExpression = [];
        updateExampleQuery();
    }

    function scrollToSection(sectionId) {
        const element = document.getElementById(sectionId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    </script>
</section>