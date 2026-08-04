<?php

if (!function_exists('prediction_methods')) {
    /**
     * Metadata for the supported forecast methods.
     *
     * @return array<string, array{label: string, description: string, chip: string}>
     */
    function prediction_methods(): array
    {
        return [
            'linear_regression' => [
                'label'       => 'Linear Regression',
                'description' => 'Extrapolates the recent price trend into a straight line.',
                'chip'        => 'bg-sky-900/40 text-sky-300 border-sky-700',
            ],
            'ema_crossover' => [
                'label'       => 'EMA Crossover',
                'description' => 'Tracks fast EMA crossing above or below the slow EMA.',
                'chip'        => 'bg-violet-900/40 text-violet-300 border-violet-700',
            ],
            'mean_reversion' => [
                'label'       => 'Mean Reversion',
                'description' => 'Expects price to snap back toward its moving average.',
                'chip'        => 'bg-amber-900/40 text-amber-300 border-amber-700',
            ],
            'rsi_reversion' => [
                'label'       => 'RSI Reversion',
                'description' => 'Fades overbought / oversold RSI extremes back to 50.',
                'chip'        => 'bg-rose-900/40 text-rose-300 border-rose-700',
            ],
            'bollinger_bounce' => [
                'label'       => 'Bollinger Bounce',
                'description' => 'Predicts a reversal off the Bollinger Band edges.',
                'chip'        => 'bg-teal-900/40 text-teal-300 border-teal-700',
            ],
            'macd_divergence' => [
                'label'       => 'MACD Divergence',
                'description' => 'Reads divergence between price and MACD momentum.',
                'chip'        => 'bg-fuchsia-900/40 text-fuchsia-300 border-fuchsia-700',
            ],
            'vwap_reversion' => [
                'label'       => 'VWAP Reversion',
                'description' => 'Fades deviation from the volume-weighted average price.',
                'chip'        => 'bg-cyan-900/40 text-cyan-300 border-cyan-700',
            ],
            'supertrend_follow' => [
                'label'       => 'Supertrend Follow',
                'description' => 'Follows the Supertrend direction while it persists.',
                'chip'        => 'bg-orange-900/40 text-orange-300 border-orange-700',
            ],
        ];
    }
}

if (!function_exists('prediction_method_label')) {
    function prediction_method_label(?string $method): string
    {
        $methods = prediction_methods();
        return $methods[$method]['label'] ?? ucfirst(str_replace('_', ' ', (string) $method));
    }
}

if (!function_exists('prediction_method_chip')) {
    function prediction_method_chip(?string $method): string
    {
        $methods = prediction_methods();
        return $methods[$method]['chip'] ?? 'bg-page border border-gray-600 text-gray-300';
    }
}

if (!function_exists('prediction_status_meta')) {
    /**
     * @return array{class: string, label: string}
     */
    function prediction_status_meta(?string $status): array
    {
        return match ($status) {
            'completed' => ['class' => 'bg-green-900/40 text-green-300 border-green-700', 'label' => 'Completed'],
            'running'   => ['class' => 'bg-amber-900/40 text-amber-300 border-amber-700', 'label' => 'Running'],
            default     => ['class' => 'bg-gray-800 text-gray-300 border-gray-600', 'label' => 'Pending'],
        };
    }
}

if (!function_exists('prediction_signal_meta')) {
    /**
     * @return array{class: string, icon: string, label: string}
     */
    function prediction_signal_meta(?string $signal): array
    {
        return match (strtoupper((string) $signal)) {
            'BULLISH' => ['class' => 'bg-green-900/40 text-green-300 border-green-700', 'icon' => 'fa-arrow-trend-up', 'label' => 'Bullish'],
            'BEARISH' => ['class' => 'bg-red-900/40 text-red-300 border-red-700', 'icon' => 'fa-arrow-trend-down', 'label' => 'Bearish'],
            default   => ['class' => 'bg-gray-800 text-gray-300 border-gray-600', 'icon' => 'fa-minus', 'label' => 'Neutral'],
        };
    }
}

if (!function_exists('prediction_criteria_to_query_text')) {
    /**
     * Convert compiled filter arrays (criteria + technical_criteria) back into
     * a human-readable query string, e.g. "rsi < 30 AND close > sma(50)".
     *
     * @param array|string|null $criteria
     * @param array|string|null $technicalCriteria
     */
    function prediction_criteria_to_query_text($criteria, $technicalCriteria = null): string
    {
        $filters = [];
        foreach ([$criteria, $technicalCriteria] as $raw) {
            if (is_string($raw) && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $filters = array_merge($filters, $decoded);
                }
            } elseif (is_array($raw)) {
                $filters = array_merge($filters, $raw);
            }
        }

        $parts = [];
        foreach ($filters as $f) {
            if (!is_array($f)) {
                continue;
            }

            $field = (string) ($f['field'] ?? '');
            $op = (string) ($f['op'] ?? '');
            $isTech = !empty($f['is_technical']);
            $indicator = (string) ($f['indicator'] ?? '');
            $period = (int) ($f['period'] ?? 0);
            $value = $f['value'] ?? '';

            if ($isTech && $indicator !== '' && $period > 0) {
                $field = $indicator . '(' . $period . ')';
            }

            if (!empty($f['is_indicator_ref']) && is_string($value)) {
                $value = $value . '(' . (int) ($f['indicator_period'] ?? 14) . ')';
            } elseif (is_string($value) && preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $value) && empty($f['is_string'])) {
                $value = $value;
            }

            if ($field !== '' && $op !== '') {
                $parts[] = $field . ' ' . $op . ' ' . (is_string($value) ? $value : (string) $value);
            }
        }

        return implode(' AND ', $parts);
    }
}

