<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Group extends Migration
{
   public function up()
     {
        $this->forge->addField([
            'id_group' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

            
           'name_group' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
             
           ],

           'type_group' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
             
           ],

           'working_days' => [
                'type' => 'VARCHAR',
                'constraint'=>100,
             
           ],

            'start_time' => [
                'type' => 'TIME'
            ],

            'end_time' => [
                'type' => 'TIME'
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

        $this->forge->addKey('id_group',true);
        $this->forge->createTable('group');
    }

    public function down()
    {
        $this->forge->dropTable('group');
    }
}
