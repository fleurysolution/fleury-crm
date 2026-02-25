<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentCertificatesTable extends Migration
{
    public function up(): void
    {
        // Payment certificates (progress claims / IPC)
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'       => ['type' => 'INT', 'unsigned' => true],
            'contract_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'cert_number'      => ['type' => 'VARCHAR', 'constraint' => 30],
            'period_from'      => ['type' => 'DATE', 'null' => true],
            'period_to'        => ['type' => 'DATE', 'null' => true],
            'gross_amount'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'retention_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'net_amount'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'status'           => ['type' => 'ENUM', 'constraint' => ['draft','submitted','approved','paid'], 'default' => 'draft'],
            'submitted_by'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'approved_by'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'approved_at'      => ['type' => 'DATETIME', 'null' => true],
            'paid_at'          => ['type' => 'DATE', 'null' => true],
            'notes'            => ['type' => 'TEXT', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'status']);
        $this->forge->createTable('payment_certificates');

        // Payment certificate line items (BOQ progress per cert period)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'cert_id'        => ['type' => 'INT', 'unsigned' => true],
            'boq_item_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'description'    => ['type' => 'VARCHAR', 'constraint' => 500],
            'unit'           => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'qty_this_period'=> ['type' => 'DECIMAL', 'constraint' => '12,4', 'default' => 0],
            'unit_rate'      => ['type' => 'DECIMAL', 'constraint' => '15,4', 'default' => 0],
            'amount'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['cert_id']);
        $this->forge->createTable('payment_cert_items');
    }

    public function down(): void
    {
        $this->forge->dropTable('payment_cert_items', true);
        $this->forge->dropTable('payment_certificates', true);
    }
}
