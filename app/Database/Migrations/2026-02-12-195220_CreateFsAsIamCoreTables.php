<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFsAsIamCoreTables extends Migration
{
    public function up()
    {
        /**
         * fs_as_iam_accounts
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 190,
                'null'       => false,
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'active', // active|inactive|suspended
                'null'       => false,
            ],
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
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
        $this->forge->addUniqueKey('email', 'uq_fs_as_iam_accounts_email');
        $this->forge->addKey('status', false, false, 'idx_fs_as_iam_accounts_status');
        $this->forge->addKey('created_at', false, false, 'idx_fs_as_iam_accounts_created_at');
        $this->forge->createTable('fs_as_iam_accounts', true);

        /**
         * fs_as_iam_roles
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'display_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_system' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('role_key', 'uq_fs_as_iam_roles_role_key');
        $this->forge->createTable('fs_as_iam_roles', true);

        /**
         * fs_as_iam_permissions
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'permission_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 140,
                'null'       => false,
            ],
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('permission_key', 'uq_fs_as_iam_permissions_key');
        $this->forge->addKey('module', false, false, 'idx_fs_as_iam_permissions_module');
        $this->forge->createTable('fs_as_iam_permissions', true);

        /**
         * fs_as_iam_role_permissions
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'permission_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['role_id', 'permission_id'], 'uq_fs_as_iam_role_permissions_pair');
        $this->forge->addKey('role_id', false, false, 'idx_fs_as_iam_role_permissions_role_id');
        $this->forge->addKey('permission_id', false, false, 'idx_fs_as_iam_role_permissions_permission_id');
        $this->forge->createTable('fs_as_iam_role_permissions', true);

        /**
         * fs_as_iam_account_roles
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'account_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'role_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'assigned_by' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'assigned_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['account_id', 'role_id'], 'uq_fs_as_iam_account_roles_pair');
        $this->forge->addKey('account_id', false, false, 'idx_fs_as_iam_account_roles_account_id');
        $this->forge->addKey('role_id', false, false, 'idx_fs_as_iam_account_roles_role_id');
        $this->forge->createTable('fs_as_iam_account_roles', true);

        /**
         * fs_as_iam_sessions
         */
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => false,
            ],
            'account_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => false,
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'payload' => [
                'type' => 'BLOB',
                'null' => false,
            ],
            'last_activity_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'revoked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('account_id', false, false, 'idx_fs_as_iam_sessions_account_id');
        $this->forge->addKey('expires_at', false, false, 'idx_fs_as_iam_sessions_expires_at');
        $this->forge->createTable('fs_as_iam_sessions', true);

        /**
         * fs_as_iam_reset_tokens
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'account_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'token_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
            ],
            'issued_ip' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('token_hash', 'uq_fs_as_iam_reset_tokens_token_hash');
        $this->forge->addKey('account_id', false, false, 'idx_fs_as_iam_reset_tokens_account_id');
        $this->forge->addKey('expires_at', false, false, 'idx_fs_as_iam_reset_tokens_expires_at');
        $this->forge->createTable('fs_as_iam_reset_tokens', true);

        /**
         * fs_as_iam_audit_events
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'event_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 140,
                'null'       => false,
            ],
            'actor_account_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'target_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
            ],
            'target_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'metadata_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('event_key', false, false, 'idx_fs_as_iam_audit_events_event_key');
        $this->forge->addKey('actor_account_id', false, false, 'idx_fs_as_iam_audit_events_actor');
        $this->forge->addKey('created_at', false, false, 'idx_fs_as_iam_audit_events_created_at');
        $this->forge->createTable('fs_as_iam_audit_events', true);

        /**
         * Foreign Keys
         */
        $this->db->query('ALTER TABLE fs_as_iam_role_permissions
            ADD CONSTRAINT fk_fs_as_iam_role_permissions_role
            FOREIGN KEY (role_id) REFERENCES fs_as_iam_roles(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_iam_role_permissions
            ADD CONSTRAINT fk_fs_as_iam_role_permissions_permission
            FOREIGN KEY (permission_id) REFERENCES fs_as_iam_permissions(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_iam_account_roles
            ADD CONSTRAINT fk_fs_as_iam_account_roles_account
            FOREIGN KEY (account_id) REFERENCES fs_as_iam_accounts(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_iam_account_roles
            ADD CONSTRAINT fk_fs_as_iam_account_roles_role
            FOREIGN KEY (role_id) REFERENCES fs_as_iam_roles(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_iam_account_roles
            ADD CONSTRAINT fk_fs_as_iam_account_roles_assigned_by
            FOREIGN KEY (assigned_by) REFERENCES fs_as_iam_accounts(id)
            ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_iam_sessions
            ADD CONSTRAINT fk_fs_as_iam_sessions_account
            FOREIGN KEY (account_id) REFERENCES fs_as_iam_accounts(id)
            ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_iam_reset_tokens
            ADD CONSTRAINT fk_fs_as_iam_reset_tokens_account
            FOREIGN KEY (account_id) REFERENCES fs_as_iam_accounts(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_iam_audit_events
            ADD CONSTRAINT fk_fs_as_iam_audit_events_actor
            FOREIGN KEY (actor_account_id) REFERENCES fs_as_iam_accounts(id)
            ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop child tables first
        $this->forge->dropTable('fs_as_iam_audit_events', true);
        $this->forge->dropTable('fs_as_iam_reset_tokens', true);
        $this->forge->dropTable('fs_as_iam_sessions', true);
        $this->forge->dropTable('fs_as_iam_account_roles', true);
        $this->forge->dropTable('fs_as_iam_role_permissions', true);
        $this->forge->dropTable('fs_as_iam_permissions', true);
        $this->forge->dropTable('fs_as_iam_roles', true);
        $this->forge->dropTable('fs_as_iam_accounts', true);
    }
}
