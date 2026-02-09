<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserAnswersTable extends Migration
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
            'question_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'selected_option_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'is_correct' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => true,
            ],
            'answered_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('question_id');
        $this->forge->addUniqueKey(['session_id', 'question_id']); // Prevent duplicate answers
        $this->forge->addForeignKey('session_id', 'exam_sessions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('question_id', 'questions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('selected_option_id', 'options', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_answers');
    }

    public function down()
    {
        $this->forge->dropTable('user_answers');
    }
}
