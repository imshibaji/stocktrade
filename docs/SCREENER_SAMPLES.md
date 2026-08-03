# StockTrade — 30 Screener Sample Queries

A curated collection of ready-to-use screener queries for the StockTrade stock screening application. Each query targets a specific investment strategy or market condition.

---

## How to Use

Paste any query into the **Manual Query** field on the Screener page. Use `AND` / `OR` to combine conditions. Click **Run** to see matching stocks.

---

## 1. Value Investing

### 1.1 Classic Value — Low P/E, Low P/B, Strong Earnings
```
pe_ratio < 15 AND priceToBook < 2 AND epsTrailingTwelveMonths > 0 AND market_cap > 5000000000
```

### 1.2 Deep Value — Graham-style (Low Price vs. Intrinsic)
```
price < bookValue AND pe_ratio < 10 AND dividend_yield > 2 AND market_cap > 1000000000
```

### 1.3 Low P/E Growth — Value with Earnings Growth
```
pe_ratio < 18 AND forward_pe < 15 AND epsForward > 0 AND market_cap > 3000000000
```

### 1.4 Net-Net Working Capital (Benjamin Graham)
```
price < (bookValue - totalLiabilities) AND market_cap < 5000000000
```

### 1.5 Dividend Value — Undervalued with Income
```
dividend_yield > 3 AND pe_ratio < 20 AND priceToBook < 2 AND market_cap > 2000000000
```

---

## 2. Growth Investing

### 2.1 High Revenue Growth
```
revenueGrowth > 20 AND market_cap > 5000000000 AND pe_ratio < 50
```

### 2.2 Earnings Growth Accelerator
```
epsGrowth > 25 AND forward_pe < 30 AND market_cap > 1000000000
```

### 2.3 Small-Cap Growth
```
market_cap > 500000000 AND market_cap < 10000000000 AND revenueGrowth > 30 AND epsGrowth > 20
```

### 2.4 PEG Ratio — Growth at a Reasonable Price
```
peg_ratio < 1 AND pe_ratio < 30 AND epsGrowth > 15 AND market_cap > 1000000000
```

### 2.5 High Forward Growth
```
forward_pe < 20 AND epsForward > 0 AND epsGrowth > 20 AND market_cap > 2000000000
```

---

## 3. Momentum & Trend Following

### 3.1 Price Above 200-Day MA (Long-Term Uptrend)
```
price > two_hundred_day_average AND price > fifty_day_average AND market_cap > 5000000000
```

### 3.2 RSI Oversold Bounce Setup
```
rsi(14) < 30 AND price > two_hundred_day_average AND market_cap > 1000000000
```

### 3.3 MACD Bullish Crossover
```
macd > macd_signal AND macd_histogram > 0 AND price > fifty_day_average
```

### 3.4 Supertrend Bullish with Volume
```
supertrend_dir == 1 AND volume_ratio(20) > 1.5 AND market_cap > 2000000000
```

### 3.5 50-Day MA Crossover (Golden Cross)
```
fifty_day_average_change > 0 AND price > fifty_day_average AND two_hundred_day_average_change > 0
```

### 3.6 Breakout Above 52-Week High with Volume
```
price > week_52_high AND volume_ratio(20) > 2 AND market_cap > 1000000000
```

---

## 4. Income & Dividend Investing

### 4.1 High Dividend Yield — Income Focus
```
dividend_yield > 4 AND market_cap > 2000000000 AND pe_ratio < 25
```

### 4.2 Dividend Aristocrat — Consistent Payer
```
dividend_yield > 2 AND dividend_yield < 6 AND pe_ratio < 20 AND market_cap > 5000000000
```

### 4.3 Growing Dividend with Low Payout
```
dividend_yield > 2 AND pe_ratio < 20 AND market_cap > 1000000000
```

### 4.4 High Yield + Low Volatility
```
dividend_yield > 3 AND beta < 1 AND market_cap > 2000000000
```

---

## 5. Quality Investing

### 5.1 High Return on Equity
```
roe > 15 AND pe_ratio < 25 AND market_cap > 2000000000
```

### 5.2 Strong Balance Sheet + Low Debt
```
debtToEquity < 0.5 AND currentRatio > 1.5 AND market_cap > 5000000000
```

### 5.3 Consistent Profitability
```
epsTrailingTwelveMonths > 0 AND forward_pe < 25 AND market_cap > 3000000000
```

### 5.4 High Margins + Growth
```
grossMargin > 40 AND revenueGrowth > 10 AND market_cap > 2000000000
```

---

## 6. Technical Breakout & Chart Patterns

