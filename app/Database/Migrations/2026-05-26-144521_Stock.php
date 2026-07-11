<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Stock extends Migration
{
    public function up()
   {
        $this->forge->addField([
            'id_stock' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

             'name_stock' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

             'expiry_date' => [
                'type' => 'DATE'
            ],

             'quantity_available' => [
                'type' => 'INT'
            ],


            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

        ]);

        $this->forge->addKey('id_stock',true);
        $this->forge->createTable('Stock');
    }

    public function down()
    {
        $this->forge->dropTable('Stock');
    }
}
