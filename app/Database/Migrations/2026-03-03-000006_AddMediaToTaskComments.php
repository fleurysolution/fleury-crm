<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMediaToTaskComments extends Migration
{
    public function up()
    {
        $fields = [
            'attachment_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'attachment_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ]
        ];
        
        $this->forge->addColumn('task_comments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('task_comments', 'attachment_path');
        $this->forge->dropColumn('task_comments', 'attachment_name');
    }
}
