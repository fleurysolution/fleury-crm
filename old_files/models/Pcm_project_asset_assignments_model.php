<?php
namespace App\Models;

use CodeIgniter\Model;

class Pcm_project_asset_assignments_model extends Model
{
    protected $table = 'pcm_project_asset_assignments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'project_id','asset_id','quantity','unit_id','assigned_by',
        'assigned_date','return_date','status','remarks','related_action_id',
        'deleted','created_at','updated_at'
    ];
    protected $useTimestamps = false;

    /**
     * Get assignments for a project (active or all)
     */
    public function getByProject($project_id, $onlyActive = true)
    {
        $builder = $this->builder();
        $builder->select('pcm_project_asset_assignments.*, pcm_assets.asset_name, pcm_asset_units.title AS unit_title');
        $builder->join('pcm_assets', 'pcm_assets.id = pcm_project_asset_assignments.asset_id', 'left');
        $builder->join('pcm_asset_units', 'pcm_asset_units.id = pcm_project_asset_assignments.unit_id', 'left');
        $builder->where('pcm_project_asset_assignments.project_id', $project_id);
        if ($onlyActive) {
            $builder->where('pcm_project_asset_assignments.status', 'assigned');
            $builder->where('pcm_project_asset_assignments.deleted', 0);
        }
        return $builder->orderBy('pcm_project_asset_assignments.assigned_date', 'DESC')->get()->getResultArray();
    }

    /**
     * Simple create
     */
    public function createAssignment(array $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->insert($data);
        return $this->insertID();
    }

    /**
     * Mark as returned
     */
    public function markReturned($id, $return_date = null, $remarks = null)
    {
        $return_date = $return_date ?: date('Y-m-d H:i:s');
        $update = [
            'status' => 'returned',
            'return_date' => $return_date,
            'remarks' => $remarks,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->update($id, $update);
    }

    /**
     * Soft delete
     */
    public function softDeleteAssignment($id)
    {
        return $this->update($id, ['deleted' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
    }
}
