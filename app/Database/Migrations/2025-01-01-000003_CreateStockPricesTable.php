<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockPricesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'stock_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'price_date'=> ['type' => 'DATE'],
            'open'     => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'high'     => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'low'      => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'close'    => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'volume'   => ['type' => 'BIGINT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('stock_id', 'stocks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_prices');
    }

    public function down()
    {
        $this->forge->dropTable('stock_prices');
    }
}
