<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProjectGeofencing extends Migration
{
    public function up()
    {
        $fields = [
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],
            'geofence_radius' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 100, // meters
            ],
        ];
        $this->forge->addColumn('projects', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('projects', ['latitude', 'longitude', 'geofence_radius']);
    }
}