### 6.1 Bollinger Band Squeeze (Low Volatility Before Breakout)
```
bb_width(20) < 0.05 AND volume_ratio(20) > 1.2 AND market_cap > 1000000000
```

### 6.2 RSI Overbought with Strong Trend
```
rsi(14) > 70 AND price > fifty_day_average AND market_cap > 2000000000
```

### 6.3 Stochastic Oversold Reversal
```
stoch_k(14) < 20 AND stoch_d(14) < 20 AND market_cap > 1000000000
```

### 6.4 CCI Extreme Oversold
```
cci(20) < -100 AND price > two_hundred_day_average
```

### 6.5 Williams %R Deep Oversold
```
williams_r(14) < -80 AND price > fifty_day_average
```

### 6.6 Aroon Uptrend Confirmation
```
aroon_up(25) > 70 AND aroon_down(25) < 30 AND market_cap > 1000000000
```

---

## 7. Volume & Liquidity

### 7.1 High Volume Surge
```
volume_ratio(20) > 2 AND market_cap > 2000000000
```

### 7.2 Accumulation — Rising Volume + Price
```
cmf(20) > 0.05 AND volume_ratio(20) > 1.5 AND price > fifty_day_average
```

### 7.3 Low Float High Volume
```
avg_volume > 5000000 AND sharesOutstanding < 100000000 AND market_cap > 500000000
```

### 7.4 Force Index Positive — Buying Pressure
```
force_index(13) > 0 AND price > fifty_day_average AND market_cap > 1000000000
```

---

## 8. Volatility & Risk Management

### 8.1 Low Volatility — Defensive
```
atr(14) / price < 0.02 AND beta < 0.8 AND market_cap > 5000000000
```

### 8.2 High Beta — Aggressive
```
beta > 1.5 AND market_cap > 1000000000 AND price > fifty_day_average
```

### 8.3 Low Downside Risk (Sortino Ratio)
```
sortino_ratio > 1.5 AND market_cap > 2000000000
```

### 8.4 TTM Squeeze — Volatility Compression
```
ttm_squeeze == 1 AND market_cap > 2000000000
```

---

## 9. Sector & Market Structure

### 9.1 Large Cap Blue Chip
```
market_cap > 50000000000 AND beta < 1.2 AND pe_ratio < 25
```

### 9.2 Mid-Cap with Momentum
```
market_cap > 2000000000 AND market_cap < 10000000000 AND price > fifty_day_average AND volume_ratio(20) > 1.5
```

### 9.3 Small-Cap Value
```
market_cap > 300000000 AND market_cap < 2000000000 AND pe_ratio < 15 AND dividend_yield > 1
```

### 9.4 Institutional Activity — Smart Money
```
institutionalOwnership > 50 AND price > fifty_day_average AND market_cap > 5000000000
```

---

## 10. AI Prediction-Driven

### 10.1 Strong Buy Signal with High Confidence
```
prediction_score > 0.8 AND prediction_direction == "UP" AND confidence > 0.7
```

### 10.2 Mean Reversion Opportunity
```
zscore(20) < -2 AND price < fifty_day_average AND market_cap > 1000000000
```

### 10.3 Trend Continuation with ML Edge
```
linreg_slope(20) > 0 AND linreg_rsq(20) > 0.7 AND volume_ratio(20) > 1.2
```

### 10.4 Reversal Setup — Oversold with Momentum Shift
```
rsi(14) < 30 AND macd_histogram > 0 AND price < two_hundred_day_average
```

---

## 11. Combined Strategy Queries

### 11.1 Quality Value Compound
```
pe_ratio < 15 AND priceToBook < 2 AND roe > 15 AND dividend_yield > 2 AND market_cap > 3000000000
```

### 11.2 Growth at Reasonable Price (GARP)
```
peg_ratio < 1.2 AND pe_ratio < 25 AND epsGrowth > 15 AND market_cap > 2000000000
```

### 11.3 Momentum + Quality Screen
```
price > fifty_day_average AND roe > 15 AND volume_ratio(20) > 1.5 AND market_cap > 5000000000
```

### 11.4 Defensive Income Compound
```
dividend_yield > 3 AND beta < 1 AND pe_ratio < 20 AND currentRatio > 1.5 AND market_cap > 5000000000
```

### 11.5 Breakout + Volume Confirmation
```
price > week_52_high AND volume_ratio(20) > 2 AND rsi(14) > 50 AND rsi(14) < 70 AND market_cap > 1000000000
```

### 11.6 Mean Reversion + Oversold
```
zscore(20) < -1.5 AND rsi(14) < 35 AND price > two_hundred_day_average AND market_cap > 2000000000
```

