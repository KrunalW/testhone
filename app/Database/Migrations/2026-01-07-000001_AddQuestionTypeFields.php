<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQuestionTypeFields extends Migration
{
    public function up()
    {
        // Add question_type field
        $fields = [
            'question_type' => [
                'type'       => 'ENUM',
                'constraint' => ['text', 'image'],
                'default'    => 'text',
                'after'      => 'question_text'
            ],
            'question_image_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'question_type'
            ]
        ];

        $this->forge->addColumn('questions', $fields);

        // Update existing records to have question_type = 'text'
        $db = \Config\Database::connect();
        $db->query("UPDATE questions SET question_type = 'text' WHERE question_type IS NULL");
    }

    public function down()
    {
        $this->forge->dropColumn('questions', ['question_type', 'question_image_path']);
    }
}
