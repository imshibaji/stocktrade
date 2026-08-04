<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePredictionQueryResultsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'query_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'stock_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'predicted_price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,4',
                'null' => false,
            ],
            'predicted_change_pct' => [
                'type' => 'DECIMAL',
                'constraint' => '8,4',
                'null' => false,
            ],
            'signal' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => false,
            ],
            'confidence_score' => [
                'type' => 'SMALLINT',
                'unsigned' => true,
                'null' => false,
            ],
            'method' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'horizon_days' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'actual_price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,4',
                'null' => true,
            ],
            'actual_change_pct' => [
                'type' => 'DECIMAL',
                'constraint' => '8,4',
                'null' => true,
            ],
            'outcome' => [
                'type' => 'ENUM',
                'constraint' => ['hit', 'miss', 'pending'],
                'default' => 'pending',
                'null' => false,
            ],
            'generated_at' => [
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false,
            ],
            'forecast_date' => [
                'type' => 'DATE',
                'null' => false,
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
        $this->forge->addForeignKey('query_id', 'prediction_queries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('stock_id', 'stocks', 'id', 'NO ACTION', 'NO ACTION');

        $this->forge->createTable('prediction_query_results');
    }

    public function down()
    {
        $this->forge->dropTable('prediction_query_results');
    }
}