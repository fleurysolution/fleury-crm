<?php

namespace App\Models;

use CodeIgniter\Model;

class PayAppItemModel extends Model
{
    protected $table          = 'project_pay_app_items';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false; 

    protected $allowedFields = [
        'pay_app_id',
        'sov_item_id',
        'change_order_id',
        'work_completed_this_period',
        'materials_presently_stored'
    ];
}
