<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePredictionQueriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'criteria' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'technical_criteria' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'match_mode' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
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
            'last_run_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'running', 'completed'],
                'default' => 'pending',
                'null' => false,
            ],
            'is_public' => [
                'type' => 'BOOLEAN',
                'default' => 0,
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('prediction_queries');
    }

    public function down()
    {
        $this->forge->dropTable('prediction_queries');
    }
}