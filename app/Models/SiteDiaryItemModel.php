<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteDiaryItemModel extends Model
{
    protected $table         = 'site_diary_items';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['diary_id','type','description','area_id','task_id', 'boq_item_id', 'quantity_done', 'sort_order'];

    public function forDiary(int $diaryId): array
    {
        return $this->where('diary_id', $diaryId)->orderBy('sort_order')->orderBy('id')->findAll();
    }
}
