<?php

namespace App\Controllers;

use App\Libraries\App_folders;

class Appointment_services extends Security_Controller {
    // use App_folders;

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("order");        
        $this->Appointment_service_categories_model = model('Appointment_service_categories_model');
        $this->Services_model = model('Services_model');
        $this->Appointment_availability_model = model('Appointment_availability_model');
        $this->Appointment_services_model = model('Appointment_services_model');
        $this->Service_team_members_model = model('Service_team_members_model');
        $this->Service_assignment_state_model = model('Service_assignment_state_model');
        $this->Users_model = model('Users_model'); 
    } 
    
    protected function validate_access_to_appointments(){
        $access_invoice = $this->get_access_info("invoice");
        $access_estimate = $this->get_access_info("estimate");

        //don't show the items if invoice/estimate module is not enabled
        if (!(get_setting("module_invoice") == "1" || get_setting("module_estimate") == "1" )){
            app_redirect("forbidden");
        }

        if ($this->login_user->is_admin) {
            return true;
        } else if ($access_invoice->access_type === "all" || $access_estimate->access_type === "all") {
            return true;
        } else {
            app_redirect("forbidden");
        }
    }


   /* function availability_schedule ($category_id='') {      
        $this->access_only_team_members();
        $view_data['category_id']='';
        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();
        $staff_id = session()->get('user_id');
        $options=array('staff_id'=>$staff_id);
        $availability = $this->Appointment_availability_model->get_details($options)->getResult();

        $data = [];
        foreach ($availability as $row) {
            $day = strtolower($row->day_of_week);
            $data[$day] = [
                'start' => $row->start_time,
                'end' => $row->end_time,
                'active' => $row->is_available,
            ];
        }
        return $this->template->rander("appointment_services/availability-schedule", $view_data);
    }*/


   /* public function availability_schedule()
        {
            $staff_id = session()->get('user_id');
            // Load all availability for this staff
            $savedAvailability = $this->Appointment_availability_model->get_details(array('staff_id'=>$staff_id))->getResult();

            $availabilityByDay = [];
            foreach ($savedAvailability as $row) {
                // Convert day_of_week like 'Mon' to 'monday'
                $dayLower = strtolower(date('l', strtotime($row->day_of_week))); 
                // If $row->day_of_week is string like 'Mon', you can map manually:
                $dayMap = ['Mon' => 'monday', 'Tue' => 'tuesday', 'Wed' => 'wednesday', 'Thu' => 'thursday', 'Fri' => 'friday', 'Sat' => 'saturday', 'Sun' => 'sunday'];
                $dayLower = $dayMap[$row->day_of_week] ?? '';

                $availabilityByDay[$dayLower] = [
                    'active'     => $row->is_available,
                    'start'      => substr($row->start_time, 0, 5), // HH:mm format
                    'end'        => substr($row->end_time, 0, 5),
                ];
            }

            // Pass to view
            return $this->template->rander('appointment_services/availability-schedule', [
                'availabilityByDay' => $availabilityByDay,
            ]);
        }

            public function save_weekly()
                {
                    try {
                        $staff_id = session()->get('user_id'); // Adjust session key if needed
                        $availability = $this->request->getPost('availability');

                        if (!is_array($availability)) {
                            $errorMessage = 'Invalid availability input.';

                            if ($this->request->isAJAX()) {
                                return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
                            }

                            return redirect()->back()->with('error', $errorMessage);
                        }

                        // Delete old availability records for this staff
                        $this->Appointment_availability_model->deleteOldAvailability($staff_id, true);

                        foreach ($availability as $day => $info) {
                            $is_available = isset($info['active']) ? 1 : 0;

                            if (!$is_available) {
                                continue; // Skip if not available on this day
                            }

                            $start_time = $info['start'] ?? '09:00:00';
                            $end_time = $info['end'] ?? '18:00:00';
                            $duration = $info['duration'] ?? '';

                            // Convert full day name to 3-letter abbreviation
                            $day_short = ucfirst(substr($day, 0, 3));

                            $item_data = [
                                'staff_id'     => $staff_id,
                                'day_of_week'  => $day_short,
                                'start_time'   => $start_time,
                                'end_time'     => $end_time,
                                'duration'     => $duration,
                                'is_available' => 1,
                                'created_at'   => date('Y-m-d H:i:s')
                            ];

                            $this->Appointment_availability_model->ci_save($item_data);
                        }

                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON([
                                'status' => 'success',
                                'message' => 'Weekly availability saved successfully!'
                            ]);
                        }

                        return redirect()->to('/appointment_services/availability_schedule')->with('success', 'Weekly availability saved.');
                    } catch (\Exception $e) {
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON([
                                'status' => 'error',
                                'message' => 'An error occurred while saving: ' . $e->getMessage()
                            ]);
                        }

                        return redirect()->back()->with('error', 'An error occurred while saving availability.');
                    }
                }


*/

    public function availability_schedule()
        {
            $this->access_only_team_members();

            $staff_id = (int) session()->get('user_id');

            $savedAvailability = $this->Appointment_availability_model
                ->get_details(['staff_id' => $staff_id])
                ->getResult();

            $dayMap = [
                'Mon' => 'monday', 'Tue' => 'tuesday', 'Wed' => 'wednesday',
                'Thu' => 'thursday', 'Fri' => 'friday', 'Sat' => 'saturday', 'Sun' => 'sunday'
            ];

            $availabilityByDay = [];

            foreach ($savedAvailability as $row) {
                $dayLower = $dayMap[$row->day_of_week] ?? null;
                if (!$dayLower) {
                    continue;
                }

                $availabilityByDay[$dayLower] = [
                    'active'      => (int) $row->is_available,
                    'start'       => $row->start_time ? substr($row->start_time, 0, 5) : '',
                    'end'         => $row->end_time ? substr($row->end_time, 0, 5) : '',
                    'break_start' => $row->break_start_time ? substr($row->break_start_time, 0, 5) : '',
                    'break_end'   => $row->break_end_time ? substr($row->break_end_time, 0, 5) : '',
                ];
            }

            return $this->template->rander('appointment_services/availability-schedule', [
                'availabilityByDay' => $availabilityByDay,
            ]);
        }


        public function save_weekly()
        {
            $this->access_only_team_members();

            try {
                $staff_id = (int) session()->get('user_id');
                $availability = $this->request->getPost('availability');

                if (!is_array($availability)) {
                    $msg = 'Invalid availability input.';
                    return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
                }

                // Soft-delete old rows (keep history)
                $this->Appointment_availability_model->softDeleteByStaff($staff_id, false);

                foreach ($availability as $day => $info) {
                    $is_available = isset($info['active']) ? 1 : 0;
                    if (!$is_available) {
                        continue;
                    }

                    $start_time = $info['start'] ?? null;       // HH:MM
                    $end_time   = $info['end'] ?? null;         // HH:MM
                    $break_start = $info['break_start'] ?? null; // HH:MM or null
                    $break_end   = $info['break_end'] ?? null;   // HH:MM or null

                    if (!$start_time || !$end_time) {
                        // Skip invalid
                        continue;
                    }

                    // Validate HH:MM values by strtotime (simple) + ordering
                    $startTs = strtotime($start_time);
                    $endTs   = strtotime($end_time);

                    if ($startTs === false || $endTs === false || $endTs <= $startTs) {
                        continue; // invalid window
                    }

                    // Break validation (optional)
                    $breakStartSql = null;
                    $breakEndSql   = null;

                    if ($break_start && $break_end) {
                        $breakStartTs = strtotime($break_start);
                        $breakEndTs   = strtotime($break_end);

                        // Break must be within start/end and positive duration
                        if (
                            $breakStartTs !== false && $breakEndTs !== false &&
                            $breakEndTs > $breakStartTs &&
                            $breakStartTs >= $startTs &&
                            $breakEndTs <= $endTs
                        ) {
                            $breakStartSql = $break_start . ':00';
                            $breakEndSql   = $break_end . ':00';
                        }
                    }

                    // Compute working minutes (end-start minus break)
                    $totalMinutes = (int) round(($endTs - $startTs) / 60);

                    $breakMinutes = 0;
                    if ($breakStartSql && $breakEndSql) {
                        $breakMinutes = (int) round((strtotime($break_end) - strtotime($break_start)) / 60);
                    }

                    $workingMinutes = max(0, $totalMinutes - $breakMinutes);

                    // Convert full day name to 3-letter abbreviation
                    // Expects day keys like "monday" from view
                    $day_short = ucfirst(substr($day, 0, 3)); // Mon/Tue/...

                    $item_data = [
                        'staff_id'         => $staff_id,
                        'day_of_week'      => $day_short,
                        'start_time'       => $start_time . ':00',
                        'end_time'         => $end_time . ':00',
                        'break_start_time' => $breakStartSql,
                        'break_end_time'   => $breakEndSql,
                        'duration'         => (string) $workingMinutes, // store as minutes (your column is varchar(50))
                        'is_available'     => 1,
                        'deleted'          => 0,
                        'created_at'       => date('Y-m-d H:i:s')
                    ];

                    $this->Appointment_availability_model->upsertDay($item_data);
                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Weekly availability saved successfully!'
                ]);

            } catch (\Throwable $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'An error occurred while saving: ' . $e->getMessage()
                ]);
            }
        }



    function index() {		
        $this->access_only_team_members();
        $view_data['category_id']='';
        $view_data['service_id']='';
        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();
        $view_data['services_dropdown'] = $this->_get_services_dropdown();
        return $this->template->rander("appointment_services/appointments", $view_data);
    }

    function appointment_categories($category_id='') {      
        $this->access_only_team_members();
        $view_data['category_id']='';
        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();
        return $this->template->rander("appointment_services/service_category", $view_data);
    }

    function services($category_id='') {      
        $this->access_only_team_members();
        $view_data['services_id']='';
        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();
        return $this->template->rander("appointment_services/services", $view_data);
    }

    function appointments_list($category_id='') {
        $this->access_only_team_members();
        $view_data['category_id']=$category_id; 
        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        return $this->template->rander("appointment_services/index", $view_data);
    }

     //get categories dropdown
    private function _get_categories_dropdown() {
        $categories = $this->Appointment_service_categories_model->get_all_where(array("deleted" => 0), 0, 0, "name")->getResult();
        // print_r($categories); die;
        $categories_dropdown = array(array("id" => "", "text" => "- " . app_lang("category") . " -"));
        foreach ($categories as $category) {
            $categories_dropdown[] = array("id" => $category->id, "text" => $category->name);
        }

        return json_encode($categories_dropdown);
    }

   //get categories dropdown
    private function _get_services_dropdown() {
        $services = $this->Services_model->get_all_where(array("deleted" => 0), 0, 0, "name")->getResult();
        // print_r($categories); die;
        $services_dropdown = array(array("id" => "", "text" => "- " . app_lang("service") . " -"));
        foreach ($services as $category) {
            $services_dropdown[] = array("id" => $category->id, "text" => $category->name);
        }

        return json_encode($services_dropdown);
    }

   


    /*
    * Appointments List
    * list of items, prepared for datatable  
    */

    function list_data() {
        $this->access_only_team_members();
        $category_id = $this->request->getPost('category_id');
        $options = array("category_id" => $category_id);
        $list_data = $this->Appointment_services_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* prepare a row of item list table */

    private function _make_item_row($data) {
        $show_in_client_portal_icon = "";        

        return array(
            custom_nl2br($data->id ? $data->id : ""),
            custom_nl2br($data->services_title ? $data->services_title : ""),
            modal_anchor(get_uri("appointment_services/view"), $show_in_client_portal_icon . $data->name, array("title" => app_lang("item_details"), "data-post-id" => $data->id)),
            custom_nl2br($data->name ? $data->email : ""),
            custom_nl2br($data->name ? $data->phone : ""),           
            custom_nl2br($data->start_time ? $data->start_time : ""),
            custom_nl2br($data->end_time ? $data->end_time : ""),            
            ucfirst($data->status ? $data->status : ""),
            modal_anchor(get_uri("appointment_services/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_item'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("appointment_services/delete"), "data-action" => "delete"))
        );
    }

    function view() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $model_info = $this->Appointment_services_model->get_details(array("id" => $this->request->getPost('id'), "login_user_id" => $this->login_user->id))->getRow();

        $view_data['model_info'] = $model_info;
        $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);

        return $this->template->view('appointment_services/view', $view_data);
    }


     /* load item modal */

    function modal_form() {
        $this->access_only_team_members();
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));
        $view_data['model_info'] = $this->Appointment_services_model->get_one($this->request->getPost('id'));
        if($this->Services_model){
        $view_data['services_dropdown'] = $this->Services_model->get_dropdown_list(array("name"));
        $view_data['categories_dropdown'] = $this->Appointment_service_categories_model->get_dropdown_list(array("name"));
        
        } 
        return $this->template->view('appointment_services/appointment_model', $view_data);
    }

    /* add or edit an item */

    function save() {
        $this->access_only_team_members();
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
            "description" => $this->request->getPost('description'),
            "category_id" => $this->request->getPost('category_id'),
            "status" => $this->request->getPost('status')
        ); 

        $item_id = $this->Appointment_services_model->ci_save($item_data, $id);
        if ($item_id) {
            $options = array("id" => $item_id);
            $item_info = $this->Appointment_services_model->get_details($options)->getRow();
            echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_item_row($item_info), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo an item */

    function delete() {
        $this->access_only_team_members();
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Appointment_services_model->delete($id, true)) {
                $options = array("id" => $id);
                $item_info = $this->Appointment_services_model->get_details($options)->getRow();
                echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_item_row($item_info), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Appointment_services_model->delete($id)) {
                $item_info = $this->Appointment_services_model->get_one($id);
                echo json_encode(array("success" => true, "id" => $item_info->id, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }


     /*
    * Appointments Categories List
    * list of items, prepared for datatable  
    */

    function categories_list_data() {
        $this->access_only_team_members();

        $is_active = $this->request->getPost('is_active'); // "1" or "0" or ""
        $assignment_mode = $this->request->getPost('default_assignment_mode'); // round_robin/manual or ""
        $free_policy = $this->request->getPost('default_allow_free'); // "1" or "0" or ""

        $options = array(
            "is_active" => ($is_active === "0" || $is_active === "1") ? $is_active : null
        );

        // We'll filter assignment + free in SQL later if needed.
        // For now, model supports only is_active. We'll filter other two in controller.
        $list_data = $this->Appointment_service_categories_model->get_details($options)->getResult();

        $result = array();
        foreach ($list_data as $data) {
            // Filter optional: default_allow_free
            if ($free_policy === "0" || $free_policy === "1") {
                if ((int)$data->default_allow_free !== (int)$free_policy) {
                    continue;
                }
            }

            // Filter optional: default_assignment_mode
            if ($assignment_mode) {
                if (($data->default_assignment_mode ?? 'round_robin') !== $assignment_mode) {
                    continue;
                }
            }

            $result[] = $this->_make_categories_row($data);
        }

        echo json_encode(array("data" => $result));
    }


    /* prepare a row of item list table */

    private function _make_categories_row($data) {

            $active_badge = ((int)($data->is_active ?? 1) === 1)
                ? "<span class='badge bg-success'>Active</span>"
                : "<span class='badge bg-secondary'>Inactive</span>";

            $free_badge = ((int)($data->default_allow_free ?? 0) === 1)
                ? "<span class='badge bg-info'>Free Allowed</span>"
                : "<span class='badge bg-light text-dark'>Paid Default</span>";

            $mode = $data->default_assignment_mode ?? "round_robin";
            $mode_display = ($mode === "manual") ? "Manual" : "Round-robin";
            $assignment_badge = "<span class='badge bg-primary'>" . esc($mode_display) . "</span>";

            return array(
                custom_nl2br($data->id ? $data->id : ""),
                modal_anchor(get_uri("appointment_services/view_services_categories"),
                    $data->name,
                    array("title" => app_lang("item_details"), "data-post-id" => $data->id)
                ),
                custom_nl2br($data->display_order ?? ""),
                $active_badge,
                $free_badge,
                $assignment_badge,
                modal_anchor(get_uri("appointment_services/modal_form_categories"),
                    "<i data-feather='edit' class='icon-16'></i>",
                    array("class" => "edit", "title" => app_lang('edit_item'), "data-post-id" => $data->id)
                )
                . js_anchor("<i data-feather='x' class='icon-16'></i>",
                    array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id,
                        "data-action-url" => get_uri("appointment_services/delete_categories"), "data-action" => "delete")
                )
            );
        }


    function view_services_categories() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $model_info = $this->Appointment_service_categories_model->get_details(array("id" => $this->request->getPost('id'), "login_user_id" => $this->login_user->id))->getRow();

        $view_data['model_info'] = $model_info;
        // $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);

        return $this->template->view('appointment_services/view-services-category', $view_data);
    }


     /* load item modal */

    function modal_form_categories() {
        $this->access_only_team_members();
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));
        $view_data['model_info'] = $this->Appointment_service_categories_model->get_one($this->request->getPost('id'));
        $view_data['categories_dropdown'] = $this->Appointment_service_categories_model->get_dropdown_list(array("name"));
        return $this->template->view('appointment_services/service-category-model', $view_data);
    }

    /* add or edit an item */

    function save_categories() {
        $this->access_only_team_members();
        $this->validate_submitted_data(array(
            "id" => "numeric",
        ));

        $id = $this->request->getPost('id');
        $item_data = array(
            "name" => $this->request->getPost('name'),
            "display_order" => $this->request->getPost('display_order'),
             // NEW POLICY FIELDS
            "is_active" => $this->request->getPost('is_active') ? 1 : 0,
            "default_allow_free" => $this->request->getPost('default_allow_free') ? 1 : 0,
            "default_assignment_mode" => $this->request->getPost('default_assignment_mode') ?: "round_robin",
        );

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "services-categories");
        $new_files = unserialize($files_data);

        if ($id) {
            $item_info = $this->Appointment_service_categories_model->get_one($id);
            $timeline_file_path = get_setting("timeline_file_path");

            $new_files = update_saved_files($timeline_file_path, $item_info->files, $new_files);
        }

        $item_data["files"] = serialize($new_files);

        $item_id = $this->Appointment_service_categories_model->ci_save($item_data, $id);
        if ($item_id) {
            $options = array("id" => $item_id);
            $item_info = $this->Appointment_service_categories_model->get_details($options)->getRow();
            echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_categories_row($item_info), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo an item */

    function delete_categories() {
        $this->access_only_team_members();
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Appointment_service_categories_model->delete($id, true)) {
                $options = array("id" => $id);
                $item_info = $this->Appointment_service_categories_model->get_details($options)->getRow();
                echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_categories_row($item_info), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Appointment_service_categories_model->delete($id)) {
                $item_info = $this->Appointment_service_categories_model->get_one($id);
                echo json_encode(array("success" => true, "id" => $item_info->id, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }



     /*
    * Appointments services List
    * list of items, prepared for datatable  
    */

    function services_list_data() {
        $this->access_only_team_members();

        $category_id = $this->request->getPost('category_id');
        $is_active = $this->request->getPost('is_active'); // "1" or "0" or ""
        $payment_policy = $this->request->getPost('payment_policy'); // required/free_allowed/free_only
        $assignment_mode = $this->request->getPost('assignment_mode'); // round_robin/manual/inherit

        $options = array(
            "category_id" => $category_id,
            "is_active" => ($is_active === "0" || $is_active === "1") ? $is_active : null,
            "payment_policy" => $payment_policy ? $payment_policy : null,
            "assignment_mode" => $assignment_mode ? $assignment_mode : null
        );

        $list_data = $this->Services_model->get_details($options)->getResult();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_services_row($data);
        }

        echo json_encode(array("data" => $result));
    }


    /* prepare a row of item list table */

   private function _make_services_row($data) {

            $price = isset($data->price) ? $data->price : "0";
            $price_display = "$" . $price;

            $active_badge = ((int)($data->is_active ?? 1) === 1)
                ? "<span class='badge bg-success'>Active</span>"
                : "<span class='badge bg-secondary'>Inactive</span>";

            // Payment policy display
            $requires_payment = (int)($data->requires_payment ?? 1);
            $allow_free = (int)($data->allow_free_booking ?? 0);

            $payment_policy = "Payment Required";
            if ((float)$price <= 0) {
                $payment_policy = "Free Only";
            } else if ($allow_free) {
                $payment_policy = "Free Allowed";
            } else if (!$requires_payment) {
                $payment_policy = "No Payment";
            }

            $payment_badge = "<span class='badge bg-info'>" . esc($payment_policy) . "</span>";

            // Assignment display
            $mode = $data->assignment_mode ?? "";
            $mode_display = "Inherit";
            if ($mode === "round_robin") $mode_display = "Round-robin";
            if ($mode === "manual") $mode_display = "Manual";
            $assignment_badge = "<span class='badge bg-primary'>" . esc($mode_display) . "</span>";

            return array(
                custom_nl2br($data->id ?? ""),
                modal_anchor(get_uri("appointment_services/view_services"), $data->name, array(
                    "title" => app_lang("item_details"),
                    "data-post-id" => $data->id
                )),
                custom_nl2br($data->category_title ?? "-"),
                custom_nl2br($data->duration_minutes ?? ""),
                $price_display,
                $active_badge,
                $payment_badge,
                $assignment_badge,
                modal_anchor(get_uri("appointment_services/modal_form_services"),
                    "<i data-feather='edit' class='icon-16'></i>",
                    array("class" => "edit", "title" => app_lang('edit_item'), "data-post-id" => $data->id)
                ) .
                js_anchor("<i data-feather='x' class='icon-16'></i>",
                    array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id,
                        "data-action-url" => get_uri("appointment_services/delete_services"), "data-action" => "delete")
                )
            );
        }


    function view_services() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $model_info = $this->Services_model->get_details(array("id" => $this->request->getPost('id'), "login_user_id" => $this->login_user->id))->getRow();
        $view_data['model_info'] = $model_info;
        return $this->template->view('appointment_services/view-services', $view_data);
    }


     /* load item modal */

    function modal_form_services() {
        $this->access_only_team_members();
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));
        $view_data['model_info'] = $this->Services_model->get_one($this->request->getPost('id'));
        $view_data['categories_dropdown'] = $this->Appointment_service_categories_model->get_dropdown_list(array("name"));
        return $this->template->view('appointment_services/services-model', $view_data);
    }

    /* add or edit an item */

    function save_services() {
        $this->access_only_team_members();
        $this->validate_submitted_data(array(
            "id" => "numeric",
        ));

        $id = $this->request->getPost('id');
        $item_data = array(
            "name" => $this->request->getPost('name'),
            "description" => $this->request->getPost('description'),
            "category_id" => $this->request->getPost('category_id'),
            "duration_minutes" => $this->request->getPost('duration_minutes'),
            "price" => $this->request->getPost('price'),
            "is_active" => $this->request->getPost('is_active') ? 1 : 0,
            "allow_free_booking" => $this->request->getPost('allow_free_booking') ? 1 : 0,
            "requires_payment" => $this->request->getPost('requires_payment') ? 1 : 0,
            "assignment_mode" => $this->request->getPost('assignment_mode') ?: null,

           "slot_interval_minutes" => (int)($this->request->getPost('slot_interval_minutes') ?? 15),
           "buffer_before_minutes" => (int)($this->request->getPost('buffer_before_minutes') ?? 0),
           "buffer_after_minutes" => (int)($this->request->getPost('buffer_after_minutes') ?? 0),
           "min_notice_minutes" => (int)($this->request->getPost('min_notice_minutes') ?? 0),
           "max_advance_days" => (int)($this->request->getPost('max_advance_days') ?? 365),
            
        );

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "services-categories");
        $new_files = unserialize($files_data);

        if ($id) {
            $item_info = $this->Services_model->get_one($id);
            $timeline_file_path = get_setting("timeline_file_path");

            $new_files = update_saved_files($timeline_file_path, $item_info->files, $new_files);
        }

        $item_data["files"] = serialize($new_files);

        $item_id = $this->Services_model->ci_save($item_data, $id);
        if ($item_id) {
            $options = array("id" => $item_id);
            $item_info = $this->Services_model->get_details($options)->getRow();
            echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_services_row($item_info), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo an item */

    function delete_services() {
        $this->access_only_team_members();
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Services_model->delete($id, true)) {
                $options = array("id" => $id);
                $item_info = $this->Services_model->get_details($options)->getRow();
                echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_services_row($item_info), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Services_model->delete($id)) {
                $item_info = $this->Services_model->get_one($id);
                echo json_encode(array("success" => true, "id" => $item_info->id, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }




    function save_files_sort() {
        $this->access_only_allowed_members();
        $id = $this->request->getPost("id");
        $sort_values = $this->request->getPost("sort_values");
        if ($id && $sort_values) {
            //extract the values from the :,: separated string
            $sort_array = explode(":,:", $sort_values);

            $item_info = $this->Appointment_service_categories_model->get_one($id);
            if ($item_info->id) {
                $updated_file_indexes = update_file_indexes($item_info->files, $sort_array);
                $item_data = array(
                    "files" => serialize($updated_file_indexes)
                );

                $this->Appointment_service_categories_model->ci_save($item_data, $id);
            }
        }
    }

    function book_appointment($service_id='',$category_id=''){
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

    function booking_save() {
        //$this->access_only_team_members();
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
            "description" => $this->request->getPost('description'),
            "category_id" => $this->request->getPost('category_id'),
            "status" => $this->request->getPost('status')
        ); 

        $item_id = $this->Appointment_services_model->ci_save($item_data, $id);
        if ($item_id) {
            $options = array("id" => $item_id);
            $item_info = $this->Appointment_services_model->get_details($options)->getRow();
            app_redirect('appointment_services/book_appointment?status=success');
        } else {
           app_redirect('appointment_services/book_appointment?status=fail');
        }
    }



    // In Appointment_services controller

        private function _validate_date_ymd(string $date): bool
        {
            // strict YYYY-MM-DD
            return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
        }

        private function _day_short_from_date(string $date): string
        {
            // date('D') returns Mon/Tue/Wed...
            return date('D', strtotime($date));
        }

        /**
         * Public: returns available slots for a given service + date (+ optional staff)
         * URL example: /appointment_services/get_available_slots?service_id=1&date=2025-12-20&staff_id=5
         */
        public function get_available_slots()
        {
            // Public endpoint: DO NOT require team member access.
            // Add CSRF/rate-limit at middleware level if possible.

            $service_id = (int) $this->request->getGet('service_id');
            $date       = (string) $this->request->getGet('date');
            $staff_id   = $this->request->getGet('staff_id'); // may be null
            $staff_id   = ($staff_id !== null && $staff_id !== '') ? (int)$staff_id : null;

            if (!$service_id || !$date || !$this->_validate_date_ymd($date)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Invalid service_id or date. Expected date format: YYYY-MM-DD.'
                ]);
            }

            // Get service duration (minutes)
            // Assumes your Services_model has get_one($id) with duration_minutes
            $service = $this->Services_model->get_one($service_id);
            if (!$service || empty($service->id)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Service not found.'
                ]);
            }

            $duration = (int) ($service->duration_minutes ?? 0);
            if ($duration <= 0) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Invalid service duration.'
                ]);
            }

            // If staff_id not provided, you have two paths:
            // A) return error and force staff selection
            // B) pick the first staff who has availability that day
            // For now: implement B (more user-friendly)

            $day_short = $this->_day_short_from_date($date); // Mon/Tue/...

            if ($staff_id === null) {
                $staff_id = $this->Appointment_availability_model->findFirstAvailableStaffForDay($day_short);
                if (!$staff_id) {
                    return $this->response->setJSON([
                        'status'  => 'success',
                        'slots'   => [],
                        'message' => 'No staff availability for this date.'
                    ]);
                }
            }

            // Fetch availability for that staff/day
            $availability = $this->Appointment_availability_model
                ->getDayAvailability($staff_id, $day_short);

            if (!$availability || (int)$availability->is_available !== 1) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'slots'  => [],
                ]);
            }

            // Build datetime windows
            $workStart = $date . ' ' . substr($availability->start_time, 0, 8);
            $workEnd   = $date . ' ' . substr($availability->end_time, 0, 8);

            // Optional break
            $breakStart = null;
            $breakEnd   = null;
            if (!empty($availability->break_start_time) && !empty($availability->break_end_time)) {
                $breakStart = $date . ' ' . substr($availability->break_start_time, 0, 8);
                $breakEnd   = $date . ' ' . substr($availability->break_end_time, 0, 8);
            }

            // Pull existing bookings for that staff on that date
            $existing = $this->Appointment_services_model
                ->getBookingsForStaffDate($staff_id, $date)
                ->getResult();

            // Generate slots
            $slots = $this->_generate_slots([
                'date'        => $date,
                'duration'    => $duration,
                'workStart'   => $workStart,
                'workEnd'     => $workEnd,
                'breakStart'  => $breakStart,
                'breakEnd'    => $breakEnd,
                'bookings'    => $existing,
                'stepMinutes' => $duration // you can change to 15 later if needed
            ]);

            return $this->response->setJSON([
                'status'   => 'success',
                'staff_id' => $staff_id,
                'slots'    => $slots
            ]);
        }

        /**
         * Internal slot generator:
         * Returns array of slots with start/end and a label.
         */
        private function _generate_slots(array $p): array
        {
            $duration = (int)$p['duration'];
            $step     = (int)$p['stepMinutes'];

            $workStartTs = strtotime($p['workStart']);
            $workEndTs   = strtotime($p['workEnd']);

            if (!$workStartTs || !$workEndTs || $workEndTs <= $workStartTs) {
                return [];
            }

            $breakStartTs = $p['breakStart'] ? strtotime($p['breakStart']) : null;
            $breakEndTs   = $p['breakEnd'] ? strtotime($p['breakEnd']) : null;

            $bookingWindows = [];
            foreach (($p['bookings'] ?? []) as $b) {
                // ignore cancelled and deleted by query, but safe guard:
                if (!empty($b->start_time) && !empty($b->end_time)) {
                    $bookingWindows[] = [
                        'start' => strtotime($b->start_time),
                        'end'   => strtotime($b->end_time),
                    ];
                }
            }

            $slots = [];
            $slotStart = $workStartTs;

            // Last possible start = workEnd - duration
            $lastStart = $workEndTs - ($duration * 60);

            while ($slotStart <= $lastStart) {
                $slotEnd = $slotStart + ($duration * 60);

                // Exclude break overlaps
                if ($breakStartTs && $breakEndTs) {
                    $overlapsBreak = ($slotStart < $breakEndTs) && ($slotEnd > $breakStartTs);
                    if ($overlapsBreak) {
                        $slotStart += ($step * 60);
                        continue;
                    }
                }

                // Exclude booking overlaps
                $conflict = false;
                foreach ($bookingWindows as $w) {
                    if (($slotStart < $w['end']) && ($slotEnd > $w['start'])) {
                        $conflict = true;
                        break;
                    }
                }
                if ($conflict) {
                    $slotStart += ($step * 60);
                    continue;
                }

                $slots[] = [
                    'start' => date('Y-m-d H:i:s', $slotStart),
                    'end'   => date('Y-m-d H:i:s', $slotEnd),
                    'label' => date('h:i A', $slotStart) . ' - ' . date('h:i A', $slotEnd),
                ];

                $slotStart += ($step * 60);
            }

            return $slots;
        }

       /* public function assign_staff($appointment_id, $staff_id, $assigned_by) {
            $appointments = $this->db->prefixTable('customer_appointments');

            $appointment_id = (int)$appointment_id;
            $staff_id = (int)$staff_id;
            $assigned_by = (int)$assigned_by;

            $sql = "UPDATE $appointments
                    SET staff_id=$staff_id,
                        assignment_status='assigned',
                        assigned_at=NOW(),
                        assigned_by=$assigned_by
                    WHERE id=$appointment_id AND deleted=0";

            return $this->db->query($sql);
        }

        public function get_one_active($appointment_id) {
            $appointments = $this->db->prefixTable('customer_appointments');
            $appointment_id = (int)$appointment_id;

            $sql = "SELECT * FROM $appointments WHERE id=$appointment_id AND deleted=0 LIMIT 1";
            return $this->db->query($sql)->getRow();
        }*/

        public function modal_service_team_members()
            {
                $this->access_only_team_members();

                $this->validate_submitted_data([
                    "service_id" => "required|numeric"
                ]);

                $service_id = (int) $this->request->getPost('service_id');

                // ✅ Fetch active staff using model
                $staff = $this->Appointment_services_model->get_active_staff_basic_list();

                // ✅ Get mapped staff IDs for this service
                $mapped_ids = $this->Service_team_members_model->get_mapped_member_ids($service_id);

                return $this->template->view('appointment_services/service-team-members-modal', [
                    "service_id" => $service_id,
                    "staff_list" => $staff,
                    "mapped_ids" => $mapped_ids
                ]);
            }


        public function save_service_team_members() {
                $this->access_only_team_members();

                $this->validate_submitted_data([
                    "service_id" => "required|numeric"
                ]);

                $service_id = (int)$this->request->getPost('service_id');
                $member_ids = $this->request->getPost('team_member_ids');

                if (!is_array($member_ids)) {
                    $member_ids = [];
                }

                $this->Service_team_members_model->replace_mappings($service_id, $member_ids);

                echo json_encode([
                    "success" => true,
                    "message" => "Eligible staff updated successfully."
                ]);
            }

            private function _pick_round_robin_staff_id_for_slot($service_id, $start_dt, $end_dt) {
                    $eligible = $this->Service_team_members_model->get_eligible_members($service_id)->getResult();
                    if (!$eligible || count($eligible) === 0) {
                        return 0;
                    }

                    // Filter eligible by availability
                    $available_ids = [];
                    foreach ($eligible as $u) {
                        if ($this->Appointment_services_model->is_staff_available((int)$u->id, $start_dt, $end_dt)) {
                            $available_ids[] = (int)$u->id;
                        }
                    }

                    if (count($available_ids) === 0) {
                        return 0; // no one available for that slot
                    }

                    $last = $this->Service_assignment_state_model->get_last_assigned($service_id);
                    $idx = array_search($last, $available_ids, true);

                    if ($idx === false) {
                        return $available_ids[0];
                    }

                    $next_index = ($idx + 1) % count($available_ids);
                    return $available_ids[$next_index];
                }



            public function auto_assign_appointment($appointment_id) {
                    $this->access_only_team_members();

                    $appointment_id = (int)$appointment_id;
                    $appointment = $this->Appointment_services_model->get_one_active($appointment_id);

                    if (!$appointment) {
                        echo json_encode(["success" => false, "message" => "Appointment not found."]);
                        return;
                    }

                    // Decide service assignment mode (inherit handled at service/category layer; for now default round-robin)
                    $service_id = (int)$appointment->service_id;

                    $staff_id = $this->_pick_round_robin_staff_id_for_slot(
                            $service_id,
                            $appointment->start_time,
                            $appointment->end_time
                        );
                    if (!$staff_id) {
                        echo json_encode([
                            "success" => false,
                            "message" => "No eligible staff configured for this service. Appointment remains unassigned."
                        ]);
                        return;
                    }

                    $assigned_by = (int)$this->login_user->id;

                    $this->Appointment_services_model->assign_staff($appointment_id, $staff_id, $assigned_by);
                    $this->Service_assignment_state_model->set_last_assigned($service_id, $staff_id);

                    echo json_encode(["success" => true, "message" => "Appointment assigned successfully."]);
                }

                public function reassign_appointment() {
                    $this->access_only_team_members();

                    $this->validate_submitted_data([
                        "appointment_id" => "required|numeric",
                        "staff_id" => "required|numeric"
                    ]);

                    $appointment_id = (int)$this->request->getPost('appointment_id');
                    $staff_id = (int)$this->request->getPost('staff_id');

                    $appointment = $this->Appointment_services_model->get_one_active($appointment_id);
                    if (!$appointment) {
                        echo json_encode(["success" => false, "message" => "Appointment not found."]);
                        return;
                    }

                    // Validate staff exists & is active staff
                    $users_table = $this->db->prefixTable('users');
                    $valid = $this->db->query("
                        SELECT id FROM $users_table
                        WHERE id=$staff_id AND deleted=0 AND user_type='staff' AND status='active' AND disable_login=0
                        LIMIT 1
                    ")->getRow();

                    if (!$valid) {
                        echo json_encode(["success" => false, "message" => "Invalid staff selection."]);
                        return;
                    }

                    $assigned_by = (int)$this->login_user->id;

                    $this->Appointment_services_model->assign_staff($appointment_id, $staff_id, $assigned_by);

                    // Optional: update RR state so manual assignment influences rotation fairness
                    $this->Service_assignment_state_model->set_last_assigned((int)$appointment->service_id, $staff_id);

                    echo json_encode(["success" => true, "message" => "Appointment reassigned successfully."]);
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




}
