<?php
namespace App\Controllers;

use App\Libraries\Stripe;

class Stripe_webhook extends App_Controller {


      public function index()
    {
        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Stripe webhook working!'
        ]);
    }
    
    public function stripe()
    {
        // Load Stripe PHP SDK
        require_once APPPATH . 'third_party/stripe-php/init.php';

        // Your Stripe secret keys
        $endpoint_secret = "whsec_your_webhook_secret"; // webhook signing secret
        $payload = @file_get_contents("php://input");
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle event
        switch ($event->type) {
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                $subscription = $event->data->object;

                $stripe_subscription_id = $subscription->id;
                $customer_id = $subscription->customer;
                $status = $subscription->status;
                $current_period_start = date('Y-m-d H:i:s', $subscription->current_period_start);
                $current_period_end = date('Y-m-d H:i:s', $subscription->current_period_end);
                $plan_id = $subscription->plan->id;

                // Update DB
                $data = array(
                    'customer_id' => $customer_id,
                    'status' => $status,
                    'plan_id' => $plan_id,
                    'current_period_start' => $current_period_start,
                    'current_period_end' => $current_period_end,
                    'updated_at' => date('Y-m-d H:i:s')
                );

                $exists = $this->db
                    ->where('stripe_subscription_id', $stripe_subscription_id)
                    ->get('subscriptions')
                    ->row();

                if ($exists) {
                    $this->db->where('stripe_subscription_id', $stripe_subscription_id)->update('subscriptions', $data);
                } else {
                    $data['stripe_subscription_id'] = $stripe_subscription_id;
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('subscriptions', $data);
                }
                break;

            default:
                // Ignore other events
                break;
        }

        http_response_code(200); // ✅ Acknowledge Stripe
    }
}
