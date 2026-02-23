<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OfficeSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Fetch region IDs by code (safer than hardcoding IDs)
        $regions = $this->db->table('regions')
            ->select('id, code')
            ->where('deleted', 0)
            ->get()
            ->getResultArray();

        $regionMap = [];
        foreach ($regions as $r) {
            $regionMap[$r['code']] = (int) $r['id'];
        }

        $data = [
            [
                'region_id'  => $regionMap['NR'] ?? 1,
                'name'       => 'Head Office - North',
                'address'    => 'North City, Main Street 1',
                'email'      => 'north.office@example.com',
                'phone'      => '+91-00000-00001',
                'deleted'    => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'region_id'  => $regionMap['SR'] ?? 2,
                'name'       => 'Head Office - South',
                'address'    => 'South City, Main Street 2',
                'email'      => 'south.office@example.com',
                'phone'      => '+91-00000-00002',
                'deleted'    => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('offices')->insertBatch($data);
    }
}