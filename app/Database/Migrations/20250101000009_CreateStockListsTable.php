<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockListsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'criteria' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'technical_criteria' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'stock_ids' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'stock_symbols' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'stock_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_lists');
    }

    public function down()
    {
        $this->forge->dropTable('stock_lists');
    }
}
