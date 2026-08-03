# StockTrade — Business Plan

**Version:** 1.0
**Date:** August 2026
**Status:** Open-source MVP → SaaS conversion

---

## 1. Executive Summary

StockTrade is a CodeIgniter 4 web application that provides stock market analysis tools — screeners, watchlist analysis, AI-driven predictions, portfolio tracking, and detailed stock summaries — powered by Yahoo Finance data. It is **not** a trading platform; it is an analysis and research tool.

The application is currently open-source with 48 passing tests (246 assertions), a TDD workflow, and a functional MVP featuring stock search, screening, watchlists, predictions, portfolio tracking, and theme switching. The product targets Indian retail investors who need analysis tools but cannot afford expensive platforms like TradingView (₹995–₹17,333/mo) or Moneycontrol PRO (₹1499/mo).

**The core business thesis:** Convert the open-source MVP into a freemium SaaS by adding a user account system with subscription tiers, a curated data pipeline, and community features — monetizing power users and teams while keeping a generous free tier to drive acquisition.

**Revenue target:** ₹4.8L ARR by Month 12, ₹14.4L ARR by Month 24, scaling to ₹50L+ ARR by Month 36.

---

## 2. Value Proposition

### For Individual Investors
- **Free stock screening** with 30+ technical indicators and fundamental filters — no other free Indian tool offers this depth
- **AI-driven price predictions** with confidence scores — differentiator vs. TradingView (which has no AI predictions)
- **Portfolio tracking with Indian tax calculations** (STCG 15%, LTCG 10% above ₹1L) — unique to the Indian market
- **Watchlist analysis** with bucket organization and prediction overlays
- **Yahoo Finance data** with autocomplete search — no API key required for basic data

### For Teams & Advisory Firms
- **Shared screeners** with public/private lists
- **Bulk data import/export** via API
- **Custom reports** with tax-aware P&L calculations
- **SLA-backed uptime** on Enterprise tier

### Competitive Moat
| Capability | StockTrade | TradingView (Free) | Yahoo Finance | Moneycontrol Free |
|---|---|---|---|---|
| Technical screener (30+ indicators) | ✅ | ❌ (3 indicators) | ❌ | ❌ |
| AI price predictions | ✅ | ❌ | ❌ | ❌ (Alpha Generators are human-curated) |
| Indian tax-aware portfolio tracking | ✅ | ❌ | ❌ | ❌ |
| Watchlist buckets & analysis | ✅ | ❌ (basic watchlist) | ✅ (basic) | ✅ (basic) |
| Custom screener save/share | ✅ | ❌ | ❌ | ❌ |
| Free tier | ✅ | ✅ (limited) | ✅ | ✅ (delayed data) |
| Real-time NSE data | ❌ (delayed via Yahoo) | Paid add-on | Delayed for India | 30-sec refresh (free) |

**Key differentiator:** No other free tool combines Indian tax-aware portfolio tracking, 30+ technical indicator screening, and AI predictions in a single open-source application.

---

## 3. Target Audience & User Personas

### Primary Personas

**Persona 1: "Ravi" — The Retail Investor**
- Age: 25–40, India
- Invests ₹5K–₹50K/month in Indian equities
- Uses Moneycontrol or Yahoo Finance for tracking
- Pain point: No free tool that screens stocks with technical indicators AND tracks his portfolio with Indian taxes
- Acquisition channel: Organic search (SEO), Reddit r/IndianStockMarket, YouTube
- Willing to pay: ₹0–₹499/month

**Persona 2: "Priya" — The Active Trader**
- Age: 30–45, India
- Trades intraday/swing, needs real-time analysis
- Currently uses TradingView (paid) or Zerodha Kite
- Pain point: TradingView is expensive for what she needs; she wants AI predictions and tax tracking in one place
- Acquisition channel: Twitter/X, trading communities, affiliate partnerships
- Willing to pay: ₹499–₹1999/month

**Persona 3: "Arjun" — The Financial Advisor**
- Age: 35–50, India
- Manages 20–100 client portfolios
- Pain point: Needs bulk data, API access, and client-facing reports
- Acquisition channel: LinkedIn, financial advisor communities, direct sales
- Willing to pay: ₹499–₹9999/month (Enterprise)

**Persona 4: "Developer/Contributor" — The Open-Source Enthusiast**
- Builds on the codebase, contributes features
- Pain point: Wants a well-documented, testable codebase to extend
- Acquisition channel: GitHub, CodeIgniter forums, HackerNews
- Monetization: Indirect (community growth → brand awareness → user conversions)

