<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Pick first active office in each region for demo divisions
        $offices = $this->db->table('offices')
            ->select('id, region_id, name')
            ->where('deleted', 0)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        if (!$offices) {
            return; // offices not seeded yet
        }

        // Create a couple divisions under first 2 offices if present
        $office1 = $offices[0];
        $office2 = $offices[1] ?? $offices[0];

        $data = [
            [
                'region_id'    => (int) $office1['region_id'],
                'office_id'    => (int) $office1['id'],
                'name'         => 'Operations',
                'description'  => 'Operations division',
                'deleted'      => 0,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'region_id'    => (int) $office1['region_id'],
                'office_id'    => (int) $office1['id'],
                'name'         => 'Sales',
                'description'  => 'Sales division',
                'deleted'      => 0,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'region_id'    => (int) $office2['region_id'],
                'office_id'    => (int) $office2['id'],
                'name'         => 'HR',
                'description'  => 'Human resources',
                'deleted'      => 0,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        $this->db->table('divisions')->insertBatch($data);
    }
}