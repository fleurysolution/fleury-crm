<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvoicesExpensesTable extends Migration
{
    public function up(): void
    {
        // project_invoices (income/expense, project-scoped – different from CRM invoices table)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'     => ['type' => 'INT', 'unsigned' => true],
            'contract_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'cert_id'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'invoice_number' => ['type' => 'VARCHAR', 'constraint' => 50],
            'direction'      => ['type' => 'ENUM', 'constraint' => ['income','expense'], 'default' => 'income'],
            'party_name'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'invoice_date'   => ['type' => 'DATE', 'null' => true],
            'due_date'       => ['type' => 'DATE', 'null' => true],
            'subtotal'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tax_amount'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total_amount'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'paid_amount'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'status'         => ['type' => 'ENUM', 'constraint' => ['draft','sent','partial','paid','overdue','void'], 'default' => 'draft'],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'filepath'       => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_by'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'direction', 'status']);
        $this->forge->createTable('project_invoices');

        // project_expenses
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id'   => ['type' => 'INT', 'unsigned' => true],
            'cost_code_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'category'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'description'  => ['type' => 'VARCHAR', 'constraint' => 500],
            'amount'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'currency'     => ['type' => 'VARCHAR', 'constraint' => 5, 'default' => 'USD'],
            'expense_date' => ['type' => 'DATE', 'null' => true],
            'vendor'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'receipt_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'status'       => ['type' => 'ENUM', 'constraint' => ['pending','approved','rejected'], 'default' => 'pending'],
            'submitted_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'approved_by'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'approved_at'  => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'status']);
        $this->forge->createTable('project_expenses');
    }

    public function down(): void
    {
        $this->forge->dropTable('project_expenses', true);
        $this->forge->dropTable('project_invoices', true);
    }
}
