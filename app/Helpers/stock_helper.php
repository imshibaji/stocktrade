<?php

if (!function_exists('stock_currency')) {
    function stock_currency(?string $exchange): string
    {
        $ex = strtoupper(trim($exchange ?? 'NSE'));
        static $map = [
            'NSE' => 'INR', 'BSE' => 'INR', 'NSI' => 'INR',
            'LSE' => 'GBP', 'TSE' => 'JPY', 'HKEX' => 'HKD',
            'KRX' => 'KRW', 'TSX' => 'CAD', 'ASX' => 'AUD',
            'SWX' => 'CHF', 'FRA' => 'EUR', 'ETR' => 'EUR',
            'Euronext' => 'EUR', 'MEX' => 'MXN', 'BVMF' => 'BRL',
            'NMS' => 'USD', 'NYQ' => 'USD', 'NGM' => 'USD',
        ];
        return $map[$ex] ?? 'USD';
    }
}

if (!function_exists('exchange_display')) {
    function exchange_display(?string $stored, ?string $api = null): string
    {
        if ($api) return $api;
        return match ($stored) {
            'NSE' => 'NSI',
            'BSE' => 'BSE',
            default => (string) ($stored ?? ''),
        };
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(string $currency): string
    {
        $symbols = [
            'INR' => '₹', 'USD' => '$', 'EUR' => '€',
            'GBP' => '£', 'JPY' => '¥', 'AUD' => 'A$',
            'CAD' => 'C$', 'CHF' => 'CHF', 'CNY' => '¥', 'SGD' => 'S$',
            'HKD' => 'HK$', 'KRW' => '₩', 'MXN' => 'Mex$', 'BRL' => 'R$',
            'NZD' => 'NZ$', 'ZAR' => 'R', 'SEK' => 'kr', 'NOK' => 'kr',
            'DKK' => 'kr', 'PLN' => 'zł', 'CZK' => 'Kč', 'HUF' => 'Ft',
            'RUB' => '₽', 'TRY' => '₺', 'ILS' => '₪', 'THB' => '฿',
            'MYR' => 'RM', 'IDR' => 'Rp', 'PHP' => '₱', 'TWD' => 'NT$',
            'VND' => '₫', 'AED' => 'د.إ', 'SAR' => '﷼', 'QAR' => 'QR',
            'KWD' => 'KD', 'OMR' => 'ر.ع.', 'BHD' => '.د.ب',
        ];
        return $symbols[$currency] ?? ($currency . ' ');
    }
}

if (!function_exists('format_price')) {
    function format_price($price, string $currency = 'INR'): string
    {
        $symbols = [
            'INR' => '₹', 'USD' => '$', 'EUR' => '€',
            'GBP' => '£', 'JPY' => '¥', 'AUD' => 'A$',
            'CAD' => 'C$', 'CHF' => 'CHF ', 'CNY' => '¥', 'SGD' => 'S$',
            'HKD' => 'HK$', 'KRW' => '₩', 'MXN' => 'Mex$', 'BRL' => 'R$',
            'NZD' => 'NZ$', 'ZAR' => 'R', 'SEK' => 'kr', 'NOK' => 'kr',
            'DKK' => 'kr', 'PLN' => 'zł', 'CZK' => 'Kč', 'HUF' => 'Ft',
            'RUB' => '₽', 'TRY' => '₺', 'ILS' => '₪', 'THB' => '฿',
            'MYR' => 'RM', 'IDR' => 'Rp', 'PHP' => '₱', 'TWD' => 'NT$',
            'VND' => '₫', 'AED' => 'د.إ', 'SAR' => '﷼', 'QAR' => 'QR',
            'KWD' => 'KD', 'OMR' => 'ر.ع.', 'BHD' => '.د.ب',
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

if (!function_exists('sparkline_points')) {
    function sparkline_points(array $values, int $width = 100, int $height = 28): string
    {
        if (empty($values)) {
            return '';
        }
        $count = count($values);
        $min = min($values);
        $max = max($values);
        $range = ($max - $min) ?: 1;
        $points = [];
        foreach ($values as $i => $v) {
            $x = ($count === 1) ? 0 : ($i / ($count - 1)) * $width;
            $y = $height - (($v - $min) / $range) * $height;
            $points[] = round($x, 1) . ',' . round($y, 1);
        }
        return implode(' ', $points);
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
                'description' => 'Short Term Capital Gains Tax (held < 1 year)',
            ],
            'ltcg' => [
                'rate' => $ltcgRate . '%',
                'rate_val' => $ltcgRate / 100,
                'description' => 'Long Term Capital Gains Tax (held > 1 year). Exemption up to ₹1,00,000.',
            ],
        ];
    }
}

if (!function_exists('get_fee_rates')) {
    function get_fee_rates(?array $user = null): array
    {
        $user = $user ?? (current_user() ?: []);
        $defaults = [
            'brokerage_pct' => 0.05,
            'stt_pct'       => 0.01,
            'exchange_pct'  => 0.003,
            'gst_pct'       => 18,
            'stamp_duty_pct'=> 0.005,
            'sebi_fees'     => 0.0001,
        ];
        $rates = [];
        foreach ($defaults as $key => $default) {
            $value = $user[$key] ?? null;
            $rates[$key] = (is_numeric($value) && (float) $value != 0) ? (float) $value : $default;
        }
        return $rates;
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
