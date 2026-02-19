<?php

namespace App\Controllers;

use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use App\Libraries\ReCAPTCHA;

// use App\Libraries\Google_Meet_Integration_Google_Calendar;
use CodeIgniter\Database\Config;
use CodeIgniter\Controller;


#[\AllowDynamicProperties]
class Front extends App_Controller {

    private $signin_validation_errors;
    public $Verification_model;
    public $Front_model;
    protected $Appointment_breaks_model;
    function __construct() {
        //parent::__construct();
            $this->signin_validation_errors = array();
            $this->Package_price_model = model('Package_price_model');
            $this->Users_model = model('Users_model');
            $this->Clients_model = model('Clients_model');
            $this->Email_templates_model = model('Email_templates_model');
            $this->Payment_methods_model = model('Payment_methods_model');
            $this->Subscriptions_model =model('Subscriptions_model');
            $this->Subscription_items_model=model('Subscription_items_model');
            $this->Invoices_model=model('Invoices_model');
            $this->Invoice_items_model=model('Invoice_items_model');            
            $this->Invoice_payments_model=model('Invoice_payments_model');
            $this->Appointment_service_categories_model = model('Appointment_service_categories_model');
            $this->Services_model = model('Services_model');
            $this->Front_model = model('Front_model');
            $this->Appointment_services_model = model('Appointment_services_model');
            $this->Appointment_availability_model = model('Appointment_availability_model');
            $this->Weekly_breaks_model = model('Weekly_breaks_model');            
            $this->Appointment_breaks_model = model('Appointment_breaks_model');
        helper('email');
        helper('form'); 
        helper('general');
       // parent::__construct();
        $this->Verification_model = model('App\Models\Verification_model');        
    }

    function index() {
        $list_data = $this->Package_price_model->get_active_package()->getResult();
        $view_data["redirect"] = "";
            if (isset($_REQUEST["redirect"])) {
                $view_data["redirect"] = $_REQUEST["redirect"];
            }
            $view_data["priceList"] =$list_data;
        return view('frontend/index', $view_data);
    }


    function privacy_policy() {
         $view_data["meta"] = "";
         $view_data["site_title"] = "";
         $view_data["meta_keywords"] = "";
         return view('frontend/privacy-policy', $view_data);
    }
    function terms_condition() {
         $view_data["meta"] = "Terms Of Service";
         $view_data["site_title"] = "Terms Of Service";
         $view_data["meta_keywords"] = "Terms Of Service";
         return view('frontend/terms-of-service', $view_data);
    }
    
    function appointments() {
         $view_data["meta"] = "Book Your Appointments";
         $view_data["site_title"] = "Book Your Appointments";
         $view_data["meta_keywords"] = "Book Your Appointments, BPMS 247";
         return view('frontend/book-appointment', $view_data);
    }

    function members_list() {
         $view_data["meta"] = "Members List - BPMS 247";
         $view_data["site_title"] = "Members List - BPMS247";
         $view_data["meta_keywords"] = "Members List - BPMS 247";
         return view('frontend/appointment/members_list', $view_data);
    }


    public function register($package){

         $view_data["type"] = "client";
        $view_data["signup_type"] = "new_client";
        $view_data["signup_message"] = 'Enter your details to register your account';
         $uri = service('uri');
         $view_data["id"]= $uri->getSegment(3);
        
        $package_id=base64_decode($package);
        $view_data['packageDetails']=$this->Package_price_model->getPackageById($package)->getRow();
        return view('frontend/sign-up-form', $view_data);
    }
    // Validate wheather email already exist or not 

    public function email_check(){   
        if ($this->request->isAJAX()) {
            $email = $this->request->getPost('email');

            if ($this->Users_model->is_email_exists($email)) {
                return $this->response->setBody("exists");
            }else{
                return $this->response->setBody("available");
            }
           
        }
    }
    public function check_subscription_details() {   
        $subdomain = $this->request->getPost('subdomain');        
        $clientId= $this->Subscriptions_model->get_details_client($subdomain);      
        $subscription_info = $this->Subscriptions_model->get_details_subscription($clientId);
        if ($subscription_info) {
            return $this->response->setJSON($subscription_info);
        } else {
            return $this->response->setJSON(['error' => 'No subscription found']);
        }
    }


    public function subdomain_check(){   
        if ($this->request->isAJAX()) {
            $subdomain = $this->request->getPost('subdomain');

            if ($this->Clients_model->is_duplicate_subdomain_name($subdomain)) {
                return $this->response->setBody("exists");
            }else{
                return $this->response->setBody("available");
            }
           
        }
    }
    

    //validate submitted data
    protected function validate_submitted_data($fields = array(), $return_errors = false, $json_response = true) {
        $final_fields = array();

        foreach ($fields as $field => $validate) {
            //we've to add permit_empty rule if the field is not required
            if (strpos($validate, 'required') !== false) {
                //this is required field
            } else {
                //so, this field isn't required, add permit_empty rule
                $validate .= "|permit_empty";
            }

            $final_fields[$field] = $validate;
        }

        if (!$final_fields) {
            //no fields to validate in this context, so nothing to validate
            return true;
        }

        $validate = $this->validate($final_fields);

        if (!$validate) {
            if (ENVIRONMENT === 'production') {
                $message = app_lang('something_went_wrong');
            } else {
                $validation = \Config\Services::validation();
                $message = $validation->getErrors();
            }

            if ($return_errors) {
                return $message;
            }
            if ($json_response) {
                echo json_encode(array("success" => false, 'message' => json_encode($message)));
            } else {
                echo view("errors/html/error_general", array("heading" => "404 Bad Request", "message" => app_lang("re_captcha_error-bad-request")));
            }
            exit();
        }
    }

