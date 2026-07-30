<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExchangeToStocks extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('stocks', [
            'exchange' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'NSE',
                'after'      => 'symbol',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('stocks', 'exchange');
    }
}