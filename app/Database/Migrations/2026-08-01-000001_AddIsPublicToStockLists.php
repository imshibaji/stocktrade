<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsPublicToStockLists extends Migration
{
    public function up()
    {
        $this->forge->addColumn('stock_lists', [
            'is_public' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
                'after'   => 'stock_count',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('stock_lists', 'is_public');
    }
}
