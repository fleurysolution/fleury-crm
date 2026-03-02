<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DefaultRolesSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('fs_roles');

        $roles = [
            [
                'title'       => 'Subcontractor / Vendor',
                'description' => 'External companies providing services or materials, with limited portal access.',
                'is_default'  => 0,
            ],
            [
                'title'       => 'Client',
                'description' => 'The owner or customer of the project, with restricted view access.',
                'is_default'  => 0,
            ],
            [
                'title'       => 'Project Manager',
                'description' => 'Internal employee managing full project scope.',
                'is_default'  => 0,
            ]
        ];

        foreach ($roles as $r) {
            // Check if exists first to avoid duplicates
            if ($builder->where('title', $r['title'])->countAllResults() === 0) {
                // We need to generate a valid slug if we are mimicking the Roles controller, but looking at old code, title is what matters
                // Actually, the previous Roles controller used 'name' and 'slug'. Let's check the schema.
            }
        }
    }
}
