<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientRelatedTables extends Migration
{
    public function up()
    {
        // Projects Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'start_date' => ['type' => 'DATE', 'null' => true],
            'deadline' => ['type' => 'DATE', 'null' => true],
            'price' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'status' => ['type' => 'ENUM', 'constraint' => ['open', 'completed', 'hold', 'canceled'], 'default' => 'open'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('projects', true);

        // Tickets Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'ticket_type_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['new', 'open', 'closed', 'client_replied'], 'default' => 'new'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tickets', true);

        // General Files Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'file_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'file_size' => ['type' => 'DOUBLE', 'default' => 0],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'uploaded_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('general_files', true);

        // Expenses Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'expense_date' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('expenses', true);

        // Notes Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'description' => ['type' => 'TEXT'],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'labels' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notes', true);

        // Events Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'start_date' => ['type' => 'DATETIME'],
            'end_date' => ['type' => 'DATETIME'],
            'location' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('events', true);
        
        // Proposals Table
         $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'proposal_date' => ['type' => 'DATE'],
            'valid_until' => ['type' => 'DATE'],
            'status' => ['type' => 'ENUM', 'constraint' => ['draft', 'sent', 'accepted', 'declined'], 'default' => 'draft'],
            'content' => ['type' => 'LONGTEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('proposals', true);

        // Contracts Table
         $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'contract_date' => ['type' => 'DATE'],
            'valid_until' => ['type' => 'DATE'],
            'status' => ['type' => 'ENUM', 'constraint' => ['draft', 'sent', 'accepted', 'declined'], 'default' => 'draft'],
            'content' => ['type' => 'LONGTEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('contracts', true);
        
        // Orders Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'order_date' => ['type' => 'DATE'],
             'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'processing', 'completed', 'cancelled'], 'default' => 'pending'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('orders', true);
        
        // Subscriptions Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'next_billing_date' => ['type' => 'DATE', 'null' => true],
             'status' => ['type' => 'ENUM', 'constraint' => ['active', 'inactive', 'cancelled'], 'default' => 'active'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('subscriptions', true);
        
        // Estimate Requests Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
             'status' => ['type' => 'ENUM', 'constraint' => ['new', 'processing', 'estimated', 'cancelled'], 'default' => 'new'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('estimate_requests', true);
        
        
    }

    public function down()
    {
        $this->forge->dropTable('projects', true);
        $this->forge->dropTable('tickets', true);
        $this->forge->dropTable('general_files', true);
        $this->forge->dropTable('expenses', true);
        $this->forge->dropTable('notes', true);
        $this->forge->dropTable('events', true);
        $this->forge->dropTable('proposals', true);
        $this->forge->dropTable('contracts', true);
        $this->forge->dropTable('orders', true);
        $this->forge->dropTable('subscriptions', true);
        $this->forge->dropTable('estimate_requests', true);
    }
}
