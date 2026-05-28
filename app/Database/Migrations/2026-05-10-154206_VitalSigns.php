<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VitalSigns extends Migration
{
    public function up()
    {
             $this->forge->addField([
            'id_vi' =>[
               'type' => 'INT',
                'auto_increment' => true
            ],

        'temperature' =>[
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'weight' =>[
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            'blood_pressure' =>[
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            'height' =>[
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            'heart_beat' =>[
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

        $this->forge->addKey('id_vi',true);
        $this->forge->addForeignKey('id_medicalAct','MedicalAct','id_medicalAct','CASCADE');
        $this->forge->createTable('VitalSigns');
    }

    public function down()
    {
        $this->forge->dropTable('VitalSigns');
    }
}
