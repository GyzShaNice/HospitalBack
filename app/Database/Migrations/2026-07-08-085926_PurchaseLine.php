<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PurchaseLine extends Migration
{
    public function up()
     {
        $this->forge->addField([
            'id_purchaseL' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            

            'id_product' => [
                'type' => 'INT',
                
            ],

            'id_purchase' => [
                'type' => 'INT',
            ],

             'id_stockMvt' => [
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

        $this->forge->addKey('id_purchaseL',true);
        $this->forge->addForeignKey('id_purchase','Purchase','id_purchase','CASCADE');
        $this->forge->addForeignKey('id_stockMvt','StockMovement','id_stockMvt','CASCADE');
        $this->forge->addForeignKey('id_product','Product','id_product','CASCADE');
        $this->forge->createTable('PurchaseL');
     }

    public function down()
    {
        $this->forge->dropTable('PurchaseL');
    }
}
