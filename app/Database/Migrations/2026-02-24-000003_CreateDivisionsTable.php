<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDivisionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'region_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'office_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deleted' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('region_id');
        $this->forge->addKey('office_id');
        $this->forge->addKey('deleted');

        $this->forge->addForeignKey(
            'region_id',
            'regions',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'office_id',
            'offices',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->createTable('divisions', true);
    }

    public function down()
    {
        $this->forge->dropTable('divisions', true);
    }
}