<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Consultation extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_consult' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

            // consultation attributes
            'motif' =>[
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            'symptoms' =>[
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            'diagnois' =>[
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            'observation' =>[
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            'recommendation' =>[
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            'id_medicalAct' => [
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

        $this->forge->addKey('id_consult',true);
        $this->forge->addForeignKey('id_medicalAct','MedicalAct','id_medicalAct','CASCADE');
        $this->forge->createTable('consultation');
    }

    public function down()
    {
        $this->forge->dropTable('consultation');
    }
}
