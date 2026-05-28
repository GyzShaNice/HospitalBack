<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Absence extends Migration
{
     public function up()
     {
        $this->forge->addField([
            'id_absence' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

            

            'id_personel' => [
                'type' => 'INT',
            ],

            

           'motif' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
             
           ],

            'date' => [
                'type' => 'DATETIME',
                
            ],

            'statut' => [
                'type' => 'ENUM',
                'constraint' => ['accepted', 'modified','cancel'],
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

        $this->forge->addKey('id_absence',true);
        $this->forge->addForeignKey('id_personel','personel','id_personel','CASCADE');
        $this->forge->createTable('Absence');
    }

    public function down()
    {
        $this->forge->dropTable('Absence');
    }
}
