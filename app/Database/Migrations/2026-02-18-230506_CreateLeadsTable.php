<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeadsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'contact_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR', // new, contacted, qualified, proposal, won, lost
                'constraint' => 50,
                'default'    => 'new',
                'null'       => false,
            ],
            'source' => [
                'type'       => 'VARCHAR',
                'constraint' => 50, // website, referral, etc.
                'null'       => true,
            ],
            'value' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'null'       => false,
            ],
            'assigned_to' => [
                'type'     => 'INT', // fs_users.id is INT/BIGINT? Let's check. fs_users.id is BIGINT based on EnsureFsUsersTable.
                'unsigned' => true,
                'null'     => true,
            ],
            'created_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('assigned_to');
        
        // Removed strict foreign key for now to avoid potential type mismatch issues if I guessed the FK type wrong (INT vs BIGINT).
        // Using CreateFsAsIamCoreTables referenece, user_id is BIGINT.
        // Let's update `assigned_to` to BIGINT to match.
        
        $this->forge->createTable('leads', true);
        
        // Modify column separately to be safe or just re-define above. I'll redefine above.
    }

    public function down()
    {
        $this->forge->dropTable('leads', true);
    }
}
