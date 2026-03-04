<?php
require 'public/index.php'; // Boot CodeIgniter
$db = \Config\Database::connect();
$fields = $db->getFieldNames('estimate_items');
print_r($fields);
