<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'amount', 'category_id', 'client_id', 'expense_date'];
    protected $useTimestamps = true;
}
