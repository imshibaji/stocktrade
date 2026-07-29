<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWatchlistTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'stock_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'=> ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'stock_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('stock_id', 'stocks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('watchlist');
    }

    public function down()
    {
        $this->forge->dropTable('watchlist');
    }
}
