<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShiftAndExtendGroupAffecter extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_shift' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_group' => [
                'type' => 'INT',
            ],
            'shift_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'start_time' => [
                'type' => 'TIME',
            ],
            'end_time' => [
                'type' => 'TIME',
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

        $this->forge->addKey('id_shift', true);
        $this->forge->addForeignKey('id_group', 'group', 'id_group', 'CASCADE', 'CASCADE', 'shift_group_fk');
        // Le reste du schema (group, groupaffecter, ...) est en MyISAM : on aligne ce
        // nouveau tableau sur le meme moteur pour que la FK ci-dessus soit acceptee
        // (InnoDB refuse de referencer une table MyISAM : "Failed to open the referenced table").
        $this->forge->createTable('Shift', false, ['ENGINE' => 'MyISAM']);

        $this->forge->addColumn('groupAffecter', [
            'id_shift' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'id_group',
            ],
            'week_number' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'id_shift',
            ],
        ]);

        $this->forge->addForeignKey('id_shift', 'Shift', 'id_shift', 'CASCADE', 'SET NULL', 'groupaffecter_shift_fk');
        $this->forge->processIndexes('groupAffecter');
    }

    public function down()
    {
        $this->forge->dropForeignKey('groupAffecter', 'groupaffecter_shift_fk');
        $this->forge->dropColumn('groupAffecter', ['id_shift', 'week_number']);
        $this->forge->dropTable('Shift');
    }
}
