<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReceiptFieldsToPurchaseAndPurchaseLine extends Migration
{
    public function up()
    {
        $this->forge->addColumn('Purchase', [
            'receipt_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'unique'     => true,
                'after'      => 'final_amount',
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'receipt_number',
            ],
            'id_personel' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'payment_method',
            ],
        ]);

        $this->forge->addForeignKey('id_personel', 'personel', 'id_personel', 'CASCADE', 'SET NULL', 'purchase_personel_fk');
        $this->forge->processIndexes('Purchase');

        $this->forge->addColumn('PurchaseL', [
            'unit_price' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'id_product',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropForeignKey('Purchase', 'purchase_personel_fk');
        $this->forge->dropColumn('Purchase', ['receipt_number', 'payment_method', 'id_personel']);
        $this->forge->dropColumn('PurchaseL', 'unit_price');
    }
}
