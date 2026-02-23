<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'name'        => 'North Region',
                'code'        => 'NR',
                'description' => 'North zone operations',
                'deleted'     => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'South Region',
                'code'        => 'SR',
                'description' => 'South zone operations',
                'deleted'     => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        $this->db->table('regions')->insertBatch($data);
    }
}