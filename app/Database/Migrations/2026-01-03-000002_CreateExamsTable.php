<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamsTable extends Migration
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
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'duration_minutes' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'total_questions' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'pass_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 40.00,
            ],
            'has_negative_marking' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'negative_marks_per_question' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'marks_per_question' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 1.00,
            ],
            'randomize_questions' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'randomize_options' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'prevent_tab_switch' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'max_tab_switches_allowed' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 3,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('exams');
    }

    public function down()
    {
        $this->forge->dropTable('exams');
    }
}
