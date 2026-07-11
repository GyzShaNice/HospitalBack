<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProductLine extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_ProductPres' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

            'id_presc' => [
                'type' => 'INT'
            ],

             'id_product' => [
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

        $this->forge->addKey('id_ProductPres',true);
        $this->forge->addForeignKey('id_presc','prescrib','id_presc','CASCADE');
        $this->forge->addForeignKey('id_product','product','id_product','CASCADE');
        $this->forge->createTable('ProductPres');
    }

    public function down()
    {
        $this->forge->dropTable('ProductPres');
    }
}
