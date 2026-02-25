<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationSettingsTable extends Migration
{
    public function up(): void
    {
        // Create notification_settings table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'event' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false, 'default' => ''],
            'category' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'default' => ''],
            'sort' => ['type' => 'INT', 'default' => 0],
            'notify_to_terms' => ['type' => 'TEXT', 'null' => true],
            'notify_to_team' => ['type' => 'TEXT', 'null' => true],
            'notify_to_team_members' => ['type' => 'TEXT', 'null' => true],
            'enable_email' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'enable_web' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'enable_slack' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('category');
        $this->forge->createTable('notification_settings', true);

        // Seed demo notification events
        $now = date('Y-m-d H:i:s');
        $db  = \Config\Database::connect();

        $events = [
            ['event' => 'client_created',       'category' => 'client',    'sort' => 1, 'enable_email' => 1, 'enable_web' => 1, 'enable_slack' => 0, 'notify_to_terms' => 'creator'],
            ['event' => 'invoice_created',       'category' => 'invoice',   'sort' => 2, 'enable_email' => 1, 'enable_web' => 1, 'enable_slack' => 0, 'notify_to_terms' => 'creator'],
            ['event' => 'lead_created',          'category' => 'lead',      'sort' => 3, 'enable_email' => 1, 'enable_web' => 1, 'enable_slack' => 0, 'notify_to_terms' => 'assignee'],
            ['event' => 'ticket_created',        'category' => 'ticket',    'sort' => 4, 'enable_email' => 1, 'enable_web' => 1, 'enable_slack' => 0, 'notify_to_terms' => 'creator'],
            ['event' => 'project_created',       'category' => 'project',   'sort' => 5, 'enable_email' => 1, 'enable_web' => 1, 'enable_slack' => 0, 'notify_to_terms' => 'creator'],
            ['event' => 'task_assigned',         'category' => 'project',   'sort' => 6, 'enable_email' => 1, 'enable_web' => 1, 'enable_slack' => 0, 'notify_to_terms' => 'assignee'],
            ['event' => 'contract_accepted',     'category' => 'contract',  'sort' => 7, 'enable_email' => 1, 'enable_web' => 1, 'enable_slack' => 0, 'notify_to_terms' => 'creator'],
            ['event' => 'estimate_accepted',     'category' => 'estimate',  'sort' => 8, 'enable_email' => 1, 'enable_web' => 1, 'enable_slack' => 0, 'notify_to_terms' => 'creator'],
            ['event' => 'message_received',      'category' => 'message',   'sort' => 9, 'enable_email' => 1, 'enable_web' => 1, 'enable_slack' => 0, 'notify_to_terms' => 'receiver'],
            ['event' => 'announcement_created',  'category' => 'announcement', 'sort' => 10, 'enable_email' => 1, 'enable_web' => 1, 'enable_slack' => 0, 'notify_to_terms' => 'all_staff'],
        ];

        foreach ($events as $event) {
            $exists = $db->table('notification_settings')->where('event', $event['event'])->countAllResults();
            if (!$exists) {
                $db->table('notification_settings')->insert(array_merge($event, [
                    'notify_to_team'         => null,
                    'notify_to_team_members' => null,
                    'created_at'             => $now,
                    'updated_at'             => $now,
                ]));
            }
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('notification_settings', true);
    }
}
