<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskCollaboratorModel extends Model
{
    protected $table         = 'task_collaborators';
    protected $primaryKey    = 'task_id'; // Note: CI4 normally expects single PK, but doing deletes via where() bypasses this.
    protected $useTimestamps = false;
    protected $allowedFields = ['task_id', 'user_id', 'created_at'];

    public function getCollaborators(int $taskId): array
    {
        return $this->select('task_collaborators.*, fs_users.first_name, fs_users.last_name, fs_users.email')
                    ->join('fs_users', 'fs_users.id = task_collaborators.user_id')
                    ->where('task_id', $taskId)
                    ->findAll();
    }

    public function syncCollaborators(int $taskId, array $userIds)
    {
        // Remove existing
        $this->where('task_id', $taskId)->delete();
        // Insert new
        foreach ($userIds as $uid) {
            if (empty($uid)) continue;
            // Need base db builder for mass inserts without messing up the PK
            $this->db->table($this->table)->insert([
                'task_id'    => $taskId,
                'user_id'    => (int)$uid,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
