<?php

namespace App\Controllers;

class Settings extends BaseController
{
    public function __construct()
    {
        helper('settings');
    }

    public function index()
    {
        return redirect()->to('settings/general');
    }

    public function general()
    {
        $data = [
            'title' => 'General Settings',
            'tab'   => 'general',
        ];
        return view('settings/general', $data);
    }

    public function email()
    {
        $data = [
            'title' => 'Email Settings',
            'tab'   => 'email',
        ];
        return view('settings/email', $data);
    }

    public function invoices()
    {
        $data = ['title' => 'Invoice Settings', 'tab' => 'invoices'];
        return view('settings/invoices/index', $data);
    }

    public function events()
    {
        $data = ['title' => 'Event Settings', 'tab' => 'events'];
        return view('settings/events', $data);
    }

    public function notifications()
    {
        $data = ['title' => 'Notification Settings', 'tab' => 'notifications'];
        return view('settings/notifications/index', $data);
    }

    public function modules()
    {
        $data = ['title' => 'Module Settings', 'tab' => 'modules'];
        return view('settings/modules', $data);
    }

    public function cron_job()
    {
        $data = ['title' => 'Cron Job', 'tab' => 'cron_job'];
        return view('settings/cron_job', $data);
    }

    public function integration()
    {
        $data = ['title' => 'Integration Settings', 'tab' => 'integration'];
        return view('settings/integration/index', $data);
    }

    public function tickets()
    {
        $data = ['title' => 'Ticket Settings', 'tab' => 'tickets'];
        return view('settings/tickets/index', $data);
    }

    public function tasks()
    {
        $data = ['title' => 'Task Settings', 'tab' => 'tasks'];
        return view('settings/tasks', $data);
    }

    public function client_permissions()
    {
        $data = ['title' => 'Client Permissions', 'tab' => 'client_permissions'];
        return view('settings/client_permissions', $data);
    }

    public function ip_restriction()
    {
        $data = ['title' => 'IP Restriction', 'tab' => 'ip_restriction'];
        return view('settings/ip_restriction', $data);
    }

    public function db_backup()
    {
        $data = ['title' => 'Database Backup', 'tab' => 'db_backup'];
        return view('settings/db_backup', $data);
    }

    public function save()
    {
        $post = $this->request->getPost();
        $group = $this->request->getPost('setting_group') ?? 'general';
        
        // Handle file uploads if any (generic)
        // ...

        foreach ($post as $key => $value) {
            if ($key !== 'setting_group' && $key !== 'csrf_test_name') {
                update_setting($key, $value, $group);
            }
        }

        return redirect()->back()->with('message', 'Settings saved successfully.');
    }
}
