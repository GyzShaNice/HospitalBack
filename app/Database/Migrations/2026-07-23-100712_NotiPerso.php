<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NotiPerso extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_notiPerso'=>[
                'type'=>'INT',
                'auto_increment' => true,

            ],

            'id_personel'=>[
                'type'=>'INT',
                'constraint'=>11
            ],

            'id_notif'=>[
                'type'=>'INT',
                'constraint'=>11
            ],

            'is_read'=>[
                'type'=>'TINYINT',
                'constraint'=>1,
                'default'=>0
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

        $this->forge->addPrimaryKey('id_notiPerso');
        $this->forge->addForeignKey('id_notif','Notification','id_notif','CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_personel','personel','id_personel','CASCADE', 'CASCADE');
         $this->forge->createTable('NotiPerso');
    }

    public function down()
    {
        $this->forge->dropTable('NotiPerso');
    }
}