    function get_current_utc_time()
        {
            return (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
        }

    public function create_account(){        
        $this->parser = \Config\Services::parser();
        $signup_key = $this->request->getPost("signup_key");
        $verify_email_key = $this->request->getPost("verify_email_key");
              
        //check if there reCaptcha is enabled
        //if reCaptcha is enabled, check the validation
        //reCaptcha isn't necessary for a verified user
        if (!$verify_email_key) {
            $ReCAPTCHA = new ReCAPTCHA();
            $ReCAPTCHA->validate_recaptcha();
        }
       
        $first_name = $this->request->getPost("first_name");
        $last_name = $this->request->getPost("last_name");

        $user_data = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            "job_title" => $this->request->getPost("job_title") ? $this->request->getPost("job_title") : "Untitled",
            // "created_at" => get_current_utc_time()
        );

        $user_data = clean_data($user_data);

        // don't clean password since there might be special characters 
        $user_data["password"] = password_hash($this->request->getPost("password"), PASSWORD_DEFAULT);

            //create a client directly
            if (get_setting("disable_client_signup")) {
                show_404();
            }

                $this->validate_submitted_data(array(
                    "email" => "required|valid_email"
                ));

                $email = $this->request->getPost("email");

                if ($this->Users_model->is_email_exists($email)) {
                    echo json_encode(array("success" => false, 'message' => app_lang("account_already_exists_for_your_mail") . " " . anchor(get_uri("signin"), app_lang('signin'), array("class" => "text-white text-off"))));
                    return false;
                }
            

            $company_name = $this->request->getPost("company_name") ? $this->request->getPost("company_name") : $first_name . " " . $last_name; //save user name as company name if there is no company name entered
            $subdomain=$this->request->getPost("subdomain");
            $client_data = array(
                "company_name" => $company_name,
                "type" => $this->request->getPost("account_type"),
                "subdomain"=>$subdomain,
                "created_by" => 1 //add default admin
                
            );

            $client_data = clean_data($client_data);

            //check duplicate company name, if found then show an error message
            if (get_setting("disallow_duplicate_client_company_name") == "1" && $this->Clients_model->is_duplicate_company_name($company_name)) {
                echo json_encode(array("success" => false, 'message' => app_lang("account_already_exists_for_your_company_name") . " " . anchor(get_uri("signin"), app_lang('signin'), array("class" => "text-white text-off"))));
                return false;
            }


            //create a client
            
            $client_id = $this->Clients_model->ci_save($client_data);
            
            $package_id = $this->request->getPost("package_id");
            $options = array("id" => $package_id);
            $package_url = $this->Package_price_model->get_details($options)->getRow();
         
            if ($client_id) {
                //client created, now create the client contact
                $user_data["user_type"] = "client";
                $user_data["email"] = $email;
                $user_data["client_id"] = $client_id;
                $user_data["is_primary_contact"] = 1;
                $user_data['client_permissions'] = "all";
                $user_id = $this->Users_model->ci_save($user_data);
                
                //user can't create account two times with the same code
                if ($verify_email_key) {
                    $options = array("code" => $verify_email_key, "type" => "verify_email");
                    $verification_info = $this->Verification_model->get_details($options)->getRow();
                    if ($verification_info->id) {
                        $this->Verification_model->delete_permanently($verification_info->id);
                    }
                }

                log_notification("client_signup", array("client_id" => $client_id), $user_id);

                //send welcome email
                
                $email_template = $this->Email_templates_model->get_final_template("new_client_greetings"); //use default template since creating new client

                $parser_data["SIGNATURE"] = $email_template->signature;
                $parser_data["CONTACT_FIRST_NAME"] = $first_name;
                $parser_data["CONTACT_LAST_NAME"] = $last_name;

                $Company_model = model('App\Models\Company_model');
                $company_info = $Company_model->get_one_where(array("is_default" => true));
                $parser_data["COMPANY_NAME"] = $company_info->name;

                $parser_data["DASHBOARD_URL"] = base_url();
                $parser_data["CONTACT_LOGIN_EMAIL"] = $email;
                $parser_data["CONTACT_LOGIN_PASSWORD"] = $this->request->getPost("password");
                $parser_data["LOGO_URL"] = get_logo_url();

                $message = $this->parser->setData($parser_data)->renderString($email_template->message);
                $subject = $this->parser->setData($parser_data)->renderString($email_template->subject);

                send_app_mail($email, $subject, $message);
                // Rakesh Code starts here for multi tenancy
                    $mysql_user = 'root';
                    $mysql_password = '581LxyG4U3byRjF2Lk';
                    $host = 'localhost';
                    $existing_database = 'veloraweb_pcmmasterdb';
                    $new_database = 'veloraweb_pcm360_client_'.$subdomain;
                    
                    // Command to create a new database
                        $create_db_command = "mysql -u $mysql_user -p$mysql_password -e 'CREATE DATABASE $new_database;'";
                        
                        // Command to grant all privileges to the root user
                        $grant_privileges_command = "mysql -u $mysql_user -p$mysql_password -e \"GRANT ALL PRIVILEGES ON $new_database.* TO 'root'@'$host'; FLUSH PRIVILEGES;\"";
                        
                        // Command to copy the existing database to the new one
                        $copy_db_command = "mysqldump -u $mysql_user -p$mysql_password $existing_database | mysql -u $mysql_user -p$mysql_password $new_database";
                        
                        exec($create_db_command, $output1, $return_var1);
                        exec($grant_privileges_command, $output2, $return_var2);
                        exec($copy_db_command, $output3, $return_var3);
                       
                       // add admin signup
                       
        
                        $adminData = [
                            'username' => 'admin',
                            'password' => password_hash($this->request->getPost("password"), PASSWORD_DEFAULT), // Securely hashed password
                            'email' => $email,
                            'role' => 'admin'
                        ];
                         $user_data["user_type"] = "staff";
                         $user_data["client_id"] = 0;
                         $user_data["is_admin"] = 1;
                        $adminData=$user_data;
                
                        // Step 3: Switch to the new database
                        $config = [
                            'DSN'      => '',
                            'hostname' => 'localhost',
                            'username' => $mysql_user,
                            'password' => $mysql_password,
                            'database' => $new_database,
                            'DBDriver' => 'MySQLi',
                            'DBPrefix' => 'pcm_',
                            'pConnect' => false,
                            'DBDebug'  => true,
                            'charset'  => 'utf8',
                            'DBCollat' => 'utf8_general_ci',
                        ];
                        //$newDb = $this->load->database($config, TRUE); // TRUE to return the db instance
                        // 

                        $newDb = \Config\Database::connect($config);
                        // Step 4: Insert admin user data into the new database
                        //$newDb->insert('pcm_users', $adminData); // Assuming 'users' is the table for user data
                        $newDb->table('pcm_users')->insert($adminData);
                   
                // Rake code ends for multi tenancy
                 
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
                return false;
            }
        


        if ($user_id) {
             $strip_url = $package_url->payment_button; 
             $encoded_id = $client_id;
           // $url = $strip_url . '?client_reference_id=' . $encoded_id;
            //return redirect()->to($url);
            // Custom checkout 
            $payment_setting=$this->Payment_methods_model->get_oneline_payment_method("stripe"); 
            $secretKey=$payment_setting->secret_key;
            require_once APPPATH . 'ThirdParty/Stripe/vendor/autoload.php';
             \Stripe\Stripe::setApiKey($secretKey);
             $successUrl = base_url('payment-success').'?session_id={CHECKOUT_SESSION_ID}&package_name=' . urlencode($package_url->package_name) . '&price=' . urlencode($package_url->price). '&pkgId=' . $package_url->id;
             $priceId=$package_url->stripe_price_id;
        
             $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId, 
                    'quantity' => 1,
                ]],
                'mode' => 'subscription', 
                'success_url' => $successUrl,
                'cancel_url' => base_url('/payment-cancel'),
                'allow_promotion_codes' => true,
                'client_reference_id'=> $client_id,
            ]);

            return redirect()->to($session->url);
            //  Custom checkout ends here    
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    
    }
    
    public function checkLogin()
    {
        
         $isLoggedIn = $this->Users_model->login_user_id();
       
       
        return $this->response->setJSON(['isLoggedIn' => $isLoggedIn]);
    }
    
    /*public function startPayment($packageId)
        {
            require_once APPPATH . 'ThirdParty/Stripe/vendor/autoload.php';
            // Load package details
            $package = $this->Package_price_model->getPackageById($packageId)->getRow();
            if (!$package) {
                throw new \Exception('Package not found');
            }
            $payment_setting=$this->Payment_methods_model->get_oneline_payment_method("stripe"); 
            $secretKey=$payment_setting->secret_key;
            // Initialize Stripe
            \Stripe\Stripe::setApiKey($secretKey);
            $successUrl = base_url('/payment-success?package_name=' . urlencode($package->package_name) . '&price=' . urlencode($package->price). '&pkgId=' . $package->id);

        
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $package->package_name,
                        ],
                        'unit_amount' => $package->price * 100, // Amount in cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment', 
                'success_url' => $successUrl,
                'cancel_url' => base_url('/payment-cancel'),
                'allow_promotion_codes' => true,
            ]);
        
            return redirect()->to($session->url);
        }*/
        
        // For subscription 
         public function startPayment($packageId)
        {
            //echo "hi"; die;
            require_once APPPATH . 'ThirdParty/Stripe/vendor/autoload.php';
            // Load package details
            $package = $this->Package_price_model->getPackageById($packageId)->getRow();
            $contact_info = $this->Users_model->get_one($this->Users_model->login_user_id());
            //print_r($contact_info->client_id); die;
            // $view_data['model_info'] = $this->Clients_model->get_one($client_id);
            $userId=$contact_info->client_id;
            if (!$package) {
                throw new \Exception('Package not found');
            }
            $payment_setting=$this->Payment_methods_model->get_oneline_payment_method("stripe"); 
            $secretKey=$payment_setting->secret_key;
            // Initialize Stripe
            \Stripe\Stripe::setApiKey($secretKey);
           // $successUrl = base_url('/payment-success?response={CHECKOUT_SESSION_ID}&package_name=' . urlencode($package->package_name) . '&price=' . urlencode($package->price). '&pkgId=' . $package->id);
            $successUrl = base_url('payment-success').'?session_id={CHECKOUT_SESSION_ID}&package_name=' . urlencode($package->package_name) . '&price=' . urlencode($package->price). '&pkgId=' . $package->id;
            $priceId=$package->stripe_price_id;
        
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId, 
                    'quantity' => 1,
                ]],
                'mode' => 'subscription', 
                'success_url' => $successUrl,
                'cancel_url' => base_url('/payment-cancel'),
                'allow_promotion_codes' => true,
                'client_reference_id'=> $userId,
            ]);
        
            return redirect()->to($session->url);
        }


        public function paymentSuccess()
        { 
            // Get Stripe payment settings from the database
            $payment_setting = $this->Payment_methods_model->get_oneline_payment_method("stripe"); 
            $secretKey = $payment_setting->secret_key;
            $stripekey = $secretKey;
          
            $sessionId = '';
            if (isset($_GET['session_id']) && !empty($_GET['session_id'])) {
                $sessionId = $_GET['session_id'];
            } 
        
            // Load Stripe library
            require_once APPPATH . 'ThirdParty/Stripe/vendor/autoload.php';
            \Stripe\Stripe::setApiKey($stripekey);
        
            try {
                // Retrieve Stripe session
                if($sessionId){
                     $session = \Stripe\Checkout\Session::retrieve($sessionId);
                }else{
                    $session='';
                }
                if (!$session) {
                    die('Error: Failed to retrieve Stripe session.');
                }
                $subscriptionId = $session->subscription;
                $subscription = \Stripe\Subscription::retrieve($subscriptionId);
               
                
                $items = $subscription->items->data;
                foreach ($items as $item) {
                    $productId = $item->price->product;
                }
                $package = $this->Package_price_model->getPackageByStripeId($productId)->getRow();
             
                // Get invoice ID from session
                $invoice_id = $session->invoice;
                if (!$invoice_id) {
                    die('Error: Invoice ID not found in Stripe session.');
                }
        
                // Retrieve invoice details from Stripe
                $invoiceData = \Stripe\Invoice::retrieve($invoice_id);
                if (!$invoiceData) {
                    die('Error: Failed to retrieve invoice data.');
                }
                  // Create billing portal session (for customer to manage subscription)
                $portalSession = \Stripe\BillingPortal\Session::create([
                    'customer' => $session->customer,
                    'return_url' => 'https://bpms247.com/',
                ]);
                 $portal_url = $portalSession->url;
              //  echo "Customer Portal URL: " . $portal_url;

                // Extract payment details
                $clientId = $session->client_reference_id;
                $amount_paid = $session->amount_total / 100; // Convert from cents
                
        
                $payment_details = array(
                    'checkout_session_data' => $session,
                    'invoiceData' => $invoiceData,
                    'client_reference_id' => $session->client_reference_id,
                    'customer_id' => $session->customer,
                    'subscription_id' => $session->subscription,
                    'payment_method' => $session->payment_method,
                    'payment_status' => $session->payment_status,
                    'amount_total' => $amount_paid,
                    'currency' => $session->currency,
                    'invoice_id' => $session->invoice,
                    'reciept_url' => $invoiceData->hosted_invoice_url,
                );
            // echo "<pre>"; print_r($invoiceData); //die;
                // Extract subscription data
                $subscriptionData = array(
                    'title' => $session->customer_details->name . '(' . $session->customer_details->email . ')',
                    'client_id' => $session->client_reference_id,
                    'bill_date' => date('Y-m-d', $invoiceData->lines->data[0]->period->start),
                    'end_date' => '',
                    'note' => '',
                    'status' => 'active',
                    'payment_status' => 'success',
                    'repeat_every' => $invoiceData->lines->data[0]->plan->interval_count,
                    'repeat_type' => $invoiceData->lines->data[0]->plan->interval . 's',
                    'next_recurring_date' => date('Y-m-d', $invoiceData->lines->data[0]->period->end),
                    'no_of_cycles_completed' => 1,
                    'type' => 'stripe',
                    'stripe_subscription_id' => $invoiceData->lines->data[0]->subscription,
                    'stripe_product_id' => $invoiceData->lines->data[0]->plan->product,
                    'stripe_product_price_id' => $invoiceData->lines->data[0]->plan->id,
                    'max_no_of_staff' =>$package->max_staff,
                    'max_no_of_client' =>$package->max_clients,
                    'invoice_limit' =>$package->max_invoices,
                    'project_limit' => $package->max_projects,
                    'storage_limit' =>$package->max_storage_mb,
                    'duration' =>$package->duration,
                    'support' =>$package->support,
                    'project_and_task_management' =>$package->project_task_management,
                    'enhanced_reporting' => $package->enhanced_reporting,
                    'collaboration_tools' => $package->collaboration_tools,
                    'stripe_hosted_invoice_url' => $portal_url,
                );
        
                // Check if the subscription already exists
                $model_info = $this->Subscriptions_model->get_details(array('client_id' => $clientId))->getResult();
                if (count($model_info) > 0) {
                    $subscription_id = $model_info[0]->id;
                    $subscription_id = $this->Subscriptions_model->ci_save($subscriptionData, $subscription_id);
                } else {
                    $subscription_id = $this->Subscriptions_model->ci_save($subscriptionData);
                }
        
                if ($subscription_id) {
                    // Save subscription items
                    $subscriptionItems = array(
                        'title' => $invoiceData->lines->data[0]->description,
                        'description' => $invoiceData->lines->data[0]->description,
                        'quantity' => 1,
                        'unit_type' => 'Unit',
                        'rate' => $amount_paid,
                        'total' => $amount_paid,
                        'subscription_id' => $subscription_id,
                    );
        
                    $this->Subscription_items_model->ci_save($subscriptionItems);
        
                    // Create Invoice
                    $inv_data = array(
                        'type' => 'invoice',
                        'client_id' => $clientId,
                        'bill_date' => date('Y-m-d', $invoiceData->lines->data[0]->period->start),
                        'due_date' => date('Y-m-d', $invoiceData->lines->data[0]->period->start),
                        'status' => 'not_paid',
                        'subscription_id' => $subscription_id,
                        'invoice_total' => $amount_paid,
                        'invoice_subtotal' => $amount_paid,
                    );
        
                    $invoice_id = $this->Invoices_model->ci_save($inv_data);
                    if ($invoice_id) {
                        $inv_updateData = array(
                            'number_sequence' => $invoice_id,
                            'number_year' => 0,
                            'display_id' => 'INVOICE #' . $invoice_id,
                        );
                        $this->Invoices_model->ci_save($inv_updateData, $invoice_id);
        
                        // Save invoice items
                        $invoiceItems = array(
                            'title' => $invoiceData->lines->data[0]->description,
                            'description' => $invoiceData->lines->data[0]->description,
                            'quantity' => 1,
                            'unit_type' => 'Unit',
                            'rate' => $amount_paid,
                            'total' => $amount_paid,
                            'invoice_id' => $invoice_id,
                        );
                        $this->Invoice_items_model->ci_save($invoiceItems);
        
                        // Save invoice payment
                        $inv_payData = array(
                            'amount' => $amount_paid,
                            'payment_date' => date('Y-m-d'),
                            'payment_method_id' => 2, // Assuming 2 is Stripe
                            'invoice_id' => $invoice_id,
                            'transaction_id' => $invoiceData->payment_intent,
                            'status' => 1,
                            'created_by' => '',
                        );
                        $this->Invoice_payments_model->ci_save($inv_payData);
                    $this->parser = \Config\Services::parser();
                   $email_template = $this->Email_templates_model->get_final_template("purchase_order_to_contact");
                  

                    $parser_data["SIGNATURE"] = $email_template->signature;
                    $parser_data["CONTACT_NAME"] = $session->customer_details->name;
                    
                    // Dummy / calculated values - you can update these based on actual PO data if needed
                    $parser_data["PO_NUMBER"] = "INV-" . $invoice_id;
                    $parser_data["ORDER_DATE"] = date('Y-m-d', $invoiceData->created);
                    $parser_data["PO_NAME"] = $invoiceData->lines->data[0]->description;
                    $parser_data["PO_SUBTOTAL"] = number_format($amount_paid, 2);
                    $parser_data["PO_TAX_VALUE"] = "0.00"; // Update if tax is available
                    $parser_data["PO_VALUE"] = number_format($amount_paid, 2);
                    $parser_data["PO_LINK"] = $invoiceData->hosted_invoice_url;
                     
                    // Render the template
                    $message = $this->parser->setData($parser_data)->renderString($email_template->message);
                    $subject = $this->parser->setData($parser_data)->renderString($email_template->subject);
                    
                    send_app_mail($session->customer_details->email, $subject, $message);


                    }
                }
        
            } catch (\Stripe\Exception\ApiErrorException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        
            // Ensure $payment_details is not empty before returning view
            if (empty($payment_details)) {
                die('Debug: Payment details not found. Something went wrong.');
            }
        
            return view('frontend/payment-success', $payment_details);
        }

        
//         public function paymentSuccess()
//             { 
//                 $payment_setting=$this->Payment_methods_model->get_oneline_payment_method("stripe"); 
//                 $secretKey=$payment_setting->secret_key;
//                 $stripekey=$secretKey;
//                 $sessionId='';
//                 if(isset($_GET['response'])){
//                      $sessionId=$_GET['response'];
//                 }
//                 //die; stripekey
//                 require_once APPPATH . 'ThirdParty/Stripe/vendor/autoload.php';
//                 \Stripe\Stripe::setApiKey($stripekey);
//             try {
//                             $session = \Stripe\Checkout\Session::retrieve($sessionId);
//                             $invoice_id = $session->invoice;
//                             $invoiceData = \Stripe\Invoice::retrieve($invoice_id);
//                             //$subscriptionData = \Stripe\Subscriptions::retrieve($session->subscription);
//                             /*echo "<pre>";
//                             print_r($session); print_r($invoiceData);*/
//                             $clientId=$session->client_reference_id;
//                             $amount_paid=$session->amount_total/100;
//                             $payment_details = array(
//                                 'checkout_session_data'=>$session,
//                                 'invoiceData'=>$invoiceData,
//                                 'client_reference_id'=>$session->client_reference_id,
//                                 'customer_id'=>$session->customer,
//                                 'subscription_id' => $session->subscription,
//                                 'payment_method' => $session->payment_method,
//                                 'payment_status' => $session->payment_status,
//                                 'amount_total' => $amount_paid,
//                                 'currency' => $session->currency,
//                                 'invoice_id' => $session->invoice,
//                                 'reciept_url' => $invoiceData->hosted_invoice_url,

//                                 // Add more payment details as needed
//                             );
//                             $subscriptionData=array(
//                                 'title'=>$session->customer_details->name.'('.$session->customer_details->email.')',
//                                 'client_id'=>$session->client_reference_id,
//                                 'bill_date'=>date('Y-m-d',$invoiceData->lines->data[0]->period->start),
//                                 'end_date'=>'',
//                                 'note'=>'',
//                                 'status'=>'active',
//                                 'payment_status'=>'success',
//                                 'repeat_every'=>$invoiceData->lines->data[0]->plan->interval_count,
//                                 'repeat_type'=>$invoiceData->lines->data[0]->plan->interval.'s',
//                                 'next_recurring_date'=>date('Y-m-d',$invoiceData->lines->data[0]->period->end),
//                                 'no_of_cycles_completed'=>1,
//                                 'type'=>'stripe',
//                                 'stripe_subscription_id'=>$invoiceData->lines->data[0]->subscription,
//                                 'stripe_product_id'=>$invoiceData->lines->data[0]->plan->product,
//                                 'stripe_product_price_id'=>$invoiceData->lines->data[0]->plan->id,
//                             );
//                             $model_info = $this->Subscriptions_model->get_details(array('client_id'=>$clientId))->getResult();
//                         if(count($model_info)>0){
//                             $subscription_id= $model_info[0]->id;
//                             $subscription_id = $this->Subscriptions_model->ci_save($subscriptionData,$subscription_id);
//                         }else{                  
//                             $subscription_id = $this->Subscriptions_model->ci_save($subscriptionData);
//                         }
                    
//                         if ($subscription_id) {
                           
//                                 $subscriptionItems=array(
//                                         'title'=>$invoiceData->lines->data[0]->description,
//                                         'description'=>$invoiceData->lines->data[0]->description,
//                                         'quantity'=>1,
//                                         'unit_type'=>'Unit',
//                                         'rate'=>$session->amount_total/100,
//                                         'total'=>$session->amount_total/100,
//                                         'subscription_id'=>$subscription_id,
//                                 );

//                                 $this->Subscription_items_model->ci_save($subscriptionItems);

//                                 // Invoice Create 
//                                 $inv_data['type']='invoice';
//                                 $inv_data['client_id']=$clientId;
//                                 $inv_data['bill_date']=date('Y-m-d',$invoiceData->lines->data[0]->period->start);
//                                 $inv_data['due_date']=date('Y-m-d',$invoiceData->lines->data[0]->period->start);
//                                 $inv_data['status']='not_paid';
//                                 $inv_data['subscription_id']=$subscription_id;
//                                 $inv_data['invoice_total']=$amount_paid;
//                                 $inv_data['invoice_subtotal']=$amount_paid;
//                                 $inv_data['client_id']=$clientId;
//                                 $invoice_id=$this->Invoices_model->ci_save($inv_data);
//                                 if($invoice_id){
//                                     $inv_updateData['number_sequence']=$invoice_id;
//                                     $inv_updateData['number_year']=0;
//                                     $inv_updateData['display_id']='INVOICE #'.$invoice_id;
//                                     $this->Invoices_model->ci_save($inv_updateData,$invoice_id);
//                                     $invocieItems=array(
//                                         'title'=>$invoiceData->lines->data[0]->description,
//                                         'description'=>$invoiceData->lines->data[0]->description,
//                                         'quantity'=>1,
//                                         'unit_type'=>'Unit',
//                                         'rate'=>$amount_paid,
//                                         'total'=>$amount_paid,
//                                         'invoice_id'=>$invoice_id,
//                                 );
//                                     $invoice_items_saved=$this->Invoice_items_model->ci_save($invocieItems);

//                                     $inv_payData['amount']=$amount_paid;
//                                     $inv_payData['payment_date']=date('Y-m-d');
//                                     $inv_payData['payment_method_id']=2;
//                                     $inv_payData['invoice_id']=$invoice_id;
//                                     $inv_payData['transaction_id']=$invoiceData->payment_intent;
//                                     $inv_payData['status']=1;
//                                     $inv_payData['created_by']='';
//                                     $invoice_payment_saved= $this->Invoice_payments_model->ci_save($inv_payData);
                                   
//                                     $subject='BPMS 247: Subscription Successful';
//                                     /*$message='<!DOCTYPE html>
// <html>
// <head>
//     <meta charset="UTF-8">
//     <meta name="viewport" content="width=device-width, initial-scale=1">
//     <title>Subscription Successful</title>
//     <style>
//         body {
//             font-family: Arial, sans-serif;
//             background-color: #f4f4f4;
//             margin: 0;
//             padding: 0;
//         }
//         .container {
//             max-width: 600px;
//             margin: 20px auto;
//             background: #ffffff;
//             padding: 20px;
//             border-radius: 8px;
//             box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
//         }
//         .header {
//             text-align: center;
//             background-color: #007bff;
//             color: #ffffff;
//             padding: 15px;
//             border-radius: 8px 8px 0 0;
//             font-size: 22px;
//             font-weight: bold;
//         }
//         .content {
//             padding: 20px;
//             text-align: center;
//         }
//         .button {
//             display: inline-block;
//             background-color: #007bff;
//             color: #ffffff;
//             padding: 12px 20px;
//             border-radius: 5px;
//             text-decoration: none;
//             font-size: 16px;
//             margin-top: 20px;
//         }
//         .footer {
//             text-align: center;
//             padding: 15px;
//             font-size: 14px;
//             color: #666666;
//         }
//         .footer a {
//             color: #007bff;
//             text-decoration: none;
//         }
//     </style>
// </head>
// <body>

//     <div class="container">
//         <div class="header">🎉 Subscription Successful!</div>

//         <div class="content">
//             <p>Hey <strong></strong>,</p>
//             <p>Thank you for subscribing to <strong>BPMS 247 CRM</strong>! 🚀</p>
//             <p>Your subscription is now **active**, and you can start managing your business efficiently with our powerful CRM solution.</p>
            
//             <p><strong>Subscription Details:</strong></p>
//             <p> $invoiceData->lines->data[0]->description </p>
//             <p> Subscription ID: $invoiceData->lines->data[0]->subscription </p>
//             <p> Repeat Every: $invoiceData->lines->data[0]->plan->interval </p>

//             <p>📅 Start Date: <strong>'.date("m-d-Y").'</strong></p>
//             <p>📆 Expiry Date: <strong>'.date("m-d-Y",$invoiceData->lines->data[0]->period->end).'</strong></p>
//             <p> Next Payment Date : '.date("m-d-Y",$invoiceData->lines->data[0]->period->end).' </p>
//             <p>💳 Payment: <strong>'.$amount_paid.'</strong></p>

//             <a href="https://bpms247.com" class="button">Go to Dashboard</a>
//         </div>

//         <div class="footer">
//             Need any help? Contact our support team at  
//             <a href="mailto:support@bpms247.com">support@bpms247.com</a>  
//             <br><br>
//             Thank you for choosing <strong>BPMS 247 CRM</strong>!  
//         </div>
//     </div>

// </body>
// </html>
//                                     ';


//                                     $this->sendEmail($clientId,$subject,$message);
//                                     */
//                                 }


//                             }
                        
//             } catch (\Stripe\Exception\ApiErrorException $e) {
//                 echo 'Error: ' . $e->getMessage();
//             }
//                 if (empty($payment_details)) {
//                     throw new \Exception('Payment data not found.');
//                 }
            
//                 return view('frontend/payment-success', $payment_details);
               
//             }
            
       public function paymentCancel()
          {
           // echo "Payment cancelled.";            
            app_redirect('signin');
          }




   
    

    private function has_recaptcha_error() {

        $ReCAPTCHA = new ReCAPTCHA();
        $response = $ReCAPTCHA->validate_recaptcha(false);

        if ($response === true) {
            return true;
        } else {
            array_push($this->signin_validation_errors, $response);
            return false;
        }
    }

    // check authentication
    function authenticate() {
        $validation = $this->validate_submitted_data(array(
            "email" => "required|valid_email",
            "password" => "required"
        ), true);

        $email = $this->request->getPost("email");
        $password = $this->request->getPost("password");
        if (!$email) {
            //loaded the page directly
            app_redirect('signin');
        }

        if (is_array($validation)) {
            //has validation errors
            $this->signin_validation_errors = $validation;
        }

        //check if there reCaptcha is enabled
        //if reCaptcha is enabled, check the validation
        if (get_setting("re_captcha_secret_key")) {
            //in this function, if any error found in recaptcha, that will be added
            $this->has_recaptcha_error();
        }

        //don't check password if there is any error
        if ($this->signin_validation_errors) {
            $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
            app_redirect('signin');
        }

        if (!$this->Users_model->authenticate($email, $password)) {
            //authentication failed
            array_push($this->signin_validation_errors, app_lang("authentication_failed"));
            $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
            app_redirect('signin');
        }

        //authentication success
        $redirect = $this->request->getPost("redirect");
        if ($redirect) {
            $allowed_host = $_SERVER['HTTP_HOST'];

            $parsed_redirect = parse_url($redirect);
            $redirect_host = get_array_value($parsed_redirect, "host");
            if ($allowed_host === $redirect_host) {
                return redirect()->to($redirect);
            } else {
                app_redirect('dashboard/view');
            }
        } else {
            app_redirect('dashboard/view');
        }
    }

    function sign_out() {
        $this->Users_model->sign_out();
    }

    //send an email to users mail with reset password link
   

  
    //finally reset the old password and save the new password
    function do_reset_password() {
        $this->validate_submitted_data(array(
            "key" => "required",
            "password" => "required"
        ));

        $key = $this->request->getPost("key");
        $password = $this->request->getPost("password");
        $valid_key = $this->is_valid_reset_password_key($key);

        if ($valid_key) {
            $email = get_array_value($valid_key, "email");
            $this->Users_model->update_password($email, password_hash($password, PASSWORD_DEFAULT));

            //user can't reset password two times with the same code
            $options = array("code" => $key, "type" => "reset_password");
            $verification_info = $this->Verification_model->get_details($options)->getRow();
            if ($verification_info->id) {
                $this->Verification_model->delete_permanently($verification_info->id);
            }

            echo json_encode(array("success" => true, 'message' => app_lang("password_reset_successfully") . " " . anchor("signin", app_lang("signin"))));
            return true;
        }

        echo json_encode(array("success" => false, 'message' => app_lang("error_occurred")));
    }

    //check valid key
    private function is_valid_reset_password_key($verification_code = "") {

        if ($verification_code) {
            $options = array("code" => $verification_code, "type" => "reset_password");
            $verification_info = $this->Verification_model->get_details($options)->getRow();

            if ($verification_info && $verification_info->id) {
                $reset_password_info = unserialize($verification_info->params);

                $email = get_array_value($reset_password_info, "email");
                $expire_time = get_array_value($reset_password_info, "expire_time");

                if ($email && filter_var($email, FILTER_VALIDATE_EMAIL) && $expire_time && $expire_time > time()) {
                    return array("email" => $email);
                }
            }
        }
    }

    public function sendEmail($client_id,$subject,$message)
    {
       // Set email parameters
        $email->setFrom('admin@bpms247.com', 'BPMS 247');
        $email->setTo('recipient@example.com');
        $email->setSubject($subject);
        $email->setMessage($message);

        // Send email
        if ($email->send()) {
            return 'Email sent successfully!';
        } else {
            return $email->printDebugger(['headers']);
        }
    }



   /* function book_appointment($service_id='',$category_id=''){
        $view_data['title']='Schedule your appointment with our specialists';
        $category_options=array();
        if($service_id !=''){
            $service_options=array('id'=>$service_id);
        }else{
         $service_options=array();   
        }
        if($category_id !=''){
            $category_options=array('id'=>$category_id);
        }
        $view_data['categories_list']= $this->Appointment_service_categories_model->get_details($category_options)->getResult();      
        $view_data['services_list']= $this->Services_model->get_details($service_options)->getResult();
        return view('appointment_services/book-appointments', $view_data);
    }*/

    public function book_appointment($service_id = '', $category_id = '')
        {
            helper(['url']);

            $view_data = [];
            $view_data['title'] = 'Schedule your appointment with our specialists';

            // Normalize & validate
            $service_id = ($service_id !== '' && is_numeric($service_id)) ? (int)$service_id : 0;
            $category_id = ($category_id !== '' && is_numeric($category_id)) ? (int)$category_id : 0;

            // If service is given but category is not, infer category from service
            if ($service_id && !$category_id) {
                $service = $this->Services_model->get_details(['id' => $service_id])->getRow();
                if ($service && !empty($service->category_id)) {
                    $category_id = (int) $service->category_id;
                }
            }

            // Categories list (only active + not deleted)
            // Note: your model currently checks deleted=0. If you added is_active in categories, include it in query too.
            $categories = $this->Appointment_service_categories_model->get_details([])->getResult();

            // Optionally filter in PHP if you have is_active column (safe if column doesn't exist)
            $categories_filtered = [];
            foreach ($categories as $c) {
                if (property_exists($c, 'is_active')) {
                    if ((int)$c->is_active !== 1) {
                        continue;
                    }
                }
                $categories_filtered[] = $c;
            }

            // If category_id is passed but invalid, reset it
            if ($category_id) {
                $valid_cat = false;
                foreach ($categories_filtered as $c) {
                    if ((int)$c->id === $category_id) {
                        $valid_cat = true;
                        break;
                    }
                }
                if (!$valid_cat) {
                    $category_id = 0;
                    $service_id = 0;
                }
            }

            // Services list:
            // - If category chosen: only show services for that category
            // - Else: show all services
            $service_options = [];
            if ($category_id) {
                $service_options['category_id'] = $category_id;
            }
            if ($service_id) {
                $service_options['id'] = $service_id;
            }

            $services = $this->Services_model->get_details($service_options)->getResult();

            // If service_id is passed but doesn't belong to category_id (or doesn't exist), reset service_id
            if ($service_id) {
                $valid_service = false;
                foreach ($services as $s) {
                    if ((int)$s->id === $service_id) {
                        $valid_service = true;
                        break;
                    }
                }
                if (!$valid_service) {
                    $service_id = 0;
                    // Reload services for category only (or all)
                    $service_options = [];
                    if ($category_id) {
                        $service_options['category_id'] = $category_id;
                    }
                    $services = $this->Services_model->get_details($service_options)->getResult();
                }
            }

            // Optionally filter inactive services if you added is_active column to services (safe if not present)
            $services_filtered = [];
            foreach ($services as $s) {
                if (property_exists($s, 'is_active')) {
                    if ((int)$s->is_active !== 1) {
                        continue;
                    }
                }
                $services_filtered[] = $s;
            }

            // View data
            $view_data['categories_list'] = $categories_filtered;
            $view_data['services_list'] = $services_filtered;

            // Preselected IDs (use in view/JS to auto-select & load service details)
            $view_data['selected_category_id'] = $category_id;
            $view_data['selected_service_id'] = $service_id;

            // Status messaging remains compatible with your current ?status=success/fail
            $view_data['status'] = $this->request->getGet('status');

            return view('appointment_services/book-appointments', $view_data);
        }


    function list_categories(){
        $view_data['title']='Schedule your appointment with our specialists';
        $options=array();
        $view_data['categories_list']= $this->Appointment_service_categories_model->get_details($options)->getResult();
        return view('appointment_services/book-appointments', $view_data);
    }

    function list_services(){
        $view_data['title']='Schedule your appointment with our specialists';
        $options=array();
        $view_data['services_list']= $this->Appointment_services_model->get_details($options)->getResult();
        return view('appointment_services/book-appointments', $view_data);
    }

    /*function booking_save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "service_id" => "required",
        ));

        $id = $this->request->getPost('id');

        $item_data = array(
            "name" => $this->request->getPost('name'),
            "email" => $this->request->getPost('email'),
            "phone" => $this->request->getPost('phone'),
            "start_time" => $this->request->getPost('start_time'),
            "end_time" => $this->request->getPost('end_time'),
            "meeting_link" => $this->request->getPost('meeting_link'),
            "notes" => $this->request->getPost('notes'),
            "payment_status" => $this->request->getPost('payment_status'),
            "service_id" => $this->request->getPost('service_id'),
            "price" => $this->request->getPost('price'),
            "service_description" => $this->request->getPost('service_description'),
            "duration" => $this->request->getPost('duration_minutes'),
            "description" => $this->request->getPost('description'),
            "category_id" => $this->request->getPost('category_id'),
            "service_description" => $this->request->getPost('service_description'),
           
              
        ); 
        
        $item_id = $this->Appointment_services_model->ci_save($item_data, $id);
        if ($item_id) {
            $options = array("id" => $item_id);
            $item_info = $this->Appointment_services_model->get_details($options)->getRow();
            // app_redirect('front/book_appointment?status=success');
            if($this->request->getPost('price') !='0'){
             $checkouturl="front/checkout?sid=".$item_data['service_id'].'&cid='.$item_id;
            echo "<script>window.top.location.href = '" . base_url($checkouturl) . "';</script>";
            }else{

             $checkouturl="front/thank_you?un=".base64_encode($item_data['name'])."&apr=".base64_encode($item_data['price']).'&aid='.base64_encode($item_id);
            //echo  "<script>window.top.location.href = '" . base_url($checkouturl) . "';</script>";
            app_redirect($checkouturl);
             
            }
    exit;

        } else {
           app_redirect('front/book_appointment?status=fail');
        }
    }
    

  public function booking_save()
        {

            $this->validate_submitted_data(array(
                "service_id"  => "required|numeric",
                "category_id" => "required|numeric",
                "name"        => "required",
                "email"       => "required",
                "phone"       => "required",
                "start_time"  => "required",
                "end_time"    => "required"
            ),true);

            try {
                $service_id  = (int)$this->request->getPost('service_id');
                $category_id = (int)$this->request->getPost('category_id');

                $service = $this->Services_model->get_details(["id" => $service_id])->getRow();
                if (!$service || (int)$service->category_id !== $category_id) {
                    return redirect()->to(base_url('front/book_appointment?status=fail&msg=invalid_service'));
                }

                // Slot selection must be present (your new UI posts hidden UTC fields)
                $start_raw = trim((string)$this->request->getPost('start_time'));
                $end_raw   = trim((string)$this->request->getPost('end_time'));

                if (!$start_raw || !$end_raw) {
                    return redirect()->to(base_url('front/book_appointment?status=fail&msg=invalid_time'));
                }

                // Accept ISO-8601 (UTC) or datetime-local; normalize to Y-m-d H:i:s
                $start_ts = strtotime($start_raw);
                $end_ts   = strtotime($end_raw);

                if (!$start_ts || !$end_ts || $end_ts <= $start_ts) {
                    return redirect()->to(base_url('front/book_appointment?status=fail&msg=invalid_time'));
                }

                if ($start_ts < time()) {
                    return redirect()->to(base_url('front/book_appointment?status=fail&msg=past_time'));
                }

                // Duration must be server-authoritative
                $duration_minutes = (int)($service->duration_minutes ?? 0);
                if ($duration_minutes <= 0) {
                    return redirect()->to(base_url('front/book_appointment?status=fail&msg=invalid_service'));
                }

                $start_dt = date('Y-m-d H:i:s', $start_ts);
                $end_dt   = date('Y-m-d H:i:s', strtotime("+{$duration_minutes} minutes", $start_ts));

                // Pricing policy (server authoritative)
                if (method_exists($this->Services_model, 'resolve_pricing_policy')) {
                    $policy = $this->Services_model->resolve_pricing_policy($service_id);
                    $payment_required = (int)$policy['requires_payment'];
                    $payment_amount   = (float)$policy['price'];
                    $payment_source   = $policy['source'] ?? 'service';
                } else {
                    $payment_amount   = (float)($service->price ?? 0);
                    $payment_required = $payment_amount > 0 ? 1 : 0;
                    $payment_source   = 'service';
                }
                
                // MUST exist and must not fatal
                if (!method_exists($this->Appointment_services_model, 'pick_best_available_staff')) {
                    log_message('error', 'pick_best_available_staff() is missing in Appointment_services_model');
                    return redirect()->to(base_url('front/book_appointment?status=fail&msg=server_error'));
                } 
                        
                $staff_id = (int)$this->Appointment_services_model->pick_best_available_staff($start_dt, $end_dt);
                //echo $staff_id; die;
                if (!$staff_id) {
                    return redirect()->to(base_url('front/book_appointment?status=fail&msg=no_staff_available'));
                }
        //print_r($this->request->getPost()); die;
                $item_data = [
                    "name"                => trim((string)$this->request->getPost('name')),
                    "email"               => trim((string)$this->request->getPost('email')),
                    "phone"               => trim((string)$this->request->getPost('phone')),
                    "description"         => (string)$this->request->getPost('description'),
                    "notes"               => (string)$this->request->getPost('notes'),
                    "meeting_link"        => trim((string)$this->request->getPost('meeting_link')),
                    "category_id"         => $category_id,
                    "service_id"          => $service_id,
                    "staff_id"            => $staff_id,
                    "start_time"          => $start_dt,
                    "end_time"            => $end_dt,
                    "duration"            => (string)$duration_minutes,
                    "price"               => (string)$payment_amount,
                    "service_description" => strip_tags((string)($service->description ?? '')),
                    "status"              => "pending",
                    "payment_status"      => $payment_required ? "unpaid" : "waived",
                ];
        //print_r($item_data); die;
                $appointment_id = $this->Appointment_services_model->ci_save($item_data);

                if (!$appointment_id) {
                    return redirect()->to(base_url('front/book_appointment?status=fail&msg=save_failed'));
                }

                if ($payment_required && $payment_amount > 0) {
                    return redirect()->to(base_url('front/checkout?cid=' . (int)$appointment_id));
                }

                return redirect()->to(base_url('front/thank_you?aid=' . base64_encode($appointment_id)));

            } catch (\Throwable $e) {
                // CRITICAL: log the real error
                log_message('error', 'booking_save failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());

                // keep message generic for customers
                return redirect()->to(base_url('front/book_appointment?status=fail&msg=server_error'));
            }
        }
        */


/*
public function checkout($cid = '')
{
    helper('url');

    try {
        // Allow both /checkout/123 and /checkout?cid=123
        $cid = $cid ?: $this->request->getGet('cid');
        $cid = (is_numeric($cid) && (int)$cid > 0) ? (int)$cid : 0;

        if (!$cid) {
            return redirect()->to(base_url('front/book_appointment?status=fail&msg=invalid_appointment'));
        }

        require_once APPPATH . 'ThirdParty/Stripe/vendor/autoload.php';

        // Use model to prepare checkout payload
        $payload = $this->Appointment_services_model->prepare_checkout_payload($cid);

        if (!$payload || empty($payload['appointment'])) {
            return redirect()->to(base_url('front/book_appointment?status=fail&msg=not_found'));
        }

      
        $appointment      = $payload['appointment'];
        $amount           = (float)$payload['amount'];
        $requires_payment = (bool)$payload['requires_payment'];

        // If no payment needed or already paid -> go to thank you
        if (!$requires_payment) {
            return redirect()->to(
                base_url('front/thank_you?aid=' . base64_encode((string)$appointment->id))
            );
        }

        // Stripe configuration
        $payment_setting = $this->Payment_methods_model->get_oneline_payment_method("stripe");
        $secretKey       = $payment_setting->secret_key ?? null;

        if (!$secretKey) {
            return redirect()->to(base_url('front/book_appointment?status=fail&msg=stripe_not_configured'));
        }

        \Stripe\Stripe::setApiKey($secretKey);

        $encodedAid = base64_encode((string)$appointment->id);

        $successUrl = base_url(
            '/front/thank_you?aid=' . $encodedAid . '&session_id={CHECKOUT_SESSION_ID}'
        );

        // IMPORTANT: pass aid so failed_payment can identify the appointment
        $cancelUrl  = base_url(
            'front/failed_payment?aid=' . $encodedAid
        );

        // Create Stripe Checkout Session
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => (($appointment->services_title ?? 'Service') . ' - Appointment'),
                    ],
                    'unit_amount' => (int)round($amount * 100), // cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'allow_promotion_codes' => true,
            'client_reference_id'=> $appointment->id,
            'metadata' => [
                'appointment_id' => (string)$appointment->id
            ],
        ]);

        // Delegate persistence of Stripe session id to the model
        $this->Appointment_services_model->save_stripe_session_id(
            (int)$appointment->id,
            (string)$session->id
        );

        return redirect()->to($session->url);

    } catch (\Throwable $e) {
        log_message(
            'error',
            'checkout error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine()
        );

        return redirect()->to(base_url('front/book_appointment?status=fail&msg=server_error'));
    }
}




    

    public function thank_you()
        {
            helper('url');

            try {
                // 1) Validate & decode appointment id
                $aidEncoded = (string)$this->request->getGet('aid');
                if (!$aidEncoded) {
                    return redirect()->to(base_url('front/book_appointment?status=fail&msg=invalid_appointment'));
                }

                $appointment_id = (int)base64_decode($aidEncoded);
                if ($appointment_id <= 0) {
                    return redirect()->to(base_url('front/book_appointment?status=fail&msg=invalid_appointment'));
                }

                // 2) Load appointment
                $options = array('id' => $appointment_id);
                $appointment = $this->Appointment_services_model->get_details($options)->getRow();

                if (!$appointment) {
                    return redirect()->to(base_url('front/book_appointment?status=fail&msg=not_found'));
                }

                // 3) Handle payment / status update
                $isPaidService = (float)$appointment->price > 0;

                if ($isPaidService) {
                    // If you later wire Stripe session verification, do it here.
                    // For now, consider reaching this page == successful payment.
                    $updateData = array(
                        "payment_status" => "paid",
                        "status"         => "confirmed",
                        "payment_amount" => $appointment->price,
                    );
                    $this->Appointment_services_model->ci_save($updateData, $appointment_id);
                } else {
                    // Free/waived service: ensure status is at least confirmed
                    if ($appointment->status === "pending") {
                        $this->Appointment_services_model->ci_save(array(
                            "status" => "confirmed",
                            "payment_amount" => $appointment->price
                        ), $appointment_id);
                    }
                }

                // Reload updated appointment for the view/email
                $view_data['appointment_details'] = $this->Appointment_services_model
                    ->get_details($options)
                    ->getRow();

                // 4) Prepare and send confirmation email
                $this->parser = \Config\Services::parser();
                $email_template = $this->Email_templates_model->get_final_template("new_appointment_confirmation");

                $Company_model = model('App\Models\Company_model');
                $company_info  = $Company_model->get_one_where(array("is_default" => true));

                $appointmentDetails = $view_data['appointment_details'];
                $email              = $appointmentDetails->email;

                $parser_data = array();
                $parser_data["SIGNATURE"]          = $email_template->signature;
                $parser_data["CONTACT_FIRST_NAME"] = $appointmentDetails->name; // from appointment, not $_GET
                $parser_data["COMPANY_NAME"]       = $company_info->name;
                $parser_data["LOGO_URL"]           = get_logo_url();

                $parser_data["EMAIL"]          = $appointmentDetails->email;
                $parser_data["START_DATE_TIME"] = $appointmentDetails->start_time;
                $parser_data["END_DATE_TIME"]   = $appointmentDetails->end_time;
                $parser_data["DURATION"]        = $appointmentDetails->duration;
                $parser_data["SERVICE_NAME"]    = $appointmentDetails->services_title ?? '';

                $message = $this->parser->setData($parser_data)->renderString($email_template->message);
                $subject = $this->parser->setData($parser_data)->renderString($email_template->subject);

                // Use your existing helper
                $send_result = send_app_mail($email, $subject, $message);

                // Optional: fallback using CI email service (as in your current code)
                $emailService = \Config\Services::email();
                $emailService->initialize([
                    'protocol'   => 'smtp',
                    'SMTPHost'   => 'mail.bpms247.com',
                    'SMTPUser'   => 'no-reply@bpms247.com',
                    'SMTPPass'   => 'Stillbon@001',
                    'SMTPPort'   => 587,
                    'SMTPCrypto' => 'tls',
                    'mailType'   => 'html',
                    'charset'    => 'utf-8',
                    'newline'    => "\r\n",
                ]);

                $emailService->setTo($email);
                // $emailService->setCc('admin@bpms247.com,info@fleurysolutions.com');
                $emailService->setFrom('no-reply@bpms247.com', 'BPMS247');
                $emailService->setSubject($subject);
                $emailService->setMessage($message);
                $emailService->send();

                // 5) Render thank you page
                return view('appointment_services/thank-you', $view_data);

            } catch (\Throwable $e) {
                log_message(
                    'error',
                    'thank_you failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine()
                );

                return redirect()->to(base_url('front/book_appointment?status=fail&msg=server_error'));
            }
        }


    public function failed_payment()
{
    helper('url');

    try {
        // Try to identify the appointment (if cancel_url passes ?aid=base64(id))
        $aidEncoded     = (string)$this->request->getGet('aid');
        $appointment    = null;
        $appointment_id = 0;

        if ($aidEncoded !== '') {
            $appointment_id = (int)base64_decode($aidEncoded);
            if ($appointment_id > 0) {
                $options      = array('id' => $appointment_id);
                $appointment  = $this->Appointment_services_model->get_details($options)->getRow();
            }
        }

        if ($appointment) {
            // Make sure payment is marked as not completed
            $updateData = array(
                "payment_status" => "unpaid",
            );

            // Optionally mark status as "pending" (so admin can decide to retry / reassign)
            if ($appointment->status !== "complete" && $appointment->status !== "cancelled") {
                $updateData["status"] = "pending";
            }

            $this->Appointment_services_model->ci_save($updateData, $appointment_id);

            // Send customer a polite notification about payment cancellation
            $customerEmail = $appointment->email;

            if ($customerEmail) {
                // Basic subject/body – you can later replace this with a template if desired.
                $subject = "Your appointment payment was not completed";

                $serviceName = $appointment->services_title ?? 'your appointment';
                $startTime   = $appointment->start_time;
                $price       = $appointment->price;

                $body  = "<p>Dear " . htmlspecialchars($appointment->name) . ",</p>";
                $body .= "<p>We noticed that the payment for your appointment ";
                $body .= "(<strong>" . htmlspecialchars($serviceName) . "</strong>) ";
                $body .= "scheduled for <strong>" . htmlspecialchars($startTime) . "</strong> ";
                $body .= "was not completed.</p>";

                if ($price !== null && $price !== '') {
                    $body .= "<p>Appointment amount: <strong>" . htmlspecialchars($price) . "</strong></p>";
                }

                $body .= "<p>Your booking is currently marked as <strong>pending</strong>. ";
                $body .= "If this was unintentional, you can return to the booking page and try again, ";
                $body .= "or contact our team if you need assistance.</p>";

                $body .= "<p>Best regards,<br>BPMS247 Team</p>";

                // Use CI Email service (same SMTP config pattern you already use in thank_you)
                $emailService = \Config\Services::email();
                $emailService->initialize([
                    'protocol'   => 'smtp',
                    'SMTPHost'   => 'mail.bpms247.com',
                    'SMTPUser'   => 'no-reply@bpms247.com',
                    'SMTPPass'   => 'Stillbon@001',
                    'SMTPPort'   => 587,
                    'SMTPCrypto' => 'tls',
                    'mailType'   => 'html',
                    'charset'    => 'utf-8',
                    'newline'    => "\r\n",
                ]);

                $emailService->setTo($customerEmail);
                $emailService->setFrom('no-reply@bpms247.com', 'BPMS247');
                $emailService->setSubject($subject);
                $emailService->setMessage($body);
                $emailService->send();

                // OPTIONAL: you can also send an internal notification to admins here
                // (e.g. to a fixed mailbox or to admin users).
            }
        }

        // In all cases, send the user back to booking page with a clear message
        return redirect()->to(base_url('front/book_appointment?status=fail&msg=payment_cancelled'));

    } catch (\Throwable $e) {
        log_message(
            'error',
            'failed_payment error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine()
        );

        return redirect()->to(base_url('front/book_appointment?status=fail&msg=server_error'));
    }
}
*/



    /* public function getServicesByCategory()
    {
        $categoryId = $this->request->getGet('category_id');
        if ($categoryId) {
            $service_options = array("category_id" => $categoryId);
             $services= $this->Services_model->get_details($service_options)->getResult();
             $dropdown='<select name="service_id" class="form-select" id="service_id" onchange="getService(this.value);">

             <option value="">-- Select --</option>
             ';
             foreach($services as $servicesData){
                $dropdown .= '<option value="'.$servicesData->id.'">'.$servicesData->name.'</option>';
             }
             $dropdown.='</select>';
            echo $dropdown;
        } else {
            return $dropdown;
        }
    }

      public function getServicesByService()
    {
        $serviceId = $this->request->getGet('service_id');
        if ($serviceId) {
            $service_options = array("id" => $serviceId);
             $services= $this->Services_model->get_details($service_options)->getResult();
             $dropdown ='';
             foreach($services as $servicesData){
                $dropdown .='<div class="form-group">
                                <div class="row">
                                    <label for="title" class=" col-md-3">'.app_lang("price").'</label>
                                    <div class="col-md-9">';
                $dropdown .= '<input type="text" readonly="true" name="price" class="form-control" value="'.$servicesData->price.'">';
                $dropdown .='</div> </div> </div>';

                $dropdown .='<div class="form-group">
                                <div class="row">
                                    <label for="title" class=" col-md-3">'.app_lang("duration_minutes").'</label>
                                    <div class="col-md-9">';
                $dropdown .= '<input type="text" readonly="true" id="duration_minutes" name="duration_minutes" class="form-control" value="'.$servicesData->duration_minutes.'">';
                $dropdown .='</div> </div> </div>';


                $dropdown .='<div class="form-group">
                                <div class="row">
                                    <label for="title" class=" col-md-3">'.app_lang("description").'</label>
                                    <div class="col-md-9">';
                $dropdown .= '<textarea type="text" readonly="true" name="service_description" class="form-control">'.$servicesData->description.' </textarea>';
                $dropdown .='</div> </div> </div>';

             }
             

            echo $dropdown;
        } else {
            return $dropdown;
        }
    }*/


    public function getServicesByCategory()
        {
            $categoryId = $this->request->getGet('category_id');
            $categoryId = (is_numeric($categoryId) && (int)$categoryId > 0) ? (int)$categoryId : 0;

            if (!$categoryId) {
                return $this->response->setStatusCode(400)->setBody('<select name="service_id" class="form-select" id="service_id"><option value="">-- Select --</option></select>');
            }

            // Fetch services for category (model already filters deleted=0)
            $services = $this->Services_model->get_details(["category_id" => $categoryId])->getResult();

            // Optional filter for is_active if column exists
            $filtered = [];
            foreach ($services as $s) {
                if (property_exists($s, 'is_active') && (int)$s->is_active !== 1) {
                    continue;
                }
                $filtered[] = $s;
            }

            $html = '<select name="service_id" class="form-select" id="service_id" onchange="getService(this.value);">';
            $html .= '<option value="">-- Select --</option>';

            foreach ($filtered as $s) {
                $html .= '<option value="' . (int)$s->id . '">' . esc($s->name) . '</option>';
            }

            $html .= '</select>';

            return $this->response->setContentType('text/html')->setBody($html);
        }


        public function getServicesByService()
            {
                $serviceId = $this->request->getGet('service_id');
                $serviceId = (is_numeric($serviceId) && (int)$serviceId > 0) ? (int)$serviceId : 0;

                if (!$serviceId) {
                    return $this->response->setStatusCode(400)->setBody('');
                }

                $service = $this->Services_model->get_details(["id" => $serviceId])->getRow();
                if (!$service) {
                    return $this->response->setStatusCode(404)->setBody('<div class="alert alert-danger">Service not found.</div>');
                }

                // Optional: if you added is_active on services, enforce it
                if (property_exists($service, 'is_active') && (int)$service->is_active !== 1) {
                    return $this->response->setStatusCode(403)->setBody('<div class="alert alert-warning">This service is not available.</div>');
                }

                // Default behavior (until pricing lock fully wired):
                // paid if price > 0, else free
                $price = (float)($service->price ?? 0);
                $duration = (int)($service->duration_minutes ?? 0);

                // If you already implemented resolve_pricing_policy(), use it:
                // try { $policy = $this->Services_model->resolve_pricing_policy($serviceId); ... } catch (\Exception $e) {}
                $payment_required = ($price > 0) ? 1 : 0;
                $payment_badge = $payment_required
                    ? '<span class="badge bg-primary">Paid appointment</span>'
                    : '<span class="badge bg-success">Free appointment</span>';

                $desc = $service->description ?? '';

                $html = '';
                $html .= '<div class="alert alert-light border">';
                $html .= '<div class="d-flex justify-content-between align-items-center">';
                $html .= '<strong>' . esc($service->name) . '</strong>';
                $html .= $payment_badge;
                $html .= '</div>';
                $html .= '</div>';

                // Price field
                $html .= '<div class="form-group">
                            <div class="row">
                                <label class="col-md-3">' . app_lang("price") . '</label>
                                <div class="col-md-9">
                                    <input type="text" readonly="true" class="form-control" value="' . esc(number_format($price, 2)) . '">
                                    <input type="hidden" name="price" value="' . esc($price) . '">
                                    <input type="hidden" name="payment_required" value="' . (int)$payment_required . '">
                                </div>
                            </div>
                          </div>';

                // Duration
                $html .= '<div class="form-group">
                            <div class="row">
                                <label class="col-md-3">' . app_lang("duration_minutes") . '</label>
                                <div class="col-md-9">
                                    <input type="text" readonly="true" id="duration_minutes" name="duration_minutes" class="form-control" value="' . esc($duration) . '">
                                </div>
                            </div>
                          </div>';

                // Description
                $html .= '<div class="form-group">
                            <div class="row">
                                <label class="col-md-3">' . app_lang("description") . '</label>
                                <div class="col-md-9">
                                    <textarea readonly="true" name="service_description" class="form-control" rows="4">' . esc(strip_tags($desc)) . '</textarea>
                                </div>
                            </div>
                          </div>';

                return $this->response->setContentType('text/html')->setBody($html);
            }


    function newsletter_subscription(){
         $Front_model = model('App\Models\Front_model');
         $emailData['email']=$this->request->getPost('email');
         $mail=$emailData['email'];
            $subject="New Subscriber Added-BPMS247";
            $message="Dear Admin, <br> Hope you are doing great! <br> New user has subscribed for the newsletter. Here are the details below.<br> Email: $mail <br> Thank you <br> Admin BPMS247.com";
               $response= $this->Front_model->save_newsletter($emailData); 
               If($response){

                $emailService = \Config\Services::email();
                $emailService->initialize([
                        'protocol' => 'smtp',
                        'SMTPHost' => 'mail.bpms247.com',
                        'SMTPUser' => 'no-reply@bpms247.com',
                        'SMTPPass' => 'Stillbon@001',
                        'SMTPPort' => 587,
                        'SMTPCrypto' => 'tls',
                        'mailType' => 'html',
                        'charset' => 'utf-8',
                        'newline' => "\r\n",
                    ]);
                     
                     $emailService->setTo('admin@bpms247.com');
                 // $emailService->setTo('rakeshacn123@gmail.com');
                     $emailService->setCc('info@fleurysolutions.com');
                    $emailService->setFrom('no-reply@bpms247.com', 'BPMS247');
                    $emailService->setSubject($subject);
                    $emailService->setMessage($message);
                    $emailService->send();
                    app_redirect('front/index?response=success');
               }else{
                app_redirect('front/index?response=fail');
                }
                    
    }

    
        

            private function _slot_overlaps_breaks(\DateTime $slotStart, \DateTime $slotEnd, $breaks, $date, $tz)
            {
                foreach ($breaks as $b) {
                    $bStart = new \DateTime("$date {$b->break_start}", new \DateTimeZone($tz));
                    $bEnd = new \DateTime("$date {$b->break_end}", new \DateTimeZone($tz));

                    // overlap if start < breakEnd AND end > breakStart
                    if ($slotStart < $bEnd && $slotEnd > $bStart) return true;
                }
                return false;
            }

            private function _has_conflict($staffId, \DateTime $startUtc, \DateTime $endUtc)
            {
                // overlap if existing.start < newEnd AND existing.end > newStart
                $table = $this->db->prefixTable('customer_appointments');

                $sql = "SELECT id FROM $table
                        WHERE deleted=0
                          AND staff_id=" . (int)$staffId . "
                          AND status IN ('pending','confirmed')
                          AND start_time < " . $this->db->escape($endUtc->format('Y-m-d H:i:s')) . "
                          AND end_time > " . $this->db->escape($startUtc->format('Y-m-d H:i:s')) . "
                        LIMIT 1";

                $row = $this->db->query($sql)->getRow();
                return (bool)$row;
            }

        public function generateSlots($staff, $date, $duration, $userTimezone)
    {
        $slots = [];

        foreach ($staff as $member) {

            $availability = $this->getAvailability($member->id, $date);
            $breaks = $this->getBreaks($member->id, $date);
            $booked = $this->getBookedSlots($member->id, $date);

            $cursor = new DateTime("$date {$availability->start_time}", new DateTimeZone($availability->timezone));
            $end = new DateTime("$date {$availability->end_time}", new DateTimeZone($availability->timezone));

            while ($cursor < $end) {
                $slotEnd = clone $cursor;
                $slotEnd->modify("+{$duration} minutes");

                if ($this->conflicts($cursor, $slotEnd, $breaks, $booked)) {
                    $cursor->modify("+15 minutes");
                    continue;
                }

                $display = clone $cursor;
                $display->setTimezone(new DateTimeZone($userTimezone));

                $slots[] = [
                    'start_utc' => $cursor->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
                    'end_utc'   => $slotEnd->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
                    'display'   => $display->format('h:i A')
                ];

                $cursor->modify("+15 minutes");
            }
        }

        return $slots;
    }


    private function _pick_round_robin_staff($serviceId, \DateTime $startUtc, \DateTime $endUtc)
{
    $userTz = $this->request->getPost('user_timezone') ?: 'UTC';
    $local = (clone $startUtc)->setTimezone(new \DateTimeZone($userTz));
    $date = $local->format('Y-m-d');
    $dayShort = $local->format('D'); // Mon/Tue...

    $staffList = $this->Users_model->get_active_staff()->getResult();
    $eligible = [];

    foreach ($staffList as $staff) {
        $staffTz = $staff->timezone ?: $userTz;

        // Convert slot into staff timezone for availability check
        $staffStart = (clone $startUtc)->setTimezone(new \DateTimeZone($staffTz));
        $staffEnd = (clone $endUtc)->setTimezone(new \DateTimeZone($staffTz));

        if ($staffStart->format('Y-m-d') !== $staffEnd->format('Y-m-d')) continue;

        $avail = $this->Appointment_availability_model->get_details(['staff_id' => $staff->id])->getResult();
        $todayAvail = null;
        foreach ($avail as $a) {
            if ($a->day_of_week === $dayShort && (int)$a->is_available === 1) {
                $todayAvail = $a;
                break;
            }
        }
        if (!$todayAvail) continue;

        // within availability window
        $aStart = new \DateTime($date . ' ' . $todayAvail->start_time, new \DateTimeZone($staffTz));
        $aEnd = new \DateTime($date . ' ' . $todayAvail->end_time, new \DateTimeZone($staffTz));
        if ($staffStart < $aStart || $staffEnd > $aEnd) continue;

        // breaks check
        $breaks = $this->Weekly_breaks_model->get_details(['staff_id' => $staff->id, 'day_of_week' => $dayShort])->getResult();
        if ($this->_slot_overlaps_breaks($staffStart, $staffEnd, $breaks, $date, $staffTz)) continue;

        // conflicts check (UTC stored)
        if ($this->_has_conflict($staff->id, $startUtc, $endUtc)) continue;

        $eligible[] = $staff->id;
    }

    if (!count($eligible)) return null;

    // Choose least booked TODAY
    $table = $this->db->prefixTable('customer_appointments');
    $counts = [];
    foreach ($eligible as $sid) {
        $sql = "SELECT COUNT(*) as c FROM $table
                WHERE deleted=0 AND staff_id=" . (int)$sid . "
                  AND DATE(start_time)= " . $this->db->escape($startUtc->format('Y-m-d')) . "
                  AND status IN ('pending','confirmed')";
        $counts[$sid] = (int)$this->db->query($sql)->getRow()->c;
    }

    asort($counts);
    return array_key_first($counts);
}

       
public function get_available_dates()
{
    $service_id = (int)$this->request->getGet("service_id");
    $from = (string)$this->request->getGet("from");
    $to   = (string)$this->request->getGet("to");
    $tz   = (string)$this->request->getGet("timezone") ?: (get_setting("timezone") ?: "UTC");

    if (!$service_id || !$from || !$to) {
        return $this->response->setJSON([
            "success" => false,
            "message" => "Missing parameters."
        ]);
    }

    try {
        $res = $this->Appointment_services_model->get_available_dates_range($service_id, $from, $to, $tz);

        // Ensure array
        if (is_object($res)) {
            $res = (array)$res;
        }
        if (!is_array($res)) {
            $res = [];
        }

        $dates = [];
        if (isset($res["available_dates"]) && is_array($res["available_dates"])) {
            $dates = $res["available_dates"];
        } elseif (isset($res["dates"]) && is_array($res["dates"])) {
            $dates = $res["dates"];
        }

        $dates = array_values(array_unique(array_filter($dates)));

        return $this->response->setJSON([
            "success" => true,
            "available_dates" => $dates,
            "timezone_label" => $res["timezone_label"] ?? $tz,
        ]);

    } catch (\Throwable $e) {
        log_message(
            "error",
            "get_available_dates failed: {$e->getMessage()} | {$e->getFile()}:{$e->getLine()}"
        );

        return $this->response->setJSON([
            "success" => false,
            "message" => "Server error."
        ]);
    }
}



public function get_available_slots()
{
    $service_id = (int)$this->request->getGet("service_id");
    $date = (string)$this->request->getGet("date");
    $tz   = (string)$this->request->getGet("timezone") ?: "UTC";

    if (!$service_id || !$date) {
        return $this->response->setJSON(["success" => false, "message" => "Missing parameters."]);
    }

    $res = $this->Appointment_services_model->get_available_slots_for_date($service_id, $date, $tz);
    return $this->response->setJSON($res);
}

public function booking_save()
{
    $this->validate_submitted_data([
        "service_id"  => "required|numeric",
        "category_id" => "required|numeric",
        "name"        => "required",
        "email"       => "required",
        "phone"       => "required",
        "start_time"  => "required",
        "end_time"    => "required",
    ], true);

    try {
        $service_id  = (int) $this->request->getPost("service_id");
        $category_id = (int) $this->request->getPost("category_id");

        $service = $this->Services_model->get_details(["id" => $service_id])->getRow();
        if (!$service || (int) $service->category_id !== $category_id) {
            return redirect()->to(base_url("front/book_appointment?status=fail&msg=invalid_service"));
        }

        $start_iso = trim((string) $this->request->getPost("start_time"));
        $end_iso   = trim((string) $this->request->getPost("end_time"));
        if (!$start_iso || !$end_iso) {
            return redirect()->to(base_url("front/book_appointment?status=fail&msg=invalid_time"));
        }

        // Parse timestamps safely
        $start_ts = strtotime($start_iso);
        if (!$start_ts) {
            return redirect()->to(base_url("front/book_appointment?status=fail&msg=invalid_time"));
        }
        if ($start_ts < time()) {
            return redirect()->to(base_url("front/book_appointment?status=fail&msg=past_time"));
        }

        // Server-authoritative duration
        $duration_minutes = (int) ($service->duration_minutes ?? 0);
        if ($duration_minutes <= 0) {
            return redirect()->to(base_url("front/book_appointment?status=fail&msg=invalid_service"));
        }

        // Store in UTC
        $start_dt = gmdate("Y-m-d H:i:s", $start_ts);
        $end_dt   = gmdate("Y-m-d H:i:s", strtotime("+{$duration_minutes} minutes", $start_ts));

        // Decide staff assignment
        $assignment_mode = (string) ($service->assignment_mode ?? "round_robin");

        $staff_id = null;
        $assigned_by = null; // optional; set if you want (admin id)
        $assigned_at = null;

        if ($assignment_mode === "round_robin") {
            try {
                $staff_id = (int) $this->Appointment_services_model
                    ->pick_staff_for_slot_round_robin($service_id, $start_dt, $end_dt);
            } catch (\Throwable $rrEx) {
                log_message("error", "Round-robin failed: ".$rrEx->getMessage()." | ".$rrEx->getFile().":".$rrEx->getLine());
                $staff_id = 0;
            }

            // Fallback: if nobody available, assign default admin (per your requirement)
            if (!$staff_id) {
                $admin_id = (int) $this->Appointment_services_model->get_default_admin_id();
                if ($admin_id > 0) {
                    $staff_id = $admin_id;
                }
            }

            if ($staff_id) {
                $assigned_at = gmdate("Y-m-d H:i:s");
            }
        }

        // Payment rules (service-level based on your schema)
        $allowFree       = ((int) ($service->allow_free_booking ?? 0) === 1);
        $requiresPayment = ((int) ($service->requires_payment ?? 0) === 1);
        $price           = (float) ($service->price ?? 0);

        $payment_required = ($requiresPayment && !$allowFree && $price > 0) ? 1 : 0;
        $payment_amount   = $payment_required ? $price : 0.00;
        $payment_status   = $payment_required ? "unpaid" : "waived";

        $item_data = [
            "name"                => trim((string) $this->request->getPost("name")),
            "email"               => trim((string) $this->request->getPost("email")),
            "phone"               => trim((string) $this->request->getPost("phone")),
            "description"         => (string) $this->request->getPost("description"),
            "notes"               => (string) $this->request->getPost("notes"),

            "category_id"         => $category_id,
            "service_id"          => $service_id,

            "staff_id"            => $staff_id ?: null,
            "assignment_status"   => $staff_id ? "assigned" : "unassigned",
            "assigned_at"         => $assigned_at,
            "assigned_by"         => $assigned_by,

            "start_time"          => $start_dt,
            "end_time"            => $end_dt,
            "duration"            => (string) $duration_minutes,

            "price"               => (string) $payment_amount,
            "service_description" => strip_tags((string) ($service->description ?? "")),

            "status"              => "pending",
            "payment_status"      => $payment_status,
            "payment_required"    => $payment_required,
            "payment_amount"      => $payment_amount,
            "payment_decision_source" => "service",
        ];

        // Save appointment
        $appointment_id = null;
        try {
            $appointment_id = $this->Appointment_services_model->ci_save($item_data);
        } catch (\Throwable $saveEx) {
            log_message("error", "Appointment save failed: ".$saveEx->getMessage()." | ".$saveEx->getFile().":".$saveEx->getLine());
            return redirect()->to(base_url("front/book_appointment?status=fail&msg=save_failed"));
        }

        if (!$appointment_id) {
            return redirect()->to(base_url("front/book_appointment?status=fail&msg=save_failed"));
        }

        // NOTE: Meeting link creation + notifications should happen AFTER save.
        // Keep booking reliable first. Then add async-like behavior or post-save calls.

        if ($payment_required) {
            return redirect()->to(base_url("front/checkout?cid=" . (int) $appointment_id));
        }

        return redirect()->to(base_url("front/thank_you?aid=" . base64_encode((string) $appointment_id)));

    } catch (\Throwable $e) {
        log_message("error", "booking_save failed: ".$e->getMessage()." | ".$e->getFile().":".$e->getLine());
        return redirect()->to(base_url("front/book_appointment?status=fail&msg=server_error"));
    }
}


public function checkout($cid = "")
{
    $cid = $cid ?: $this->request->getGet("cid");
    $cid = (is_numeric($cid) && (int)$cid > 0) ? (int)$cid : 0;

    if (!$cid) {
        return redirect()->to(base_url("front/book_appointment?status=fail&msg=invalid_appointment"));
    }

    require_once APPPATH . "ThirdParty/Stripe/vendor/autoload.php";

    $appointment = $this->Appointment_services_model->get_details(["id" => $cid])->getRow();
    if (!$appointment) {
        return redirect()->to(base_url("front/book_appointment?status=fail&msg=not_found"));
    }

    if (($appointment->payment_status ?? "") === "paid" || (float)($appointment->payment_amount ?? 0) <= 0) {
        return redirect()->to(base_url("front/thank_you?aid=" . base64_encode((string)$appointment->id)));
    }

    $payment_setting = $this->Payment_methods_model->get_oneline_payment_method("stripe");
    $secretKey = $payment_setting->secret_key ?? null;
    if (!$secretKey) {
        return redirect()->to(base_url("front/book_appointment?status=fail&msg=stripe_not_configured"));
    }

    \Stripe\Stripe::setApiKey($secretKey);

    $amount = (float)$appointment->payment_amount;
    $successUrl = base_url()."/front/thank_you?aid=" . base64_encode((string)$appointment->id) . "&session_id={CHECKOUT_SESSION_ID}";
    $cancelUrl  = base_url(). "/front/failed_payment?aid=" . base64_encode((string)$appointment->id);

    $session = \Stripe\Checkout\Session::create([
        "payment_method_types" => ["card"],
        "line_items" => [[
            "price_data" => [
                "currency" => "usd",
                "product_data" => [
                    "name" => ($appointment->services_title ?? "Service") . " - Appointment",
                ],
                "unit_amount" => (int)round($amount * 100),
            ],
            "quantity" => 1,
        ]],
        "mode" => "payment",
        'allow_promotion_codes' => true,
        "success_url" => $successUrl,
        "cancel_url" => $cancelUrl,
        "metadata" => [
            "appointment_id" => (string)$appointment->id
        ],
    ]);

    // Persist via model (as you requested)
    $this->Appointment_services_model->save_stripe_session_id((int)$appointment->id, (string)$session->id);

    return redirect()->to($session->url);
}

public function thank_you()
{
    helper('url');

    $aid_raw = (string)$this->request->getGet('aid');
    $appointment_id = (int)base64_decode($aid_raw);

    if (!$appointment_id) {
        return redirect()->to(base_url('front/book_appointment?status=fail&msg=invalid_appointment'));
    }

    $appointment = $this->Appointment_services_model->get_details(['id' => $appointment_id])->getRow();
    if (!$appointment) {
        return redirect()->to(base_url('front/book_appointment?status=fail&msg=not_found'));
    }

    // If paid flow, you should validate Stripe and mark paid BEFORE sending confirmation.
    // If you already do it elsewhere, keep it. Otherwise:
    if (($appointment->payment_required ?? 0) == 1 && ($appointment->payment_status ?? '') !== 'paid') {
        // In paid flow, thank_you is called with session_id. Validate it.
        $session_id = (string)$this->request->getGet('session_id');
        if ($session_id) {
            $ok = $this->Appointment_services_model->confirm_stripe_payment_and_mark_paid($appointment_id, $session_id);
            if (!$ok) {
                // Don’t confirm appointment email if payment not confirmed
                log_message('error', "Payment not confirmed for appointment {$appointment_id}, session_id={$session_id}");
                return redirect()->to(base_url('front/book_appointment?status=fail&msg=payment_not_confirmed'));
            }

            // Reload updated appointment
            $appointment = $this->Appointment_services_model->get_details(['id' => $appointment_id])->getRow();
        }

        

    }
// $meet = $this->create_google_meet_for_appointment($appointment->id);
// print_r($meet); die;
      /**
     * -------------------------------------------------
     * 2. CREATE GOOGLE MEET (ONLY ONCE)
     * -------------------------------------------------
     */
 /*   if (empty($appointment->meeting_link)) {

    echo $meetingLink = $this->create_meeting_from_appointment($appointment);
die;
    if ($meetingLink) {
        $this->Appointment_services_model->ci_save([
            'meeting_link' => $meetingLink
        ], $appointment->id);
    }
}
*/
    // Build email parser data
    $Company_model = model('App\Models\Company_model');
    $company_info = $Company_model->get_one_where(["is_default" => true]);

    $parser_data = [
        "CONTACT_FIRST_NAME" => (string)$appointment->name,
        "COMPANY_NAME"       => (string)($company_info->name ?? "Company"),
        "LOGO_URL"           => get_logo_url(),
        "EMAIL"              => (string)$appointment->email,
        "START_DATE_TIME"    => (string)$appointment->start_time,
        "END_DATE_TIME"      => (string)$appointment->end_time,
        "DURATION"           => (string)$appointment->duration,
        "SERVICE_NAME"       => (string)($appointment->services_title ?? ''), // if your get_details joins service title
        "MEETING_LINK"       => (string)($appointment->meeting_link ?? ''),
        "PAYMENT_STATUS"     => (string)($appointment->payment_status ?? ''),
    ];

    // Customer email
    $tpl = $this->render_email_template("new_appointment_confirmation", $parser_data);
    $customer_ok = $this->send_mail_safe((string)$appointment->email, $tpl['subject'], $tpl['message']);

    // Staff email (if assigned)
    $staff_ok = true;
    if (!empty($appointment->staff_id)) {
        $staff_email = $this->Appointment_services_model->get_staff_email((int)$appointment->staff_id);
        if ($staff_email) {
            $tpl_staff = $this->render_email_template("new_appointment_assigned_staff", $parser_data);
            $staff_ok = $this->send_mail_safe($staff_email, $tpl_staff['subject'], $tpl_staff['message']);
        }
    }

    // Admin email
    $admin_emails = array('rakeshacn123@gmail.com');
    $admin_ok = true;
    if (!empty($admin_emails)) {
        $tpl_admin = $this->render_email_template("new_appointment_admin_notification", $parser_data);
        // send to first admin, BCC others (avoids exposing addresses)
        $to = $admin_emails[0];
        $bcc = array_slice($admin_emails, 1);
        $admin_ok = $this->send_mail_safe($to, $tpl_admin['subject'], $tpl_admin['message'], [], $bcc);
    }

    // Optional: record notification status in logs
    log_message('info', "Appointment {$appointment_id} emails: customer=" . (int)$customer_ok . " staff=" . (int)$staff_ok . " admin=" . (int)$admin_ok);

    $view_data['appointment_details'] = $appointment;
    return view('appointment_services/thank-you', $view_data);
}

/**
 * Create Google Meet using API and return meeting link
 *
 * @param object $appointment
 * @return string|false  Google Meet link or false on failure
 */
private function create_meeting_from_appointment(object $appointment)
{
    $apiUrl = base_url('https://bpms247.com/google_meet_meetings/create_google_meeting_api');
    $apiKey = 'bpms247_active';

    // Safety check
    if (empty($appointment->start_time)) {
        log_message('error', 'Appointment start_time missing');
        return false;
    }

    $payload = [
        'title' => 'Appointment: ' . ($appointment->services_title ?? 'Consultation'),
        'description' => 'Appointment with ' . ($appointment->name ?? ''),
        'start_date' => date('Y-m-d', strtotime($appointment->start_time)),
        'start_time' => date('h:i A', strtotime($appointment->start_time)),
        'share_with_team_members' => 'specific',
        'share_with_specific_team_members' => (string) ($appointment->staff_id ?? ''),
        'share_with_client_contacts' => 'none',
        'created_by' => (int) ($appointment->staff_id ?? 0)
    ];

    $ch = curl_init($apiUrl);

    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'X-API-KEY: ' . $apiKey
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        log_message('error', 'Meeting API cURL error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode !== 200 || empty($result['success'])) {
        log_message('error', 'Meeting API failed: ' . $response);
        return false;
    }

    return $result['data']['google_meet_link'] ?? false;
}

public function failed_payment()
{
    $aid_b64 = (string)$this->request->getGet("aid");
    $appointment_id = $aid_b64 ? (int)base64_decode($aid_b64) : 0;

    if ($appointment_id) {
        try {
            $this->Appointment_services_model->mark_payment_unpaid($appointment_id);
            $appointment = $this->Appointment_services_model->get_details(["id" => $appointment_id])->getRow();
            if ($appointment) {
                // Notify about failure (re-using same template is OK short-term)
                $this->Appointment_services_model->send_appointment_emails($appointment, "payment_failed");
            }
        } catch (\Throwable $e) {
            log_message("error", "failed_payment error: ".$e->getMessage());
        }
    }

    return redirect()->to(base_url("front/book_appointment?status=fail&msg=payment_failed"));
}

private function mail_config(): array
{
    // Ideally load from settings table, env, or CI Email config.
    // Keeping your current SMTP values but DO NOT hardcode passwords long-term.
    return [
        'protocol'   => 'smtp',
        'SMTPHost'   => 'mail.bpms247.com',
        'SMTPUser'   => 'no-reply@bpms247.com',
        'SMTPPass'   => 'Stillbon@001',
        'SMTPPort'   => 587,
        'SMTPCrypto' => 'tls',
        'mailType'   => 'html',
        'charset'    => 'utf-8',
        'newline'    => "\r\n",
        'CRLF'       => "\r\n",
    ];
}

private function send_mail_safe(string $to, string $subject, string $message, array $cc = [], array $bcc = []): bool
{
    try {
        $email = \Config\Services::email();
        $email->initialize($this->mail_config());

        $email->setFrom('no-reply@bpms247.com', 'BPMS247');
        $email->setTo($to);

        if (!empty($cc))  $email->setCC($cc);
        if (!empty($bcc)) $email->setBCC($bcc);

        $email->setSubject($subject);
        $email->setMessage($message);

        $ok = $email->send();

        if (!$ok) {
            // This is critical: get the actual error
            log_message('error', 'Email send failed: ' . print_r($email->printDebugger(['headers', 'subject', 'body']), true));
        }

        return (bool)$ok;

    } catch (\Throwable $e) {
        log_message('error', 'Email exception: ' . $e->getMessage());
        return false;
    }
}

private function render_email_template(string $template_slug, array $parser_data): array
{
    $this->parser = \Config\Services::parser();
    $email_template = $this->Email_templates_model->get_final_template($template_slug);

    if (!$email_template) {
        // fallback
        return [
            'subject' => 'Appointment Update',
            'message' => '<p>Your appointment has been updated.</p>'
        ];
    }

    $parser_data["SIGNATURE"] = $email_template->signature ?? '';

    $subject = $this->parser->setData($parser_data)->renderString((string)$email_template->subject);
    $message = $this->parser->setData($parser_data)->renderString((string)$email_template->message);

    return ['subject' => $subject, 'message' => $message];
}


/**
 * Create a Google Meet link for an appointment and save it into the appointment record.
 *
 * Requirements:
 * - Appointment start_time is stored in UTC (Y-m-d H:i:s)
 * - Google_Meet_Integration_Google_Calendar::save_event() returns:
 *     ['google_event_id' => '...', 'join_url' => 'https://meet.google.com/...']
 * - Appointment table has columns: meeting_link, google_event_id (optional but recommended)
 *
 * Notes:
 * - This does NOT use HTTP/cURL.
 * - This does NOT touch Google_Meet_meetings_model table.
 * - Create the Meet:
 *     - For free appointments: right after appointment save
 *     - For paid appointments: only after Stripe payment is confirmed
 *
 * @param int $appointmentId
 * @return array
 */
private function create_google_meet_for_appointment(int $appointmentId): array
{
    try {
        helper(['general', 'date']);

        // Load appointment
        $appointment = $this->Appointment_services_model->get_details(['id' => $appointmentId])->getRow();
        if (!$appointment) {
            return ['success' => false, 'meeting_link' => null, 'message' => 'Appointment not found.'];
        }

        // Idempotent check
        if (!empty($appointment->meeting_link)) {
            return ['success' => true, 'meeting_link' => (string)$appointment->meeting_link, 'message' => 'Meeting already exists.'];
        }

        if (empty($appointment->start_time)) {
            return ['success' => false, 'meeting_link' => null, 'message' => 'Appointment start_time is missing.'];
        }

        // Build meeting data
        $serviceTitle = (string)($appointment->services_title ?? 'Consultation');
        $customerName = (string)($appointment->name ?? 'Customer');
        $customerEmail = trim((string)($appointment->email ?? ''));

        $title = 'Appointment: ' . $serviceTitle;
        $description = 'Appointment with ' . $customerName . ($customerEmail ? " ({$customerEmail})" : '');

        // created_by: use assigned staff if present, else 0
        $createdBy = !empty($appointment->staff_id) ? (int)$appointment->staff_id : 0;

        // Use App Library (no plugin modification)
        $meetService = new \App\Libraries\AppointmentGoogleMeetService();

        $res = $meetService->createMeet([
            'title' => $title,
            'description' => $description,
            'start_time_utc' => (string)$appointment->start_time, // UTC
            'created_by' => $createdBy,

            // Optional: control visibility in plugin meeting list
            'share_with_team_members' => 'all',
            'share_with_client_contacts' => 'none',
        ]);

        if (empty($res['success'])) {
            return ['success' => false, 'meeting_link' => null, 'message' => (string)($res['message'] ?? 'Failed to create Meet')];
        }

        // Save to appointment table
        $update = [
            'meeting_link' => (string)$res['join_url'],
        ];

        // Optional: if your appointment table has google_event_id column
        if (!empty($res['google_event_id'])) {
            $update['google_event_id'] = (string)$res['google_event_id'];
        }

        // Optional: if you want to store plugin meeting row id for traceability
        // if (!empty($res['meeting_row_id'])) { $update['google_meet_meeting_row_id'] = (int)$res['meeting_row_id']; }

        $this->Appointment_services_model->ci_save($update, $appointmentId);

        return [
            'success' => true,
            'meeting_link' => (string)$res['join_url'],
            'message' => 'Google Meet created successfully.'
        ];

    } catch (\Throwable $e) {
        log_message('error', 'create_google_meet_for_appointment failed: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
        return ['success' => false, 'meeting_link' => null, 'message' => (ENVIRONMENT !== 'production') ? $e->getMessage() : 'Server error.'];
    }
}



}
