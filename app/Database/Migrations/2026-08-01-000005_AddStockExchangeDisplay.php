<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStockExchangeDisplay extends Migration
{
    public function up()
    {
        $this->forge->addColumn('stocks', [
            'exchange_display' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'exchange',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('stocks', 'exchange_display');
    }
}