### 11.7 Supertrend + CMF + RSI (Full Trend System)
```
supertrend_dir == 1 AND cmf(20) > 0 AND rsi(14) > 50 AND rsi(14) < 70 AND market_cap > 1000000000
```

### 11.8 Keltner Channel Breakout
```
price > kc_pct(20, 10, 2.0) AND volume_ratio(20) > 1.5 AND market_cap > 2000000000
```

### 11.9 Donchian Channel Breakout
```
price > dc_pct(20) AND volume_ratio(20) > 1.8 AND market_cap > 1000000000
```

### 11.10 Ultimate Multi-Indicator Screen
```
pe_ratio < 20 AND price > fifty_day_average AND rsi(14) > 40 AND rsi(14) < 70 AND macd > macd_signal AND volume_ratio(20) > 1.2 AND market_cap > 2000000000
```

---

## Quick Reference: Available Filter Fields

### Fundamental Fields
| Field | Description |
|-------|-------------|
| `price` / `current_price` | Current stock price |
| `market_cap` | Market capitalization |
| `pe_ratio` | Price-to-Earnings ratio |
| `forward_pe` | Forward P/E |
| `peg_ratio` | PEG ratio |
| `priceToBook` | Price-to-Book |
| `dividend_yield` | Dividend yield % |
| `beta` | Beta coefficient |
| `epsTrailingTwelveMonths` | TTM EPS |
| `epsForward` | Forward EPS |
| `avg_volume` | Average daily volume |
| `week_52_high` / `week_52_low` | 52-week range |
| `fifty_day_average` | 50-day moving average |
| `two_hundred_day_average` | 200-day moving average |
| `bookValue` | Book value per share |
| `sharesOutstanding` | Total shares |
| `enterprise_value` | Enterprise value |
| `sector` | Sector name (string) |
| `name` | Company name (string) |
| `symbol` | Ticker symbol (string) |

### Technical Indicator Fields (use `period` suffix)
| Field | Description |
|-------|-------------|
| `rsi(period)` | Relative Strength Index |
| `macd` | MACD line |
| `macd_signal` | MACD signal line |
| `macd_histogram` | MACD histogram |
| `sma_pct(period)` | Price as % of SMA |
| `ema_pct(period)` | Price as % of EMA |
| `bb_pct(period)` | Bollinger Band %B |
| `bb_width(period)` | Bollinger Band width |
| `atr(period)` | Average True Range |
| `stoch_k(period)` | Stochastic %K |
| `stoch_d(period)` | Stochastic %D |
| `cci(period)` | Commodity Channel Index |
| `roc(period)` | Rate of Change |
| `williams_r(period)` | Williams %R |
| `supertrend_dir` | Supertrend direction (1/-1) |
| `volume_ratio(period)` | Current volume vs. SMA |
| `cmf(period)` | Chaikin Money Flow |
| `mfi(period)` | Money Flow Index |
| `obv` | On-Balance Volume |
| `force_index(period)` | Force Index |
| `aroon_up(period)` / `aroon_down(period)` | Aroon indicator |
| `zscore(period)` | Z-Score |
| `linreg_slope(period)` | Linear regression slope |
| `linreg_rsq(period)` | R-squared |
| `hurst(period)` | Hurst exponent |
| `chop(period)` | Choppiness index |
| `kama(period)` | Kaufman's Adaptive MA |
| `ttm_squeeze` | TTM Squeeze (1/0) |
| `connors_rsi` | Connors RSI |
| `tsi` | True Strength Index |
| `cmo(period)` | Chande Momentum Oscillator |
| `mass_index` | Mass Index |
| `vi_plus(period)` / `vi_minus(period)` | Vortex indicator |
| `dpo(period)` | Detrended Price Oscillator |
| `efficiency_ratio(period)` | Kaufman Efficiency Ratio |
| `downside_dev` | Downside deviation |
| `sortino_ratio` | Sortino ratio |
| `cvar` | Conditional VaR |
| `historical_var` | Historical Value at Risk |
| `martin_ratio` | Martin ratio |
| `vp_poc` / `vp_vah` / `vp_val` | Volume Profile levels |
| `fib_61.8` | Fibonacci 61.8% retracement ratio |
| `pivot` | Pivot point level |

### Operators
`>`, `>=`, `<`, `<=`, `==`, `!=`

### Logic
`AND`, `OR`

### Math on Values
`field + N`, `field - N`, `field * N`, `field / N`, `field % N`

### String Matching
`sector == "Technology"`, `name == "Apple"`
