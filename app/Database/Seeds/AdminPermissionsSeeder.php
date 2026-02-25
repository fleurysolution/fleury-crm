<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * AdminPermissionsSeeder
 *
 * Ensures admin@bpms.com has the 'admin' role (both old + fs_ tables),
 * grants ALL permissions, and seeds demo settings data.
 */
class AdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // ──────────────────────────────────────────────────
        // 1. Find admin@bpms.com in fs_users
        // ──────────────────────────────────────────────────
        $adminEmail = 'admin@bpms.com';
        $user       = $db->table('fs_users')->where('email', $adminEmail)->get()->getRow();

        if (!$user) {
            // Create admin user if not found
            $db->table('fs_users')->insert([
                'employee_code'     => 'BPMS-0001',
                'email'             => $adminEmail,
                'password_hash'     => password_hash('password123', PASSWORD_DEFAULT),
                'first_name'        => 'System',
                'last_name'         => 'Admin',
                'phone'             => null,
                'avatar_url'        => null,
                'status'            => 'active',
                'email_verified_at' => $now,
                'last_login_at'     => null,
                'created_at'        => $now,
                'updated_at'        => $now,
                'deleted_at'        => null,
            ]);
            $user = $db->table('fs_users')->where('email', $adminEmail)->get()->getRow();
            echo "Created fs_users record for admin@bpms.com\n";
        }

        // ──────────────────────────────────────────────────
        // 2. OLD-STYLE RBAC: roles / permissions / role_permissions / user_roles
        // ──────────────────────────────────────────────────
        $this->seedOldStyleRBAC($db, $user, $now);

        // ──────────────────────────────────────────────────
        // 3. NEW-STYLE RBAC: fs_roles / fs_permissions / fs_role_permissions / fs_user_roles
        // ──────────────────────────────────────────────────
        $this->seedFsStyleRBAC($db, $user, $now);

        // ──────────────────────────────────────────────────
        // 4. Seed demo settings data
        // ──────────────────────────────────────────────────
        $this->seedSettings($db, $now);

        echo "AdminPermissionsSeeder completed successfully.\n";
    }

    // ──────────────────────────────────────────────────────────────────────────
    private function seedOldStyleRBAC($db, $user, string $now): void
    {
        // Tables: roles, permissions, role_permissions, user_roles
        // Ensure admin role exists
        $adminRole = $db->table('roles')->where('slug', 'admin')->get()->getRow();
        if (!$adminRole) {
            $db->table('roles')->insert([
                'name'        => 'Administrator',
                'slug'        => 'admin',
                'description' => 'Full access to all system features.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $adminRole = $db->table('roles')->where('slug', 'admin')->get()->getRow();
        }

        // Ensure all required permissions exist
        $permList = [
            ['name' => 'Manage Settings',   'slug' => 'manage_settings',   'description' => 'Edit system settings'],
            ['name' => 'Manage Team',        'slug' => 'manage_team',       'description' => 'Manage team members'],
            ['name' => 'Manage Roles',       'slug' => 'manage_roles',      'description' => 'Manage roles and permissions'],
            ['name' => 'View Reports',       'slug' => 'view_reports',      'description' => 'View system reports'],
            ['name' => 'Manage Clients',     'slug' => 'manage_clients',    'description' => 'Manage clients'],
            ['name' => 'Manage Invoices',    'slug' => 'manage_invoices',   'description' => 'Manage invoices'],
            ['name' => 'Manage Leads',       'slug' => 'manage_leads',      'description' => 'Manage leads'],
            ['name' => 'Manage Tickets',     'slug' => 'manage_tickets',    'description' => 'Manage support tickets'],
            ['name' => 'Manage Projects',    'slug' => 'manage_projects',   'description' => 'Manage projects'],
            ['name' => 'Manage Estimates',   'slug' => 'manage_estimates',  'description' => 'Manage estimates'],
            ['name' => 'Manage Contracts',   'slug' => 'manage_contracts',  'description' => 'Manage contracts'],
            ['name' => 'Manage Proposals',   'slug' => 'manage_proposals',  'description' => 'Manage proposals'],
        ];

        foreach ($permList as $perm) {
            $exists = $db->table('permissions')->where('slug', $perm['slug'])->get()->getRow();
            if (!$exists) {
                $db->table('permissions')->insert(array_merge($perm, ['created_at' => $now, 'updated_at' => $now]));
            }
        }

        // Assign ALL permissions to admin role
        $allPerms = $db->table('permissions')->get()->getResult();
        foreach ($allPerms as $perm) {
            $exists = $db->table('role_permissions')
                ->where('role_id', $adminRole->id)
                ->where('permission_id', $perm->id)
                ->countAllResults();
            if (!$exists) {
                $db->table('role_permissions')->insert([
                    'role_id'       => $adminRole->id,
                    'permission_id' => $perm->id,
                ]);
            }
        }

        // Assign admin role to admin@bpms.com in user_roles
        $exists = $db->table('user_roles')
            ->where('user_id', $user->id)
            ->where('role_id', $adminRole->id)
            ->countAllResults();
        if (!$exists) {
            $db->table('user_roles')->insert([
                'user_id'    => $user->id,
                'role_id'    => $adminRole->id,
                'created_at' => $now,
            ]);
        }

        echo "Old-style RBAC seeded for admin@bpms.com\n";
    }

    // ──────────────────────────────────────────────────────────────────────────
    private function seedFsStyleRBAC($db, $user, string $now): void
    {
        // Correct table names: fs_as_roles, fs_as_permissions, fs_as_role_permissions, fs_as_user_roles
        $adminRole = $db->table('fs_as_roles')->where('role_key', 'admin')->get()->getRow();
        if (!$adminRole) {
            $db->table('fs_as_roles')->insert([
                'role_key'     => 'admin',
                'display_name' => 'Admin',
                'description'  => 'Full admin access',
                'is_system'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
            $adminRole = $db->table('fs_as_roles')->where('role_key', 'admin')->get()->getRow();
        }

        // Ensure settings-related permission exists in fs_as_permissions
        $settingsPerms = [
            ['permission_key' => 'settings.manage',          'module_key' => 'settings',  'display_name' => 'Manage Settings'],
            ['permission_key' => 'settings.general',         'module_key' => 'settings',  'display_name' => 'General Settings'],
            ['permission_key' => 'settings.email',           'module_key' => 'settings',  'display_name' => 'Email Settings'],
            ['permission_key' => 'settings.rbac',            'module_key' => 'settings',  'display_name' => 'RBAC Management'],
            ['permission_key' => 'settings.approval',        'module_key' => 'settings',  'display_name' => 'Approval Workflows'],
            ['permission_key' => 'iam.users.view',           'module_key' => 'iam',       'display_name' => 'View users'],
            ['permission_key' => 'iam.users.manage',         'module_key' => 'iam',       'display_name' => 'Manage users'],
            ['permission_key' => 'iam.roles.manage',         'module_key' => 'iam',       'display_name' => 'Manage roles'],
            ['permission_key' => 'approval.workflow.manage', 'module_key' => 'approval',  'display_name' => 'Manage workflows'],
            ['permission_key' => 'approval.request.view',    'module_key' => 'approval',  'display_name' => 'View requests'],
            ['permission_key' => 'approval.request.action',  'module_key' => 'approval',  'display_name' => 'Approve/reject'],
            ['permission_key' => 'audit.events.view',        'module_key' => 'audit',     'display_name' => 'View audit events'],
        ];

        foreach ($settingsPerms as $sp) {
            $exists = $db->table('fs_as_permissions')->where('permission_key', $sp['permission_key'])->get()->getRow();
            if (!$exists) {
                $db->table('fs_as_permissions')->insert(array_merge($sp, [
                    'description' => null,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]));
            }
        }

        // Assign ALL fs_as_permissions to admin role
        $allFsPerms = $db->table('fs_as_permissions')->get()->getResult();
        foreach ($allFsPerms as $fp) {
            $exists = $db->table('fs_as_role_permissions')
                ->where('role_id', $adminRole->id)
                ->where('permission_id', $fp->id)
                ->countAllResults();
            if (!$exists) {
                $db->table('fs_as_role_permissions')->insert([
                    'role_id'       => $adminRole->id,
                    'permission_id' => $fp->id,
                    'created_at'    => $now,
                ]);
            }
        }

        // Assign admin role to user in fs_as_user_roles
        $exists = $db->table('fs_as_user_roles')
            ->where('user_id', $user->id)
            ->where('role_id', $adminRole->id)
            ->countAllResults();
        if (!$exists) {
            $db->table('fs_as_user_roles')->insert([
                'user_id'     => $user->id,
                'role_id'     => $adminRole->id,
                'assigned_by' => $user->id,
                'assigned_at' => $now,
            ]);
        }

        echo "fs_as-style RBAC seeded for admin@bpms.com\n";
    }

    // ──────────────────────────────────────────────────────────────────────────
    private function seedSettings($db, string $now): void
    {
        $settings = [
            // General
            ['key' => 'app_title',                         'value' => 'BPMS247 CRM',           'group' => 'general'],
            ['key' => 'company_email',                     'value' => 'info@bpms247.com',       'group' => 'general'],
            ['key' => 'rows_per_page',                     'value' => '25',                     'group' => 'general'],
            ['key' => 'default_language',                  'value' => 'english',                'group' => 'general'],
            ['key' => 'accepted_file_formats',             'value' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip', 'group' => 'general'],
            ['key' => 'show_background_image_in_signin_page', 'value' => '1',                  'group' => 'general'],
            ['key' => 'show_logo_in_signin_page',          'value' => '1',                     'group' => 'general'],
            ['key' => 'enable_rich_text_editor',           'value' => '1',                     'group' => 'general'],
            ['key' => 'scrollbar',                         'value' => '1',                     'group' => 'general'],
            // Email
            ['key' => 'email_sent_from_address',           'value' => 'no-reply@bpms247.com',  'group' => 'email'],
            ['key' => 'email_sent_from_name',              'value' => 'BPMS247 CRM',           'group' => 'email'],
            ['key' => 'email_protocol',                    'value' => 'smtp',                  'group' => 'email'],
            ['key' => 'email_smtp_host',                   'value' => 'smtp.example.com',      'group' => 'email'],
            ['key' => 'email_smtp_port',                   'value' => '587',                   'group' => 'email'],
            ['key' => 'email_smtp_user',                   'value' => '',                      'group' => 'email'],
            ['key' => 'email_smtp_pass',                   'value' => '',                      'group' => 'email'],
            ['key' => 'email_smtp_security_type',          'value' => 'tls',                   'group' => 'email'],
            // Localization
            ['key' => 'timezone',                          'value' => 'Asia/Kolkata',          'group' => 'localization'],
            ['key' => 'date_format',                       'value' => 'd-m-Y',                 'group' => 'localization'],
            ['key' => 'time_format',                       'value' => 'h:i A',                 'group' => 'localization'],
            ['key' => 'first_day_of_week',                 'value' => '1',                     'group' => 'localization'],
            ['key' => 'default_currency',                  'value' => 'INR',                   'group' => 'localization'],
            ['key' => 'currency_symbol',                   'value' => '₹',                     'group' => 'localization'],
            ['key' => 'currency_position',                 'value' => 'left',                  'group' => 'localization'],
            ['key' => 'decimal_separator',                 'value' => '.',                     'group' => 'localization'],
            ['key' => 'no_of_decimals',                    'value' => '2',                     'group' => 'localization'],
            // Invoice
            ['key' => 'invoice_prefix',                    'value' => 'INV-',                  'group' => 'invoice'],
            ['key' => 'invoice_color',                     'value' => '#4a90e2',               'group' => 'invoice'],
            ['key' => 'invoice_footer',                    'value' => 'Thank you for your business.', 'group' => 'invoice'],
            ['key' => 'invoice_style',                     'value' => 'style1',                'group' => 'invoice'],
            ['key' => 'initial_number_of_the_invoice',     'value' => '1001',                  'group' => 'invoice'],
            ['key' => 'invoice_number_format',             'value' => '{PREFIX}{NUMBER}',       'group' => 'invoice'],
            ['key' => 'default_due_date_after_billing_date','value' => '15',                   'group' => 'invoice'],
            // Estimate
            ['key' => 'estimate_prefix',                   'value' => 'EST-',                  'group' => 'estimate'],
            ['key' => 'estimate_color',                    'value' => '#27ae60',               'group' => 'estimate'],
            ['key' => 'initial_number_of_the_estimate',    'value' => '1001',                  'group' => 'estimate'],
            ['key' => 'enable_comments_on_estimates',      'value' => '1',                     'group' => 'estimate'],
            // Contract
            ['key' => 'contract_prefix',                   'value' => 'CTR-',                  'group' => 'contract'],
            ['key' => 'contract_color',                    'value' => '#8e44ad',               'group' => 'contract'],
            ['key' => 'initial_number_of_the_contract',    'value' => '1001',                  'group' => 'contract'],
            // Proposal
            ['key' => 'proposal_prefix',                   'value' => 'PRP-',                  'group' => 'proposal'],
            ['key' => 'proposal_color',                    'value' => '#e67e22',               'group' => 'proposal'],
            ['key' => 'initial_number_of_the_proposal',    'value' => '1001',                  'group' => 'proposal'],
            // Order
            ['key' => 'order_prefix',                      'value' => 'ORD-',                  'group' => 'order'],
            ['key' => 'order_color',                       'value' => '#2980b9',               'group' => 'order'],
            ['key' => 'initial_number_of_the_order',       'value' => '1001',                  'group' => 'order'],
            // Ticket
            ['key' => 'ticket_prefix',                     'value' => 'TKT-',                  'group' => 'ticket'],
            ['key' => 'auto_close_ticket_after',           'value' => '7',                     'group' => 'ticket'],
            ['key' => 'auto_reply_to_tickets',             'value' => '1',                     'group' => 'ticket'],
            ['key' => 'auto_reply_to_tickets_message',     'value' => 'Thank you for contacting us. We will respond within 24 hours.', 'group' => 'ticket'],
            ['key' => 'show_recent_ticket_comments_at_the_top', 'value' => '1',               'group' => 'ticket'],
            // Task
            ['key' => 'project_task_reminder_on_the_day_of_deadline', 'value' => '1',         'group' => 'task'],
            ['key' => 'project_task_deadline_pre_reminder',   'value' => '2',                  'group' => 'task'],
            ['key' => 'project_task_deadline_overdue_reminder','value' => '1',                 'group' => 'task'],
            ['key' => 'enable_recurring_option_for_tasks',     'value' => '1',                 'group' => 'task'],
            // GDPR
            ['key' => 'enable_gdpr',                          'value' => '0',                  'group' => 'gdpr'],
            ['key' => 'allow_clients_to_export_their_data',   'value' => '0',                  'group' => 'gdpr'],
            ['key' => 'clients_can_request_account_removal',  'value' => '0',                  'group' => 'gdpr'],
            ['key' => 'show_terms_and_conditions_in_client_signup_page', 'value' => '0',       'group' => 'gdpr'],
            // Modules
            ['key' => 'module_invoice',        'value' => '1',  'group' => 'module'],
            ['key' => 'module_lead',           'value' => '1',  'group' => 'module'],
            ['key' => 'module_ticket',         'value' => '1',  'group' => 'module'],
            ['key' => 'module_project',        'value' => '1',  'group' => 'module'],
            ['key' => 'module_estimate',       'value' => '1',  'group' => 'module'],
            ['key' => 'module_contract',       'value' => '1',  'group' => 'module'],
            ['key' => 'module_proposal',       'value' => '1',  'group' => 'module'],
            ['key' => 'module_event',          'value' => '1',  'group' => 'module'],
            ['key' => 'module_expense',        'value' => '1',  'group' => 'module'],
            ['key' => 'module_leave',          'value' => '1',  'group' => 'module'],
            ['key' => 'module_attendance',     'value' => '1',  'group' => 'module'],
            ['key' => 'module_message',        'value' => '1',  'group' => 'module'],
            ['key' => 'module_timeline',       'value' => '1',  'group' => 'module'],
            ['key' => 'module_knowledge_base', 'value' => '1',  'group' => 'module'],
            ['key' => 'module_announcement',   'value' => '1',  'group' => 'module'],
            ['key' => 'module_order',          'value' => '1',  'group' => 'module'],
            ['key' => 'module_subscription',   'value' => '0',  'group' => 'module'],
            ['key' => 'module_store',          'value' => '0',  'group' => 'module'],
            ['key' => 'module_gantt',          'value' => '1',  'group' => 'module'],
            ['key' => 'module_reminder',       'value' => '1',  'group' => 'module'],
            ['key' => 'module_file_manager',   'value' => '1',  'group' => 'module'],
            // IP Restriction
            ['key' => 'allowed_ip_addresses',  'value' => '',   'group' => 'security'],
            // Store
            ['key' => 'visitors_can_see_store_before_login',             'value' => '0', 'group' => 'store'],
            ['key' => 'show_payment_option_after_submitting_the_order',  'value' => '1', 'group' => 'store'],
            ['key' => 'accept_order_before_login',                       'value' => '0', 'group' => 'store'],
            // Subscription
            ['key' => 'subscription_prefix',                  'value' => 'SUB-', 'group' => 'subscription'],
            ['key' => 'initial_number_of_the_subscription',   'value' => '1001', 'group' => 'subscription'],
            ['key' => 'enable_stripe_subscription',           'value' => '0',    'group' => 'subscription'],
            // PWA
            ['key' => 'pwa_theme_color',  'value' => '#4a90e2', 'group' => 'pwa'],
            // Push notification
            ['key' => 'enable_push_notification', 'value' => '0', 'group' => 'integration'],
            // Slack
            ['key' => 'enable_slack', 'value' => '0', 'group' => 'integration'],
            // Lead
            ['key' => 'can_create_lead_from_public_form',              'value' => '1', 'group' => 'lead'],
            ['key' => 'enable_embedded_form_to_get_leads',             'value' => '0', 'group' => 'lead'],
            ['key' => 'after_submit_action_of_public_lead_form',       'value' => 'message', 'group' => 'lead'],
            // Client permissions
            ['key' => 'disable_client_login',   'value' => '0', 'group' => 'client'],
            ['key' => 'disable_client_signup',  'value' => '0', 'group' => 'client'],
            ['key' => 'client_can_create_projects', 'value' => '0', 'group' => 'client'],
            ['key' => 'client_can_view_tasks',   'value' => '1', 'group' => 'client'],
        ];

        foreach ($settings as $s) {
            $exists = $db->table('settings')->where('key', $s['key'])->get()->getRow();
            if (!$exists) {
                $db->table('settings')->insert(array_merge($s, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }

        echo "Demo settings data seeded (" . count($settings) . " settings)\n";
    }
}
