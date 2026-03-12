<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewConstructionPermissions extends Migration
{
    public function up()
    {
        $permissions = [
            [
                'name'           => 'Manage Cybersecurity',
                'slug'           => 'manage_cybersecurity',
                'description'    => 'Can manage cybersecurity settings and view security logs'
            ],
            [
                'name'           => 'View Production Control',
                'slug'           => 'view_production_control',
                'description'    => 'Can view project production and control metrics'
            ],
            [
                'name'           => 'Manage P6 Scheduler',
                'slug'           => 'manage_p6_scheduler',
                'description'    => 'Can manage project schedules and Gantt charts'
            ],
            [
                'name'           => 'Manage Preconstruction',
                'slug'           => 'manage_preconstruction',
                'description'    => 'Can manage preconstruction and procurement items'
            ],
            [
                'name'           => 'Manage Handover & QC',
                'slug'           => 'manage_handover_qc',
                'description'    => 'Can manage handover assets and quality control checks'
            ],
        ];

        foreach ($permissions as $p) {
            $this->db->table('permissions')->insert($p);
        }
    }

    public function down()
    {
        $keys = [
            'manage_cybersecurity',
            'view_production_control',
            'manage_p6_scheduler',
            'manage_preconstruction',
            'manage_handover_qc'
        ];
        $this->db->table('permissions')->whereIn('slug', $keys)->delete();
    }
}
