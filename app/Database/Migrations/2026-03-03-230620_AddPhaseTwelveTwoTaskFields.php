<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhaseTwelveTwoTaskFields extends Migration
{
    public function up()
    {
        // 1. Add fields to tasks table
        $fields = [];
        if (!$this->db->fieldExists('points', 'tasks')) $fields['points'] = ['type' => 'INT', 'constraint' => 11, 'default' => 0];
        if (!$this->db->fieldExists('milestone_id', 'tasks')) $fields['milestone_id'] = ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true];
        if (!$this->db->fieldExists('labels', 'tasks')) $fields['labels'] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true];
        if (!$this->db->fieldExists('start_date', 'tasks')) $fields['start_date'] = ['type' => 'DATE', 'null' => true];
        if (!$this->db->fieldExists('start_time', 'tasks')) $fields['start_time'] = ['type' => 'TIME', 'null' => true];
        if (!$this->db->fieldExists('end_time', 'tasks')) $fields['end_time'] = ['type' => 'TIME', 'null' => true];
        if (!$this->db->fieldExists('recurring_rule', 'tasks')) $fields['recurring_rule'] = ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true];

        if (!empty($fields)) {
            $this->forge->addColumn('tasks', $fields);
        }

        // Add foreign key for milestone_id ONLY if we just created the field or it lacks a key. For simplicity, ignore adding the FK dynamically unless we know it's purely missing. Let's just create task_collaborators.

        // 2. Create task_collaborators table
        if (!$this->db->tableExists('task_collaborators')) {
            $this->forge->addField([
                'task_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey(['task_id', 'user_id'], true); // composite primary key
            $this->forge->addForeignKey('task_id', 'tasks', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('user_id', 'fs_users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('task_collaborators');
        }
    }

    public function down()
    {
        $this->forge->dropTable('task_collaborators');
        
        $this->db->query('ALTER TABLE `tasks` DROP FOREIGN KEY `fk_tasks_milestone_id`');
        
        $this->forge->dropColumn('tasks', [
            'points', 'milestone_id', 'labels', 'start_date', 'start_time', 'end_time', 'recurring_rule'
        ]);
    }
}
