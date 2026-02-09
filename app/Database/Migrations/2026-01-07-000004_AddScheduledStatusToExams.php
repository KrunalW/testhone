<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScheduledStatusToExams extends Migration
{
    public function up()
    {
        // Alter the status column to add 'scheduled', 'completed', and 'archived' options
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE exams MODIFY COLUMN status ENUM('draft', 'scheduled', 'active', 'inactive', 'completed', 'archived') NOT NULL DEFAULT 'draft'");

        echo "Added 'scheduled', 'completed', and 'archived' to exam status enum\n";
    }

    public function down()
    {
        // Revert back to original enum
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE exams MODIFY COLUMN status ENUM('draft', 'active', 'inactive') NOT NULL DEFAULT 'active'");
    }
}
