<?php
// Script to add cybersecurity fields to fs_users
$db = \Config\Database::connect();
$forge = \Config\Database::forge();

$fields = [
    'mfa_secret' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => true,
        'after' => 'password_hash'
    ],
    'mfa_enabled' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 0,
        'after' => 'mfa_secret'
    ],
    'login_attempts' => [
        'type' => 'INT',
        'constraint' => 11,
        'default' => 0,
        'after' => 'status'
    ],
    'locked_until' => [
        'type' => 'DATETIME',
        'null' => true,
        'after' => 'login_attempts'
    ],
    'last_ip_address' => [
        'type' => 'VARCHAR',
        'constraint' => 45,
        'null' => true,
        'after' => 'last_login_at'
    ]
];

// Check if columns exist before adding
$existingFields = $db->getFieldNames('fs_users');
$toAdd = [];
foreach ($fields as $name => $def) {
    if (!in_array($name, $existingFields)) {
        $toAdd[$name] = $def;
    }
}

if (!empty($toAdd)) {
    $forge->addColumn('fs_users', $toAdd);
    echo "Added " . count($toAdd) . " cybersecurity fields to fs_users.\n";
} else {
    echo "Cybersecurity fields already exist.\n";
}

// Create security_log table
if (!$db->tableExists('security_log')) {
    $forge->addField([
        'id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
            'auto_increment' => true
        ],
        'user_id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
            'null' => true
        ],
        'event_type' => [
            'type' => 'VARCHAR',
            'constraint' => 50
        ],
        'severity' => [
            'type' => 'ENUM',
            'constraint' => ['low', 'medium', 'high', 'critical'],
            'default' => 'low'
        ],
        'description' => [
            'type' => 'TEXT'
        ],
        'ip_address' => [
            'type' => 'VARCHAR',
            'constraint' => 45
        ],
        'user_agent' => [
            'type' => 'TEXT'
        ],
        'details' => [
            'type' => 'JSON',
            'null' => true
        ],
        'created_at' => [
            'type' => 'DATETIME'
        ]
    ]);
    $forge->addKey('id', true);
    $forge->addKey('user_id');
    $forge->addKey('event_type');
    $forge->createTable('security_log');
    echo "Created security_log table.\n";
} else {
    echo "security_log table already exists.\n";
}
