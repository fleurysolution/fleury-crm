<?php

namespace App\Models;

use CodeIgniter\Model;

class Office_model extends Model
{
    protected $table         = 'offices';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['region_id', 'name', 'address', 'email', 'phone', 'deleted', 'created_at', 'updated_at'];

    public function getAllWithRegion(): array
    {
        return $this->select('offices.*, regions.name AS region_name')
            ->join('regions', 'regions.id = offices.region_id', 'left')
            ->where('offices.deleted', 0)
            ->orderBy('offices.id', 'DESC')
            ->findAll();
    }

    public function getByRegion(int $regionId): array
    {
        return $this->where('deleted', 0)
            ->where('region_id', $regionId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    public function findActiveById(int $id): ?array
    {
        $row = $this->where('deleted', 0)->find($id);
        return $row ?: null;
    }

    public function saveOffice(array $data, int $id = 0): int
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