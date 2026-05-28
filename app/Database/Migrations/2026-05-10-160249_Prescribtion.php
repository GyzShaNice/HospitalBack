<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Prescribtion extends Migration
{
    public function up()
     {
        $this->forge->addField([
            'id_presc' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

            
            'date_presc' => [
                'type' => 'DATE'
            ],

            'instructions' =>[
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],


            'id_consult' => [
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

        $this->forge->addKey('id_presc',true);
        $this->forge->addForeignKey('id_consult','consultation','id_consult','CASCADE');
        $this->forge->createTable('prescrib');
    }

    public function down()
    {
        $this->forge->dropTable('prescrib');
    }
}
