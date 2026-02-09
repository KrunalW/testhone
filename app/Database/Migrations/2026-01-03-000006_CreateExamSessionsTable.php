<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamSessionsTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'exam_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'start_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'end_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'actual_submit_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['in_progress', 'completed', 'expired', 'terminated'],
                'default'    => 'in_progress',
            ],
            'tab_switch_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'terminated_reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'total_questions_attempted' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'correct_answers' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'wrong_answers' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'unanswered' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'raw_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'final_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('exam_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('exam_id', 'exams', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('exam_sessions');
    }

    public function down()
    {
        $this->forge->dropTable('exam_sessions');
    }
}
