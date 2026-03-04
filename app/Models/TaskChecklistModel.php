<?php namespace App\Models;
use CodeIgniter\Model;
class TaskChecklistModel extends Model {
    protected $table         = 'task_checklists';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $updatedField  = '';
    protected $allowedFields = ['task_id','item_text','is_done','done_by','done_at','sort_order'];
    public function forTask(int $taskId): array {
        return $this->where('task_id', $taskId)->orderBy('sort_order')->findAll();
    }
    public function toggle(int $id, int $userId): void {
        $item = $this->find($id);
        if (!$item) return;
        $done = !$item['is_done'];
        $this->update($id, [
            'is_done' => (int)$done,
            'done_by' => $done ? $userId : null,
            'done_at' => $done ? date('Y-m-d H:i:s') : null,
        ]);
    }
}
