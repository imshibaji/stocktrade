<?php

if (!function_exists('stock_currency')) {
    function stock_currency(?string $exchange): string
    {
        return in_array($exchange ?? 'NSE', ['NSE', 'BSE']) ? 'INR' : 'USD';
    }
}

if (!function_exists('format_price')) {
    function format_price($price, string $currency = 'INR'): string
    {
        $symbols = [
            'INR' => '&#8377;', 'USD' => '&#36;', 'EUR' => '&#8364;',
            'GBP' => '&#163;', 'JPY' => '&#165;', 'AUD' => 'A&#36;',
            'CAD' => 'C&#36;', 'CHF' => 'CHF ', 'CNY' => '&#165;', 'SGD' => 'S&#36;',
        ];
        $symbol = $symbols[$currency] ?? ($currency . ' ');
        return $symbol . number_format((float) $price, 2);
    }
}

if (!function_exists('format_large_number')) {
    function format_large_number($number): string
    {
        if ($number >= 10000000000000) {
            return round($number / 10000000000000, 2) . ' Lakh Cr';
        }
        if ($number >= 10000000) {
            return round($number / 10000000, 2) . ' Cr';
        }
        if ($number >= 100000) {
            return round($number / 100000, 2) . ' L';
        }
        return number_format($number);
    }
}

if (!function_exists('get_price_change')) {
    function get_price_change(float $current, float $previous): array
    {
        $change = $current - $previous;
        $percent = $previous > 0 ? ($change / $previous) * 100 : 0;
        return ['change' => round($change, 2), 'percent' => round($percent, 2)];
    }
}

if (!function_exists('tax_bracket_info')) {
    function tax_bracket_info(?array $user = null): array
    {
        $stcgRate = 15.0;
        $ltcgRate = 10.0;
        if ($user) {
            $stcgRate = (float) ($user['stcg_rate'] ?? $stcgRate);
            $ltcgRate = (float) ($user['ltcg_rate'] ?? $ltcgRate);
        }
        return [
            'stcg' => [
                'rate' => $stcgRate . '%',
                'rate_val' => $stcgRate / 100,
                'description' => 'Short Term Capital Gains Tax (held &lt; 1 year)',
            ],
            'ltcg' => [
                'rate' => $ltcgRate . '%',
                'rate_val' => $ltcgRate / 100,
                'description' => 'Long Term Capital Gains Tax (held &gt; 1 year). Exemption up to &#8377;1,00,000.',
            ],
        ];
    }
}

if (!function_exists('calc_transaction_fees')) {
    function calc_transaction_fees(float $amount, array $rates): array
    {
        $brokerage = $amount * ((float) ($rates['brokerage_pct'] ?? 0) / 100);
        $stt = $amount * ((float) ($rates['stt_pct'] ?? 0) / 100);
        $exchange = $amount * ((float) ($rates['exchange_pct'] ?? 0) / 100);
        $gstBase = $brokerage + $exchange;
        $gst = $gstBase * ((float) ($rates['gst_pct'] ?? 18) / 100);
        $stampDuty = $amount * ((float) ($rates['stamp_duty_pct'] ?? 0) / 100);
        $sebi = $amount * ((float) ($rates['sebi_fees'] ?? 0) / 100);
        $total = $brokerage + $stt + $exchange + $gst + $stampDuty + $sebi;
        return [
            'brokerage' => round($brokerage, 2),
            'stt' => round($stt, 2),
            'exchange' => round($exchange, 2),
            'gst' => round($gst, 2),
            'stamp_duty' => round($stampDuty, 2),
            'sebi' => round($sebi, 2),
            'total' => round($total, 2),
        ];
    }
}

if (!function_exists('format_mcap')) {
    function format_mcap(float $mcap): string
    {
        if ($mcap >= 10000000000000) {
            return round($mcap / 10000000000000, 2) . 'L Cr';
        }
        if ($mcap >= 10000000) {
            return round($mcap / 10000000, 2) . ' Cr';
        }
        if ($mcap >= 100000) {
            return round($mcap / 100000, 2) . ' L';
        }
        return number_format($mcap);
    }
}

if (!function_exists('format_volume')) {
    function format_volume(float $vol): string
    {
        if ($vol >= 10000000) {
            return round($vol / 10000000, 2) . ' Cr';
        }
        if ($vol >= 100000) {
            return round($vol / 100000, 2) . ' L';
        }
        return number_format($vol);
    }
}

if (!function_exists('market_status')) {
    function market_status(): array
    {
        static $cached = null;
        if ($cached !== null) return $cached;

        $now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $dayOfWeek = (int) $now->format('N');
        $hour = (int) $now->format('G');
        $minute = (int) $now->format('i');
        $currentMinutes = $hour * 60 + $minute;

        if ($dayOfWeek >= 6) {
            $cached = ['open' => false, 'label' => 'Market Closed (Weekend)', 'color' => 'gray'];
            return $cached;
        }

        if ($currentMinutes < 555 || $currentMinutes >= 930) {
            $cached = ['open' => false, 'label' => 'Market Closed', 'color' => 'gray'];
            return $cached;
        }

        $cached = ['open' => true, 'label' => 'Market Open (NSE/BSE)', 'color' => 'green'];
        return $cached;
    }
}

if (!function_exists('generate_price_history')) {
    function generate_price_history(int $stockId, float $basePrice): void
    {
        $db = \Config\Database::connect();
        $prices = [];
        for ($i = 90; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $volatility = $basePrice * 0.03;
            $change = (mt_rand(-1000, 1000) / 1000) * $volatility;
            $close = round($basePrice + $change, 2);
            $open = round($close - (mt_rand(-500, 500) / 1000) * $volatility, 2);
            $high = round(max($open, $close) + abs(mt_rand(0, 500) / 1000) * $volatility, 2);
            $low = round(min($open, $close) - abs(mt_rand(0, 500) / 1000) * $volatility, 2);
            $volume = mt_rand(100000, 50000000);
            $prices[] = [
                'stock_id'   => $stockId,
                'price_date' => $date,
                'open'       => $open,
                'high'       => $high,
                'low'        => $low,
                'close'      => $close,
                'volume'     => $volume,
            ];
            $basePrice = $close;
        }
        $db->table('stock_prices')->insertBatch($prices);
    }
}

if (!function_exists('generate_predictions')) {
    function generate_predictions(int $stockId, float $basePrice): void
    {
        $db = \Config\Database::connect();
        $predictions = [];
        for ($i = 1; $i <= 30; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} days"));
            $trend = (mt_rand(-100, 100) / 10000) * $basePrice;
            $predictedPrice = round($basePrice + ($trend * $i), 2);
            $confidence = round(max(60, min(95, 95 - ($i * 0.5))), 2);
            $predictions[] = [
                'stock_id'        => $stockId,
                'predicted_date'  => $date,
                'predicted_price' => $predictedPrice,
                'confidence_score'=> $confidence,
                'method'          => 'Monte Carlo + EMA',
                'created_at'      => date('Y-m-d H:i:s'),
            ];
        }
        $db->table('predictions')->insertBatch($predictions);
    }
}
