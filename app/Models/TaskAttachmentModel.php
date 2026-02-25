<?php namespace App\Models;
use CodeIgniter\Model;
class TaskAttachmentModel extends Model {
    protected $table         = 'task_attachments';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['task_id','user_id','filename','filepath','filesize','mime_type'];
    public function forTask(int $taskId): array {
        return $this->where('task_id', $taskId)->orderBy('created_at','DESC')->findAll();
    }
}
