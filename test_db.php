<?php
require 'vendor/autoload.php';

$app = Config\Paths::class;
$app = new \Config\Paths();
define('APPPATH', realpath($app->appDirectory) . DIRECTORY_SEPARATOR);
define('ROOTPATH', realpath(APPPATH . '../') . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', realpath($app->systemDirectory) . DIRECTORY_SEPARATOR);
define('WRITEPATH', realpath($app->writableDirectory) . DIRECTORY_SEPARATOR);
require_once SYSTEMPATH . 'bootstrap.php';

$db = \Config\Database::connect();
try {
    $db->query("ALTER TABLE clients ADD COLUMN tenant_id INT(11) UNSIGNED NULL AFTER id, ADD COLUMN branch_id INT(11) UNSIGNED NULL AFTER tenant_id;");
    echo "Clients updated.\n";
} catch (\Exception $e) { echo $e->getMessage() . "\n"; }

try {
    $db->query("ALTER TABLE leads ADD COLUMN tenant_id INT(11) UNSIGNED NULL AFTER id, ADD COLUMN branch_id INT(11) UNSIGNED NULL AFTER tenant_id;");
    echo "Leads updated.\n";
} catch (\Exception $e) { echo $e->getMessage() . "\n"; }

try {
    $db->query("ALTER TABLE tasks ADD COLUMN tenant_id INT(11) UNSIGNED NULL AFTER id, ADD COLUMN branch_id INT(11) UNSIGNED NULL AFTER tenant_id;");
    echo "Tasks updated.\n";
} catch (\Exception $e) { echo $e->getMessage() . "\n"; }

echo "Done.\n";
