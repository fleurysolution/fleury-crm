<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFsAsIdentityAndApprovalCore extends Migration
{
    public function up()
    {
        /**
         * fs_as_users
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employee_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 190, // safe for utf8mb4 indexed unique
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
                'null'       => false,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'avatar_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'active', // active|inactive|suspended
                'null'       => false,
            ],
            'email_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addUniqueKey('email', 'uq_fs_as_users_email');
        $this->forge->addUniqueKey('employee_code', 'uq_fs_as_users_employee_code');
        $this->forge->addKey('status', false, false, 'idx_fs_as_users_status');
        $this->forge->createTable('fs_as_users', true);

        /**
         * fs_as_roles
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
        $this->forge->addUniqueKey('role_key', 'uq_fs_as_roles_role_key');
        $this->forge->createTable('fs_as_roles', true);

        /**
         * fs_as_permissions
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
            'module_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
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
        $this->forge->addUniqueKey('permission_key', 'uq_fs_as_permissions_permission_key');
        $this->forge->addKey('module_key', false, false, 'idx_fs_as_permissions_module_key');
        $this->forge->createTable('fs_as_permissions', true);

        /**
         * fs_as_role_permissions
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
        $this->forge->addUniqueKey(['role_id', 'permission_id'], 'uq_fs_as_role_permissions_pair');
        $this->forge->addKey('role_id', false, false, 'idx_fs_as_role_permissions_role_id');
        $this->forge->addKey('permission_id', false, false, 'idx_fs_as_role_permissions_permission_id');
        $this->forge->createTable('fs_as_role_permissions', true);

        /**
         * fs_as_user_roles
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
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
        $this->forge->addUniqueKey(['user_id', 'role_id'], 'uq_fs_as_user_roles_pair');
        $this->forge->addKey('user_id', false, false, 'idx_fs_as_user_roles_user_id');
        $this->forge->addKey('role_id', false, false, 'idx_fs_as_user_roles_role_id');
        $this->forge->createTable('fs_as_user_roles', true);

        /**
         * fs_as_user_permissions (direct grants/denies override)
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'permission_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'effect' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'allow', // allow|deny
                'null'       => false,
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
        $this->forge->addUniqueKey(['user_id', 'permission_id'], 'uq_fs_as_user_permissions_pair');
        $this->forge->addKey('user_id', false, false, 'idx_fs_as_user_permissions_user_id');
        $this->forge->addKey('permission_id', false, false, 'idx_fs_as_user_permissions_permission_id');
        $this->forge->createTable('fs_as_user_permissions', true);

        /**
         * fs_as_user_sessions
         */
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => false,
            ],
            'user_id' => [
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
        $this->forge->addKey('user_id', false, false, 'idx_fs_as_user_sessions_user_id');
        $this->forge->addKey('expires_at', false, false, 'idx_fs_as_user_sessions_expires_at');
        $this->forge->createTable('fs_as_user_sessions', true);

        /**
         * fs_as_password_reset_tokens
         * token_hash is CHAR(64) to avoid key-length errors
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'token_hash' => [
                'type'       => 'CHAR',
                'constraint' => 64, // SHA-256 hex
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
        $this->forge->addUniqueKey('token_hash', 'uq_fs_as_password_reset_tokens_token_hash');
        $this->forge->addKey('user_id', false, false, 'idx_fs_as_password_reset_tokens_user_id');
        $this->forge->addKey('expires_at', false, false, 'idx_fs_as_password_reset_tokens_expires_at');
        $this->forge->createTable('fs_as_password_reset_tokens', true);

        /**
         * fs_as_approval_workflows
         * Defines an approval template by module/action
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'workflow_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => false,
            ],
            'module_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => false,
            ],
            'entity_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => false,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
            ],
            'created_by' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
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
        $this->forge->addUniqueKey('workflow_key', 'uq_fs_as_approval_workflows_workflow_key');
        $this->forge->addKey('module_key', false, false, 'idx_fs_as_approval_workflows_module_key');
        $this->forge->addKey('entity_key', false, false, 'idx_fs_as_approval_workflows_entity_key');
        $this->forge->createTable('fs_as_approval_workflows', true);

        /**
         * fs_as_approval_workflow_steps
         * Defines ordered steps for a workflow
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'workflow_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'step_no' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'step_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => false,
            ],
            'approver_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'role', // role|user|manager|dynamic
                'null'       => false,
            ],
            'approver_role_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'approver_user_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'min_approvals' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 1,
                'null'     => false,
            ],
            'is_mandatory' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
            ],
            'sla_hours' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['workflow_id', 'step_no'], 'uq_fs_as_approval_workflow_steps_workflow_step');
        $this->forge->addKey('approver_role_id', false, false, 'idx_fs_as_approval_workflow_steps_role');
        $this->forge->addKey('approver_user_id', false, false, 'idx_fs_as_approval_workflow_steps_user');
        $this->forge->createTable('fs_as_approval_workflow_steps', true);

        /**
         * fs_as_approval_requests
         * One request per business entity operation needing approval
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'request_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => false,
            ],
            'workflow_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'module_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => false,
            ],
            'entity_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => false,
            ],
            'entity_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => false,
            ],
            'requested_by' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending', // pending|approved|rejected|cancelled
                'null'       => false,
            ],
            'current_step_no' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 1,
                'null'     => false,
            ],
            'payload_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'completed_at' => [
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('request_key', 'uq_fs_as_approval_requests_request_key');
        $this->forge->addKey(['module_key', 'entity_type', 'entity_id'], false, false, 'idx_fs_as_approval_requests_entity');
        $this->forge->addKey('status', false, false, 'idx_fs_as_approval_requests_status');
        $this->forge->addKey('requested_by', false, false, 'idx_fs_as_approval_requests_requested_by');
        $this->forge->createTable('fs_as_approval_requests', true);

        /**
         * fs_as_approval_request_steps
         * Runtime execution of each step
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'approval_request_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'workflow_step_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'step_no' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending', // pending|approved|rejected|skipped
                'null'       => false,
            ],
            'acted_by' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'acted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'action_note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'due_at' => [
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['approval_request_id', 'step_no'], 'uq_fs_as_approval_request_steps_request_step');
        $this->forge->addKey('workflow_step_id', false, false, 'idx_fs_as_approval_request_steps_workflow_step_id');
        $this->forge->addKey('acted_by', false, false, 'idx_fs_as_approval_request_steps_acted_by');
        $this->forge->addKey('status', false, false, 'idx_fs_as_approval_request_steps_status');
        $this->forge->createTable('fs_as_approval_request_steps', true);

        /**
         * fs_as_approval_comments
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'approval_request_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'commented_by' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'comment' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('approval_request_id', false, false, 'idx_fs_as_approval_comments_request_id');
        $this->forge->addKey('commented_by', false, false, 'idx_fs_as_approval_comments_commented_by');
        $this->forge->createTable('fs_as_approval_comments', true);

        /**
         * fs_as_audit_events (global security/business audit)
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
            'module_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
            ],
            'actor_user_id' => [
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
        $this->forge->addKey('event_key', false, false, 'idx_fs_as_audit_events_event_key');
        $this->forge->addKey('module_key', false, false, 'idx_fs_as_audit_events_module_key');
        $this->forge->addKey('actor_user_id', false, false, 'idx_fs_as_audit_events_actor_user_id');
        $this->forge->addKey('created_at', false, false, 'idx_fs_as_audit_events_created_at');
        $this->forge->createTable('fs_as_audit_events', true);

        /**
         * Foreign keys
         */
        $this->db->query('ALTER TABLE fs_as_role_permissions
            ADD CONSTRAINT fk_fs_as_role_permissions_role
            FOREIGN KEY (role_id) REFERENCES fs_as_roles(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_role_permissions
            ADD CONSTRAINT fk_fs_as_role_permissions_permission
            FOREIGN KEY (permission_id) REFERENCES fs_as_permissions(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_user_roles
            ADD CONSTRAINT fk_fs_as_user_roles_user
            FOREIGN KEY (user_id) REFERENCES fs_as_users(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_user_roles
            ADD CONSTRAINT fk_fs_as_user_roles_role
            FOREIGN KEY (role_id) REFERENCES fs_as_roles(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_user_roles
            ADD CONSTRAINT fk_fs_as_user_roles_assigned_by
            FOREIGN KEY (assigned_by) REFERENCES fs_as_users(id)
            ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_user_permissions
            ADD CONSTRAINT fk_fs_as_user_permissions_user
            FOREIGN KEY (user_id) REFERENCES fs_as_users(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_user_permissions
            ADD CONSTRAINT fk_fs_as_user_permissions_permission
            FOREIGN KEY (permission_id) REFERENCES fs_as_permissions(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_user_permissions
            ADD CONSTRAINT fk_fs_as_user_permissions_assigned_by
            FOREIGN KEY (assigned_by) REFERENCES fs_as_users(id)
            ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_user_sessions
            ADD CONSTRAINT fk_fs_as_user_sessions_user
            FOREIGN KEY (user_id) REFERENCES fs_as_users(id)
            ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_password_reset_tokens
            ADD CONSTRAINT fk_fs_as_password_reset_tokens_user
            FOREIGN KEY (user_id) REFERENCES fs_as_users(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_approval_workflows
            ADD CONSTRAINT fk_fs_as_approval_workflows_created_by
            FOREIGN KEY (created_by) REFERENCES fs_as_users(id)
            ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_approval_workflow_steps
            ADD CONSTRAINT fk_fs_as_approval_workflow_steps_workflow
            FOREIGN KEY (workflow_id) REFERENCES fs_as_approval_workflows(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_approval_workflow_steps
            ADD CONSTRAINT fk_fs_as_approval_workflow_steps_role
            FOREIGN KEY (approver_role_id) REFERENCES fs_as_roles(id)
            ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_approval_workflow_steps
            ADD CONSTRAINT fk_fs_as_approval_workflow_steps_user
            FOREIGN KEY (approver_user_id) REFERENCES fs_as_users(id)
            ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_approval_requests
            ADD CONSTRAINT fk_fs_as_approval_requests_workflow
            FOREIGN KEY (workflow_id) REFERENCES fs_as_approval_workflows(id)
            ON DELETE RESTRICT ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_approval_requests
            ADD CONSTRAINT fk_fs_as_approval_requests_requested_by
            FOREIGN KEY (requested_by) REFERENCES fs_as_users(id)
            ON DELETE RESTRICT ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_approval_request_steps
            ADD CONSTRAINT fk_fs_as_approval_request_steps_request
            FOREIGN KEY (approval_request_id) REFERENCES fs_as_approval_requests(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_approval_request_steps
            ADD CONSTRAINT fk_fs_as_approval_request_steps_workflow_step
            FOREIGN KEY (workflow_step_id) REFERENCES fs_as_approval_workflow_steps(id)
            ON DELETE RESTRICT ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_approval_request_steps
            ADD CONSTRAINT fk_fs_as_approval_request_steps_acted_by
            FOREIGN KEY (acted_by) REFERENCES fs_as_users(id)
            ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_approval_comments
            ADD CONSTRAINT fk_fs_as_approval_comments_request
            FOREIGN KEY (approval_request_id) REFERENCES fs_as_approval_requests(id)
            ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_approval_comments
            ADD CONSTRAINT fk_fs_as_approval_comments_commented_by
            FOREIGN KEY (commented_by) REFERENCES fs_as_users(id)
            ON DELETE RESTRICT ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE fs_as_audit_events
            ADD CONSTRAINT fk_fs_as_audit_events_actor_user
            FOREIGN KEY (actor_user_id) REFERENCES fs_as_users(id)
            ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // drop children first
        $this->forge->dropTable('fs_as_audit_events', true);
        $this->forge->dropTable('fs_as_approval_comments', true);
        $this->forge->dropTable('fs_as_approval_request_steps', true);
        $this->forge->dropTable('fs_as_approval_requests', true);
        $this->forge->dropTable('fs_as_approval_workflow_steps', true);
        $this->forge->dropTable('fs_as_approval_workflows', true);
        $this->forge->dropTable('fs_as_password_reset_tokens', true);
        $this->forge->dropTable('fs_as_user_sessions', true);
        $this->forge->dropTable('fs_as_user_permissions', true);
        $this->forge->dropTable('fs_as_user_roles', true);
        $this->forge->dropTable('fs_as_role_permissions', true);
        $this->forge->dropTable('fs_as_permissions', true);
        $this->forge->dropTable('fs_as_roles', true);
        $this->forge->dropTable('fs_as_users', true);
    }
}
