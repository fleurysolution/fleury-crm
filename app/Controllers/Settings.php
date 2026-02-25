<?php

namespace App\Controllers;

use App\Models\SettingModel;
use App\Models\NotificationSettingModel;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\FsRoleModel;

/**
 * Settings Controller – full CI4 refactor of old Settings.php
 *
 * All routes are protected by permission:manage_settings (see Routes.php).
 * Each section has a GET page method + POST save method.
 */
class Settings extends BaseController
{
    protected SettingModel             $settingModel;
    protected NotificationSettingModel $notifModel;
    protected RoleModel                $roleModel;
    protected PermissionModel          $permissionModel;

    public function __construct()
    {
        helper(['url', 'form', 'settings']);

        $this->settingModel    = new SettingModel();
        $this->notifModel      = new NotificationSettingModel();
        $this->roleModel       = new RoleModel();
        $this->permissionModel = new PermissionModel();
    }

    // =========================================================================
    // INDEX
    // =========================================================================

    public function index()
    {
        return redirect()->to(site_url('settings/general'));
    }

    // =========================================================================
    // GENERAL
    // =========================================================================

    public function general()
    {
        return view('settings/general', [
            'title' => 'General Settings',
            'tab'   => 'general',
        ]);
    }

    public function save_general_settings()
    {
        $keys = [
            'app_title', 'company_email', 'rows_per_page', 'default_language',
            'accepted_file_formats', 'show_background_image_in_signin_page',
            'show_logo_in_signin_page', 'enable_rich_text_editor', 'scrollbar',
            'timezone', 'date_format',
        ];

        foreach ($keys as $key) {
            $value = $this->request->getPost($key);
            $this->settingModel->setValue($key, $value ?? '', 'general');
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Settings saved.']);
    }

    // =========================================================================
    // EMAIL
    // =========================================================================

    public function email()
    {
        return view('settings/email', [
            'title' => 'Email Settings',
            'tab'   => 'email',
            'settings' => $this->settingModel->getGroup('email'),
        ]);
    }

    public function save_email_settings()
    {
        $keys = [
            'email_sent_from_address', 'email_sent_from_name', 'email_protocol',
            'email_smtp_host', 'email_smtp_port', 'email_smtp_user',
            'email_smtp_pass', 'email_smtp_security_type',
        ];

        foreach ($keys as $key) {
            $value = $this->request->getPost($key) ?? '';
            if ($key === 'email_smtp_pass' && $value === '******') {
                continue; // don't overwrite masked password
            }
            $this->settingModel->setValue($key, $value, 'email');
        }

        $testTo = $this->request->getPost('send_test_mail_to');
        if ($testTo) {
            return $this->response->setJSON(['success' => true, 'message' => 'Settings saved. Test email feature requires mail library setup.']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Email settings saved.']);
    }

    // =========================================================================
    // IP RESTRICTION
    // =========================================================================

    public function ip_restriction()
    {
        return view('settings/ip_restriction', [
            'title' => 'IP Restriction',
            'tab'   => 'ip_restriction',
        ]);
    }

    public function save_ip_settings()
    {
        $this->settingModel->setValue('allowed_ip_addresses', $this->request->getPost('allowed_ip_addresses') ?? '', 'security');
        return $this->response->setJSON(['success' => true, 'message' => 'IP settings saved.']);
    }

    // =========================================================================
    // DB BACKUP
    // =========================================================================

    public function db_backup()
    {
        return view('settings/db_backup', [
            'title' => 'Database Backup',
            'tab'   => 'db_backup',
        ]);
    }

    // =========================================================================
    // CLIENT PERMISSIONS
    // =========================================================================

    public function client_permissions()
    {
        return view('settings/client_permissions', [
            'title' => 'Client Permissions',
            'tab'   => 'client_permissions',
        ]);
    }

    public function save_client_settings()
    {
        $keys = [
            'disable_client_login', 'disable_client_signup', 'client_message_users',
            'hidden_client_menus', 'client_can_create_projects', 'client_can_create_tasks',
            'client_can_edit_tasks', 'client_can_assign_tasks', 'client_can_view_tasks',
            'client_can_comment_on_tasks', 'client_can_view_project_files',
            'client_can_add_project_files', 'client_can_comment_on_files',
            'client_can_delete_own_files_in_project', 'client_can_view_milestones',
            'client_can_view_overview', 'client_can_view_gantt', 'client_can_view_files',
            'client_can_add_files', 'client_can_edit_projects', 'client_can_view_activity',
            'client_message_own_contacts', 'disable_user_invitation_option_by_clients',
            'client_can_access_store', 'verify_email_before_client_signup',
            'client_can_create_reminders', 'client_can_access_notes',
        ];

        foreach ($keys as $key) {
            $this->settingModel->setValue($key, $this->request->getPost($key) ?? '', 'client');
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Client permissions saved.']);
    }

    // =========================================================================
    // INVOICES
    // =========================================================================

    public function invoices()
    {
        return view('settings/invoices/index', [
            'title'    => 'Invoice Settings',
            'tab'      => 'invoices',
            'settings' => $this->settingModel->getGroup('invoice'),
        ]);
    }

    public function save_invoice_settings()
    {
        $keys = [
            'invoice_prefix', 'invoice_color', 'invoice_item_list_background',
            'invoice_footer', 'invoice_style', 'initial_number_of_the_invoice',
            'invoice_number_format', 'year_based_on', 'reset_invoice_number_every_year',
            'enable_background_image_for_invoice_pdf', 'set_invoice_pdf_background_only_on_first_page',
            'default_due_date_after_billing_date', 'allow_partial_invoice_payment_from_clients',
            'client_can_pay_invoice_without_login', 'enable_invoice_lock_state',
            'send_invoice_due_pre_reminder', 'send_invoice_due_after_reminder',
            'send_bcc_to',
        ];

        foreach ($keys as $key) {
            $this->settingModel->setValue($key, $this->request->getPost($key) ?? '', 'invoice');
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Invoice settings saved.']);
    }

    // =========================================================================
    // EVENTS (Google Calendar)
    // =========================================================================

    public function events()
    {
        return view('settings/events', [
            'title' => 'Event Settings',
            'tab'   => 'events',
        ]);
    }

    public function save_event_settings()
    {
        $keys = ['enable_google_calendar_api', 'google_calendar_client_id', 'google_calendar_client_secret'];
        foreach ($keys as $key) {
            $this->settingModel->setValue($key, $this->request->getPost($key) ?? '', 'integration');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Event settings saved.']);
    }

    // =========================================================================
    // NOTIFICATIONS
    // =========================================================================

    public function notifications()
    {
        $categories = [
            'announcement', 'client', 'contract', 'event', 'estimate',
            'invoice', 'leave', 'lead', 'message', 'order',
            'project', 'proposal', 'subscription', 'ticket', 'timeline', 'general_task',
        ];

        $categoryOptions = [['id' => '', 'text' => '-- All Categories --']];
        foreach ($categories as $cat) {
            $categoryOptions[] = ['id' => $cat, 'text' => ucfirst(str_replace('_', ' ', $cat))];
        }

        return view('settings/notifications/index', [
            'title'               => 'Notification Settings',
            'tab'                 => 'notifications',
            'categories_dropdown' => json_encode($categoryOptions),
        ]);
    }

    public function notification_settings_list_data()
    {
        $category  = $this->request->getPost('category');
        $options   = $category ? ['category' => $category] : [];
        $list_data = $this->notifModel->getDetails($options)->getResult();
        $result    = [];
        foreach ($list_data as $data) {
            $result[] = $this->_makeNotificationRow($data);
        }
        return $this->response->setJSON(['data' => $result]);
    }

    private function _makeNotificationRow(object $data): array
    {
        $yes = '<i class="fa-solid fa-check-circle text-success"></i>';
        $no  = '<i class="fa-solid fa-circle text-muted opacity-25"></i>';

        $notifyTo = '';
        if ($data->notify_to_terms) {
            foreach (explode(',', $data->notify_to_terms) as $term) {
                if ($term) {
                    $notifyTo .= '<span class="badge bg-secondary me-1">' . esc(ucfirst(str_replace('_', ' ', $term))) . '</span>';
                }
            }
        }

        return [
            $data->sort,
            esc(ucfirst(str_replace('_', ' ', $data->event))),
            $notifyTo ?: '<span class="text-muted">—</span>',
            '<span class="badge bg-info">' . esc(ucfirst($data->category)) . '</span>',
            $data->enable_email ? $yes : $no,
            $data->enable_web   ? $yes : $no,
            $data->enable_slack ? $yes : $no,
            '<button class="btn btn-sm btn-outline-primary edit-notification-btn" data-id="' . $data->id . '">'
            . '<i class="fa-solid fa-edit"></i></button>',
        ];
    }

    public function notification_modal_form()
    {
        $id   = (int)$this->request->getPost('id');
        $info = $this->notifModel->getDetails(['id' => $id])->getRow();
        if (!$info) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Not found']);
        }

        $terms = $info->notify_to_terms ? explode(',', $info->notify_to_terms) : [];

        return view('settings/notifications/modal_form', [
            'model_info'       => $info,
            'selected_terms'   => $terms,
            'available_terms'  => $this->notifModel->notifyToTerms(),
        ]);
    }

    public function save_notification_settings()
    {
        $id = (int)$this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid ID.']);
        }

        $terms     = $this->notifModel->notifyToTerms();
        $notifyStr = '';
        foreach ($terms as $term) {
            if (!in_array($term, ['team', 'team_members']) && $this->request->getPost($term)) {
                $notifyStr .= ($notifyStr ? ',' : '') . $term;
            }
        }

        $data = [
            'enable_web'             => $this->request->getPost('enable_web')    ? 1 : 0,
            'enable_email'           => $this->request->getPost('enable_email')  ? 1 : 0,
            'enable_slack'           => $this->request->getPost('enable_slack')  ? 1 : 0,
            'notify_to_terms'        => $notifyStr,
            'notify_to_team'         => $this->request->getPost('team')          ?? '',
            'notify_to_team_members' => $this->request->getPost('team_members')  ?? '',
        ];

        if ($this->notifModel->saveById($data, $id)) {
            $row = $this->notifModel->getDetails(['id' => $id])->getRow();
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification settings updated.',
                'data'    => $this->_makeNotificationRow($row),
                'id'      => $id,
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to save.']);
    }

    // =========================================================================
    // MODULES
    // =========================================================================

    public function modules()
    {
        return view('settings/modules', [
            'title' => 'Module Settings',
            'tab'   => 'modules',
        ]);
    }

    public function save_module_settings()
    {
        $modules = [
            'module_timeline', 'module_event', 'module_todo', 'module_note',
            'module_message', 'module_chat', 'module_invoice', 'module_expense',
            'module_attendance', 'module_leave', 'module_estimate', 'module_estimate_request',
            'module_lead', 'module_ticket', 'module_announcement', 'module_project_timesheet',
            'module_help', 'module_knowledge_base', 'module_gantt', 'module_order',
            'module_proposal', 'module_contract', 'module_file_manager', 'module_reminder',
            'module_subscription',
        ];
        foreach ($modules as $key) {
            $this->settingModel->setValue($key, $this->request->getPost($key) ?? '0', 'module');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Module settings saved.']);
    }

    // =========================================================================
    // CRON JOB
    // =========================================================================

    public function cron_job()
    {
        return view('settings/cron_job', [
            'title' => 'Cron Job',
            'tab'   => 'cron_job',
        ]);
    }

    // =========================================================================
    // INTEGRATION HUB
    // =========================================================================

    public function integration()
    {
        return view('settings/integration/index', [
            'title' => 'Integrations',
            'tab'   => 'integration',
        ]);
    }

    // reCAPTCHA
    public function re_captcha()
    {
        return view('settings/integration/re_captcha', [
            'title' => 'reCAPTCHA Settings',
            'tab'   => 'integration',
        ]);
    }

    public function save_re_captcha_settings()
    {
        foreach (['re_captcha_protocol', 're_captcha_site_key', 're_captcha_secret_key'] as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'integration');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'reCAPTCHA settings saved.']);
    }

    // Push Notification
    public function push_notification()
    {
        return view('settings/integration/push_notification/index', [
            'title' => 'Push Notification',
            'tab'   => 'integration',
        ]);
    }

    public function save_push_notification_settings()
    {
        foreach (['enable_push_notification', 'pusher_app_id', 'pusher_key', 'pusher_secret', 'pusher_cluster', 'enable_chat_via_pusher'] as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'integration');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Push notification settings saved.']);
    }

    // Slack
    public function slack()
    {
        return view('settings/integration/slack', [
            'title' => 'Slack Integration',
            'tab'   => 'integration',
        ]);
    }

    public function save_slack_settings()
    {
        foreach (['enable_slack', 'slack_webhook_url', 'slack_dont_send_any_projects'] as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'integration');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Slack settings saved.']);
    }

    // Google Drive
    public function google_drive()
    {
        return view('settings/integration/google_drive', [
            'title' => 'Google Drive',
            'tab'   => 'integration',
        ]);
    }

    public function save_google_drive_settings()
    {
        foreach (['enable_google_drive_api_to_upload_file', 'google_drive_client_id', 'google_drive_client_secret'] as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'integration');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Google Drive settings saved.']);
    }

    // GitHub
    public function github()
    {
        return view('settings/integration/github', [
            'title' => 'GitHub Integration',
            'tab'   => 'integration',
        ]);
    }

    public function save_github_settings()
    {
        $this->settingModel->setValue('enable_github_commit_logs_in_tasks', $this->request->getPost('enable_github_commit_logs_in_tasks') ?? '', 'integration');
        return $this->response->setJSON(['success' => true, 'message' => 'GitHub settings saved.']);
    }

    // Bitbucket
    public function bitbucket()
    {
        return view('settings/integration/bitbucket', [
            'title' => 'Bitbucket Integration',
            'tab'   => 'integration',
        ]);
    }

    public function save_bitbucket_settings()
    {
        $this->settingModel->setValue('enable_bitbucket_commit_logs_in_tasks', $this->request->getPost('enable_bitbucket_commit_logs_in_tasks') ?? '', 'integration');
        return $this->response->setJSON(['success' => true, 'message' => 'Bitbucket settings saved.']);
    }

    // TinyMCE
    public function tinymce()
    {
        return view('settings/integration/tinymce', [
            'title' => 'TinyMCE',
            'tab'   => 'integration',
        ]);
    }

    public function save_tinymce_settings()
    {
        foreach (['enable_tinymce', 'tinymce_api_key'] as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'integration');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'TinyMCE settings saved.']);
    }

    // =========================================================================
    // TICKETS
    // =========================================================================

    public function tickets()
    {
        return view('settings/tickets/index', [
            'title' => 'Ticket Settings',
            'tab'   => 'tickets',
        ]);
    }

    public function save_ticket_settings()
    {
        $keys = [
            'show_recent_ticket_comments_at_the_top', 'ticket_prefix',
            'project_reference_in_tickets', 'auto_close_ticket_after',
            'auto_reply_to_tickets', 'auto_reply_to_tickets_message',
            'enable_embedded_form_to_get_tickets',
        ];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'ticket');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Ticket settings saved.']);
    }

    public function imap_settings()
    {
        return view('settings/tickets/imap_settings', [
            'title' => 'IMAP Settings',
            'tab'   => 'tickets',
        ]);
    }

    public function save_imap_settings()
    {
        $keys = [
            'enable_email_piping', 'create_tickets_only_by_registered_emails',
            'imap_encryption', 'imap_host', 'imap_port', 'imap_email',
            'imap_password', 'imap_type',
        ];
        foreach ($keys as $k) {
            $value = $this->request->getPost($k) ?? '';
            if ($k === 'imap_password' && $value === '******') {
                continue;
            }
            $this->settingModel->setValue($k, $value, 'ticket');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'IMAP settings saved.']);
    }

