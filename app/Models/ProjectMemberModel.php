<?php namespace App\Models;
use CodeIgniter\Model;
class ProjectMemberModel extends Model {
    protected $table         = 'project_members';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['project_id','user_id','role'];
    public function getMembers(int $projectId): array {
        return $this->select('project_members.*, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS name, fs_users.email')
            ->join('fs_users','fs_users.id = project_members.user_id','left')
            ->where('project_id', $projectId)->findAll();
    }
    public function isMember(int $projectId, int $userId): bool {
        return (bool) $this->where(['project_id'=>$projectId,'user_id'=>$userId])->first();
    }
}
