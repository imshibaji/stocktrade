<?php

use CodeIgniter\Cache\Cache;

$instance = null;

function get_currency_cache(): Cache
{
    if ($instance === null) {
        $instance = new Cache();
    }
    return $instance;
}

function fetch_exchange_rate(string $from, string $to): ?float
{
    $from = strtoupper($from);
    $to = strtoupper($to);

    if ($from === $to) {
        return 1.0;
    }

    $cache = get_currency_cache();
    $cacheKey = "fx_rate_{$from}_{$to}";
    $cacheTtl = 86400;

    $cached = $cache->get($cacheKey);
    if ($cached !== null) {
        return (float) $cached;
    }

    $fallbackRates = get_fallback_exchange_rates();
    $directKey = "{$from}_{$to}";
    if (isset($fallbackRates[$directKey])) {
        $rate = $fallbackRates[$directKey];
        $cache->save($cacheKey, $rate, $cacheTtl);
        return $rate;
    }

    $inverseKey = "{$to}_{$from}";
    if (isset($fallbackRates[$inverseKey])) {
        $rate = round(1.0 / $fallbackRates[$inverseKey], 6);
        $cache->save($cacheKey, $rate, $cacheTtl);
        return $rate;
    }

    $client = service('curlrequest');
    $symbol = "{$from}{$to}=X";

    for ($attempt = 0; $attempt <= 1; $attempt++) {
        try {
            $response = $client->request('GET', "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}", [
                'query' => ['interval' => '1d', 'range' => '1d'],
                'headers' => ['User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'],
                'timeout' => 5,
            ]);

            $data = json_decode($response->getBody(), true);
            $rate = $data['chart']['result'][0]['meta']['regularMarketPrice'] ?? null;

            if ($rate !== null) {
                $cache->save($cacheKey, (float) $rate, $cacheTtl);
                return (float) $rate;
            }
        } catch (\Throwable $e) {
            if ($attempt === 0) {
                usleep(200000);
            }
        }
    }

    return null;
}

function get_fallback_exchange_rates(): array
{
    return [
        'INR_USD' => 0.012,
        'INR_EUR' => 0.011,
        'INR_GBP' => 0.0095,
        'INR_JPY' => 1.75,
        'INR_AUD' => 0.018,
        'INR_CAD' => 0.016,
        'INR_CHF' => 0.011,
        'INR_CNY' => 0.086,
        'INR_SGD' => 0.016,
        'USD_INR' => 83.0,
        'USD_EUR' => 0.92,
        'USD_GBP' => 0.79,
        'USD_JPY' => 145.0,
        'USD_AUD' => 1.50,
        'USD_CAD' => 1.34,
        'USD_CHF' => 0.92,
        'USD_CNY' => 7.20,
        'USD_SGD' => 1.34,
        'EUR_USD' => 1.09,
        'EUR_INR' => 92.0,
        'EUR_GBP' => 0.86,
        'EUR_JPY' => 158.0,
        'EUR_AUD' => 1.63,
        'EUR_CAD' => 1.46,
        'EUR_CHF' => 0.99,
        'EUR_CNY' => 7.80,
        'EUR_SGD' => 1.46,
        'GBP_USD' => 1.27,
        'GBP_INR' => 105.0,
        'GBP_EUR' => 1.16,
        'GBP_JPY' => 184.0,
        'GBP_AUD' => 1.90,
        'GBP_CAD' => 1.69,
        'GBP_CHF' => 1.15,
        'GBP_CNY' => 8.90,
        'GBP_SGD' => 1.69,
        'JPY_INR' => 0.57,
        'JPY_USD' => 0.0069,
        'JPY_EUR' => 0.0063,
        'JPY_GBP' => 0.0054,
        'JPY_AUD' => 0.0103,
        'JPY_CAD' => 0.0092,
        'JPY_CHF' => 0.0063,
        'JPY_CNY' => 0.049,
        'JPY_SGD' => 0.0092,
        'AUD_INR' => 56.0,
        'AUD_USD' => 0.67,
        'AUD_EUR' => 0.61,
        'AUD_GBP' => 0.53,
        'AUD_JPY' => 96.0,
        'AUD_CAD' => 0.89,
        'AUD_CHF' => 0.61,
        'AUD_CNY' => 4.80,
        'AUD_SGD' => 0.89,
        'CAD_INR' => 62.0,
        'CAD_USD' => 0.75,
        'CAD_EUR' => 0.68,
        'CAD_GBP' => 0.59,
        'CAD_JPY' => 108.0,
        'CAD_AUD' => 1.12,
        'CAD_CHF' => 0.68,
        'CAD_CNY' => 5.40,
        'CAD_SGD' => 0.89,
        'CHF_INR' => 92.0,
        'CHF_USD' => 1.09,
        'CHF_EUR' => 0.99,
        'CHF_GBP' => 0.87,
        'CHF_JPY' => 158.0,
        'CHF_AUD' => 1.63,
        'CHF_CAD' => 1.46,
        'CHF_CNY' => 7.80,
        'CHF_SGD' => 1.46,
        'CNY_INR' => 11.6,
        'CNY_USD' => 0.14,
        'CNY_EUR' => 0.13,
        'CNY_GBP' => 0.11,
        'CNY_JPY' => 20.8,
        'CNY_AUD' => 0.21,
        'CNY_CAD' => 0.19,
        'CNY_CHF' => 0.13,
        'CNY_SGD' => 0.19,
        'SGD_INR' => 62.0,
        'SGD_USD' => 0.75,
        'SGD_EUR' => 0.68,
        'SGD_GBP' => 0.59,
        'SGD_JPY' => 108.0,
        'SGD_AUD' => 1.12,
        'SGD_CAD' => 0.89,
        'SGD_CHF' => 0.68,
        'SGD_CNY' => 5.40,
    ];
}

function convert_to_base_currency(float $amount, string $fromCurrency): float
{
    $baseCurrency = get_user_base_currency();
    if ($fromCurrency === $baseCurrency) {
        return $amount;
    }
    $rate = fetch_exchange_rate($fromCurrency, $baseCurrency);
    if ($rate !== null) {
        return $amount * $rate;
    }
    return $amount;
}

function get_user_base_currency(): string
{
    $user = current_user();
    if ($user && isset($user['base_currency'])) {
        return strtoupper($user['base_currency']);
    }
    return 'INR';
}

function format_price_base(float $price, string $currency = ''): string
{
    $currency = $currency ?: get_user_base_currency();
    $symbols = [
        'INR' => '₹',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'JPY' => '¥',
        'AUD' => 'A$',
        'CAD' => 'C$',
        'CHF' => 'CHF ',
        'CNY' => '¥',
        'SGD' => 'S$',
    ];
    $symbol = $symbols[$currency] ?? ($currency . ' ');
    return $symbol . number_format($price, 2, '.', ',');
}

function format_price_dual(float $price, string $nativeCurrency, string $baseCurrency = ''): string
{
    $baseCurrency = $baseCurrency ?: get_user_base_currency();
    $native = format_price_base($price, $nativeCurrency);
    if ($nativeCurrency === $baseCurrency) {
        return $native;
    }
    $converted = format_price_base(convert_to_base_currency($price, $nativeCurrency), $baseCurrency);
    return "{$native} (~{$converted})";
}