### Secondary Personas
- **NRI investors** wanting INR-based portfolio tracking with USD holdings
- **Students** learning stock analysis (free tier users who may convert later)
- **Hobby investors** who want a clean, ad-free alternative to Moneycontrol

---

## 4. User Acquisition Strategy

### Phase 1: Organic Growth (Months 1–6)
**Goal:** 1,000 registered users, 100 paying users

| Channel | Tactic | Expected Impact |
|---|---|---|
| **SEO / Content Marketing** | Publish 10+ blog posts targeting long-tail Indian stock analysis queries (e.g., "best stocks under ₹500 with high RSI", "how to screen stocks by market cap and PE ratio India") | 500–2,000 organic visits/month by Month 6 |
| **GitHub Open Source** | Maintain active repo, encourage PRs, tag releases, write contributor docs | 500–2,000 GitHub stars, 50–200 forks |
| **Reddit & Forums** | Post in r/IndianStockMarket, r/IndiaInvestments, CodeIgniter forum with genuine value (not spam) | 20–50 signups/month |
| **Twitter/X** | Daily stock analysis threads using StockTrade, share screening results | 200–500 followers, 10–30 signups/month |
| **Product Hunt** | Launch with a compelling "free alternative to TradingView for Indian stocks" angle | 500–2,000 visitors on launch day |
| **Google My Business** | List as a free stock analysis tool | Long-tail search visibility |

**Actionable steps:**
1. Write and publish 2 blog posts/week on stock analysis topics
2. Each blog post must include a CTA linking to StockTrade
3. Optimize all pages for SEO: meta descriptions, structured data, semantic HTML
4. Create a `/blog` route in CodeIgniter 4 with a Blog controller and model
5. Add Open Graph tags to all public pages for social sharing
6. Create a `docs/` section with a "Getting Started" guide that doubles as SEO content

### Phase 2: Community & Partnerships (Months 3–9)
**Goal:** 5,000 registered users, 500 paying users

| Channel | Tactic | Expected Impact |
|---|---|---|
| **YouTube Tutorials** | Create 5–10 screencast videos: "How to screen NSE stocks with StockTrade", "Building your first watchlist", "Understanding AI predictions" | 1,000–5,000 views/video, 50–200 signups/video |
| **Affiliate Program** | Offer 20% recurring commission to finance bloggers and YouTubers who refer paying users | 10–50 affiliate-driven conversions/month |
| **WhatsApp Community** | Create a StockTrade WhatsApp group for users to share screening results and watchlists | High engagement, viral loops within Indian investor community |
| **Partnerships with Broker Apps** | Partner with Groww, Kuvera, or small brokers for co-marketing (they refer users, you provide analysis tools) | 50–200 signups/month |
| **Guest Posts** | Write for Indian finance publications (Economic Times, Moneycontrol blog, Groww blog) | 200–1,000 referral visits per post |
| **Email Newsletter** | Weekly "Stock of the Week" with screening results and predictions | 30–50% open rate, drives repeat visits |

### Phase 3: Paid Acquisition (Months 6–18)
**Goal:** 20,000 registered users, 2,000 paying users

| Channel | Budget | Expected Impact |
|---|---|---|
| **Google Ads** | ₹5,000–₹15,000/month targeting "stock screener India", "free stock analysis tool", "stock prediction India" | 500–2,000 visits/month, 5–15% conversion to signup |
| **Twitter/X Ads** | ₹3,000–₹8,000/month targeting Indian finance interests | 200–800 visits/month |
| **Reddit Ads** | ₹2,000–₹5,000/month targeting r/IndianStockMarket | 100–400 visits/month |
| **Influencer Sponsorships** | ₹5,000–₹20,000 per finance YouTuber (micro-influencers, 10K–100K subscribers) | 50–300 signups per sponsorship |

**CAC targets:**
- Organic: ₹0–₹50 per user
- Paid: ₹50–₹150 per user
- Target CAC payback: <3 months (Pro user = ₹499/month, so CAC must be <₹1,500)

### Phase 4: Viral & Network Effects (Months 9–24)
**Goal:** 50,000 registered users, 5,000 paying users

