<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOptionImagePathField extends Migration
{
    public function up()
    {
        // Add option_image_path field to options table
        $fields = [
            'option_image_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'option_text'
            ]
        ];

        $this->forge->addColumn('options', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('options', 'option_image_path');
    }
}
