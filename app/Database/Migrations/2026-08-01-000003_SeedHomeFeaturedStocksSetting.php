<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedHomeFeaturedStocksSetting extends Migration
{
    public function up()
    {
        $db = $this->db;
        $exists = $db->table('settings')
            ->where('key', 'home_featured_stocks')
            ->get()
            ->getFirstRow();

        if (!$exists) {
            $db->table('settings')->insert([
                'key'           => 'home_featured_stocks',
                'value'         => '',
                'type'          => 'textarea',
                'setting_group' => 'home_page',
                'label'         => 'Home Page Featured Stocks (stock IDs, comma-separated)',
            ]);
        }
    }

    public function down()
    {
        $this->db->table('settings')->where('key', 'home_featured_stocks')->delete();
    }
}
