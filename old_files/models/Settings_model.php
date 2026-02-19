<?php

namespace App\Models;

class Settings_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'settings';
        parent::__construct($this->table);
    }

    function get_setting($setting_name) {
        $setting_name = $this->_get_clean_value($setting_name);
        $result = $this->db_builder->getWhere(array('setting_name' => $setting_name), 1);
        if (count($result->getResult()) == 1) {
            return $result->getRow()->setting_value;
        }
    }

    function save_setting($setting_name, $setting_value, $type = "app") {
        $fields = array(
            'setting_name' => $setting_name,
            'setting_value' => $setting_value
        );

        $exists = $this->get_setting($setting_name);
        if ($exists === NULL) {
            $fields["type"] = $type; //type can't be updated

            return $this->db_builder->insert($fields);
        } else {
            $this->db_builder->where('setting_name', $setting_name);
            $this->db_builder->update($fields);
        }
    }

    //find all app settings and login user's setting
    //user's settings are saved like this: user_[userId]_settings_name;
    function get_all_required_settings($user_id = 0) {
        $user_id = $this->_get_clean_value($user_id);

        $settings_table = $this->db->prefixTable('settings');
        $sql = "SELECT $settings_table.setting_name,  $settings_table.setting_value
        FROM $settings_table
        WHERE $settings_table.deleted=0 AND ($settings_table.type = 'app' OR ($settings_table.type ='user' AND $settings_table.setting_name LIKE 'user_" . $user_id . "_%'))";
        return $this->db->query($sql);
    }


      // ===== REGIONS =====
    function get_regions($id = 0) {
        $regions_table = $this->db->prefixTable('regions');

        if ($id) {
            return $this->db->table($regions_table)
                ->where('id', $id)
                ->where('deleted', 0)
                ->get()
                ->getRow();
        }

        return $this->db->table($regions_table)
            ->where('deleted', 0)
            ->get()
            ->getResult();
    }

    function save_region($data, $id = 0) {
        $regions_table = $this->db->prefixTable('regions');

        if ($id) {
            return $this->db->table($regions_table)->where('id', $id)->update($data);
        } else {
            return $this->db->table($regions_table)->insert($data);
        }
    }

    function delete_region($id) {
        return $this->db->table('regions')->where('id', $id)->update(['deleted' => 1]);
    }

    // ===== OFFICES =====
    function get_offices($region_id = 0) {
        $offices_table = $this->db->prefixTable('offices');
        $builder = $this->db->table($offices_table)->where('deleted', 0);

        if ($region_id) {
            $builder->where('region_id', $region_id);
        }

        return $builder->get()->getResult();
    }

    function save_office($data, $id = 0) {
        $offices_table = $this->db->prefixTable('offices');

        if ($id) {
            return $this->db->table($offices_table)->where('id', $id)->update($data);
        } else {
            return $this->db->table($offices_table)->insert($data);
        }
    }

    function delete_office($id) {
        return $this->db->table('offices')->where('id', $id)->update(['deleted' => 1]);
    }

    // ===== DIVISIONS =====
    function get_divisions($office_id = 0) {
        $divisions_table = $this->db->prefixTable('divisions');
        $builder = $this->db->table($divisions_table)->where('deleted', 0);

        if ($office_id) {
            $builder->where('office_id', $office_id);
        }

        return $builder->get()->getResult();
    }

    function save_division($data, $id = 0) {
        $divisions_table = $this->db->prefixTable('divisions');

        if ($id) {
            return $this->db->table($divisions_table)->where('id', $id)->update($data);
        } else {
            return $this->db->table($divisions_table)->insert($data);
        }
    }

    function delete_division($id) {
        return $this->db->table('divisions')->where('id', $id)->update(['deleted' => 1]);
    }
    
     function get_name_data($table, $id){
        $tableData = $this->db->prefixTable($table);
        $builder = $this->db->table($tableData);

        if ($id) {
            $builder->where('id', $id);
        }

        return $builder->get()->getResult();
    }

}
