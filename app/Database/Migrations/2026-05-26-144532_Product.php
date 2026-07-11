<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Product extends Migration
{
    public function up()
   {
        $this->forge->addField([
            'id_product' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

             'name_product' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],

             'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],

            

             'minimum_quantity' => [
                'type' => 'INT',
                
            ],

            'price' => [
                'type' => 'INT',
                
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

        $this->forge->addKey('id_product',true);
        $this->forge->createTable('Product');
    }

    public function down()
    {
        $this->forge->dropTable('Product');
    }
}
