<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBaseCurrencyToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'base_currency' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'default'    => 'INR',
                'after'      => 'email',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'base_currency');
    }
}