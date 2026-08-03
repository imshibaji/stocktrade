<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastFetchedToStocks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('stocks', [
            'last_fetched' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'updated_at',
            ],
        ]);

        $this->forge->addKey('last_fetched');
    }

    public function down()
    {
        $this->forge->dropIndex('stocks', 'last_fetched');
        $this->forge->dropColumn('stocks', 'last_fetched');
    }
}