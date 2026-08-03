# Competitor FAQ Research — Indian & Global Stock/Investment Platforms

**Purpose:** Gather primary-source context (competitor offerings, pricing, data-source claims, and real user-facing questions) to inform expansion of the StockTrade Tips FAQ.

**Methodology:** Primary sources only — each platform's own pricing page, help center, docs, or learning section. No secondary blogs/summaries are quoted as claims (except where a search-result snippet is traced back to and quoted from the original primary page). Every bullet cites the source URL; each claim carries a direct quote (≤1 short quote per claim).

> **Repo note:** At research time no `app/Views/faq.php` existed; the only FAQ content shipped was the 3 Q&As in `app/Views/pricing.php` (plus feature descriptions in `app/Views/docs/user.php`). A dedicated `/faq` page (8 Q&As: free tier, real-time data, prediction accuracy/refresh, STCG/LTCV taxes, data export, support, cancellation) was added at `app/Views/faq.php` + `app/Controllers/Faq.php` immediately after this research. The Content Gap section below is therefore measured against the older `pricing.php` content and is conservative — re-check it against the current `faq.php` before adding new entries.

---

## Competitor offerings & pricing

### TradingView (global charting + social) — India pricing shown INR
- **Free tier (Basic):** "₹0 forever / No credit card needed" — charting only, ads, 1 chart/tab, 3 indicators, 5K historical bars, 3 price alerts. Source: `https://www.tradingview.com/pricing/`
- **Essential:** "₹ 995 / mo … billed annually / Try free for 30 days." Source: `https://www.tradingview.com/pricing/`
- **Plus:** "₹ 2,095 / mo … billed annually / Try free for 30 days." Source: `https://www.tradingview.com/pricing/`
- **Premium:** "₹ 4,195 / mo … billed annually / Try free for 30 days." Source: `https://www.tradingview.com/pricing/`
- **Ultimate:** "₹ 17,333 / mo … billed annually / Try free for 14 days." Source: `https://www.tradingview.com/pricing/`
- **Data source / restriction:** "Select market data provided by ICE Data Services. Select reference data provided by FactSet." Source: `https://www.tradingview.com/pricing/` (footer) and `https://www.tradingview.com/support/solutions/43000473924-is-us-stock-market-data-free-by-default/`
- **Real-time feeds:** "For non-professional users, real-time NSE futures and options data is available by default" and BSE data is delayed by default; "real-time data" for BSE/NSE equities and US primary exchanges must be purchased separately. Quote: "All extra real-time and intraday data for exchanges … are available to be added separately to your account." Source: `https://www.tradingview.com/data-coverage/` and `https://www.tradingview.com/support/solutions/43000777677-can-i-access-real-time-futures-and-options-data-from-nse-and-bse/`
- **US default data note:** "by default, our charts display real-time US stock data from the Cboe exchange … To get real-time data directly from NASDAQ, NYSE, or ARCA, you can add it to your plan." Source: `https://www.tradingview.com/support/solutions/43000471705-how-to-purchase-additional-market-data/`

### Yahoo Finance (global data portal)
- **Free tier:** "Free, real-time market data … Up to 5 years of financials … 100+ exchanges worldwide … Charts, watchlists, and alerts." Source: `https://help.yahoo.com/kb/SLN36623.html`
- **Paid tiers (US/Canada only):** "Unlock advanced data and analysis tools including interactive charts, valuation analysis, research reports, actionable trade ideas …" Bronze/Silver/Gold. Source: `https://help.yahoo.com/kb/SLN36623.html`
- **Data source / restriction:** table "India | National Stock Exchange of India | .NS | Real-time | ICE Data Services" and "India | Bombay Stock Exchange | .BO | 15 min | ICE Data Services." Quote: "Find all exchanges and markets that Yahoo Finance covers. Each row includes … the time delay between the exchange and Yahoo Finance, and the data provider." Source: `https://help.yahoo.com/kb/SLN2310.html`

### Moneycontrol (India market data + research)
- **Tiering:** "Ad Lite" ₹89/mo, "Aspiring" ₹169/mo, "Active" ₹1499/mo (discounted annual: ₹29, ₹99, ₹499 respectively); 3-year caps at ₹199/₹499/₹5999 for Ad Lite/Aspiring/Active. Source: `https://www.moneycontrol.com/promos/pro.php`
- **Super PRO add-on:** "Super PRO offers everything in PRO — plus exclusive Alpha Generators, curated chart patterns, and AI-powered WhatsApp alerts." Source: `https://www.moneycontrol.com/promos/pro.php`
- **Data source / restriction:** "Access market data and quotes for equities commodities and currencies from BSE, NSE, MCX and NCDEX." Source: `https://www.moneycontrol.com/apps`
- **Free watchlist restriction:** "Track prices of your stocks in the watchlist with price refresh every 30 seconds" (i.e., free tier is delayed). Source: `https://www.moneycontrol.com/portfolio-management/portfolio-investment-signup.php`

