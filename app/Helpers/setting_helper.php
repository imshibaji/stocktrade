<?php

if (!function_exists('site_name')) {
    function site_name(): string
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        try {
            $value = model(\App\Models\SettingModel::class)->getValue('site_name');
        } catch (\Throwable $e) {
            $value = null;
        }
        $cached = (is_string($value) && $value !== '') ? $value : 'StockTrade Tips';
        return $cached;
    }
}
