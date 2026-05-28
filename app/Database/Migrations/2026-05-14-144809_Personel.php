<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Personel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_personel' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

             'staff_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            

            'id_user' => [
                'type' => 'INT'
            ],

             'id_function' => [
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

        $this->forge->addKey('id_personel',true);
        $this->forge->addForeignKey('id_user','user','id_user','CASCADE');
        $this->forge->addForeignKey('id_function','function','id_function','CASCADE');
        $this->forge->createTable('personel');
    }

    public function down()
    {
        $this->forge->dropTable('personel');
    }
}





