<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\TenantSubscriptionModel;
use App\Models\NotificationModel;
use App\Models\FsUserModel;

class SendSubscriptionExpiryNotifications extends BaseCommand
{
    protected $group       = 'Subscription';
    protected $name        = 'subscription:notify-expiry';
    protected $description = 'Sends notifications before subscription expiry.';

    public function run(array $params)
    {
        $subscriptionModel = new TenantSubscriptionModel();
        $userModel = new FsUserModel();

        $today = date('Y-m-d');
        
        // Define notification intervals (days before expiry)
        $intervals = [
            30 => '1 month',
            15 => '15 days',
            7  => '1 week',
            0  => 'today'
        ];

        foreach ($intervals as $days => $label) {
            $targetDate = date('Y-m-d', strtotime("+$days days"));
            
            $expiringSubscriptions = $subscriptionModel->where('ends_at >=', $targetDate . ' 00:00:00')
                                                       ->where('ends_at <=', $targetDate . ' 23:59:59')
                                                       ->where('status', 'active')
                                                       ->findAll();

            foreach ($expiringSubscriptions as $sub) {
                // Find admin user for this tenant
                $admin = $userModel->where('tenant_id', $sub['tenant_id'])
                                   ->where('is_admin', 1)
                                   ->first();

                if ($admin) {
                    $message = "Your subscription will expire in $label ($sub[ends_at]). Please renew to avoid service interruption.";
                    if ($days === 0) {
                        $message = "Your subscription expires today! Please renew immediately to keep access.";
                    }

                    NotificationModel::send(
                        $admin['id'],
                        'subscription_expiry',
                        $message,
                        ['url' => 'profile'] // Link to billing/profile page
                    );
                    
                    CLI::write("Notification sent to Admin (ID: $admin[id]) for Tenant (ID: $sub[tenant_id]) - $label before expiry.", 'green');
                }
            }
        }

        CLI::write('Subscription expiry notifications check completed.', 'cyan');
    }
}
