<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserProfileFields extends Migration
{
    public function up()
    {
        $fields = [
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'username',
                'comment' => 'User full name'
            ],
            'age' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
                'after' => 'full_name',
                'comment' => 'User age'
            ],
            'mobile_number' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
                'after' => 'age',
                'comment' => 'User mobile number'
            ],
            'category' => [
                'type' => 'ENUM',
                'constraint' => ['sc/st', 'open', 'obc', 'vj/nt', 'nt-b', 'nt-c', 'nt-d', 'sebc', 'ews'],
                'null' => true,
                'after' => 'mobile_number',
                'comment' => 'User category for reservation'
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'category',
                'comment' => 'User email address'
            ],
            'preferred_language' => [
                'type' => 'ENUM',
                'constraint' => ['english', 'marathi'],
                'default' => 'english',
                'null' => false,
                'after' => 'email',
                'comment' => 'User preferred language for exams'
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Add index for email (for faster lookups)
        $this->db->query('ALTER TABLE users ADD INDEX idx_email (email)');

        // Add index for mobile_number (for faster lookups)
        $this->db->query('ALTER TABLE users ADD INDEX idx_mobile (mobile_number)');
    }

    public function down()
    {
        // Remove indexes first
        $this->db->query('ALTER TABLE users DROP INDEX IF EXISTS idx_email');
        $this->db->query('ALTER TABLE users DROP INDEX IF EXISTS idx_mobile');

        // Remove columns
        $this->forge->dropColumn('users', [
            'full_name',
            'age',
            'mobile_number',
            'category',
            'email',
            'preferred_language'
        ]);
    }
}
