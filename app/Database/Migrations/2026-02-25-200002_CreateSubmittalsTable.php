<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubmittalsTable extends Migration
{
    public function up(): void
    {
        // Submittals register
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'       => ['type' => 'INT', 'unsigned' => true],
            'submittal_number' => ['type' => 'VARCHAR', 'constraint' => 30],
            'title'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'spec_section'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'type'             => ['type' => 'ENUM', 'constraint' => ['shop_drawing','product_data','sample','o_and_m','other'], 'default' => 'shop_drawing'],
            'status'           => ['type' => 'ENUM', 'constraint' => ['draft','submitted','under_review','approved','approved_as_noted','rejected','resubmit'], 'default' => 'draft'],
            'submitted_by'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'reviewer_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'due_date'         => ['type' => 'DATE', 'null' => true],
            'current_revision' => ['type' => 'TINYINT', 'default' => 0],
            'days_in_review'   => ['type' => 'INT', 'default' => 14],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'status']);
        $this->forge->createTable('submittals');

        // Submittal revisions / review rounds
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'submittal_id' => ['type' => 'INT', 'unsigned' => true],
            'revision_no'  => ['type' => 'TINYINT', 'default' => 0],
            'status'       => ['type' => 'ENUM', 'constraint' => ['submitted','under_review','approved','approved_as_noted','rejected','resubmit'], 'default' => 'submitted'],
            'reviewer_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'reviewed_at'  => ['type' => 'DATETIME', 'null' => true],
            'notes'        => ['type' => 'TEXT', 'null' => true],
            'filepath'     => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['submittal_id']);
        $this->forge->createTable('submittal_revisions');
    }

    public function down(): void
    {
        $this->forge->dropTable('submittal_revisions', true);
        $this->forge->dropTable('submittals', true);
    }
}
