<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MedicalAct extends Migration
{
   public function up()
    {
        $this->forge->addField([
            'id_medicalAct' => [
                'type' => 'INT',
                'auto_increment' => true
            ],


             'type_act' => [
                'type' => 'ENUM',
                'constraint' => ['parametre', 'consultation'],
            ],

            'date_act' => [
                'type' => 'DATE'
            ],



            'id_personel' => [
                'type' => 'INT'
            ],
            
            'id_patient' => [
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

        $this->forge->addKey('id_medicalAct',true);
        $this->forge->addForeignKey('id_personel','personel','id_personel','CASCADE');
        $this->forge->addForeignKey('id_patient','patient','id_patient','CASCADE');
        $this->forge->createTable('MedicalAct');
    }

    public function down()
    {
        $this->forge->dropTable('MedicalAct');
    }
}
