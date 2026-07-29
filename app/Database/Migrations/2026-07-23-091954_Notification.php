<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Notification extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_notif'=>[
                'type'=>'INT',
                'auto_increment' => true,
            ],

            'id_expediteur' => [
                'type'           => 'INT',
                'constraint' => true,
            ],
            
            'titre' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],

            'message'=>[
                'type'=>'VARCHAR',
                'constraint'=>255
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

        $this->forge->addKey('id_notif',true);
        $this->forge->addForeignKey('id_expediteur','personel','id_personel','CASCADE');
        $this->forge->createTable('Notification');
    }

   

    public function down()
    {
        $this->forge->dropTable('Notification');
    }
}
