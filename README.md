# StockTrade — Stock Analysis & Prediction Platform

A CodeIgniter 4 web application for stock market analysis, AI-driven predictions, portfolio tracking, and investment management. Built with PHP 8.2+, Tailwind CSS v4, daisyUI 5, and Yahoo Finance data.

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Project Structure

| Path | Purpose |
|------|---------|
| `app/Controllers/` | Application controllers (e.g. `Pricing`, `Terms`, `Privacy`, `Docs`, `Faq`) |
| `app/Views/` | PHP views + Tailwind-rendered HTML templates (`templates/header.php`, `templates/footer.php`) |
| `app/Config/Routes.php` | Route definitions for guest pages and API endpoints |
| `src/tailwind.css` | Tailwind source with custom `sd-*` component classes |
| `public/css/style.css` | Compiled CSS (`npx @tailwindcss/cli -i src/tailwind.css -o public/css/style.css`) |
| `tests/` | PHPUnit feature tests (TDD red→green workflow) |
| `docs/research/` | Research artifacts (e.g. competitor FAQ research) |

## Key Features

- Guest pages: Pricing, Terms, Privacy, FAQ, User Docs, Developer Docs
- Stock detail page with consolidated summary (profile, snapshot, earnings, growth, institutional activity)
- Yahoo Finance data import & search autocomplete
- Theme switching (day / system / night) persisted in localStorage
- Responsive header with slide-over mobile nav, search, and theme controls
- TDD-tested: 48 tests / 246 assertions all green

## Usage Tracking & Contributions

### Tracking user and contributor activity

- **GitHub Traffic** — the repository's Traffic tab shows page views and unique visitors over time.
- **GitHub Insights → Contributors** — tracks commit count, additions, and deletions per contributor; the contribution graph shows activity frequency.
- **GitHub Actions** — CI runs (PHPUnit test suite) are logged per push/PR, giving a proxy for contributor engagement.
- **Git history** — `git log --oneline` provides a chronological record of all contributions; tags and release commits mark milestones.
- **Google Analytics / Plausible** (optional) — add a lightweight script to `templates/header.php` before `</head>` to track page views, session duration, and referrers in production.

### How contributors can participate

1. Fork the repository and create a feature branch.
2. Write tests first (TDD red), then implement (green), then refactor.
3. Run `php vendor/bin/phpunit` to verify all 48 tests still pass.
4. Open a pull request with a clear description of changes.

## Sponsorship

This is a long-term open project. If you find it useful and would like to support its continued development, please consider sponsoring:

- **GitHub Sponsors**: [Shibaji Debnath](https://github.com/sponsors/imshibaji) — recurring or one-time contributions via GitHub.
- **Buy Me a Coffee**: [shibajidebnath.com](https://www.shibajidebnath.com) — one-time or monthly support.

Sponsorship funds go toward ongoing maintenance, new features, and infrastructure costs. Even a small contribution helps keep the project alive and independent.

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
