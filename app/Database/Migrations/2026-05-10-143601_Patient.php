<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Patient extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_patient' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

            'emergency_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            

            'id_user' => [
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

        $this->forge->addKey('id_patient',true);
        $this->forge->addForeignKey('id_user','user','id_user','CASCADE');
        $this->forge->createTable('patient');
    }

    public function down()
    {
        $this->forge->dropTable('patient');
    }
}
