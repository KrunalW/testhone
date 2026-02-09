<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExamSchedulingFields extends Migration
{
    public function up()
    {
        // Add scheduling fields to exams table
        $fields = [
            'scheduled_start_time' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'max_tab_switches_allowed'
            ],
            'scheduled_end_time' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'scheduled_start_time'
            ],
            'is_scheduled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = scheduled exam, 0 = instant start exam',
                'after' => 'scheduled_end_time'
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User ID of exam creator',
                'after' => 'is_scheduled'
            ]
        ];

        $this->forge->addColumn('exams', $fields);

        // Add index on scheduled times for quick lookups
        $this->db->query('CREATE INDEX idx_exams_scheduled ON exams(is_scheduled, scheduled_start_time, scheduled_end_time, status)');
    }

    public function down()
    {
        // Drop index
        $this->db->query('DROP INDEX IF EXISTS idx_exams_scheduled ON exams');

        // Drop columns
        $this->forge->dropColumn('exams', ['scheduled_start_time', 'scheduled_end_time', 'is_scheduled', 'created_by']);
    }
}