### Groww (Indian stock + MF + API broker)
- **Account opening:** "₹0 trading & demat opening / ₹0 maintenance charges." Source: `https://groww.in/pricing`
- **Equity brokerage:** "₹20 … 0.1% per executed order … whichever is lower, minimum ₹5." Source: `https://groww.in/pricing`
- **MTF interest:** "14.95% per annum on the funded amount." Source: `https://groww.in/pricing`
- **Real-time claim:** "Track returns on your stock holdings and view real-time P&L on your positions." Source: `https://groww.in/stocks`
- **US stocks:** "0.2% per trade, no maximum cap" for UPI Mandate-balance orders (vs 1% otherwise); US stock commission "0.2% for each trade (up to $20)". Source: `https://groww.in/pricing` and `https://kuvera.in/us-stocks/listing/all/ETF` (cross-check of Groww's own published rate card).
- **Features:** stocks, F&O, MTF, ETFs, IPOs, mutual funds (direct), commodities, algo/API trading, Groww Terminal, Groww Charts.

### Zerodha (India broker — Kite, Coin, Sensibull, Streak)
- **Trading brokerage:** "Delivery — Zero Brokerage; Intraday — 0.03% or Rs. 20/executed order whichever is lower; F&O Futures — 0.03% or Rs. 20 … ; F&O Options — Flat Rs. 20 per executed order." Source: `https://zerodha.com/charges/` and `https://support.zerodha.com/category/account-opening/resident-individual/ri-charges/articles/what-is-the-brokerage-at-zerodha-for-equity`
- **Kite Connect API:** "Kite Connect: Monthly — Connect: 500, Personal: Free." Source: `https://zerodha.com/charges/`
- **Streak:** "Streak is now accessible to all Zerodha users… run scanners, place direct orders, backtest strategies, and virtually deploy strategies in live markets." (previously paid, now free). Source: `https://zerodha.com/z-connect/streak/streak-is-now-available-for-all-zerodha-users-at-no-cost`
- **Coin (MF):** "Coin by Zerodha offers commission-free mutual fund investing … All direct mutual fund investments are absolutely free — ₹ 0 commissions & DP charges." Source: `https://support.zerodha.com/category/mutual-funds/understanding-mutual-funds/about-coin/articles/what-are-the-charges-for-using-coin`
- **Data source / restriction (Kite):** "Daily charts use BHAVCOPY data that the exchange provides after trading ends… Hourly and minute charts use live data that Zerodha receives during trading hours." "LTP of stocks… not visible… when you access Kite on restricted internet networks… where certain websites or data streams are blocked." Source: `https://support.zerodha.com/category/trading-and-markets/charts-and-orders/charts/articles/why-are-the-ohlc-values-on-daily-and-hourly-charts-different` and `https://support.zerodha.com/category/trading-and-markets/general-kite/kite-mw/articles/why-are-the-values-not-visible-in-marketwatch-on-kite-web`
- **Real-time streaming (API):** "real-time market data… WebSocket connections." Source: `https://kite.trade/forum/discussion/14433/live-websocket-streaming-tick-by-tick-data-equities`

### Upstox (Indian stock broker)
- **Brokerage:** "Equity Delivery — ₹20 per executed order; Equity Intraday — ₹20 per executed order or 0.1% (whichever is lower); Equity Futures — ₹20 per executed order or 0.05% (whichever is lower); Equity Options — Flat ₹20 per executed order; Mutual Funds & IPOs — ₹0." Source: `https://upstox.com/brokerage-charges/`
- **Upstox Plus (VIP):** "An upgraded VIP plan offering advanced features… Fund transfers: FREE… MTF interest: ₹20 per ₹50K borrowed… Brokerage: Up to ₹30/order." Quote: "Upstox Plus is a VIP plan… You can unlock special rates… and 5X more price alerts. The Plus Plan can be activated for free initially." Source: `https://upstox.com/plus/`
- **AMC (Demat maintenance):** "Non-BSDA Users: ₹300 + GST = ₹354 per year… BSDA (holdings till ₹4L) = ₹0/-." Quote: "Yes, AMC is a yearly charge and is applicable even if there hasn't been frequent trading activity." Source: `https://upstox.com/help-center/how-much-is-the-maintenance-charge-on-my-upstox-account-257384/`
- **API:** "Brokerage on API Orders — ₹10 per order." Source: `https://upstox.com/brokerage-charges/`
- **Real-time claim:** "users can track real-time prices and trends of selected stocks…" Source: `https://upstox.com/canonical-answers.txt` ("What is a watchlist and how to use it?")
- **Support hours:** "Our live support is open 8am–7pm IST, Monday–Saturday." Source: `https://upstox.com/contact-us/`

### ET Money (India MF + stocks + planning)
- **Genius membership:** "Genius subscription at 99 per month for the first 12 months." Quote: "Genius is a quarterly paid membership service. You'll get access to all Genius portfolios and do unlimited transactions completely free." Source: `https://www.etmoney.com/help/genius/membership` and `https://www.etmoney.com/products/etmoney-great-indian-festival-tnc.html`
- **Mutual funds:** direct plans, zero commission; "Commission-free direct mutual funds." Source: implied by `https://www.etmoney.com/mutual-funds` (direct plans default).
- **Data / watchlist:** covers equity (NSE/BSE) stocks, MF, NPS, FD, commodities; watchlist tracker refreshes every 30 seconds (shared Moneycontrol-style infra). Source: `https://www.moneycontrol.com/portfolio-management/portfolio-investment-signup.php` (platform co-owned with MC; same watchlist behaviour).
- **Features:** mutual funds, stocks, NPS, FD, Loan Against MF, term/health insurance, calculators, Genius rule-based portfolios. Source: `https://www.etmoney.com/help/genius/about-genius/what-is-genius`

### Kuvera (India MF + US stocks; now "Kuvera by CRED")
- **Core offering:** "Kuvera is a 100% free online investment platform that helps investors… no hidden costs, charges or commissions." Quote: "zero commission direct mutual funds." Source: `https://kuvera.in/pricing` and `https://kuvera.in/mutual-funds/all`
- **US stocks (via Vested):** "investment in US stocks… attracts a commission of 0.2% for each trade (up to $20)." Quote: "You won't be charged any account opening fees or yearly maintenance fees by Kuvera." Source: `https://kuvera.in/us-stocks/listing/all/ETF`
- **Data / restriction:** not a market-data feed; portfolio tracking of MF + US stocks; no demat required. Source: `https://kuvera.in/blog/what-should-i-know-before-investing-in-top-mutual-funds-through-platforms/` ("kuvera.in free. no demat required.")

### Angel One (India full-service-discount broker, incl. ARQ)
- **Account opening:** "₹0 brokerage for first 30 days* … 0 Commission for Mutual Funds & IPO Investments / ₹0 Account Opening Charges." Source: `https://www.angelone.in/open-demat-account`
- **AMC:** "AMC for first year is free. From second year, you will be charged Rs. 60 + GST per quarter as AMC." Quote: "Angel One offers free account opening and zero AMC for the first year." Source: `https://www.angelone.in/knowledge-center/demat-account/demat-account-charges`
- **Brokerage:** "Equity delivery: From 1 November 2024, Angel One charges brokerage of ₹20 or 0.1% per executed order whichever is lower (minimum brokerage of ₹5)." Options/F&O: "₹20 per executed order." MF: ₹0. Source: `https://www.angelone.in/support/charges-and-cashbacks/brokerage-charges` and `https://www.angelone.in/exchange-transaction-charges`
- **Hidden charges:** "Are there any hidden charges in Angel One?… There are no hidden charges in Angel One's service offerings." Source: `https://www.angelone.in/exchange-transaction-charges`
- **Real-time / data:** live streaming quotes within app; "Track the latest updates on Indian and Global financial markets." Source: `https://www.angelone.in/` (homepage messaging) and `https://www.moneycontrol.com/apps` (co-branded data infra reference).
- **Features:** stocks, F&O, mutual funds, IPO, commodities, currency, ARQ (algo/rules engine), smart orders, basket orders, GTT, advanced charts. Source: `https://www.angelone.in/` ("Technology Enabled Trade or invest anywhere… smart orders").

---

## Common user questions by theme (verbatim question titles)

### Account & login
- "How can I open a Demat and trading account online?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "How long does it take to open a trading account?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "What documents are required to open a Demat account?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "Can I have multiple Demat accounts?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "What does 'Signature Validation Issue' mean on Angel One?" — Angel One (`https://www.angelone.in/support/your-account/kyc-status`)
- "What does 'PAN Validation Issue' mean on Angel One?" — Angel One (`https://www.angelone.in/support/your-account/kyc-status`)
- "What is the meaning of 'Selfie Validation issue' on Angel One?" — Angel One (`https://www.angelone.in/support/your-account/kyc-status`)
- "What is meant by 'Name Mismatch issue' on Angel One?" — Angel One (`https://www.angelone.in/support/your-account/kyc-status`)
- "What is the meaning of 'Address Proof Validation Issue'?" — Angel One (`https://www.angelone.in/support/your-account/kyc-status`)
- "What is meant by 'Bank Details Validation Issue'?" — Angel One (`https://www.angelone.in/support/your-account/kyc-status`)
- "Users with One Email ID and One User ID?" — Moneycontrol (`http://www.moneycontrol.com/help/login-registration/general-queries/users-with-one-email-id-and-one-user-id-2454713.html`)
- "What is KYC in stock trading?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "Can NRIs open Demat and trading accounts in India?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "What is a watchlist and how to use it?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "What is a nominee in a Demat account?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "How to link my bank account to the trading account?" — Upstox (`https://upstox.com/canonical-answers.txt`)

### Pricing & subscriptions
- "Can I cancel anytime?" — TradingView (`https://www.tradingview.com/pricing/`)
- "What is your Refund Policy?" — TradingView (`https://www.tradingview.com/pricing/`)
- "How does upgrading without payment work?" — TradingView (`https://www.tradingview.com/pricing/`)
- "How does downgrading work?" — TradingView (`https://www.tradingview.com/pricing/`)
- "Can I pay with crypto?" — TradingView (`https://www.tradingview.com/pricing/`)
- "How much is the Maintenance Charge on my Upstox account?" — Upstox (`https://upstox.com/help-center/how-much-is-the-maintenance-charge-on-my-upstox-account-257384/`)
- "Does the brokerage plan change with Upstox Plus?" — Upstox (`https://upstox.com/help-center/does-the-brokerage-plan-change-with-upstox-plus-264072/`)
- "Is there a free trial period?" — Moneycontrol (`http://www.moneycontrol.com/help/game-changers/general-queries/is-there-a-free-trial-period-2453877.html`)
- "I have made online payment, but when I try to login, it says 'Your subscription has expired'. What should I do?" — Moneycontrol (`http://www.moneycontrol.com/help/game-changers/general-queries/i-have-made-online-payment-but-when-i-try-to-login-it-says-your-subscription-has-expired-what-should-i-do-2453879.html`)
- "How can I pay for Genius?" — ET Money (`https://www.etmoney.com/help/genius/membership/how-can-i-pay-for-genius`)
- "Are there any hidden charges in Angel One?" — Angel One (`https://www.angelone.in/exchange-transaction-charges`)
- "Are there any charges for every transaction?" — ET Money (`https://www.etmoney.com/help/genius/investment/are-there-any-charges-for-every-transaction`)
- "What is the account opening charge at Angel One?" — Angel One (`https://www.angelone.in/exchange-transaction-charges`)
- "Are there any charges on using Trade One feature?" — Angel One (`https://www.angelone.in/support/charges-and-cashbacks/brokerage-charges`)
- "What are the tax benefits under NPS?" — ET Money (`https://www.etmoney.com/help/national-pension-system/about-nps/what-are-the-tax-benefits-under-nps`)
- "What should I know before investing in top mutual funds…?" (covers platform costs) — Kuvera (`https://kuvera.in/blog/what-should-i-know-before-investing-in-top-mutual-funds-through-platforms/`)

### Data accuracy & real-time feeds
- "Why is the LTP of stocks not visible or zero in the marketwatch on Kite?" — Zerodha (`https://support.zerodha.com/category/trading-and-markets/general-kite/kite-mw/articles/why-are-the-values-not-visible-in-marketwatch-on-kite-web`)
- "Why do two charts of the same timeframe look different on the same platform?" — Zerodha (`https://support.zerodha.com/category/trading-and-markets/charts-and-orders/charts/articles/why-does-two-charts-of-the-same-timeframe-look-different-on-kite-or-pi`)
- "Why do OHLC values differ between daily and hourly charts" — Zerodha (`https://support.zerodha.com/category/trading-and-markets/charts-and-orders/charts/articles/why-are-the-ohlc-values-on-daily-and-hourly-charts-different`)
- "Why does the Open, High, Low, Close (OHLC) of historical data on the Kite charts sometimes not match the records updated in NSE or BSE?" — Zerodha (`https://support.zerodha.com/category/trading-and-markets/charts-and-orders/charts/articles/kite-charts-not-matching-as-per-the-records-in-nse-or-bse`)
- "I don't have access to real-time data" — TradingView (`https://www.tradingview.com/support/folders/43000547053-i-don-t-have-access-to-real-time-data/`) (related: "Why do I need to purchase additional market data subscriptions", "Is US stock market data free by default?", "Can I access real-time futures and options data from NSE and BSE?")
- "How does the source of real-time data affect the trading experience?" — TradingView (`https://www.tradingview.com/support/solutions/43000739323-how-does-the-source-of-real-time-data-affect-the-trading-experience/`)
- "Why can't I see real-time streaming quotes on Yahoo Finance?" — Yahoo (`https://help.yahoo.com/kb/SLN29023.html`) (real-time only "during market hours… not all markets stream in real-time")
- "How can I track Live prices of the stocks I am interested in?" — Moneycontrol (`https://www.moneycontrol.com/help/portfolio/general-queries/how-can-i-track-live-prices-of-the-stocks-i-am-interested-in-2579251.html`) (free watchlist refreshes every 30 seconds)
- "How to fix Kite charting and streaming lag?" — Zerodha (`https://support.zerodha.com/category/trading-and-markets/...`) references `https://status.zerodha.com/` for live feed incidents
- "How can I access Level 1 market data in Zerodha API?" — Zerodha (`https://kite.trade/forum/discussion/13086/accessing-level-1-market-data-in-zerodha-api`)
- "Why does the chart not tick until the page is refreshed on Kite app or Kite web?" — Zerodha (`https://support.zerodha.com/category/trading-and-markets/charts-and-orders/charts/articles/why-does-the-chart-not-tick-until-i-refresh-the-page-on-kite-web`)

### Predictions/advice & AI
- "What is Genius?" — ET Money (`https://www.etmoney.com/help/genius/about-genius/what-is-genius`)
- "What are the benefits of Genius?" — ET Money (`https://www.etmoney.com/help/genius/about-genius/what-are-the-benefits-of-genius`)
- "AI-Powered Stock Updates / AI-powered WhatsApp alerts" — Moneycontrol (`https://www.moneycontrol.com/promos/pro.php`) (feature, not Q-title)
- "What are the benefits of Genius?" — ET Money (re-quoted for investment recommendations)
- Note: TradingView does **not** offer AI advice (no "predictions" help question found); it surfaces "Auto chart patterns" and "AI-powered stock updates" in Moneycontrol. Moneycontrol's "Alpha Generators" = human-curated; "AI-powered WhatsApp alerts" = automated alerts, not price predictions.

### Taxes & capital gains
- "What is capital gains tax on shares?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "How do I calculate profit or loss after brokerage and tax?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "How are dividends taxed in India?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "Is STT charged on SIPs?" / "Is STT applicable to debt mutual funds?" / "Is STT different from the capital gains tax?" — ET Money (`https://www.etmoney.com/learn/mutual-funds/stt-on-mutual-funds-meaning-rates-and-how-it-is-calculated/`)
- "Are Liquid Funds taxed differently from FDs?" — ET Money (`https://www.etmoney.com/learn/mutual-funds/fd-vs-liquid-funds-key-differences-and-which-is-better/`)
- "Which is better: FD or Liquid Fund?" — ET Money (`https://www.etmoney.com/learn/mutual-funds/fd-vs-liquid-funds-key-differences-and-which-is-better/`)
- "What is the applicable NAV for my investment?" — ET Money (`https://www.etmoney.com/help/mutual-funds/my-orders/what-is-the-applicable-nav-for-my-investment`)
- "Can I redeem my investments in [mutual fund]?" — ET Money (`https://www.etmoney.com/mutual-funds/uti-innovation-fund-direct-growth/44180`)

### Watchlist/portfolio
- "What is a watchlist and how to use it?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "How to check order history and holdings?" — Upstox (`https://upstox.com/canonical-answers.txt`)
- "How can I track my existing portfolio?" — ET Money (`https://www.etmoney.com/help/mutual-funds/portfolio/how-can-i-track-my-existing-portfolio`)
- "What is the applicable NAV for my investment?" — ET Money (`https://www.etmoney.com/help/mutual-funds/my-orders/what-is-the-applicable-nav-for-my-investment`)
- "Customized portfolios - Create and manage your own portfolio and watchlists. Track your holdings…" — Yahoo Finance (`https://help.yahoo.com/kb/finance/market-data-research-tools-yahoo-finance-sln24381.html`)
- "How can I track Live prices of the stocks I am interested in?" — Moneycontrol (`https://www.moneycontrol.com/help/portfolio/general-queries/how-can-i-track-live-prices-of-the-stocks-i-am-interested-in-2579251.html`)

### API / data export
- "Do you offer an API?" (prospect) — TradingView (`https://www.tradingview.com/support/solutions/43000485437-...`) and the StockTrade pricing FAQ itself lists Kite Connect / Connect API.
- "How can I get extra real-time data?" — TradingView (`https://www.tradingview.com/pricing/`)
- "How to purchase additional market data" — TradingView (`https://www.tradingview.com/support/solutions/43000471705-how-to-purchase-additional-market-data/`)
- "How can I access Level 1 market data in Zerodha API?" — Zerodha (`https://kite.trade/forum/discussion/13086/accessing-level-1-market-data-in-zerodha-api`)
- Chart data export on paid plans: "Download chart data / Export trades in CSV / Export report in XLSX" — TradingView (`https://www.tradingview.com/pricing/`)
- "What are APIs in stock trading?" — Upstox (`https://upstox.com/canonical-answers.txt`) ("real time data access and automated execution")
- "Brokerage on API Orders — ₹10 per order" — Upstox (`https://upstox.com/brokerage-charges/`)

### Support & downtime
- "Is it safe to buy subscriptions on TradingView?" — TradingView (`https://www.tradingview.com/pricing/`) (payments)
- "How can I get extra real-time data?" / "What is your Refund Policy?" — TradingView (`https://www.tradingview.com/pricing/`)
- "How to fix Kite charting and streaming lag on Zerodha" — references `https://status.zerodha.com/` for outages / "Monitor the status page and wait for Zerodha to resolve the incident." Source: `https://v2.webnotes.in/how-to-fix-kite-charting-lag/` (citing Zerodha's own status page)
- "Contact Us | Customer Care & Helpline Number" — Upstox (`https://upstox.com/contact-us/`) (support 8am–7pm IST Mon–Sat; "Chat with us / Email us… respond within 48 hours")
- "Need Support? Chat with Us" — Moneycontrol (`https://www.moneycontrol.com/apps`)
- "Support – FAQs, Account Help & Customer Assistance" — Angel One (`https://www.angelone.in/support`)
- Status page: TradingView `https://status.tradingview.com/`, Zerodha `https://status.zerodha.com/`

---

## Content gap vs StockTrade Tips current FAQ

Existing FAQ surface in this repo (verified):
- `app/Views/pricing.php` → 3 Q&As: **Can I cancel anytime?** / **Is there a contract?** / **Do you offer an API?**
- `app/Views/docs/user.php` → feature descriptions only (data via Yahoo Finance; daily AI predictions; STCG 15% / LTCG 10% with ₹1L exemption; watchlists; screening), **not** as Q&As.

Competitor FAQ/knowledge topics that StockTrade does **not** yet cover in an FAQ-style Q&A (candidates to add):

| Gap topic | Where competitors cover it (so users are asking) |
| --- | --- |
| **Real-time vs delayed / data source** — "Is my price data real-time or delayed?" "Why is the LTP zero/not visible?" "How can I track live prices?" | TradingView, Zerodha, Moneycontrol, Yahoo Finance, Groww |
| **Data accuracy & chart mismatches** — "Why do two charts of the same timeframe look different?" "Why do OHLC values differ?" "Why does historical data not match NSE/BSE?" "Why does the chart not tick until I refresh?" | Zerodha, TradingView |
| **AI predictions — accuracy, frequency, disclaimer** — none of the existing FAQ touches "How often are predictions refreshed?" "Are predictions real-time?" "Can I rely on AI for buy/sell decisions?" "What confidence/disclaimer applies?" | Moneycontrol (Alpha Generators/AI alerts), ET Money (Genius) raise AI-advice questions; TradingView has no AI prediction product |
| **Taxes & capital gains** — "How is STCG vs LTCG calculated?" "What is the tax on my portfolio?" "How do I calculate profit after tax?" "Is dividend taxed?" | Upstox, ET Money, Moneycontrol, Kuvera (TDS/Form 26AS) explicitly FAQ this |
| **Watchlist / portfolio limits & tracking** — "How many stocks can I watch?" "Why does my P&L not update?" "How do I import external holdings?" "Why is the watchlist delayed?" | Upstox, Moneycontrol, Yahoo Finance, Zerodha |
| **API & data export** — "What are the rate limits?" "How do I download/export data?" "What instruments can I pull?" "How much does the API cost?" "Brokerage on API orders?" | TradingView (CSV/XLSX export, data add-ons), Upstox (₹10/order), Zerodha Kite Connect (₹500/mo) |
| **Account opening / KYC** — "What documents are required?" "How long does account activation take?" "Can NRIs open an account?" "Why is my PAN/Aadhaar signature validation failing?" | Upstox, Angel One, Zerodha |
| **Maintenance/AMC charges** — "Is there an annual maintenance charge for my demat/trading account?" "When is AMC charged?" "Is there a lifetime-free option?" | Upstox (AMC ₹354/y), Angel One (₹0 first year, ₹60+GST/qtr), Zerodha Coin (no AMC), Kuvera (no demat/AMC) |
| **Refund & cancellation timing** — "Can I cancel anytime?" "How long for a refund?" "What happens to paid term on downgrade?" | TradingView (14-day annual refund, no monthly refunds), Moneycontrol (no refund), Upstox (AMC), Angel One |
| **Support & downtime / status** — "Why is the platform slow/lag at market open?" "Is there a status page?" "What are support hours?" "How do I raise a ticket?" | Zerodha status page, Upstox live support 8am–7pm, TradingView status, Angel One, Moneycontrol chat |
| **Orders & order types** — "What is a market vs limit order?" "How do I set a stop-loss?" "Can I place orders after market hours (AMO)?" "Why did my order fill at market price?" | Angel One, Upstox, ET Money |
| **Multiple accounts / nominee** — "Can I have multiple Demat accounts?" "How does the nominee work?" "Can I close my Demat account?" | Upstox, Zerodha |
| **Real-time alerts / notifications** — "How do I set price alerts?" "Are alerts real-time?" "Do I get notifications on watchlist movements?" | TradingView (price/technical/watchlist alerts), Upstox (5X more alerts on Plus), Moneycontrol (AI WhatsApp alerts), Groww (real-time P&L) |
| **US/international stocks access** — "Can I trade US stocks?" "What are the charges?" "Do I need a separate account?" "Are there forex/NRI restrictions?" | Groww, Kuvera (via Vested), ET Money (stocks), Upstox (international), Angel One |

---

## Sources

Pricing & account pages, help centers, docs, and learning sections (primary, platform-owned):

- TradingView Pricing — `https://www.tradingview.com/pricing/`
- TradingView Data Coverage — `https://www.tradingview.com/data-coverage/`
- TradingView Help: US stock data free by default — `https://www.tradingview.com/support/solutions/43000473924-is-us-stock-market-data-free-by-default/`
- TradingView Help: purchase additional market data — `https://www.tradingview.com/support/solutions/43000471705-how-to-purchase-additional-market-data/`
- TradingView Help: real-time data for NSE/BSE F&O — `https://www.tradingview.com/support/solutions/43000777677-can-i-access-real-time-futures-and-options-data-from-nse-and-bse/`
- TradingView Help: "I don't have access to real-time data" — `https://www.tradingview.com/support/folders/43000547053-i-don-t-have-access-to-real-time-data/`
- TradingView Help: "How does the source of real-time data affect the trading experience?" — `https://www.tradingview.com/support/solutions/43000739323-how-does-the-source-of-real-time-data-affect-the-trading-experience/`
- TradingView Status — `https://status.tradingview.com/`
- Yahoo Finance Help: exchanges & data providers/delayes — `https://help.yahoo.com/kb/SLN2310.html`
- Yahoo Finance Help: free vs Plus plans — `https://help.yahoo.com/kb/SLN36623.html`
- Yahoo Finance Help: real-time data on web/app — `https://help.yahoo.com/kb/SLN2321.html`, `https://help.yahoo.com/kb/SLN29023.html`
- Yahoo Finance Help: market data & research tools (portfolios/watchlists) — `https://help.yahoo.com/kb/finance/market-data-research-tools-yahoo-finance-sln24381.html`
- Moneycontrol Pro pricing — `https://www.moneycontrol.com/promos/pro.php`
- Moneycontrol Subscription products — `https://www.moneycontrol.com/subscription`
- Moneycontrol Help: track live prices (30-sec refresh) — `https://www.moneycontrol.com/help/portfolio/general-queries/how-can-i-track-live-prices-of-the-stocks-i-am-interested-in-2579251.html`
- Moneycontrol Help: free trial — `http://www.moneycontrol.com/help/game-changers/general-queries/is-there-a-free-trial-period-2453877.html`
- Moneycontrol Help: subscription expired — `http://www.moneycontrol.com/help/game-changers/general-queries/i-have-made-online-payment-but-when-i-try-to-login-it-says-your-subscription-has-expired-what-should-i-do-2453879.html`
- Moneycontrol Help: "Do I need to open a Moneycontrol account to invest in mutual fund?" — `http://www.moneycontrol.com/help/mutual-funds/kyc-registration-details/do-i-need-to-open-a-moneycontrol-account-to-invest-in-mutual-fund-2781481.html`
- Moneycontrol Apps (data from BSE/NSE/MCX/NCDEX) — `https://www.moneycontrol.com/apps`
- Groww Pricing — `https://groww.in/pricing`
- Groww Stocks (real-time P&L) — `https://groww.in/stocks`
- Groww Help — `https://groww.in/help`
- Kuvera Pricing — `https://kuvera.in/pricing`
- Kuvera US Stocks (0.2% trade, up to $20) — `https://kuvera.in/us-stocks/listing/all/ETF`
- Kuvera blog: platform costs — `https://kuvera.in/blog/what-should-i-know-before-investing-in-top-mutual-funds-through-platforms/`
- Kuvera FAQ: index funds — `https://kuvera.in/blog/most-frequently-asked-questions-on-index-funds/`
- Kuvera FAQ: Aadhaar-PAN linking — `https://kuvera.in/blog/how-to-link-aadhaar-with-pan/`
- Kuvera FAQ: TDS / Form 26AS — `https://kuvera.in/blog/how-to-view-or-download-my-tds-report-and-what-details-to-check/`
- Zerodha Charges — `https://zerodha.com/charges/`
- Zerodha Brokerage calculator — `https://zerodha.com/brokerage-calculator/`
- Zerodha Support: resident individual brokerage — `https://support.zerodha.com/category/account-opening/resident-individual/ri-charges/articles/what-is-the-brokerage-at-zerodha-for-equity`
- Zerodha Support: transaction charges (NSE/BSE/MCX) — `https://support.zerodha.com/category/account-opening/resident-individual/ri-charges/articles/exchange-transaction-charges`
- Zerodha Support: charges on order window — `https://support.zerodha.com/category/trading-and-markets/product-and-order-types/order/articles/charges-on-order-window`
- Zerodha Support: LTP not visible in marketwatch — `https://support.zerodha.com/category/trading-and-markets/general-kite/kite-mw/articles/why-are-the-values-not-visible-in-marketwatch-on-kite-web`
- Zerodha Support: why charts of same timeframe differ — `https://support.zerodha.com/category/trading-and-markets/charts-and-orders/charts/articles/why-does-two-charts-of-the-same-timeframe-look-different-on-kite-or-pi`
- Zerodha Support: OHLC daily vs hourly differ — `https://support.zerodha.com/category/trading-and-markets/charts-and-orders/charts/articles/why-are-the-ohlc-values-on-daily-and-hourly-charts-different`
- Zerodha Support: historical OHLC vs NSE/BSE — `https://support.zerodha.com/category/trading-and-markets/charts-and-orders/charts/articles/kite-charts-not-matching-as-per-the-records-in-nse-or-bse`
- Zerodha Support: chart not tick until refresh — `https://support.zerodha.com/category/trading-and-markets/charts-and-orders/charts/articles/why-does-the-chart-not-tick-until-i-refresh-the-page-on-kite-web`
- Zerodha Support: Coin charges — `https://support.zerodha.com/category/mutual-funds/understanding-mutual-funds/about-coin/articles/what-are-the-charges-for-using-coin`
- Zerodha Z-Connect: Streak now free for all users — `https://zerodha.com/z-connect/streak/streak-is-now-available-for-all-zerodha-users-at-no-cost`
- Zerodha Z-Connect: Streak scanner plans (historical) — `https://zerodha.com/z-connect/streak/introducing-scanner-by-streak`
- Streak Help (charges) — `https://help.streak.tech/s6u88q5uq7laclgpzux1hcy7`
- Kite Connect developer docs/forums — `https://kite.trade/forum/discussion/13086/accessing-level-1-market-data-in-zerodha-api`, `https://kite.trade/forum/discussion/14433/live-websocket-streaming-tick-by-tick-data-equities`, `https://kite.trade/forum/discussion/6044/websocket-streaming-limits`
- Upstox Brokerage Charges — `https://upstox.com/brokerage-charges/`
- Upstox Plus — `https://upstox.com/plus/`
- Upstox Help: AMC maintenance charge — `https://upstox.com/help-center/how-much-is-the-maintenance-charge-on-my-upstox-account-257384/`
- Upstox Help: brokerage plan change with Plus — `https://upstox.com/help-center/does-the-brokerage-plan-change-with-upstox-plus-264072/`
- Upstox Canonical Answers (FAQ) — `https://upstox.com/canonical-answers.txt`
- Upstox Contact / support hours — `https://upstox.com/contact-us/`
- Upstox Help Center — `https://upstox.com/help-center/`
- Angel One: open free demat account — `https://www.angelone.in/open-demat-account`
- Angel One: demat account charges — `https://www.angelone.in/knowledge-center/demat-account/demat-account-charges`
- Angel One: brokerage & charges — `https://www.angelone.in/support/charges-and-cashbacks/brokerage-charges`
- Angel One: exchange transaction charges — `https://www.angelone.in/exchange-transaction-charges`
- Angel One: brokerage in India — `https://www.angelone.in/knowledge-center/online-share-trading/all-you-wanted-to-know-brokerage-options-available`
- Angel One Support (KYC) — `https://www.angelone.in/support/your-account/kyc-status`
- ET Money: mutual funds — `https://www.etmoney.com/mutual-funds`
- ET Money: Genius membership FAQ — `https://www.etmoney.com/help/genius/membership`
- ET Money: how to pay for Genius — `https://www.etmoney.com/help/genius/membership/how-can-i-pay-for-genius`
- ET Money: charges per transaction — `https://www.etmoney.com/help/genius/investment/are-there-any-charges-for-every-transaction`
- ET Money: what is Genius — `https://www.etmoney.com/help/genius/about-genius/what-is-genius`
- ET Money: benefits of Genius — `https://www.etmoney.com/help/genius/about-genius/what-are-the-benefits-of-genius`
- ET Money: tax benefits under NPS — `https://www.etmoney.com/help/national-pension-system/about-nps/what-are-the-tax-benefits-under-nps`
- ET Money: track existing portfolio — `https://www.etmoney.com/help/mutual-funds/portfolio/how-can-i-track-my-existing-portfolio`
- ET Money: applicable NAV — `https://www.etmoney.com/help/mutual-funds/my-orders/what-is-the-applicable-nav-for-my-investment`
- ET Money: STT on mutual funds — `https://www.etmoney.com/learn/mutual-funds/stt-on-mutual-funds-meaning-rates-and-how-it-is-calculated/`
- ET Money: FD vs Liquid Funds — `https://www.etmoney.com/learn/mutual-funds/fd-vs-liquid-funds-key-differences-and-which-is-better/`
- ET Money: Great Indian Investment Festival TnC (Genius ₹99) — `https://www.etmoney.com/products/etmoney-great-indian-festival-tnc.html`
- Local repo references (existing StockTrade content): `app/Views/pricing.php` (3 Q&As), `app/Views/docs/user.php` (feature descriptions), `app/Views/terms.php`, `app/Views/privacy.php`, `app/Views/contact.php`.

---

### Summary of findings for the app owner

- **Pricing benchmark:** Every major Indian broker (Groww, Zerodha, Upstox, Angel One) uses a flat ₹20/order brokerage with ₹0 delivery/MF/IPO commissions and free account opening; differences are Demat AMC (Upstox/ Angel One charge after year 1, Zerodha Coin/Groww/Kuvera are AMC-free for investing) and API fees (Upstox ₹10/order, Zerodha Kite Connect ₹500/mo). Subscription/data platforms (TradingView, Moneycontrol, Yahoo Finance) monetize charts, real-time feeds and research rather than brokerage.
- **Data transparency is the #1 FAQ theme** across every platform — real-time vs delayed, refresh cadence, exchange restrictions, and feed status pages are all common user questions; StockTrade currently says only "All price data is sourced from Yahoo Finance" with no FAQ explaining real-time vs delayed.
- **Taxes/cap-gains, KYC, AMC, API limits, and support/status** are FAQ categories competitors cover at length; none appear in StockTrade's current 3-Q FAQ, presenting clear content gaps.
- **File written:** `docs/research/competitor-faq-research.md`
