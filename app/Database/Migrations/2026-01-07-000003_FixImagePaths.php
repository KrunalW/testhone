<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixImagePaths extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Fix question image paths
        $db->query("UPDATE questions
                   SET question_image_path = REPLACE(question_image_path, 'writable/uploads/', 'uploads/')
                   WHERE question_image_path LIKE 'writable/uploads/%'");

        // Fix option image paths
        $db->query("UPDATE options
                   SET option_image_path = REPLACE(option_image_path, 'writable/uploads/', 'uploads/')
                   WHERE option_image_path LIKE 'writable/uploads/%'");

        echo "Fixed image paths in questions and options tables\n";
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Revert question image paths
        $db->query("UPDATE questions
                   SET question_image_path = REPLACE(question_image_path, 'uploads/', 'writable/uploads/')
                   WHERE question_image_path LIKE 'uploads/%'
                   AND question_image_path NOT LIKE 'writable/uploads/%'");

        // Revert option image paths
        $db->query("UPDATE options
                   SET option_image_path = REPLACE(option_image_path, 'uploads/', 'writable/uploads/')
                   WHERE option_image_path LIKE 'uploads/%'
                   AND option_image_path NOT LIKE 'writable/uploads/%'");
    }
}
