<?php

namespace App\Models;

use CodeIgniter\Model;

class FsSettingsModel extends Model
{
    protected $table      = 'fs_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $allowedFields = [
        'setting_name',
        'setting_value',
        'user_id',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';

    // Keep compatibility with your BaseAppController call:
    public function get_all_required_settings($userId = null)
    {
        $builder = $this->builder()
            ->select('setting_name, setting_value')
            ->where('deleted_at', null);

        // If user-scoped settings exist
        if ($userId) {
            $builder->groupStart()
                ->where('user_id', 0)
                ->orWhere('user_id', (int)$userId)
            ->groupEnd();
        } else {
            $builder->where('user_id', 0);
        }

        return $builder->get();
    }
}