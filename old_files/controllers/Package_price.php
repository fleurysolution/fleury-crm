<?php
namespace App\Controllers;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use Stripe\PaymentLink;
// use App\Libraries\Stripe;

class Package_price extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
          $this->Package_price_model = model('Package_price_model');
    
    }

    //load item categories list view
    function index() {
    
       
        $payment_setting=$this->Payment_methods_model->get_oneline_payment_method("stripe"); //echo "<pre>"; 
        return $this->template->rander("package_price/index");
    }

    //load item category add/edit modal form
    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));
        
        $view_data['model_info'] = $this->Package_price_model->get_one($this->request->getPost('id'));
        return $this->template->view('package_price/modal_form', $view_data);
    }

    //save item category
    // function save() {
        
    //     $this->validate_submitted_data(array(
    //         "package_name" => "required",
    //         "price" => "required"
    //     ));

    //     $id = $this->request->getPost('id');
    //     $data = array(
    //         "package_name" => $this->request->getPost('package_name'),
    //         "price" => $this->request->getPost('price'),
    //         "duration" => $this->request->getPost('duration'),
    //         "button_text" => $this->request->getPost('button_text'),
    //         "description" => $this->request->getPost('description'),
    //     );
    //     $save_id = $this->Package_price_model->ci_save($data, $id);
    //     if ($save_id) {
    //         echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
    //     } else {
    //         echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
    //     }
    // }
    
    public function save()
    {
        
        $payment_setting=$this->Payment_methods_model->get_oneline_payment_method("stripe"); 
        $secretKey=$payment_setting->secret_key;
        $this->validate_submitted_data(array(
            "package_name" => "required",
            "price" => "required"
        ));
   
        require_once APPPATH . 'ThirdParty/Stripe/vendor/autoload.php';
    
        try {
            
            // Set the Stripe secret key
            Stripe::setApiKey($secretKey); // Replace with your actual key
    
            $id = $this->request->getPost('id');
    
            if (!empty($id)) {
                // Update existing product details
                $stripeProduct = Product::update($this->request->getPost('stripe_product_id'), [
                    'name' => $this->request->getPost('package_name'),
                    'description' => strip_tags($this->request->getPost('description')),
                ]);
    
                // Check if the product was updated successfully
                if (!isset($stripeProduct->id)) {
                    throw new \Exception('Failed to update Stripe product.');
                }
    
                // Retain the existing price (do not update)
                $stripePriceId = $this->request->getPost('stripe_price_id');
    
            } else {
                // Creating a new product and price for a new package
                $stripeProduct = Product::create([
                    'name' => $this->request->getPost('package_name'),
                    'description' => strip_tags($this->request->getPost('description')),
                ]);
                if (!isset($stripeProduct->id)) {
                    throw new \Exception('Failed to create Stripe product.');
                }
                
                $stripePrice = Price::create([
                    'unit_amount' => $this->request->getPost('price') * 100, // Convert to cents
                    'currency' => 'usd',
                    'recurring' => [
                        'interval' => $this->request->getPost('duration'),
                        'trial_period_days' => $this->request->getPost('trial_days'),
                    ],
                    'product' => $stripeProduct->id,
                ]);
    
                // Check if the price was created successfully
                if (!isset($stripePrice->id)) {
                    throw new \Exception('Failed to create Stripe price.');
                }
                $stripePriceId = $stripePrice->id;

                // Create a Payment Link
                $successUrl = base_url('payment-success').'/?response={CHECKOUT_SESSION_ID}';
                $paymentLink = PaymentLink::create([
                    'line_items' => [
                        [
                            'price' => $stripePriceId,
                            'quantity' => 1,
                        ],
                    ],
                    'after_completion' => [
                        'type' => 'redirect',
                        'redirect' => ['url' => $successUrl], // Redirect after payment
                    ],
                    'allow_promotion_codes' => true, 
                    'automatic_tax' => [
                        'enabled' => true, 
                    ],
                    'subscription_data' => [ 
                            'trial_period_days' => $this->request->getPost('trial_days'), 
                        ],
                ]);
            }

            // Prepare the data to be saved in the database
            $data = array(
                "package_name" => $this->request->getPost('package_name'),
                "price" => $this->request->getPost('price'),
                "duration" => $this->request->getPost('duration'),
                'trial_days' => $this->request->getPost('trial_days'),
                "button_text" => $this->request->getPost('button_text'),
                "description" => $this->request->getPost('description'),
                "no_of_user" => $this->request->getPost('max_staff'),
                "max_invoices" => $this->request->getPost('max_invoices'),
                "max_staff" => $this->request->getPost('max_staff'),
                "max_clients" => $this->request->getPost('max_clients'),
                "no_of_client" => $this->request->getPost('max_clients'),
                "max_projects" => $this->request->getPost('max_projects'),
                "max_file_uploads" => $this->request->getPost('max_file_uploads'),
                "max_storage_mb" => $this->request->getPost('max_storage_mb'),
                "data_limits" => $this->request->getPost('data_limits'),
                "support" => $this->request->getPost('support'),
                "status" => $this->request->getPost('package_status'),
                "project_task_management" => $this->request->getPost('project_task_management'),
                "enhanced_reporting" => $this->request->getPost('enhanced_reporting'),
                "collaboration_tools" => $this->request->getPost('collaboration_tools'),
                'stripe_product_id' => $stripeProduct->id,
                'stripe_price_id' => $stripePriceId, 
                'payment_button' => $paymentLink->url, 
            );
            
            $save_id = $this->Package_price_model->ci_save($data, $id);
    
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
    
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe API errors
            echo json_encode(array("success" => false, 'message' => $e->getMessage()));
        } catch (\Exception $e) {
            // Handle other errors
            echo json_encode(array("success" => false, 'message' => $e->getMessage()));
        }
    }




    //delete/undo an item category
  function delete() {
      
    $this->validate_submitted_data(array(
        "id" => "required|numeric"
    ));

    $id = $this->request->getPost('id');
        if ($this->Package_price_model->delete_package($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
}


    //get data for items category list
    function list_data() {
        $list_data = $this->Package_price_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    //get an expnese category list row
    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Package_price_model->get_details($options)->getRow();
        return $this->_make_row($data);
    }

    //prepare an item category list row
    private function _make_row($data) {
       
        return array($data->package_name,
            $data->price,
            modal_anchor(get_uri("package_price/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_items_category'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_items_category'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("package_price/delete"), "data-action" => "delete"))
        );
    }

    // Area Manager
      //load item categories list view
    function area_list() {
        return $this->template->rander("package_price/area_list");
    }

    function area_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

//        $view_data['categories_dropdown'] = $this->Material_categories_model->get_dropdown_list(array("title"));
        
        $view_data['model_info'] = $this->Material_area_model->get_one($this->request->getPost('id'));
        return $this->template->view('package_price/area_model_form', $view_data);
    }

    //save item category
    function area_save() {

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "areaname" => "required"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "areaname" => $this->request->getPost('areaname'),
            "description" => $this->request->getPost('description'),
        );
        $save_id = $this->Material_area_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_area_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //delete/undo an item category
    function area_delete() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Package_price_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Package_price_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    //get data for items category list
    function area_list_data() {
        $list_data = $this->Material_area_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_area_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    //get an expnese category list row
    private function _area_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Material_area_model->get_details($options)->getRow();
        return $this->_area_make_row($data);
    }

    //prepare an item category list row
    private function _area_make_row($data) {
        return array($data->areaname,
            substr($data->description, 0, 80),
            modal_anchor(get_uri("material_categories/area_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_items_area'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_items_category'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("material_categories/area_delete"), "data-action" => "delete"))
        );
    }


}

/* End of file material_categories.php */
/* Location: ./app/controllers/material_categories.php */