<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvestmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'stock_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'shares'         => ['type' => 'DECIMAL', 'constraint' => '12,4'],
            'buy_price'      => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'total_invested' => ['type' => 'DECIMAL', 'constraint' => '14,2'],
            'buy_date'       => ['type' => 'DATE'],
            'sell_price'     => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'sell_date'      => ['type' => 'DATE', 'null' => true],
            'status'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('stock_id', 'stocks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('investments');
    }

    public function down()
    {
        $this->forge->dropTable('investments');
    }
}
