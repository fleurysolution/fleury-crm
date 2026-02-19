<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinanceSchema extends Migration
{
    public function up()
    {
        // 1. Taxes Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'percentage' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('taxes');

        // 2. Estimates Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'estimate_date' => ['type' => 'DATE'],
            'valid_until' => ['type' => 'DATE'],
            'currency' => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'USD'],
            'currency_symbol' => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => '$'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'], // draft, sent, accepted, declined
            'note' => ['type' => 'TEXT', 'null' => true],
            'public_key' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'tax_id' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'tax_id2' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'discount_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'discount_amount_type' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'percentage'], // percentage, fixed
            'discount_type' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'before_tax'], // before_tax, after_tax
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('estimates');

        // 3. Estimate Items Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'estimate_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'quantity' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 1.00],
            'rate' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'sort' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('estimate_id', 'estimates', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('estimate_items');

        // 4. Invoices Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'project_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'bill_date' => ['type' => 'DATE'],
            'due_date' => ['type' => 'DATE'],
            'type' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'invoice'], // invoice, credit_note
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'], // draft, not_paid, partially_paid, fully_paid, cancelled
            'note' => ['type' => 'TEXT', 'null' => true],
            'public_key' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
             'tax_id' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'tax_id2' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'discount_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'discount_amount_type' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'percentage'],
            'discount_type' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'before_tax'],
            'invoice_total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00], // Cached total
            'payment_received' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00], // Cached payment
            'recurring' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'repeat_every' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'repeat_type' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true], // days, weeks, months, years
            'no_of_cycles' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('invoices');

        // 5. Invoice Items Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'invoice_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'quantity' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 1.00],
            'rate' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'sort' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('invoice_items');

        // 6. Invoice Payments Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'invoice_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'payment_method_id' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'payment_date' => ['type' => 'DATE'],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'transaction_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'note' => ['type' => 'TEXT', 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('invoice_payments');
    }

    public function down()
    {
        $this->forge->dropTable('invoice_payments');
        $this->forge->dropTable('invoice_items');
        $this->forge->dropTable('invoices');
        $this->forge->dropTable('estimate_items');
        $this->forge->dropTable('estimates');
        $this->forge->dropTable('taxes');
    }
}
