<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'client_id', 'ticket_type_id', 'status', 'created_by'];
    protected $useTimestamps = true;
}
