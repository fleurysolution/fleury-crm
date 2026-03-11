<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixSubscriptionData extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'fix:subscription-data';
    protected $description = 'Populates missing current_period_start/end in tenant_subscriptions';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // Find records where current_period_end is NULL or '0000-00-00 00:00:00'
        $subs = $db->table('tenant_subscriptions')
            ->groupStart()
                ->where('current_period_end', null)
                ->orWhere('current_period_end', '0000-00-00 00:00:00')
            ->groupEnd()
            ->get()
            ->getResultArray();

        if (empty($subs)) {
            CLI::write("No records need fixing.", 'green');
            return;
        }

        CLI::write("Found " . count($subs) . " records to fix.", 'yellow');

        foreach ($subs as $sub) {
            if (empty($sub['starts_at']) || empty($sub['ends_at'])) {
                CLI::write("Skipping ID: " . $sub['id'] . " (missing starts_at/ends_at)", 'red');
                continue;
            }

            $db->table('tenant_subscriptions')
                ->where('id', $sub['id'])
                ->update([
                    'current_period_start' => $sub['starts_at'],
                    'current_period_end'   => $sub['ends_at']
                ]);
            CLI::write("Fixed ID: " . $sub['id']);
        }

        CLI::write("Maintenance complete.", 'green');
    }
}
