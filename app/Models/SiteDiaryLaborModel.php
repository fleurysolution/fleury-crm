<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteDiaryLaborModel extends Model
{
    protected $table          = 'project_site_diary_labor';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false; // standard association

    protected $allowedFields = [
        'diary_id',
        'trade_or_company',
        'worker_count',
        'hours_worked'
    ];

    /**
     * Get all labor records for a specific diary entry.
     */
    public function forDiary(int $diaryId): array
    {
        return $this->where('diary_id', $diaryId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
