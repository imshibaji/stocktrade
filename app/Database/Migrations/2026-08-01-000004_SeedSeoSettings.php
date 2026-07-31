<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedSeoSettings extends Migration
{
    public function up()
    {
        $settings = [
            ['seo_meta_title', 'StockTrade Tips - Smart Stock Trading Tips, Predictions & P&L Tracker', 'text', 'Home Page SEO Title'],
            ['seo_meta_description', 'Analyze stocks, get 30-day AI price predictions, track your investments, and calculate net profit/loss after taxes - all in one place.', 'textarea', 'Meta Description'],
            ['seo_meta_keywords', 'stock trading tips, stock predictions, stock analysis, NSE, BSE, stock market India, P&L calculator, watchlist, stock screener', 'textarea', 'Meta Keywords'],
            ['seo_og_title', 'StockTrade Tips - Smart Stock Trading Tips, Predictions & P&L Tracker', 'text', 'Open Graph Title'],
            ['seo_og_description', 'Analyze stocks, get 30-day AI price predictions, track your investments, and calculate net profit/loss after taxes.', 'textarea', 'Open Graph Description'],
            ['seo_og_image', '', 'text', 'Open Graph Image URL'],
            ['seo_canonical_url', '', 'text', 'Canonical URL (leave empty to auto-use current page URL)'],
            ['seo_robots', 'index, follow', 'text', 'Robots Meta'],
            ['seo_author', '', 'text', 'Author'],
        ];

        $group = 'seo';
        $rows = [];
        foreach ($settings as [$key, $value, $type, $label]) {
            $rows[] = [
                'key'           => $key,
                'value'         => $value,
                'type'          => $type,
                'setting_group' => $group,
                'label'         => $label,
            ];
        }

        $db = $this->db;
        $existing = $db->table('settings')->where('setting_group', $group)->get()->getResultArray();
        $existingKeys = array_column($existing, 'key');
        $rows = array_values(array_filter($rows, static fn($row) => !in_array($row['key'], $existingKeys, true)));

        if (!empty($rows)) {
            $db->table('settings')->insertBatch($rows);
        }
    }

    public function down()
    {
        $this->db->table('settings')->where('setting_group', 'seo')->delete();
    }
}
