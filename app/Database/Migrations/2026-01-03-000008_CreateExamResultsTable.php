<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamResultsTable extends Migration
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
            'subject_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'total_questions_in_subject' => [
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
            'score_obtained' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
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
        $this->forge->addKey('session_id');
        $this->forge->addForeignKey('session_id', 'exam_sessions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('subject_id', 'subjects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('exam_results');
    }

    public function down()
    {
        $this->forge->dropTable('exam_results');
    }
}
