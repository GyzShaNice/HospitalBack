<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GroupAffecter extends Migration
{
     public function up()
     {
        $this->forge->addField([

            'id_groupAff' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

            'id_group' => [
                'type' => 'INT',
            ],

            'id_personel' => [
                'type' => 'INT',
            ],

            
           'month' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
             
           ],

            'year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
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

        $this->forge->addKey('id_groupAff',true);
        $this->forge->addForeignKey('id_personel','personel','id_personel','CASCADE');
        $this->forge->addForeignKey('id_group','group','id_group','CASCADE');
        $this->forge->createTable('groupAffecter');
    }

    public function down()
    {
        $this->forge->dropTable('groupAffecter');
    }
}
