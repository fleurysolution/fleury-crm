<?php

namespace App\Controllers\Webhooks;

use App\Controllers\BaseController;
use App\Services\StripeService;
use App\Models\TenantSubscriptionModel;
use App\Models\TenantModel;

class Stripe extends BaseController
{
    protected $stripeService;
    protected $subscriptionModel;
    protected $tenantModel;

    public function __construct()
    {
        $this->stripeService = new StripeService();
        $this->subscriptionModel = new TenantSubscriptionModel();
        $this->tenantModel = new TenantModel();
    }

    public function index()
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $this->request->getServer('HTTP_STRIPE_SIGNATURE');

        if (!$sigHeader) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing signature header']);
        }

        $event = $this->stripeService->handleWebhook($payload, $sigHeader);

        if (!$event) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid webhook event']);
        }

        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    protected function handlePaymentSucceeded($invoice)
    {
        $stripeSubscriptionId = $invoice->subscription;
        $subscription = $this->subscriptionModel->where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if ($subscription) {
            $this->subscriptionModel->update($subscription['id'], [
                'status' => 'active',
                'current_period_end' => date('Y-m-d H:i:s', $invoice->lines->data[0]->period->end)
            ]);
            
            $this->tenantModel->update($subscription['tenant_id'], ['status' => 'active']);

            // Notify Admin
            $admin = (new \App\Models\FsUserModel())->where('tenant_id', $subscription['tenant_id'])->where('is_admin', 1)->first();
            if ($admin) {
                \App\Models\NotificationModel::send(
                    $admin['id'],
                    'payment_confirmation',
                    "Payment of " . strtoupper($invoice->currency) . " " . ($invoice->amount_paid / 100) . " received successfully. Your subscription is now active until " . date('Y-m-d', $invoice->lines->data[0]->period->end) . ".",
                    ['url' => 'profile']
                );
            }
        }
    }

    protected function handlePaymentFailed($invoice)
    {
        $stripeSubscriptionId = $invoice->subscription;
        $subscription = $this->subscriptionModel->where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if ($subscription) {
            $this->subscriptionModel->update($subscription['id'], ['status' => 'past_due']);
            
            // Notify Admin
            $admin = (new \App\Models\FsUserModel())->where('tenant_id', $subscription['tenant_id'])->where('is_admin', 1)->first();
            if ($admin) {
                \App\Models\NotificationModel::send(
                    $admin['id'],
                    'payment_failed',
                    "Urgent: Payment for your subscription failed. Please update your payment method to avoid service interruption.",
                    ['url' => 'profile']
                );
            }
        }
    }

    protected function handleSubscriptionDeleted($subscriptionData)
    {
        $stripeSubscriptionId = $subscriptionData->id;
        $subscription = $this->subscriptionModel->where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if ($subscription) {
            $this->subscriptionModel->update($subscription['id'], ['status' => 'expired']);
            $this->tenantModel->update($subscription['tenant_id'], ['status' => 'expired']);

            // Notify Admin
            $admin = (new \App\Models\FsUserModel())->where('tenant_id', $subscription['tenant_id'])->where('is_admin', 1)->first();
            if ($admin) {
                \App\Models\NotificationModel::send(
                    $admin['id'],
                    'subscription_cancelled',
                    "Your subscription has been cancelled/expired. Features are now locked.",
                    ['url' => 'profile']
                );
            }
        }
    }
}
