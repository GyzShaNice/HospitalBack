<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Substitution extends Migration
{
     public function up()
     {
        $this->forge->addField([
            'id_subs' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

            

            'id_absence' => [
                'type' => 'INT',
            ],

            'id_personel'=>[
                'type'=>'INT',
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

        $this->forge->addKey('id_subs',true);
        $this->forge->addForeignKey('id_absence','absence','id_absence','CASCADE');
        $this->forge->addForeignKey('id_personel','personel','id_personel','CASCADE');
        $this->forge->createTable('substitution');
    }

    public function down()
    {
        $this->forge->dropTable('substitution');
    }
}
