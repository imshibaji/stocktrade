<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQueryTextToPredictionQueries extends Migration
{
    public function up()
    {
        $this->forge->addColumn('prediction_queries', [
            'query_text' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('prediction_queries', 'query_text');
    }
}
