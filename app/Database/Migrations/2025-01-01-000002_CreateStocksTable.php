<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStocksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'symbol'        => ['type' => 'VARCHAR', 'constraint' => 10, 'unique' => true],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 150],
            'sector'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'current_price' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'previous_close'=> ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'market_cap'    => ['type' => 'BIGINT', 'null' => true],
            'avg_volume'    => ['type' => 'BIGINT', 'null' => true],
            'pe_ratio'      => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true],
            'week_52_high'  => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'week_52_low'   => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'dividend_yield'=> ['type' => 'DECIMAL', 'constraint' => '6,4', 'null' => true],
            'beta'          => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('stocks');
    }

    public function down()
    {
        $this->forge->dropTable('stocks');
    }
}
