<?php

namespace App\Controllers;

use App\Models\FsUserModel;

class VendorPortal extends BaseController
{
    public function __construct()
    {
        // Require auth and ensure user is a vendor
        helper(['url', 'form']);
    }

    public function dashboard(): string
    {
        // Only allow subcontractor_vendor role
        $roles = session()->get('user_roles') ?? [];
        if (!in_array('subcontractor_vendor', $roles)) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Unauthorized access.');
        }

        $userId = session()->get('user_id');

        // 1. Fetch Assigned Tasks
        // Assume tasks are assigned via `assigned_to`
        $tasks = (new \App\Models\TaskModel())
            ->where('assigned_to', $userId)
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->orderBy('due_date', 'ASC')
            ->findAll();

        // 2. Fetch Active Purchase Orders
        $pos = (new \App\Models\PurchaseOrderModel())
            ->where('vendor_id', $userId)
            ->whereIn('status', ['Sent', 'Executed'])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // 3. Fetch Pending Bids Logged for this vendor
        // Assuming they created the bid themselves via the portal
        $bids = (new \App\Models\BidModel())
            ->where('created_by', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
            
        $data = [
            'title' => 'Vendor Dashboard',
            'assigned_tasks' => $tasks,
            'active_pos' => $pos,
            'pending_bids' => $bids
        ];

        return view('vendor_portal/dashboard', $data);
    }

    public function pos(): string
    {
        // Only allow subcontractor_vendor role
        $roles = session()->get('user_roles') ?? [];
        if (!in_array('subcontractor_vendor', $roles)) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Unauthorized access.');
        }

        $userId = session()->get('user_id');

        $pos = (new \App\Models\PurchaseOrderModel())
            ->where('vendor_id', $userId)
            ->whereIn('status', ['Sent', 'Executed', 'Void', 'Draft']) // vendors probably shouldn't see draft, but let's just do Sent & Executed
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Purchase Orders',
            'pos' => $pos
        ];

        return view('vendor_portal/pos', $data);
    }

    public function bids(): string
    {
        // Only allow subcontractor_vendor role
        $roles = session()->get('user_roles') ?? [];
        if (!in_array('subcontractor_vendor', $roles)) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Unauthorized access.');
        }

        $userId = session()->get('user_id');

        $bids = (new \App\Models\BidModel())
            ->where('created_by', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Bids & Quotes',
            'bids' => $bids
        ];

        return view('vendor_portal/bids', $data);
    }

    public function tasks(): string
    {
        // Only allow subcontractor_vendor role
        $roles = session()->get('user_roles') ?? [];
        if (!in_array('subcontractor_vendor', $roles)) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Unauthorized access.');
        }

        $userId = session()->get('user_id');

        $tasks = (new \App\Models\TaskModel())
            ->where('assigned_to', $userId)
            ->orderBy('due_date', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Assigned Tasks',
            'tasks' => $tasks
        ];

        return view('vendor_portal/tasks', $data);
    }
}
