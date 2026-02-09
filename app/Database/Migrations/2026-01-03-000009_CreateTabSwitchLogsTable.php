<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTabSwitchLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'session_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'switched_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('session_id');
        $this->forge->addForeignKey('session_id', 'exam_sessions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tab_switch_logs');
    }

    public function down()
    {
        $this->forge->dropTable('tab_switch_logs');
    }
}