    // =========================================================================
    // TASKS
    // =========================================================================

    public function tasks()
    {
        $kanbanValues    = ['id', 'project_name', 'client_name', 'parent_task'];
        $kanbanDropdown  = [];
        foreach ($kanbanValues as $v) {
            $kanbanDropdown[] = ['id' => $v, 'text' => ucfirst(str_replace('_', ' ', $v))];
        }

        return view('settings/tasks', [
            'title'                 => 'Task Settings',
            'tab'                   => 'tasks',
            'show_in_kanban_dropdown' => json_encode($kanbanDropdown),
        ]);
    }

    public function save_task_settings()
    {
        $keys = [
            'project_task_reminder_on_the_day_of_deadline',
            'project_task_deadline_pre_reminder',
            'project_task_deadline_overdue_reminder',
            'enable_recurring_option_for_tasks',
            'task_point_range', 'create_recurring_tasks_before',
            'show_in_kanban', 'show_time_with_task_start_date_and_deadline',
        ];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'task');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Task settings saved.']);
    }

    // =========================================================================
    // ESTIMATES
    // =========================================================================

    public function estimates()
    {
        return view('settings/estimates', [
            'title'    => 'Estimate Settings',
            'tab'      => 'estimates',
            'settings' => $this->settingModel->getGroup('estimate'),
        ]);
    }

    public function save_estimate_settings()
    {
        $keys = [
            'estimate_prefix', 'estimate_color', 'estimate_footer',
            'send_estimate_bcc_to', 'initial_number_of_the_estimate',
            'create_new_projects_automatically_when_estimates_gets_accepted',
            'enable_comments_on_estimates', 'show_most_recent_estimate_comments_at_the_top',
            'add_signature_option_on_accepting_estimate', 'enable_estimate_lock_state',
        ];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'estimate');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Estimate settings saved.']);
    }

    // =========================================================================
    // ORDERS
    // =========================================================================

    public function orders()
    {
        return view('settings/orders', [
            'title'    => 'Order Settings',
            'tab'      => 'orders',
            'settings' => $this->settingModel->getGroup('order'),
        ]);
    }

    public function save_order_settings()
    {
        $keys = ['order_prefix', 'order_color', 'order_footer', 'initial_number_of_the_order', 'order_tax_id', 'order_tax_id2'];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'order');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Order settings saved.']);
    }

    // =========================================================================
    // PROJECTS
    // =========================================================================

    public function projects()
    {
        $tabs = [
            'overview', 'tasks_list', 'tasks_kanban', 'milestones', 'gantt',
            'notes', 'files', 'comments', 'timesheets', 'invoices', 'payments',
            'expenses', 'contracts', 'tickets',
        ];
        $tabsDropdown = [];
        foreach ($tabs as $t) {
            $tabsDropdown[] = ['id' => $t, 'text' => ucfirst(str_replace('_', ' ', $t))];
        }

        return view('settings/projects', [
            'title'                  => 'Project Settings',
            'tab'                    => 'projects',
            'project_tabs_dropdown'  => json_encode($tabsDropdown),
        ]);
    }

    public function save_projects_settings()
    {
        $this->settingModel->setValue('project_tab_order', $this->request->getPost('project_tab_order') ?? '', 'project');
        return $this->response->setJSON(['success' => true, 'message' => 'Project settings saved.']);
    }

    // =========================================================================
    // CONTRACTS
    // =========================================================================

    public function contracts()
    {
        return view('settings/contracts', [
            'title'    => 'Contract Settings',
            'tab'      => 'contracts',
            'settings' => $this->settingModel->getGroup('contract'),
        ]);
    }

    public function save_contract_settings()
    {
        $keys = [
            'contract_prefix', 'contract_color', 'send_contract_bcc_to',
            'initial_number_of_the_contract', 'add_signature_option_on_accepting_contract',
            'default_contract_template', 'add_signature_option_for_team_members',
            'enable_contract_lock_state', 'disable_contract_pdf_for_clients',
        ];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'contract');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Contract settings saved.']);
    }

    // =========================================================================
    // LEADS
    // =========================================================================

    public function leads()
    {
        return view('settings/leads', [
            'title' => 'Lead Settings',
            'tab'   => 'leads',
        ]);
    }

    public function save_lead_settings()
    {
        $keys = [
            'can_create_lead_from_public_form', 'enable_embedded_form_to_get_leads',
            'after_submit_action_of_public_lead_form',
            'after_submit_action_of_public_lead_form_redirect_url',
            'hidden_fields_on_lead_embedded_form',
        ];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'lead');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Lead settings saved.']);
    }

    // =========================================================================
    // PROPOSALS
    // =========================================================================

    public function proposals()
    {
        return view('settings/proposals', [
            'title'    => 'Proposal Settings',
            'tab'      => 'proposals',
            'settings' => $this->settingModel->getGroup('proposal'),
        ]);
    }

    public function save_proposal_settings()
    {
        $keys = [
            'proposal_prefix', 'proposal_color', 'send_proposal_bcc_to',
            'initial_number_of_the_proposal', 'add_signature_option_on_accepting_proposal',
            'default_proposal_template', 'enable_proposal_lock_state',
            'enable_comments_on_proposals', 'disable_proposal_pdf_for_clients',
        ];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'proposal');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Proposal settings saved.']);
    }

    // =========================================================================
    // LOCALIZATION
    // =========================================================================

    public function localization()
    {
        $timezones = \DateTimeZone::listIdentifiers();
        $tzList    = [];
        foreach ($timezones as $tz) {
            $tzList[$tz] = $tz;
        }

        $currencies = [
            'USD' => 'US Dollar ($)',  'EUR' => 'Euro (€)',       'GBP' => 'British Pound (£)',
            'INR' => 'Indian Rupee (₹)', 'AED' => 'AED Dirham',   'SAR' => 'Saudi Riyal',
            'PKR' => 'Pakistani Rupee', 'BDT' => 'Bangladeshi Taka', 'MYR' => 'Malaysian Ringgit',
            'SGD' => 'Singapore Dollar', 'CAD' => 'Canadian Dollar', 'AUD' => 'Australian Dollar',
        ];

        return view('settings/localization', [
            'title'             => 'Localization',
            'tab'               => 'localization',
            'timezone_dropdown' => $tzList,
            'currency_dropdown' => $currencies,
            'settings'          => $this->settingModel->getGroup('localization'),
        ]);
    }

    public function save_localization_settings()
    {
        $keys = [
            'language', 'timezone', 'date_format', 'time_format',
            'first_day_of_week', 'weekends', 'default_currency',
            'currency_symbol', 'currency_position', 'decimal_separator', 'no_of_decimals',
        ];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'localization');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Localization settings saved.']);
    }

    // =========================================================================
    // STORE
    // =========================================================================

    public function store()
    {
        return view('settings/store', [
            'title' => 'Store Settings',
            'tab'   => 'store',
        ]);
    }

    public function save_store_settings()
    {
        $keys = [
            'visitors_can_see_store_before_login',
            'show_payment_option_after_submitting_the_order',
            'accept_order_before_login',
            'order_status_after_payment',
        ];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'store');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Store settings saved.']);
    }

    // =========================================================================
    // GDPR
    // =========================================================================

    public function gdpr()
    {
        return view('settings/gdpr', [
            'title' => 'GDPR Settings',
            'tab'   => 'gdpr',
        ]);
    }

    public function save_gdpr_settings()
    {
        $keys = [
            'enable_gdpr', 'allow_clients_to_export_their_data',
            'clients_can_request_account_removal',
            'show_terms_and_conditions_in_client_signup_page',
            'gdpr_terms_and_conditions_link',
        ];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'gdpr');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'GDPR settings saved.']);
    }

    // =========================================================================
    // FOOTER
    // =========================================================================

    public function footer()
    {
        $raw          = $this->settingModel->getValue('footer_menus', '');
        $footerMenus  = @unserialize($raw);
        if (!is_array($footerMenus)) {
            $footerMenus = [];
        }

        return view('settings/footer/index', [
            'title'        => 'Footer Settings',
            'tab'          => 'footer',
            'footer_menus' => $footerMenus,
        ]);
    }

    public function save_footer_settings()
    {
        $keys = ['enable_footer', 'footer_copyright_text'];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'general');
        }
        $menus = json_decode($this->request->getPost('footer_menus') ?? '[]', true);
        $this->settingModel->setValue('footer_menus', serialize($menus ?: []), 'general');
        return $this->response->setJSON(['success' => true, 'message' => 'Footer settings saved.']);
    }

    // =========================================================================
    // TOP MENU
    // =========================================================================

    public function top_menu()
    {
        $raw      = $this->settingModel->getValue('top_menus', '');
        $topMenus = @unserialize($raw);
        if (!is_array($topMenus)) {
            $topMenus = [];
        }

        return view('settings/top_menu/index', [
            'title'     => 'Top Menu Settings',
            'tab'       => 'top_menu',
            'top_menus' => $topMenus,
        ]);
    }

    public function save_top_menu_settings()
    {
        $this->settingModel->setValue('enable_top_menu', $this->request->getPost('enable_top_menu') ?? '', 'general');
        $menus = json_decode($this->request->getPost('top_menus') ?? '[]', true);
        $this->settingModel->setValue('top_menus', serialize($menus ?: []), 'general');
        return $this->response->setJSON(['success' => true, 'message' => 'Top menu settings saved.']);
    }

    // =========================================================================
    // SUBSCRIPTIONS
    // =========================================================================

    public function subscriptions()
    {
        return view('settings/subscriptions/index', [
            'title'    => 'Subscription Settings',
            'tab'      => 'subscriptions',
            'settings' => $this->settingModel->getGroup('subscription'),
        ]);
    }

    public function save_subscription_settings()
    {
        $keys = ['subscription_prefix', 'initial_number_of_the_subscription', 'enable_stripe_subscription'];
        foreach ($keys as $k) {
            $this->settingModel->setValue($k, $this->request->getPost($k) ?? '', 'subscription');
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Subscription settings saved.']);
    }

    // =========================================================================
    // PWA
    // =========================================================================

    public function pwa()
    {
        return view('settings/pwa/index', [
            'title' => 'PWA Settings',
            'tab'   => 'pwa',
        ]);
    }

    public function save_pwa_settings()
    {
        $this->settingModel->setValue('pwa_theme_color', $this->request->getPost('pwa_theme_color') ?? '#4a90e2', 'pwa');
        return $this->response->setJSON(['success' => true, 'message' => 'PWA settings saved.']);
    }

    // =========================================================================
    // RBAC – Roles & Permissions Management
    // =========================================================================

    public function rbac()
    {
        $roles       = $this->roleModel->findAll();
        $permissions = $this->permissionModel->findAll();
        $db          = \Config\Database::connect();

        // Build role → permissions map
        $rolePermMap = [];
        $rpRows      = $db->table('role_permissions')->get()->getResult();
        foreach ($rpRows as $rp) {
            $rolePermMap[$rp->role_id][] = $rp->permission_id;
        }

        return view('settings/rbac/index', [
            'title'        => 'RBAC: Roles & Permissions',
            'tab'          => 'rbac',
            'roles'        => $roles,
            'permissions'  => $permissions,
            'rolePermMap'  => $rolePermMap,
        ]);
    }

    public function save_role()
    {
        $id          = (int)$this->request->getPost('id');
        $name        = trim((string)$this->request->getPost('name'));
        $slug        = trim((string)$this->request->getPost('slug'));
        $description = trim((string)$this->request->getPost('description'));
        $perms       = $this->request->getPost('permissions') ?? [];

        if (!$name || !$slug) {
            return $this->response->setJSON(['success' => false, 'message' => 'Name and slug are required.']);
        }

        $db = \Config\Database::connect();

        if ($id) {
            $this->roleModel->update($id, compact('name', 'slug', 'description'));
            $roleId = $id;
            // Re-assign permissions
            $db->table('role_permissions')->where('role_id', $roleId)->delete();
        } else {
            $roleId = $this->roleModel->insert(compact('name', 'slug', 'description'));
        }

        if (!empty($perms)) {
            $rows = [];
            foreach ($perms as $pId) {
                $rows[] = ['role_id' => $roleId, 'permission_id' => (int)$pId];
            }
            $db->table('role_permissions')->insertBatch($rows);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Role saved.', 'id' => $roleId]);
    }

    public function delete_role(int $id)
    {
        $this->roleModel->delete($id);
        return $this->response->setJSON(['success' => true, 'message' => 'Role deleted.']);
    }

    // =========================================================================
    // APPROVAL WORKFLOWS
    // =========================================================================

    public function approval_workflows()
    {
        $db        = \Config\Database::connect();
        $workflows = $db->table('fs_as_approval_workflows')->get()->getResult();

        return view('settings/approval_workflows/index', [
            'title'     => 'Approval Workflows',
            'tab'       => 'approval_workflows',
            'workflows' => $workflows,
        ]);
    }

    public function save_workflow()
    {
        $db   = \Config\Database::connect();
        $id   = (int)$this->request->getPost('id');
        $now  = date('Y-m-d H:i:s');

        $data = [
            'name'        => trim((string)$this->request->getPost('name')),
            'description' => trim((string)$this->request->getPost('description')),
            'module_key'  => trim((string)$this->request->getPost('module_key')),
            'is_active'   => (int)($this->request->getPost('is_active') ?? 1),
            'updated_at'  => $now,
        ];

        if (!$data['name']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Workflow name is required.']);
        }

        if ($id) {
            $db->table('fs_as_approval_workflows')->where('id', $id)->update($data);
        } else {
            $data['created_at'] = $now;
            $db->table('fs_as_approval_workflows')->insert($data);
            $id = $db->insertID();
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Workflow saved.', 'id' => $id]);
    }

    public function delete_workflow(int $id)
    {
        $db = \Config\Database::connect();
        $db->table('fs_as_approval_workflows')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success' => true, 'message' => 'Workflow deleted.']);
    }

    // =========================================================================
    // LEGACY GENERIC SAVE (used by older views)
    // =========================================================================

    public function save()
    {
        $post  = $this->request->getPost();
        $group = $this->request->getPost('setting_group') ?? 'general';

        foreach ($post as $key => $value) {
            if (in_array($key, ['setting_group', csrf_token()])) {
                continue;
            }
            $this->settingModel->setValue($key, $value ?? '', $group);
        }

        return redirect()->back()->with('message', 'Settings saved successfully.');
    }
}
