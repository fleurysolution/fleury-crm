<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PatchDb extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:patch';
    protected $description = 'Temporarily patches db tables.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        try {
            $db->query("ALTER TABLE project_purchase_orders MODIFY status VARCHAR(50) DEFAULT 'Draft';");
            CLI::write("PO status column expanded", 'green');
        } catch(\Exception $e) { CLI::write($e->getMessage(), 'red'); }
    }
}
