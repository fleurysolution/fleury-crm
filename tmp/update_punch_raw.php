<?php
$mysqli = new mysqli("localhost", "root", "", "crm_core_dev");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$cols = [
    "latitude DECIMAL(10, 8) AFTER reported_by",
    "longitude DECIMAL(11, 8) AFTER latitude",
    "photo_path VARCHAR(255) AFTER description"
];

foreach ($cols as $colDef) {
    $colName = explode(" ", $colDef)[0];
    $check = $mysqli->query("SHOW COLUMNS FROM punch_list_items LIKE '$colName'");
    if ($check->num_rows == 0) {
        if ($mysqli->query("ALTER TABLE punch_list_items ADD COLUMN $colDef")) {
            echo "Added $colName\n";
        } else {
            echo "Error adding $colName: " . $mysqli->error . "\n";
        }
    } else {
        echo "$colName already exists\n";
    }
}
$mysqli->close();
