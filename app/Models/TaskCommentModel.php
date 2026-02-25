<?php namespace App\Models;
use CodeIgniter\Model;
class TaskCommentModel extends Model {
    protected $table          = 'task_comments';
    protected $primaryKey     = 'id';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $allowedFields  = ['task_id','user_id','parent_id','body'];
    public function forTask(int $taskId): array {
        return $this->select('task_comments.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS author_name')
            ->join('fs_users','fs_users.id = task_comments.user_id','left')
            ->where('task_id', $taskId)
            ->where('parent_id', null)
            ->orderBy('task_comments.created_at')
            ->findAll();
    }
}
