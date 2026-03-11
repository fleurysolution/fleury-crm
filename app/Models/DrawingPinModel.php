<?php

namespace App\Models;

use CodeIgniter\Model;

class DrawingPinModel extends Model
{
    protected $table          = 'drawing_pins';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields   = ['drawing_id', 'revision_id', 'pos_x', 'pos_y', 'pin_type', 'reference_id', 'content', 'created_by'];
    protected $useTimestamps   = true;
    protected $updatedField    = '';

    public function getForDrawing(int $drawingId)
    {
        return $this->where('drawing_id', $drawingId)->findAll();
    }
}
