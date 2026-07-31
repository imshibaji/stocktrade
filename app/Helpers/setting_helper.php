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

if (!function_exists('home_setting')) {
    function home_setting(string $key, string $default = ''): string
    {
        static $cache = null;
        if ($cache === null) {
            $cache = [];
            try {
                $rows = model(\App\Models\SettingModel::class)
                    ->where('setting_group', 'home_page')
                    ->findAll();
                foreach ($rows as $row) {
                    $cache[$row['key']] = $row['value'];
                }
            } catch (\Throwable $e) {
                $cache = [];
            }
        }
        $value = $cache[$key] ?? null;
        return (is_string($value) && $value !== '') ? $value : $default;
    }
}

if (!function_exists('seo_setting')) {
    function seo_setting(string $key, string $default = ''): string
    {
        static $cache = null;
        if ($cache === null) {
            $cache = [];
            try {
                $rows = model(\App\Models\SettingModel::class)
                    ->where('setting_group', 'seo')
                    ->findAll();
                foreach ($rows as $row) {
                    $cache[$row['key']] = $row['value'];
                }
            } catch (\Throwable $e) {
                $cache = [];
            }
        }
        $value = $cache[$key] ?? null;
        return (is_string($value) && $value !== '') ? $value : $default;
    }
}
