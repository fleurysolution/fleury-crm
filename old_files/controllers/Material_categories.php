<?php

namespace App\Controllers;

class Material_categories extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
    }

    //load item categories list view
    function index() {
        return $this->template->rander("material_categories/index");
    }

    //load item category add/edit modal form
    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));
        $view_data['categories_dropdown'] = $this->Material_categories_model->get_dropdown_list(array("title"));
        
        $view_data['model_info'] = $this->Material_categories_model->get_one($this->request->getPost('id'));
        return $this->template->view('material_categories/modal_form', $view_data);
    }

    //save item category
    function save() {

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description'),
            "parent_id" => $this->request->getPost('parent_id'),
        );
        $save_id = $this->Material_categories_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //delete/undo an item category
    function delete() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Material_categories_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Material_categories_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    //get data for items category list
    function list_data() {
        $list_data = $this->Material_categories_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    //get an expnese category list row
    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Material_categories_model->get_details($options)->getRow();
        return $this->_make_row($data);
    }

    //prepare an item category list row
    private function _make_row($data) {
        return array($data->title,
            $data->parent_category_name,
            modal_anchor(get_uri("material_categories/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_items_category'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_items_category'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("material_categories/delete"), "data-action" => "delete"))
        );
    }

    // Area Manager
      //load item categories list view
    function area_list() {
        return $this->template->rander("material_categories/area_list");
    }

    function area_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

//        $view_data['categories_dropdown'] = $this->Material_categories_model->get_dropdown_list(array("title"));
        
        $view_data['model_info'] = $this->Material_area_model->get_one($this->request->getPost('id'));
        return $this->template->view('material_categories/area_model_form', $view_data);
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
            if ($this->Material_area_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Material_area_model->delete($id)) {
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