<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamFeedbackTable extends Migration
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
                'comment'    => 'Exam session ID',
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'User ID',
            ],
            'exam_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Exam ID',
            ],
            'overall_experience_rating' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => true,
                'comment'    => 'Rating from 1-10',
            ],
            'web_panel_experience' => [
                'type'       => 'ENUM',
                'constraint' => ['poor', 'below_average', 'average', 'good', 'excellent'],
                'null'       => true,
                'comment'    => 'Web panel experience rating',
            ],
            'question_quality' => [
                'type'       => 'ENUM',
                'constraint' => ['poor', 'below_average', 'average', 'good', 'excellent'],
                'null'       => true,
                'comment'    => 'Question quality rating',
            ],
            'will_refer_friends' => [
                'type'       => 'BOOLEAN',
                'null'       => true,
                'comment'    => 'Will refer to friends',
            ],
            'interested_next_test' => [
                'type'       => 'BOOLEAN',
                'null'       => true,
                'comment'    => 'Interested in next mock test',
            ],
            'real_vs_mock_difference' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Difference felt between real and mock exam',
            ],
            'general_feedback' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'General feedback',
            ],
            'felt_same_pressure' => [
                'type'       => 'ENUM',
                'constraint' => ['yes', 'no', 'maybe'],
                'null'       => true,
                'comment'    => 'Felt same pressure as actual exam',
            ],
            'other_test_series' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Other test series enrolled in',
            ],
            'willing_to_pay' => [
                'type'    => 'BOOLEAN',
                'null'    => true,
                'comment' => 'Willing to pay for expert generated test',
            ],
            'amount_paid_range' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'Amount paid for test series (99-499)',
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

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('session_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('exam_id');
        $this->forge->addKey('created_at');

        // Foreign keys
        $this->forge->addForeignKey('session_id', 'exam_sessions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('exam_id', 'exams', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('exam_feedback');
    }

    public function down()
    {
        $this->forge->dropTable('exam_feedback');
    }
}
