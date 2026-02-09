<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuestionsTable extends Migration
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
            'subject_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'question_text' => [
                'type' => 'TEXT',
            ],
            'question_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'explanation' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'difficulty_level' => [
                'type'       => 'ENUM',
                'constraint' => ['easy', 'medium', 'hard'],
                'default'    => 'medium',
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
        $this->forge->addKey('subject_id');
        $this->forge->addForeignKey('subject_id', 'subjects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('questions');
    }

    public function down()
    {
        $this->forge->dropTable('questions');
    }
}
