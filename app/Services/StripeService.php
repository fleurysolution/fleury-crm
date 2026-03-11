<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class StripeService
{
    protected $secretKey;
    protected $webhookSecret;

    public function __construct()
    {
        helper('settings');
        $mode = setting('platform_stripe_mode') ?: 'sandbox';
        
        if ($mode === 'live') {
            $this->secretKey = setting('platform_stripe_live_secret_key');
        } else {
            $this->secretKey = setting('platform_stripe_test_secret_key');
        }

        $this->webhookSecret = setting('platform_stripe_webhook_secret');
        Stripe::setApiKey($this->secretKey);
    }

    /**
     * Create a Stripe Checkout session for a subscription plan.
     */
    public function createCheckoutSession(array $tenantInfo, array $package, string $userEmail, ?string $successUrl = null, ?string $cancelUrl = null)
    {
        $successUrl = $successUrl ?: site_url('signup/success?session_id={CHECKOUT_SESSION_ID}');
        $cancelUrl = $cancelUrl ?: site_url('signup/company');

        return Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $userEmail,
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($tenantInfo['currency'] ?: 'usd'),
                    'product_data' => [
                        'name' => $package['name'] . ' - BPMS247',
                    ],
                    'unit_amount' => (int)($package['price'] * 100),
                    'recurring' => [
                        'interval' => ($package['billing_interval'] === 'yearly') ? 'year' : 'month',
                    ],
                ],
                'quantity' => $package['is_per_user'] ? ($tenantInfo['employee_count'] ?: 1) : 1,
            ]],
            'mode' => 'subscription',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'tenant_name' => $tenantInfo['name'],
                'user_email' => $userEmail,
                'package_id' => $package['id'],
                'tenant_id'  => $tenantInfo['id'] ?? null
            ]
        ]);
    }

    /**
     * Handle incoming Stripe webhooks.
     */
    public function handleWebhook(string $payload, string $sigHeader)
    {
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $this->webhookSecret);
            return $event;
        } catch (\Exception $e) {
            log_message('error', '[Stripe Webhook] ' . $e->getMessage());
            return null;
        }
    }
}
