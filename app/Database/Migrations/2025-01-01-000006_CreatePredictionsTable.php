<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePredictionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'stock_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'predicted_date'  => ['type' => 'DATE'],
            'predicted_price' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'confidence_score'=> ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            'method'          => ['type' => 'VARCHAR', 'constraint' => 50],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('stock_id', 'stocks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('predictions');
    }

    public function down()
    {
        $this->forge->dropTable('predictions');
    }
}
