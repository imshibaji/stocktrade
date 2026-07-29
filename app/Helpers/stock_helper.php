<?php

if (!function_exists('format_price')) {
    function format_price($price): string
    {
        return '&#8377;' . number_format((float) $price, 2);
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
        $now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $dayOfWeek = (int) $now->format('N');
        $hour = (int) $now->format('G');
        $minute = (int) $now->format('i');
        $currentMinutes = $hour * 60 + $minute;

        if ($dayOfWeek >= 6) {
            return ['open' => false, 'label' => 'Market Closed (Weekend)', 'color' => 'gray'];
        }

        if ($currentMinutes < 555 || $currentMinutes >= 930) {
            return ['open' => false, 'label' => 'Market Closed', 'color' => 'gray'];
        }

        return ['open' => true, 'label' => 'Market Open (NSE/BSE)', 'color' => 'green'];
    }
}
