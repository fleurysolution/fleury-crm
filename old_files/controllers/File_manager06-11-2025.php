<?php

namespace App\Controllers;

use App\Libraries\App_folders;

class File_manager extends Security_Controller {

    use App_folders;

    function __construct() {
        parent::__construct();
    }

    //show attendance list view
    function index() {
        $this->access_only_team_members();
        return $this->explore();
    }

    function file_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "folder_id" => "numeric",
            "context" => "string",
            "context_id" => "numeric",

        ));

        $folder_id = $this->request->getPost('folder_id');

        $view_data['model_info'] = $this->General_files_model->get_one($this->request->getPost('id'));
        $view_data['folder_id'] = $folder_id;
        $view_data['context'] = $this->request->getPost('context');
        $view_data['context_id'] = $this->request->getPost('context_id');

        if (!$this->_can_upload_file($folder_id)) {
            app_redirect("forbidden");
        }

        return $this->template->view('file_manager/file_modal_form', $view_data);
    }
    
    

    function word_file_add_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "folder_id" => "numeric",
            "context" => "string",
            "context_id" => "numeric",

        ));

        $folder_id = $this->request->getPost('folder_id');

        $view_data['model_info'] = $this->General_files_model->get_one($this->request->getPost('id'));
        $view_data['folder_id'] = $folder_id;
        $view_data['context'] = $this->request->getPost('context');
        $view_data['context_id'] = $this->request->getPost('context_id');

        if (!$this->_can_upload_file($folder_id)) {
            app_redirect("forbidden");
        }

        return $this->template->view('file_manager/word_file_add_modal_form', $view_data);
    }

    function save_word_file_add_modal_form(){

        $this->init();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "folder_id" => "numeric"
        ));


        $folder_id = $this->request->getPost('folder_id');
        $files = $this->request->getPost("files");
        $now = get_current_utc_time();
        $success = false;
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = date('ymd-his').'-client.docx';
                    $this->create_word();  
                    $data = array(
                        "file_name" => $file_name,
                        "file_id" => '',
                        "service_type" => '',
                        "description" => $this->request->getPost('description_' . $file),
                        "file_size" => $this->request->getPost('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id,
                        "folder_id" => $folder_id,
                        "client_id" => 0,
                        "context"  => "global_files",
                        "context_id" => 0
                    );
                    $success = $this->General_files_model->ci_save($data);
               
            }
        }

        if ($success) {
           echo  json_encode(array("success" => true, 'message' => app_lang('record_saved')));          
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    
    }

    function excel_file_add_modal_form(){
       $this->validate_submitted_data(array(
            "id" => "numeric",
            "folder_id" => "numeric",
            "context" => "string",
            "context_id" => "numeric",

        ));
        $folder_id = $this->request->getPost('folder_id');

        $view_data['model_info'] = $this->General_files_model->get_one($this->request->getPost('id'));
        $view_data['folder_id'] = $folder_id;
        $view_data['context'] = $this->request->getPost('context');
        $view_data['context_id'] = $this->request->getPost('context_id');

        if (!$this->_can_upload_file($folder_id)) {
            app_redirect("forbidden");
        }

        return $this->template->view('file_manager/excel_file_add_modal_form', $view_data); 
    }


    function save_excel_file_add_modal_form(){

        $this->init();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "folder_id" => "numeric"
        ));


        $folder_id = $this->request->getPost('folder_id');
        $files = $this->request->getPost("files");
        $now = get_current_utc_time();
        $success = false;
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = date('ymd-his').'-client.xlsx';
                    $this->create_xls();  
                    $data = array(
                        "file_name" => $file_name,
                        "file_id" => '',
                        "service_type" => '',
                        "description" => $this->request->getPost('description_' . $file),
                        "file_size" => $this->request->getPost('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id,
                        "folder_id" => $folder_id,
                        "client_id" => 0,
                        "context"  => "global_files",
                        "context_id" => 0
                    );
                    $success = $this->General_files_model->ci_save($data);
               
            }
        }

        if ($success) {
           echo  json_encode(array("success" => true, 'message' => app_lang('record_saved')));          
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    
    }

    

    //used by App_folders
    private function _can_manage_folder($folder_id = 0, $context_id = 0) {
        if ($this->login_user->is_admin) {
            return true;
        } else if ($folder_id) {
            $folder_info = $this->get_folder_details($folder_id);
            if ($folder_info && ($folder_info->actual_permission_rank >= 6)) {
                return true;
            }
        }
    }

    //used by App_folders
    private function _can_upload_file($folder_id = 0, $context_id = 0) {
        if ($this->login_user->is_admin) {
            return true;
        } else if ($folder_id) {
          
            $folder_info = $this->get_folder_details($folder_id);
         
            if ($folder_info && ($folder_info->actual_permission_rank >= 3)) {
                return true;
            }
        }
    }

    private function _can_view_files_in_folder($folder_id = 0) {
        if ($this->login_user->is_admin) {
            return true;
        } else if ($folder_id) {
            $folder_info = $this->get_folder_details($folder_id);
            if ($folder_info && ($folder_info->actual_permission_rank >= 1)) {
                return true;
            }
        }
    }

    function save_file() {
        $this->init();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "folder_id" => "numeric"
        ));


        $folder_id = $this->request->getPost('folder_id');

        if (!$this->_can_upload_file($folder_id)) {
            app_redirect("forbidden");
        }


        $files = $this->request->getPost("files");

        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("global_files", "all");

        $success = false;

        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->request->getPost('file_name_' . $file);
                $file_info = move_temp_file($file_name, $target_path);
                if ($file_info) {
                    $data = array(
                        "file_name" => get_array_value($file_info, 'file_name'),
                        "file_id" => get_array_value($file_info, 'file_id'),
                        "service_type" => get_array_value($file_info, 'service_type'),
                        "description" => $this->request->getPost('description_' . $file),
                        "file_size" => $this->request->getPost('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id,
                        "folder_id" => $folder_id,
                        "client_id" => 0,
                        "context"  => "global_files",
                        "context_id" => 0
                    );

                    $success = $this->General_files_model->ci_save($data);
                } else {
                    $success = false;
                }
            }
        }

        if ($success) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function view_file($file_id = 0) {
        validate_numeric_value($file_id);

        $file_info = $this->_get_file_info($file_id);

        if ($file_info) {
            $view_data = get_file_preview_common_data($file_info, $this->_get_file_path($file_info));
            $view_data['can_comment_on_files'] = false;
            return $this->template->view("file_manager/view_file", $view_data);
        } else {
            show_404();
        }
    }


    //used by App_folders
    private function _folder_items($folder_id = "", $context_type = "", $context_id = 0) {
        $options = array(
            "folder_id" => $folder_id,
            "context_type" => $context_type,
            "is_admin" => $this->login_user->is_admin
        );

        $options["client_id"] = $context_id;
        return $this->General_files_model->get_details($options)->getResult();
    }

    //used by App_folders
    private function _folder_config() {
        $info = new \stdClass();
        $info->controller_slag = "file_manager";
        $info->add_files_modal_url = get_uri("file_manager/file_modal_form");

        $info->file_preview_url = get_uri("file_manager/view_file");
        $info->show_file_preview_sidebar = false;
        return $info;
    }

    //used by App_folders
    private function _shareable_options() {
        return array('all_team_members', 'team', 'member');
    }

    //used by App_folders
    private function _get_file_path($file_info) {
        return get_general_file_path("global_files", "all");
    }

    //used by App_folders
    private function _get_file_info($file_id) {
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->getRow();
        if ($file_info && $this->_can_view_files_in_folder($file_info->folder_id)) {
            return $file_info;
        }
    }

    //used by App_folders
    private function _download_file($id) {
        $file_info = $this->_get_file_info($id);

        if ($file_info) {
            $file_data = serialize(array(make_array_of_file($file_info)));  //serilize the path
            return $this->download_app_files($this->_get_file_path($file_info), $file_data);
        }
    }

    //used by App_folders
    private function _delete_file($id) {
        $info = $this->General_files_model->get_one($id);

        if (!$info || !$this->_can_manage_folder($info->folder_id)) {
            return false;
        }

        if ($this->General_files_model->delete($id)) {
            delete_app_files($this->_get_file_path($info), array(make_array_of_file($info)));
            return true;
        }
    }

    //used by App_folders
    private function _move_file_to_another_folder($file_id, $folder_id) {
        $data = array("folder_id" => $folder_id);
        $data = clean_data($data);

        if (!$this->_can_manage_folder($folder_id)) {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            exit();
        }

        $save_id = $this->General_files_model->ci_save($data, $file_id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => "", 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    private function _get_all_files_of_folder($folder_id, $context_id) {
        if ($this->_can_view_files_in_folder($folder_id)) {
            return $this->General_files_model->get_all_where(array("folder_id" => $folder_id, "context" => "global_files"))->getResult();
        }
    }

    //used by App_folders
    private function _can_create_folder($parent_folder_id = 0, $context_id = 0) {
        return $this->_can_manage_folder($parent_folder_id, $context_id);
    }

    public function create_word(){
        $newfilename=date('ymd-his').'-client.docx';
        $source=FCPATH.'files/system/templates/word.docx';
        $target_path = getcwd() . "/" . get_general_file_path("global_files", "all");
        $destination=$target_path.$newfilename;
        if(copy($source,$destination)){
            return true;
        }else{
            return false;
        }
    }

    public function create_xls(){
        $newfilename=date('ymd-his').'-client.xlsx';
        $source=FCPATH.'files/system/templates/standard.xlsx';
        $target_path = getcwd() . "/" . get_general_file_path("global_files", "all");
        $destination=$target_path.$newfilename;        
       if(copy($source,$destination)){
            return true;
        }else{
            return false;
        }
    }
    public function create_new_file($filetype='xls'){
        if($filetype='xls'){
            $this->create_xls();
        }else{
            $this->create_word();
        }
    }
    public function send_files_email_modal_form(){
        
       

        $folder_id = $this->request->getPost('folder_id');
        $view_data['folder_id'] = $folder_id;
        return $this->template->view('file_manager/send_file_email_modal_form', $view_data);
    
        
    }
    
     public function sharetoteam(){
        if(!empty($this->request->getPost('checkbox_file'))){
               $view_data['checkbox_file'] = $this->request->getPost('checkbox_file');
               $view_data['client_id'] = $this->request->getPost('client_id');
               $view_data['project_id'] = $this->request->getPost('project_id');
               $view_data["users"] = $this->Messages_model->get_users_for_messaging($this->get_user_options_for_query('staff'))->getResult();
               $view_data["clients"] = $this->Messages_model->get_users_for_messaging($this->get_user_options_for_query('client'))->getResult();

                return $this->template->rander("file_manager/share_file_form", $view_data);
        }else{
            return $this->explore();
        }

        
    }
    
    // public function 
        public function sendMailnow(){
             if(!empty($this->request->getPost('checkbox_file'))){  

                $checkbox_file=$this->request->getPost('checkbox_file');

                $message_file='';
                for($i=0;$i < count($checkbox_file);$i++){
                    $projects=$this->request->getPost('project_ids_data')[$i];
                    $clients=$this->request->getPost('client_ids_data')[$i];
                        if($projects != '0'){
                            $options = array(
                                "id" => $checkbox_file[$i],
                                "project_id"=>$projects           
                            );
                            $file_info = $this->Project_files_model->get_details($options)->getResult();                   
                            $file_name = $file_info[0]->file_name;
                            $attachment_file=base_url().'/files/project_files/'.$projects.'/'.$file_name;   
                                             
                        }
                         if($clients != '0'){
                            $options = array(
                                "id" => $checkbox_file[$i],
                                "client_id"=>$clients            
                            );
                            $file_info = $this->General_files_model->get_details(array("id" => $checkbox_file[$i],"client_id" => $clients))->getResult();                   
                            $file_name = $file_info[0]->file_name;
                            $attachment_file=base_url().'/files/general/client/'.$clients.'/'.$file_name;    
                                       
                        }
                        if($clients==0 && $projects == 0){
                        $file_info=$this->General_files_model->get_details(array("id" => $checkbox_file[$i]))->getRow();
                        $file_name = $file_info->file_name;
                        $attachment_file=base_url().'/files/general/global_files/all/'.$file_name;  
                        }
                        if(isNotVideoType($file_name)){                            
                            $file_paths['file_path'][]= $attachment_file;
                            $email_options["attachments"][] = array("file_path" => $attachment_file);

                        }else{
                            $message_file.='<br>'.$attachment_file.'<br>';
                        }
                    
                    } 
                $messages=$this->request->getPost('messages');
                $messages.=$message_file;
                if($this->request->getPost('user_type')=='list' && !empty($this->request->getPost('users')) ){
                    $clients=$this->request->getPost('users');
                    $count=count($clients);
                    for($i=0; $i < $count; $i++){
                       
                        $responseUser=$this->General_files_model->getuserEmail($clients[$i])->getResult();
                        $emailsto=$responseUser[0]->email; 
                        $mailresponse=send_app_mail($emailsto, "File from Fleury Solutions PCM",$messages ,$email_options);
                     }
                }else{

                    $emailsto=$this->request->getPost('emailsto');
                    $emailscc=$this->request->getPost('emailscc');
                    $emailsbcc=$this->request->getPost('emailsbcc');
                    $email_options["cc"]=$emailscc;
                    $email_options["bcc"]=$emailsbcc;
                    $email_options["cc"]=$emailscc;
                    $mailresponse=send_app_mail($emailsto, "File from Fleury Solutions PCM",$messages ,$email_options);
                       
                }
                  if($mailresponse){ 
                     return json_encode(array("success" => true, 'message' => app_lang('File_Shared')));
                     
                     }else {
                     return json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
                    
                          }
                
        }else{
            return $this->explore();
        }

            
    }

    public function sharefilenow(){
$attachment_file=base_url().'files/general/global_files/all/_file66dce59b23d4f-sampleexcel.xlsx';
$optoins = array(
'file_path'=>$attachment_file,
'file_name'=>'_file66dce59b23d4f-sampleexcel.xlsx');
$email_options["attachments"] = array(array("file_path" => $attachment_file));
  send_app_mail('rakeshacn123@gmail.com', "Test message", "This is a test message to check mail configuration.",$email_options);
    }
}

/* End of file file_manager.php */
/* Location: ./app/controllers/file_manager.php */