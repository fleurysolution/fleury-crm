<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InsertDefaultVendorRoles extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('roles');

        $roles = [
            [
                'name'        => 'Subcontractor / Vendor',
                'slug'        => 'subcontractor_vendor',
                'description' => 'External companies providing services or materials, with limited portal access.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Client',
                'slug'        => 'client',
                'description' => 'The owner or customer of the project, with restricted view access.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($roles as $role) {
            $exists = $builder->where('slug', $role['slug'])->countAllResults();
            if ($exists == 0) {
                $builder->insert($role);
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('roles');
        
        $builder->whereIn('slug', ['subcontractor_vendor', 'client'])->delete();
    }
}
