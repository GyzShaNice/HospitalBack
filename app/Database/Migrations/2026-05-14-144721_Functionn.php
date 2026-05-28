<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class functions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_function' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

             'name' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],

            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active','desactivated'],
                
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

        $this->forge->addKey('id_function',true);
        $this->forge->createTable('function');
    }

    public function down()
    {
        $this->forge->dropTable('function');
    }
}
