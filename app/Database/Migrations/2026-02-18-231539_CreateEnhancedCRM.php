<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnhancedCRM extends Migration
{
    public function up()
    {
        // 1. Modify Leads Table
        $fields = [
            'type'            => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'organization', 'after' => 'id'], // organization, person
            'website'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'phone'],
            'address'         => ['type' => 'TEXT', 'null' => true, 'after' => 'website'],
            'city'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'address'],
            'state'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'city'],
            'zip'             => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'state'],
            'country'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'zip'],
            'vat_number'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'country'],
            'gst_number'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'vat_number'],
            'currency'        => ['type' => 'VARCHAR', 'constraint' => 3, 'default' => 'USD', 'after' => 'gst_number'],
            'currency_symbol' => ['type' => 'VARCHAR', 'constraint' => 5, 'default' => '$', 'after' => 'currency'],
            'labels'          => ['type' => 'TEXT', 'null' => true, 'after' => 'currency_symbol'], // CSV strings or JSON
        ];
        
        // Check if columns exist before adding to avoid errors if re-running partially
        foreach ($fields as $col => $def) {
            if (!$this->db->fieldExists($col, 'leads')) {
                $this->forge->addColumn('leads', [$col => $def]);
            }
        }

        // 2. Create Clients Table
        if (!$this->db->tableExists('clients')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'organization',
                ],
                'company_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                ],
                'website' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'phone' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'address' => [
                    'type'       => 'TEXT',
                    'null'       => true,
                ],
                'city' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'state' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'zip' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                ],
                'country' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'vat_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'gst_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'currency' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 3,
                    'default'    => 'USD',
                ],
                'currency_symbol' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 5,
                    'default'    => '$',
                ],
                'labels' => [
                    'type'       => 'TEXT',
                    'null'       => true,
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'active', // active, inactive
                ],
                'owner_id' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                    'null'     => true,
                ],
                'created_by' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                    'null'     => true,
                ],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
                'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('clients', true);
        }

        // 3. Create Contacts Table
        if (!$this->db->tableExists('contacts')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'lead_id' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                    'null'     => true,
                ],
                'client_id' => [
                    'type'     => 'BIGINT',
                    'unsigned' => true,
                    'null'     => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => false,
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                ],
                'phone' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'job_title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'is_primary' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
                'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('lead_id');
            $this->forge->addKey('client_id');
            $this->forge->createTable('contacts', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('contacts', true);
        $this->forge->dropTable('clients', true);
        
        $columns = ['type', 'website', 'address', 'city', 'state', 'zip', 'country', 'vat_number', 'gst_number', 'currency', 'currency_symbol', 'labels'];
        $this->forge->dropColumn('leads', $columns);
    }
}
