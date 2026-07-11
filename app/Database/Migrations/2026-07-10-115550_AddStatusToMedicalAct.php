<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToMedicalAct extends Migration
{
    public function up()
    {
        $this->forge->addColumn('MedicalAct', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['en_attente', 'attente_consultation', 'consulte'],
                'default' => 'en_attente',
                'after' => 'date_act',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('MedicalAct', 'status');
    }
}