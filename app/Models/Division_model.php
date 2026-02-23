<?php

namespace App\Models;

use CodeIgniter\Model;

class Division_model extends Model
{
    protected $table         = 'divisions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['region_id', 'office_id', 'name', 'description', 'deleted', 'created_at', 'updated_at'];

    public function getAllWithRegionOffice(): array
    {
        return $this->select('divisions.*, regions.name AS region_name, offices.name AS office_name')
            ->join('regions', 'regions.id = divisions.region_id', 'left')
            ->join('offices', 'offices.id = divisions.office_id', 'left')
            ->where('divisions.deleted', 0)
            ->orderBy('divisions.id', 'DESC')
            ->findAll();
    }

    public function getByOffice(int $officeId): array
    {
        return $this->where('deleted', 0)
            ->where('office_id', $officeId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    public function findActiveById(int $id): ?array
    {
        $row = $this->where('deleted', 0)->find($id);
        return $row ?: null;
    }

    public function saveDivision(array $data, int $id = 0): int
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