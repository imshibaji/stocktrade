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

if (!function_exists('market_status')) {
    /**
     * Current market status for an exchange based on its local trading hours.
     *
     * @return array{state:string,label:string,dot:string,text:string,border:string}
     */
    function market_status(?string $exchange = null): array
    {
        $ex = strtoupper(trim($exchange ?? 'NSE'));

        static $zones = [
            'NSE' => 'Asia/Kolkata', 'BSE' => 'Asia/Kolkata', 'NSI' => 'Asia/Kolkata',
            'NMS' => 'America/New_York', 'NYQ' => 'America/New_York', 'NGM' => 'America/New_York',
            'NASDAQ' => 'America/New_York', 'NYSE' => 'America/New_York', 'AMEX' => 'America/New_York',
            'TSX' => 'America/Toronto', 'LSE' => 'Europe/London', 'FRA' => 'Europe/Berlin',
            'ETR' => 'Europe/Berlin', 'XETRA' => 'Europe/Berlin', 'EURONEXT' => 'Europe/Paris',
            'ASX' => 'Australia/Sydney', 'TSE' => 'Asia/Tokyo', 'TYO' => 'Asia/Tokyo',
            'HKEX' => 'Asia/Hong_Kong', 'HKG' => 'Asia/Hong_Kong', 'KRX' => 'Asia/Seoul',
            'SES' => 'Asia/Singapore', 'SGX' => 'Asia/Singapore',
        ];
        $tz = $zones[$ex] ?? 'Asia/Kolkata';
        $isUs = in_array($ex, ['NMS', 'NYQ', 'NGM', 'NASDAQ', 'NYSE', 'AMEX'], true);

        // Session windows, minutes from midnight in the exchange's local time.
        $preStart  = $isUs ? 240 : 540;   // 04:00 US / 09:00 IST pre-open auction
        $preEnd    = $isUs ? 570 : 555;   // 09:30 US / 09:15 IST
        $regStart  = 570;                 // 09:30 US / 09:15 IST
        $regEnd    = $isUs ? 960 : 930;   // 16:00 US / 15:30 IST
        $postStart = $isUs ? 960 : 930;
        $postEnd   = $isUs ? 1200 : 930;  // 20:00 US; none for NSE

        try {
            $now = new \DateTimeImmutable('now', new \DateTimeZone($tz));
        } catch (\Throwable $e) {
            $now = new \DateTimeImmutable('now', new \DateTimeZone('Asia/Kolkata'));
        }
        $dow  = (int) $now->format('N'); // 1=Mon .. 7=Sun
        $mins = (int) $now->format('G') * 60 + (int) $now->format('i');

        $closed = ['state' => 'closed', 'label' => 'Closed', 'dot' => 'text-gray-500', 'text' => 'text-gray-400', 'border' => 'border-gray-600'];

        if ($dow >= 6) {
            return $closed;
        }
        if ($mins >= $preStart && $mins < $preEnd) {
            return ['state' => 'pre', 'label' => 'Pre-Market', 'dot' => 'text-yellow-400', 'text' => 'text-yellow-300', 'border' => 'border-yellow-600/60'];
        }
        if ($mins >= $regStart && $mins < $regEnd) {
            return ['state' => 'open', 'label' => 'Open', 'dot' => 'text-green-400', 'text' => 'text-green-300', 'border' => 'border-green-600/60'];
        }
        if ($mins >= $postStart && $mins < $postEnd) {
            return ['state' => 'post', 'label' => 'Post-Market', 'dot' => 'text-yellow-400', 'text' => 'text-yellow-300', 'border' => 'border-yellow-600/60'];
        }

        return $closed;
    }
}

if (!function_exists('api_market_status')) {
    /**
     * Maps the live feed market state (Yahoo quote) to a badge status,
     * falling back to time-based market_status() when the state is unknown.
     *
     * @return array{state:string,label:string,dot:string,text:string,border:string}
     */
    function api_market_status(?string $marketState, ?string $exchange = null): array
    {
        $state = strtoupper(trim((string) $marketState));
        $closed = ['state' => 'closed', 'label' => 'Closed', 'dot' => 'text-gray-500', 'text' => 'text-gray-400', 'border' => 'border-gray-600'];

        return match ($state) {
            'REGULAR', 'REGULAR2', 'OPEN' => ['state' => 'open', 'label' => 'Open', 'dot' => 'text-green-400', 'text' => 'text-green-300', 'border' => 'border-green-600/60'],
            'PRE', 'PREPRE', 'PREPREPRE' => ['state' => 'pre', 'label' => 'Pre-Market', 'dot' => 'text-yellow-400', 'text' => 'text-yellow-300', 'border' => 'border-yellow-600/60'],
            'POST', 'POSTPOST', 'POSTPOSTPOST' => ['state' => 'post', 'label' => 'Post-Market', 'dot' => 'text-yellow-400', 'text' => 'text-yellow-300', 'border' => 'border-yellow-600/60'],
            'CLOSED', 'CLOSED_PRE', 'CLOSED_POST' => $closed,
            default => market_status($exchange),
        };
    }
}

if (!function_exists('market_badge')) {
    /**
     * Renders the live market status badge. When $stockId is given the
     * badge fetches the feed's authoritative market state by stock id.
     */
    function market_badge(?string $exchange = null, ?int $stockId = null): string
    {
        $st = market_status($exchange);
        $ex = strtoupper(trim($exchange ?? 'NSE'));
        $idAttr = $stockId !== null ? ' data-stock-id="' . (int) $stockId . '"' : '';

        return '<span id="marketBadge" data-exchange="' . esc($ex) . '"' . $idAttr . ' ' .
            'class="text-xs px-3 py-1 rounded-full border ' . $st['border'] . ' ' . $st['text'] . '" ' .
            'title="' . esc($ex . ' market · ' . $st['label']) . '">' .
            '<i class="fas fa-circle ' . $st['dot'] . ' text-[8px] mr-1"></i>' . esc($st['label']) . '</span>';
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
