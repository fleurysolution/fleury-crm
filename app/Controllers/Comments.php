<?php

namespace App\Controllers;

use App\Models\CommentModel;
use App\Models\NotificationModel;

class Comments extends BaseAppController
{
    /**
     * GET /comments?entity_type=task&entity_id=5
     * Returns JSON comment thread for an entity.
     */
    public function index(): \CodeIgniter\HTTP\Response
    {
        $entityType = $this->request->getGet('entity_type') ?? '';
        $entityId   = (int)($this->request->getGet('entity_id') ?? 0);

        if (!$entityType || !$entityId) {
            return $this->response->setJSON([]);
        }

        $comments = (new CommentModel())->forEntity($entityType, $entityId);
        return $this->response->setJSON($comments);
    }

    /**
     * POST /comments — post a comment
     */
    public function store(): \CodeIgniter\HTTP\Response
    {
        $body       = trim($this->request->getPost('body') ?? '');
        $entityType = $this->request->getPost('entity_type') ?? '';
        $entityId   = (int)($this->request->getPost('entity_id') ?? 0);
        $projectId  = (int)($this->request->getPost('project_id') ?? 0);
        $parentId   = (int)($this->request->getPost('parent_id') ?? 0) ?: null;

        if (!$body || !$entityType || !$entityId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields.']);
        }

        $userId = $this->currentUser['id'] ?? null;
        $newId  = CommentModel::post($entityType, $entityId, $body, $userId, $projectId ?: null, $parentId);

        // Return the newly created comment with author info for immediate DOM insertion
        $cm      = new CommentModel();
        $comment = $cm->select('comments.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS author_name, fs_users.avatar_url AS author_avatar')
            ->join('fs_users', 'fs_users.id = comments.user_id', 'left')
            ->where('comments.id', $newId)
            ->first();

        return $this->response->setJSON(['success' => true, 'comment' => $comment]);
    }

    /**
     * POST /comments/:id/delete — soft delete (author or admin)
     */
    public function delete(int $id): \CodeIgniter\HTTP\Response
    {
        $cm      = new CommentModel();
        $comment = $cm->find($id);

        if (!$comment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);
        }

        $uid = $this->currentUser['id'] ?? 0;
        if ($comment['user_id'] != $uid) {
            // Could add admin check here
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorised.']);
        }

        $cm->softDelete($id);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /comments/:id/update — edit comment body (author only)
     */
    public function update(int $id): \CodeIgniter\HTTP\Response
    {
        $cm      = new CommentModel();
        $comment = $cm->find($id);

        if (!$comment || $comment['user_id'] != ($this->currentUser['id'] ?? 0)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorised.']);
        }

        $body = trim($this->request->getPost('body') ?? '');
        if (!$body) return $this->response->setJSON(['success' => false, 'message' => 'Comment cannot be empty.']);

        $cm->update($id, ['body' => $body, 'updated_at' => date('Y-m-d H:i:s')]);
        return $this->response->setJSON(['success' => true, 'body' => $body]);
    }
}
