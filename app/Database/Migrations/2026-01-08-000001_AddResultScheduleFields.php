<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResultScheduleFields extends Migration
{
    public function up()
    {
        // Add result schedule fields to exams table
        $fields = [
            'result_publish_time' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'scheduled_end_time',
                'comment' => 'When results will be published'
            ],
            'is_result_scheduled' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'result_publish_time',
                'comment' => 'Whether result publication is scheduled'
            ],
        ];

        $this->forge->addColumn('exams', $fields);

        echo "Added result schedule fields to exams table\n";
    }

    public function down()
    {
        $this->forge->dropColumn('exams', ['result_publish_time', 'is_result_scheduled']);
    }
}
