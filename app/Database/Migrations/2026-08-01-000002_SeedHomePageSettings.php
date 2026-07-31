<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedHomePageSettings extends Migration
{
    public function up()
    {
        $settings = [
            ['home_hero_title', 'Smart Stock Trading Tips', 'text', 'Home Page Hero Title'],
            ['home_hero_title_highlight', 'Trading Tips', 'text', 'Home Page Hero Highlight Word'],
            ['home_hero_subtitle', 'Analyze stocks, get future predictions, track your investments, and calculate net profit/loss after taxes — all in one place.', 'textarea', 'Home Page Hero Subtitle'],
            ['home_hero_cta_primary', 'Get Started Free', 'text', 'Hero Primary Button (Guest)'],
            ['home_hero_cta_secondary', 'Learn More', 'text', 'Hero Secondary Button (Guest)'],
            ['home_hero_cta_primary_logged_in', 'Go to Dashboard', 'text', 'Hero Primary Button (Logged In)'],
            ['home_hero_cta_secondary_logged_in', 'Browse Stocks', 'text', 'Hero Secondary Button (Logged In)'],
            ['home_feature_1_title', 'Stock Analysis', 'text', 'Feature 1 Title'],
            ['home_feature_1_desc', 'Deep analysis with historical price trends, key metrics, and sector comparisons.', 'textarea', 'Feature 1 Description'],
            ['home_feature_2_title', 'Future Predictions', 'text', 'Feature 2 Title'],
            ['home_feature_2_desc', 'AI-powered 30-day price predictions with confidence scores for every stock.', 'textarea', 'Feature 2 Description'],
            ['home_feature_3_title', 'P&L Calculator', 'text', 'Feature 3 Title'],
            ['home_feature_3_desc', 'Calculate gross profit/loss and net returns after STCG/LTCG tax deductions.', 'textarea', 'Feature 3 Description'],
            ['home_feature_4_title', 'Watchlist', 'text', 'Feature 4 Title'],
            ['home_feature_4_desc', 'Save your favorite stocks and track them daily with real-time analysis.', 'textarea', 'Feature 4 Description'],
            ['home_topstocks_title', 'Top Stocks by Market Cap', 'text', 'Top Stocks Section Title'],
            ['home_how_title', 'How It Works', 'text', 'How It Works Section Title'],
            ['home_how_1_title', 'Build Watchlist', 'text', 'How It Works Step 1 Title'],
            ['home_how_1_desc', 'Add stocks to your watchlist for quick access and daily monitoring.', 'textarea', 'How It Works Step 1 Description'],
            ['home_how_2_title', 'Analyze & Invest', 'text', 'How It Works Step 2 Title'],
            ['home_how_2_desc', 'View detailed analysis, predictions, and record your investments with amounts.', 'textarea', 'How It Works Step 2 Description'],
            ['home_how_3_title', 'Track Profits', 'text', 'How It Works Step 3 Title'],
            ['home_how_3_desc', 'Track gross profit/loss and net returns after calculating STCG/LTCG taxes.', 'textarea', 'How It Works Step 3 Description'],
            ['home_cta_title', 'Ready to Start Trading Smarter?', 'text', 'CTA Banner Title'],
            ['home_cta_subtitle', 'Join now and get access to stock analysis, predictions, and profit tracking.', 'textarea', 'CTA Banner Subtitle'],
            ['home_cta_button_primary', 'Create Free Account', 'text', 'CTA Button (Guest)'],
            ['home_cta_button_logged_in', 'Explore Stocks', 'text', 'CTA Button (Logged In)'],
            ['home_public_lists_title', 'Community Screener Lists', 'text', 'Public Screener Lists Section Title'],
            ['home_public_lists_subtitle', 'Discover stock lists shared by the community. Click a list to view its stocks.', 'textarea', 'Public Screener Lists Section Subtitle'],
        ];

        $group = 'home_page';
        $rows = [];
        foreach ($settings as $i => [$key, $value, $type, $label]) {
            $rows[] = [
                'key'            => $key,
                'value'          => $value,
                'type'           => $type,
                'setting_group'  => $group,
                'label'          => $label,
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
        $this->db->table('settings')->where('setting_group', 'home_page')->delete();
    }
}
