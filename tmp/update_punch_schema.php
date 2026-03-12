<?php
require 'app/Config/Database.php';
$db = \Config\Database::connect();

$cols = ['latitude', 'longitude', 'photo_path'];
foreach ($cols as $col) {
    try {
        if ($col == 'latitude') {
            $db->query("ALTER TABLE punch_list_items ADD COLUMN latitude DECIMAL(10, 8) AFTER reported_by");
        } elseif ($col == 'longitude') {
            $db->query("ALTER TABLE punch_list_items ADD COLUMN longitude DECIMAL(11, 8) AFTER latitude");
        } elseif ($col == 'photo_path') {
            $db->query("ALTER TABLE punch_list_items ADD COLUMN photo_path VARCHAR(255) AFTER description");
        }
        echo "Added $col\n";
    } catch (\Exception $e) {
        echo "$col already exists or error: " . $e->getMessage() . "\n";
    }
}