| Mechanism | Details |
|---|---|
| **Public Screener Lists** | Users share their saved screeners publicly; each public list is a SEO landing page and a viral loop |
| **Watchlist Sharing** | Users can share watchlist links (like TradingView's public watchlists) |
| **Referral Program** | "Invite a friend, get 1 month Pro free" — both referrer and referee get a free month |
| **Leaderboards** | Top screeners, most accurate predictions, most active users — gamification drives engagement |
| **API for Developers** | Free tier API access attracts developers who build integrations and drive traffic back |

---

## 5. Monetization Strategy

### Freemium SaaS Model

| Tier | Price | Target | Key Features |
|---|---|---|---|
| **Free** | ₹0/month | New users, casual investors | Up to 10 watchlist stocks, basic screening (5 indicators), 30-day AI predictions, 1 saved screener list, email support |
| **Pro** | ₹499/month (₹399/mo annual) | Active retail investors, traders | Unlimited watchlist, all indicators, 90-day predictions, unlimited saved screeners, tax-aware portfolio tracking, price alerts, priority support, export CSV |
| **Pro+** | ₹999/month (₹799/mo annual) | Active traders, small teams | All Pro features + real-time data refresh (15-sec), custom dashboard widgets, advanced predictions (30-day confidence intervals), API access (100 req/day), team sharing (3 users) |
| **Enterprise** | Custom (₹5,000–₹50,000/month) | Advisory firms, algo traders, fintech | Dedicated data feed, unlimited API, custom endpoints, SLA (99.9% uptime), onboarding, white-label option, bulk data export, dedicated support |

### Pricing Psychology
- **₹499/month** is positioned as "less than a Netflix subscription" — low friction for Indian users
- **Annual billing** at 20% discount incentivizes commitment and reduces churn
- **14-day free trial** on Pro and Pro+ (no credit card required) — reduces conversion friction
- **Free tier** is genuinely useful (not a crippled demo) — users get value before paying

### Additional Revenue Streams

| Stream | Description | Expected Revenue (% of total) |
|---|---|---|
| **Subscription** | Monthly/annual Pro, Pro+, Enterprise | 70–80% |
| **Data API Pay-per-Use** | Beyond included API limits, charge per 1,000 requests | 5–10% |
| **Custom Reports** | One-time or recurring custom report generation for Enterprise | 5–10% |
| **Affiliate Commissions** | Brokerage partner referrals (Zerodha, Groww, Upstox) — earn commission on referred account openings | 5–10% |
| **Sponsored Screeners** | Allow brokers/fintechs to sponsor public screener lists with branded results | 2–5% |
| **Donations/Sponsorships** | GitHub Sponsors, Buy Me a Coffee — keep as supplementary | <2% |

### Conversion Funnel Targets

| Stage | Metric | Month 6 | Month 12 | Month 24 |
|---|---|---|---|---|
| Registered Users | Total | 1,000 | 5,000 | 20,000 |
| Free → Pro Conversion | % of free users | 3% | 5% | 7% |
| Pro → Pro+ Conversion | % of Pro users | 10% | 15% | 20% |
| Pro → Enterprise | % of Pro users | 2% | 3% | 5% |
| Monthly Churn | % of paying users | 8% | 5% | 3% |
| ARPU | Weighted avg revenue/user | ₹150 | ₹250 | ₹350 |

---

## 6. Product Roadmap

### MVP (Current — Already Shipped)
- ✅ Stock search with Yahoo Finance autocomplete
- ✅ Stock detail pages (profile, snapshot, earnings, growth, institutional)
- ✅ Basic stock screener (fundamental filters)
- ✅ Watchlist with buckets
- ✅ AI-driven predictions (30-day)
- ✅ Portfolio tracking with Indian tax calculations
- ✅ Theme switching (day/system/night)
- ✅ Responsive design with Tailwind CSS v4 + daisyUI 5
- ✅ TDD test suite (48 tests, 246 assertions)
- ✅ Public pages (Pricing, Terms, Privacy, FAQ, Docs)

### Phase 2: Core SaaS Features (Months 1–4)

| Priority | Feature | Effort | Impact |
|---|---|---|---|
| P0 | User account system with subscription management (Stripe integration) | High | Critical — enables monetization |
| P0 | Role-based access control (Free/Pro/Pro+/Enterprise) | Medium | Critical — gates features |
| P0 | Payment processing (Stripe India — supports UPI, cards, net banking) | Medium | Critical — revenue |
| P1 | Subscription billing dashboard (manage plans, invoices, payment methods) | Medium | High — reduces support load |
| P1 | Email notifications (price alerts, prediction updates, billing reminders) | Medium | High — retention |
| P1 | Saved screener lists (user-specific, shareable) | Low | High — engagement |
| P2 | API rate limiting per tier | Medium | Medium — protects infrastructure |
| P2 | Public screener/list sharing with SEO-friendly URLs | Low | High — viral growth |

### Phase 3: Growth Features (Months 4–8)

| Priority | Feature | Effort | Impact |
|---|---|---|---|
| P0 | Real-time data refresh (15-sec intervals for Pro+) via WebSocket or polling | High | Critical differentiator |
| P0 | Advanced technical indicators (50+ indicators in screener) | High | High — deepens analysis |
| P1 | Prediction accuracy tracking (show historical accuracy per stock) | Medium | High — builds trust |
| P1 | Custom dashboard with drag-and-drop widgets | Medium | High — Pro+ differentiator |
| P1 | Watchlist sharing (public links) | Low | High — viral loop |
| P2 | Multi-watchlist comparison | Medium | Medium — power user feature |
| P2 | Stock news feed integration | Medium | Medium — engagement |
| P2 | Dividend tracking and yield analysis | Low | Medium — Indian investor need |

### Phase 4: Scale & Ecosystem (Months 8–18)

| Priority | Feature | Effort | Impact |
|---|---|---|---|
| P0 | Public API for developers (free tier + paid tiers) | High | High — ecosystem growth |
| P0 | Mobile-responsive PWA (Progressive Web App) | Medium | High — mobile users |
| P1 | Broker integration (read-only portfolio import from Zerodha/Groww) | High | High — reduces friction |
| P1 | AI recommendation engine (personalized stock suggestions) | High | High — AI differentiator |
| P1 | White-label Enterprise option | Medium | High — B2B revenue |
| P2 | Community features (user profiles, public watchlists, leaderboards) | Medium | Medium — engagement |
| P2 | Multi-currency support (USD, EUR for NRI users) | Low | Medium — market expansion |
| P2 | Mobile app (React Native or Flutter) | High | High — but defer until web is stable |

---

## 7. Competitive Analysis

### Direct Competitors

| Competitor | Pricing | Strengths | Weaknesses | StockTrade Advantage |
|---|---|---|---|---|
| **TradingView** | Free / ₹995–₹17,333/mo | Best charting, largest community, real-time data | Expensive for Indian users, no Indian tax tracking, no AI predictions | Indian tax-aware portfolio, AI predictions, free tier is genuinely useful |
| **Yahoo Finance** | Free (US/Canada paid tiers) | Free data, widely trusted, 100+ exchanges | No screener, no portfolio tracking with Indian taxes, delayed India data | Integrated screener + portfolio + predictions in one tool |
| **Moneycontrol** | Free / ₹89–₹1,499/mo | India-focused, large user base, real-time data | Ad-heavy, PRO features expensive, no AI predictions | Clean UI, AI predictions, open-source transparency |
| **Groww** | Free (brokerage-based) | Zero brokerage, easy onboarding | Trading platform, not analysis tool, limited screening | Analysis-first, not trading — complementary not competing |
| **Zerodha (Streak)** | Free (Streak now free) | Broker integration, algo trading | Requires Zerodha account, not a pure analysis tool | Standalone analysis tool, works with any broker |

### Indirect Competitors
- **Google Finance** — free, but no screener or portfolio tracking
- **Screener.in** — Indian fundamental data, but no technical analysis or predictions
- **Trendlyne** — Indian analysis tool, but paid-only (₹999/mo) with no free tier

### Competitive Positioning Statement
> "StockTrade is the only free, open-source stock analysis platform built specifically for Indian investors — combining 30+ technical indicator screening, AI-driven predictions, and Indian tax-aware portfolio tracking in one place."

### Competitive Moats to Build
1. **Indian tax-aware portfolio tracking** — no competitor does this well
2. **AI predictions with confidence scores** — differentiator vs. TradingView
3. **Open-source transparency** — users trust the code; community contributions
4. **Public screener ecosystem** — shared screeners create network effects
5. **Indian market specialization** — NSE/BSE, INR, Indian taxes, Indian broker integration

---

## 8. Marketing & Growth Strategy

### Brand Positioning
- **Tagline:** "Free stock analysis for Indian investors"
- **Voice:** Data-driven, trustworthy, accessible, non-preachy
- **Visual Identity:** Clean, dark-mode-friendly, accent color (already branded as "accent" in Tailwind config)
- **Content Strategy:** Educational > promotional — 80% educational content, 20% product promotion

### Content Calendar (Monthly)

| Week | Content Type | Topic Examples |
|---|---|---|
| 1 | Blog post (SEO) | "How to screen NSE stocks with high RSI and low debt" |
| 2 | Video tutorial (YouTube) | "Building your first watchlist with StockTrade" |
| 3 | Blog post (SEO) | "Understanding AI stock predictions: accuracy and limitations" |
| 4 | Social media thread | Weekly stock screening results, user stories, feature highlights |

### SEO Strategy
- Target 50+ long-tail keywords in Indian stock analysis
- Each blog post targets a specific keyword cluster
- Technical SEO: fast page loads (Caching headers, CDN), mobile-first, structured data (Article, FAQ, Product schema)
- Build backlinks through guest posts on Indian finance sites
- Create a `/blog` route with category pages (e.g., /blog/screening, /blog/predictions, /blog/portfolio)

### Email Marketing
- **Welcome sequence** (5 emails): Welcome → How to screen → How to use watchlists → AI predictions explained → Upgrade offer
- **Weekly digest**: "Stock of the Week" with screening results and predictions
- **Re-engagement**: 30-day inactivity → "We miss you" with new feature highlights
- **Billing reminders**: 3 days before renewal, 1 day before, after cancellation

### Partnerships
1. **Broker partnerships**: Integrate with Zerodha, Groww, Upstox for read-only portfolio import
2. **Finance influencer program**: Provide free Pro accounts to 20 finance YouTubers/bloggers in exchange for mentions
3. **Educational institutions**: Offer free Pro accounts to finance courses/colleges
4. **CodeIgniter community**: Sponsor CodeIgniter forums/events, contribute to the framework ecosystem

---

## 9. Retention & Engagement Strategies

### Onboarding Flow
1. **Sign up** (email + password, or Google OAuth)
2. **Guided tour** (3-step interactive walkthrough: Search → Screen → Watchlist)
3. **First screening** (pre-built screener template: "Top 10 Large-Cap Stocks")
4. **First watchlist** (suggest 5 popular stocks based on user's sector interest)
5. **First prediction** (show AI prediction for a watchlist stock)
6. **Portfolio setup** (prompt to add first investment)

### Engagement Hooks
| Mechanism | Frequency | Purpose |
|---|---|---|
| Daily stock recommendation email | Daily | Drives daily visits |
| Weekly screener results digest | Weekly | Shows value of screening |
| Prediction accuracy report | Monthly | Builds trust in AI |
| Portfolio performance summary | Weekly | Reinforces portfolio tracking value |
| New feature announcements | Bi-weekly | Keeps users curious |
| Community highlights | Monthly | Builds belonging |

### Retention Metrics & Targets
| Metric | Month 3 | Month 6 | Month 12 | Month 24 |
|---|---|---|---|---|
| DAU/MAU ratio | 15% | 20% | 25% | 30% |
| 30-day retention | 40% | 45% | 50% | 55% |
| 90-day retention | 25% | 30% | 35% | 40% |
| Paying user retention | 70% | 75% | 80% | 85% |

### Churn Reduction Tactics
1. **Exit survey** on cancellation — understand why users leave
2. **Downgrade option** — don't force cancellation, offer Pro → Free downgrade
3. **Usage-based nudges** — if user hasn't logged in for 14 days, send "Your watchlist needs attention" email
4. **Feature gating** — free users see limited predictions; Pro users see full predictions — creates upgrade incentive
5. **Annual billing discount** — lock-in users with 20% annual discount
6. **Community belonging** — WhatsApp group, public lists, leaderboards make leaving socially costly

---

## 10. Revenue Projections & Milestones

### Revenue Model Assumptions
- **Free users**: 0 revenue, but drive SEO and word-of-mouth
- **Pro conversion rate**: 3% (Month 6) → 5% (Month 12) → 7% (Month 24)
- **Pro+ conversion rate**: 10% of Pro users (Month 12) → 15% (Month 24)
- **Enterprise conversion rate**: 2% of Pro users (Month 12) → 5% (Month 24)
- **Average monthly churn**: 8% (Month 6) → 5% (Month 12) → 3% (Month 24)
- **Average revenue per user (ARPU)**: ₹150 (weighted, including free) → ₹250 → ₹350

### Revenue Projections

| Metric | Month 6 | Month 12 | Month 18 | Month 24 | Month 36 |
|---|---|---|---|---|---|
| Registered Users | 1,000 | 5,000 | 12,000 | 20,000 | 50,000 |
| Free Users | 970 | 4,250 | 9,600 | 16,400 | 38,500 |
| Pro Users | 25 | 250 | 700 | 1,400 | 3,500 |
| Pro+ Users | 2 | 38 | 120 | 280 | 700 |
| Enterprise Users | 0 | 5 | 20 | 50 | 150 |
| **Monthly Revenue** | **₹12,500** | **₹1,25,000** | **₹3,50,000** | **₹7,00,000** | **₹18,00,000** |
| **ARR** | **₹1.5L** | **₹15L** | **₹42L** | **₹84L** | **₹2.16Cr** |
| **Gross Margin** | 60% | 70% | 75% | 80% | 85% |
| **Net Revenue (after hosting, Stripe fees)** | ₹7,500/mo | ₹87,500/mo | ₹2.6L/mo | ₹5.6L/mo | ₹15.3L/mo |

### Key Milestones

| Milestone | Target Date | Success Metric |
|---|---|---|
| MVP launched (open source) | ✅ Done | 48 tests passing |
| User accounts + Stripe integration | Month 2 | First 10 paying users |
| 100 registered users | Month 3 | 100 signups |
| 1,000 registered users | Month 6 | 1,000 signups |
| First ₹1L/month revenue | Month 9 | ₹1L MRR |
| 5,000 registered users | Month 12 | 5,000 signups |
| ₹15L ARR | Month 12 | ₹15L annual revenue |
| 20,000 registered users | Month 18 | 20,000 signups |
| ₹84L ARR | Month 24 | ₹84L annual revenue |
| 50,000 registered users | Month 30 | 50,000 signups |
| ₹2.16Cr ARR | Month 36 | ₹2.16Cr annual revenue |
| Mobile app launch | Month 20 | 5,000 mobile downloads |
| Break-even | Month 10 | Revenue > costs |

### Cost Projections

| Cost Category | Month 1–6 | Month 7–12 | Month 13–24 | Month 25–36 |
|---|---|---|---|---|
| Hosting (VPS/Cloud) | ₹2,000/mo | ₹5,000/mo | ₹15,000/mo | ₹40,000/mo |
| Stripe fees (2.5% + ₹3) | ₹500/mo | ₹3,000/mo | ₹15,000/mo | ₹45,000/mo |
| Domain + SSL | ₹500/mo | ₹500/mo | ₹500/mo | ₹500/mo |
| Email (SendGrid/Mailgun) | ₹0–500/mo | ₹500/mo | ₹2,000/mo | ₹5,000/mo |
| CDN (Cloudflare) | ₹0 | ₹0 | ₹500/mo | ₹1,000/mo |
| Marketing (paid ads) | ₹0 | ₹10,000/mo | ₹25,000/mo | ₹50,000/mo |
| Development (if hiring) | ₹0 | ₹20,000/mo | ₹50,000/mo | ₹1,00,000/mo |
| **Total Monthly Costs** | **₹3,000–5,000** | **₹42,000** | **₹1,07,000** | **₹2,11,500** |
| **Monthly Profit** | **₹4,500–7,500** | **₹45,500** | **₹1,53,000** | **₹3,48,500** |

---

## 11. Technical Infrastructure for SaaS Scaling

### Current Architecture (MVP)
```
User → Browser → Nginx → PHP 8.2 (CodeIgniter 4) → MySQL
                                        ↓
                                  Yahoo Finance API (on-demand)
                                        ↓
                                  Redis (caching, sessions)
```

### Scaling Architecture (SaaS Phase)

```
User → CDN (Cloudflare) → Load Balancer → PHP 8.2 (CodeIgniter 4) → MySQL (Primary)
                                                                          ↓
                                                                    MySQL (Replica)
                                                                          ↓
                                                                    Redis (Cache)
                                                                          ↓
                                                                    Queue Workers ( predictions, data refresh)
                                                                          ↓
                                                                    External APIs (Yahoo Finance, Alpha Vantage)
```

### Infrastructure Components

| Component | Month 1–6 | Month 7–12 | Month 13–24 | Month 25+ |
|---|---|---|---|---|
| **Web Server** | Single VPS (2GB RAM, 2 CPU) | Single VPS (4GB RAM, 4 CPU) | Load-balanced (2x 4GB VPS) | Auto-scaling group |
| **Database** | MySQL on same VPS | MySQL on separate instance (2GB) | MySQL Primary + 1 Replica | Managed MySQL (AWS RDS/Aurora) |
| **Cache** | File-based (CI4 default) | Redis (1GB) | Redis cluster (3 nodes) | Managed Redis (ElastiCache) |
| **Queue/Jobs** | None | Database queue | Redis queue + Horizon | Managed queue (SQS/Beanstalk) |
| **CDN** | Cloudflare Free | Cloudflare Pro (₹200/mo) | Cloudflare Pro | Cloudflare Business |
| **Storage** | Local disk | Local disk + S3 backup | S3 for exports/reports | S3 + Glacier for archives |
| **Monitoring** | None | UptimeRobot (free) | Datadog/New Relic (free tier) | Full observability stack |
| **CI/CD** | Manual deploy | GitHub Actions (basic) | GitHub Actions (staging + prod) | Full pipeline with canary deploys |

### Key Technical Decisions for SaaS

1. **Multi-tenancy**: Use row-level security (user_id on all tables) rather than separate databases. This keeps the architecture simple while supporting unlimited tenants.

2. **Rate limiting**: Implement per-tier API rate limits using Redis:
   - Free: 10 req/min
   - Pro: 60 req/min
   - Pro+: 300 req/min
   - Enterprise: Unlimited (with fair-use policy)

3. **Caching strategy**:
   - Stock data: Cache for 60 seconds (Free), 15 seconds (Pro+), real-time (Enterprise)
   - Predictions: Cache for 1 hour, regenerate on demand for Pro+
   - Search results: Cache for 5 minutes
   - Use Redis as the cache store, with file-based fallback

4. **Background jobs**: Move Yahoo Finance data fetching and prediction generation to queue workers. This prevents slow API calls from blocking web requests.

5. **Database optimization**:
   - Add indexes on: `stocks.symbol`, `watchlist.user_id`, `watchlist.stock_id`, `investments.user_id`, `predictions.stock_id`, `predictions.predicted_date`
   - Partition `predictions` table by month for faster queries
   - Use connection pooling (PDO persistent connections)

6. **Security for SaaS**:
   - Implement CSRF protection (CodeIgniter 4 has this built-in)
   - Rate limiting on authentication endpoints
   - Encrypt sensitive user data at rest (API keys, payment info)
   - Use HTTPS everywhere (Cloudflare SSL)
   - Regular dependency audits (`composer audit`, `npm audit`)
   - GDPR/data retention compliance for Indian users (DPDP Act 2023)

7. **Deployment**:
   - Use GitHub Actions for automated testing and deployment
   - Staging environment for all changes
   - Blue-green deployments for zero-downtime releases
   - Database migrations in CI pipeline

### Scaling Benchmarks

| Users | Monthly Active | Requests/Day | Infrastructure |
|---|---|---|---|
| 1,000 | 300 | ~5,000 | Single VPS (2GB) |
| 5,000 | 1,500 | ~25,000 | Single VPS (4GB) + Redis |
| 20,000 | 6,000 | ~100,000 | 2x VPS + MySQL replica + Redis |
| 50,000 | 15,000 | ~500,000 | Auto-scaling + managed DB + queue |
| 100,000 | 30,000 | ~1,000,000 | Load-balanced cluster + managed services |

---

## 12. Risk Analysis & Mitigation

| Risk | Probability | Impact | Mitigation |
|---|---|---|---|
| **Yahoo Finance API changes/deprecation** | Medium | High | Build abstraction layer; add fallback data sources (Alpha Vantage, FRED); cache aggressively |
| **Low free-to-paid conversion** | Medium | High | Improve onboarding; add more free-tier value; A/B test pricing page |
| **High churn in first 3 months** | Medium | High | Better onboarding; email engagement; feature education |
| **Competition from TradingView adding AI predictions** | Medium | Medium | Focus on Indian market specialization (taxes, NSE/BSE, INR) — TradingView is generic |
| **Yahoo Finance data delays for Indian markets** | High | Medium | This is inherent to Yahoo Finance; communicate data freshness clearly; Pro+ gets 15-sec refresh |
| **Scaling costs exceed revenue** | Low | High | Monitor unit economics closely; set scaling triggers (e.g., upgrade infra only when CPU > 70% for 7 days) |
| **Regulatory risk (SEBI)** | Low | Medium | StockTrade is an analysis tool, not a trading platform; add clear disclaimers; consult legal if predictions are marketed as investment advice |
| **Open-source competitors** | Medium | Low | Open-source is a moat (community contributions, trust); SaaS features (tax tracking, AI predictions) are hard to replicate in a fork |
| **Stripe/Payment gateway issues in India** | Low | Medium | Support multiple payment methods (UPI, cards, net banking); have a backup processor (Razorpay) |

---

## 13. Success Metrics & KPIs

### North Star Metric
**Monthly Active Users (MAU) who perform at least one screening or portfolio action**

### Key Metrics

| Category | Metric | Target (Month 12) | Target (Month 24) |
|---|---|---|---|
| **Acquisition** | Registered users | 5,000 | 20,000 |
| **Activation** | % of users who complete onboarding | 60% | 70% |
| **Engagement** | MAU / Registered users | 30% | 35% |
| **Engagement** | Avg sessions per user per week | 3 | 5 |
| **Retention** | 30-day retention | 50% | 55% |
| **Revenue** | MRR | ₹1,25,000 | ₹7,00,000 |
| **Revenue** | ARPU | ₹250 | ₹350 |
| **Revenue** | Net Revenue Retention (NRR) | 110% | 115% |
| **Efficiency** | CAC | ₹100 | ₹75 |
| **Efficiency** | LTV / CAC ratio | 5x | 7x |
| **Product** | % of users with saved screeners | 20% | 35% |
| **Product** | % of users with active watchlist | 40% | 55% |

### Reporting Cadence
- **Daily**: Revenue, new signups, churn
- **Weekly**: Active users, feature usage, conversion rates
- **Monthly**: Full KPI review, cohort analysis, LTV/CAC calculation
- **Quarterly**: Strategic review, roadmap adjustment, pricing experiment results

---

## 14. Immediate Next Steps (Next 30 Days)

1. **Implement user accounts with Stripe subscription management** — this is the single highest-impact action. Without it, the product remains open-source with no direct revenue.
2. **Add a blog route** (`/blog`) with 3 initial posts targeting Indian stock analysis SEO keywords.
3. **Set up GitHub Actions CI/CD** for automated testing and deployment.
4. **Create a Product Hunt launch page** and plan the launch for Month 3.
5. **Write 2 blog posts/week** on stock analysis topics.
6. **Set up Google Analytics / Plausible** for tracking user behavior.
7. **Create a WhatsApp community** for early adopters.
8. **Reach out to 10 finance YouTubers/bloggers** for early access and potential partnerships.
9. **Add SEO meta tags** and structured data to all public pages.
10. **Set up monitoring** (UptimeRobot, error tracking with Sentry free tier).

---

## Appendix A: File Structure for New Features

```
app/
├── Controllers/
│   ├── Billing.php          # Stripe webhooks, subscription management
│   ├── Blog.php             # Blog controller
│   ├── Api/
│   │   └── V1/              # Versioned API controllers
│   └── ... (existing)
├── Models/
│   ├── Subscription.php     # Subscription management model
│   ├── Invoice.php          # Invoice model
│   ├── BlogPost.php         # Blog post model
│   └── ... (existing)
├── Views/
│   ├── blog/                # Blog views
│   ├── billing/             # Billing portal views
│   ├── auth/                # Already exists
│   └── ... (existing)
├── Libraries/
│   ├── StripeService.php    # Stripe integration
│   ├── RateLimiter.php      # API rate limiting
│   └── ... (existing)
└── Config/
    ├── Routes.php           # Add billing, blog, API routes
    └── Services.php         # Add Stripe service config
```

## Appendix B: Stripe Integration (India)

- **Stripe India** supports: UPI, cards, net banking, wallets (Paytm, PhonePe)
- **Stripe Checkout** for hosted payment pages (lowest integration effort)
- **Stripe Billing** for subscription management (proration, upgrades/downgrades, dunning)
- **Webhooks** for subscription events (customer.subscription.created, updated, deleted)
- **Tax** — Stripe Tax can auto-calculate Indian GST if needed, but StockTrade handles tax calculations separately for portfolio tracking

## Appendix C: Recommended Tech Stack Additions

| Purpose | Tool | Cost |
|---|---|---|
| Payment processing | Stripe India | 2.5% + ₹3 per transaction |
| Email delivery | Resend / Mailgun | Free tier (3,000 emails/mo) |
| Analytics | Plausible / PostHog | Free tier |
| Error tracking | Sentry | Free tier (5,000 events/mo) |
| CDN | Cloudflare | Free / Pro (₹200/mo) |
| Monitoring | UptimeRobot | Free tier |
| SEO | Ahrefs / Ubersuggest | Free tier / ₹999/mo |
| Social scheduling | Buffer / Hootsuite | Free tier |
| Documentation | GitBook / Docusaurus | Free (open source) |
| Community | Discord / WhatsApp | Free |

---

*This business plan is a living document. Review and update quarterly based on actual data and market conditions.*
