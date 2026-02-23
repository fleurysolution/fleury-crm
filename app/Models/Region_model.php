<?php

namespace App\Models;

use CodeIgniter\Model;

class Region_model extends Model
{
    protected $table         = 'regions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'code', 'description', 'deleted', 'created_at', 'updated_at'];

    public function getAllActive(): array
    {
        return $this->where('deleted', 0)->orderBy('id', 'DESC')->findAll();
    }

    public function findActiveById(int $id): ?array
    {
        $row = $this->where('deleted', 0)->find($id);
        return $row ?: null;
    }

    public function saveRegion(array $data, int $id = 0): int
    {
        if ($id > 0) {
            $this->update($id, $data);
            return $id;
        }

        $this->insert($data);
        return (int) $this->getInsertID();
    }

    public function softDelete(int $id): bool
    {
        return (bool) $this->update($id, ['deleted' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
    }
}