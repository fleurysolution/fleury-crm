<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseAppController
{
    /**
     * GET /notifications — full notification inbox page
     */
    public function index(): string
    {
        $nm    = new NotificationModel();
        $items = $nm->forUser($this->currentUser['id'], 60);
        $nm->markAllRead($this->currentUser['id']);   // mark all read on page open

        return $this->render('notifications/index', [
            'notifications' => $items,
        ]);
    }

    /**
     * GET /notifications/dropdown — AJAX partial for bell dropdown (returns JSON list)
     */
    public function dropdown(): \CodeIgniter\HTTP\Response
    {
        $nm    = new NotificationModel();
        $items = $nm->forUser($this->currentUser['id'], 10);
        $count = $nm->unreadCount($this->currentUser['id']);

        return $this->response->setJSON([
            'count' => $count,
            'items' => $items,
        ]);
    }

    /**
     * POST /notifications/:id/read — mark one read, return JSON
     */
    public function markRead(int $id): \CodeIgniter\HTTP\Response
    {
        (new NotificationModel())->markRead($id, $this->currentUser['id']);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /notifications/read-all — mark all read
     */
    public function readAll(): \CodeIgniter\HTTP\Response
    {
        (new NotificationModel())->markAllRead($this->currentUser['id']);
        return $this->response->setJSON(['success' => true, 'count' => 0]);
    }

    /**
     * GET /notifications/count — unread count for polling
     */
    public function count(): \CodeIgniter\HTTP\Response
    {
        $count = (new NotificationModel())->unreadCount($this->currentUser['id']);
        return $this->response->setJSON(['count' => $count]);
    }
}
