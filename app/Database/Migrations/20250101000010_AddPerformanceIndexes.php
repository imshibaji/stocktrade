<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        $queries = [
            'CREATE INDEX IF NOT EXISTS idx_stock_prices_sid_date ON stock_prices(stock_id, price_date)',
            'CREATE INDEX IF NOT EXISTS idx_predictions_sid_date ON predictions(stock_id, predicted_date)',
            'CREATE INDEX IF NOT EXISTS idx_investments_user_status ON investments(user_id, status)',
            'CREATE INDEX IF NOT EXISTS idx_investments_stock ON investments(stock_id)',
            'CREATE INDEX IF NOT EXISTS idx_watchlist_user_stock ON watchlist(user_id, stock_id)',
            'CREATE INDEX IF NOT EXISTS idx_watchlist_bucket ON watchlist(bucket_id)',
            'CREATE INDEX IF NOT EXISTS idx_stocks_symbol ON stocks(symbol)',
            'CREATE INDEX IF NOT EXISTS idx_stocks_sector ON stocks(sector)',
            'CREATE INDEX IF NOT EXISTS idx_sell_tx_user ON sell_transactions(user_id)',
            'CREATE INDEX IF NOT EXISTS idx_sell_tx_stock ON sell_transactions(stock_id)',
            'CREATE INDEX IF NOT EXISTS idx_watchlist_buckets_user ON watchlist_buckets(user_id)',
            'CREATE INDEX IF NOT EXISTS idx_stock_lists_user ON stock_lists(user_id)',
        ];

        $db = \Config\Database::connect();
        foreach ($queries as $sql) {
            try {
                $db->query($sql);
            } catch (\Throwable $e) {
                log_message('error', 'Index creation error: ' . $e->getMessage());
            }
        }

        $db->query('PRAGMA journal_mode=WAL');
        $db->query('PRAGMA synchronous=NORMAL');
    }

    public function down()
    {
        $indexes = [
            'idx_stock_prices_sid_date',
            'idx_predictions_sid_date',
            'idx_investments_user_status',
            'idx_investments_stock',
            'idx_watchlist_user_stock',
            'idx_watchlist_bucket',
            'idx_stocks_symbol',
            'idx_stocks_sector',
            'idx_sell_tx_user',
            'idx_sell_tx_stock',
            'idx_watchlist_buckets_user',
            'idx_stock_lists_user',
        ];

        $db = \Config\Database::connect();
        foreach ($indexes as $idx) {
            try {
                $db->query("DROP INDEX IF EXISTS {$idx}");
            } catch (\Throwable $e) {
                log_message('error', 'Index drop error: ' . $e->getMessage());
            }
        }
    }
}
