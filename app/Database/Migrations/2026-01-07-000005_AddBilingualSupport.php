<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBilingualSupport extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Add Marathi language columns to questions table
        $this->forge->addColumn('questions', [
            'question_text_marathi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'question_text',
            ],
            'explanation_marathi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'explanation',
            ],
        ]);

        echo "Added Marathi columns to questions table\n";

        // Add Marathi language column to options table
        $this->forge->addColumn('options', [
            'option_text_marathi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'option_text',
            ],
        ]);

        echo "Added Marathi column to options table\n";

        // Add Marathi language columns to subjects table
        $this->forge->addColumn('subjects', [
            'name_marathi' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'name',
            ],
            'description_marathi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'description',
            ],
        ]);

        echo "Added Marathi columns to subjects table\n";

        // Add Marathi language columns to exams table
        $this->forge->addColumn('exams', [
            'title_marathi' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'title',
            ],
            'description_marathi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'description',
            ],
        ]);

        echo "Added Marathi columns to exams table\n";

        // Ensure all tables use UTF-8 encoding for proper Devanagari script support
        $db->query("ALTER TABLE questions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $db->query("ALTER TABLE options CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $db->query("ALTER TABLE subjects CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $db->query("ALTER TABLE exams CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        echo "Updated character set to UTF-8 for Devanagari script support\n";
        echo "\nBilingual support migration completed successfully!\n";
    }

    public function down()
    {
        // Remove Marathi columns from questions table
        $this->forge->dropColumn('questions', ['question_text_marathi', 'explanation_marathi']);

        // Remove Marathi column from options table
        $this->forge->dropColumn('options', ['option_text_marathi']);

        // Remove Marathi columns from subjects table
        $this->forge->dropColumn('subjects', ['name_marathi', 'description_marathi']);

        // Remove Marathi columns from exams table
        $this->forge->dropColumn('exams', ['title_marathi', 'description_marathi']);

        echo "Rolled back bilingual support migration\n";
    }
}
