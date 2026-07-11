<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQuantityAndFixMovementDateToStockMovement extends Migration
{
    public function up()
    {
        // add the new quantity column
        $this->forge->addColumn('StockMovement', [
            'quantity' => [
                'type' => 'INT',
                'after' => 'movement_type', // optional, just for readable column order
            ],
        ]);

        // rename movementDate -> movement_date (only if the old column still exists)
        $this->forge->modifyColumn('StockMovement', [
            'movementDate' => [
                'name' => 'movement_date',
                'type' => 'DATE',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('StockMovement', 'quantity');

        $this->forge->modifyColumn('StockMovement', [
            'movement_date' => [
                'name' => 'movementDate',
                'type' => 'DATE',
            ],
        ]);
    }
}