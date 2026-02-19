<?php

namespace App\Models;

class Roles_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'roles';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $roles_table = $this->db->prefixTable('roles');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $roles_table.id=$id";
        }

        $sql = "SELECT $roles_table.*
        FROM $roles_table
        WHERE $roles_table.deleted=0 $where";
        return $this->db->query($sql);
    }

    function insert_hierarchy_level($data,$id=0)
    {
        $builder = $this->db->table('role_hierarchy');
        
        if($id==0){
            $builder->insert($data);
            return $this->db->insertID(); 
        }else{
            $builder->where('id', $id);
            $builder->update($data);
            return $this->db->affectedRows(); 
        }

        
    }

      /**
     * Fetch all descendant roles recursively using CTE
     */
    public function get_sub_roles($role_id)
    {
        $roles_table = $this->db->prefixTable('roles');
        $hierarchy_table = $this->db->prefixTable('role_hierarchy');

        $sql = "
            WITH RECURSIVE role_hierarchy_cte AS (
                SELECT rh.role_id, rh.reports_to_role_id, rh.hierarchy_level
                FROM $hierarchy_table rh
                WHERE rh.role_id = ?
                UNION ALL
                SELECT rh2.role_id, rh2.reports_to_role_id, rh2.hierarchy_level
                FROM $hierarchy_table rh2
                INNER JOIN role_hierarchy_cte cte ON rh2.reports_to_role_id = cte.role_id
            )
            SELECT r.id, r.title, cte.reports_to_role_id, cte.hierarchy_level
            FROM role_hierarchy_cte cte
            INNER JOIN $roles_table r ON r.id = cte.role_id
            ORDER BY cte.hierarchy_level ASC;
        ";

        return $this->db->query($sql, [$role_id])->getResultArray();
    }

    /**
     * Fetch all ancestor (parent) roles recursively using CTE
     */
    public function get_parent_roles($role_id)
    {
        $roles_table = $this->db->prefixTable('roles');
        $hierarchy_table = $this->db->prefixTable('role_hierarchy');

        $sql = "
            WITH RECURSIVE role_ancestors_cte AS (
                SELECT rh.role_id, rh.reports_to_role_id, rh.hierarchy_level
                FROM $hierarchy_table rh
                WHERE rh.role_id = ?
                UNION ALL
                SELECT rh2.role_id, rh2.reports_to_role_id, rh2.hierarchy_level
                FROM $hierarchy_table rh2
                INNER JOIN role_ancestors_cte cte ON rh2.role_id = cte.reports_to_role_id
            )
            SELECT r.id, r.title, cte.reports_to_role_id, cte.hierarchy_level
            FROM role_ancestors_cte cte
            INNER JOIN $roles_table r ON r.id = cte.role_id
            ORDER BY cte.hierarchy_level DESC;
        ";

        return $this->db->query($sql, [$role_id])->getResultArray();
    }
      // Get all roles with parent name
    public function get_roles_with_hierarchy()
    {
        $builder = $this->db->table($this->table . ' r');
        $builder->select('r.*, p.title as parent_title');
        $builder->join($this->table . ' p', 'p.id = r.parent_id', 'left');
        $builder->where('r.deleted', 0);
        return $builder->get()->getResult();
    }

    // Recursive CTE query to fetch hierarchy tree
    public function get_role_hierarchy_tree()
    {
        $sql = "
            WITH RECURSIVE role_cte AS (
                SELECT id, title, parent_id, 0 AS level
                FROM {$this->table}
                WHERE parent_id IS NULL

                UNION ALL

                SELECT r.id, r.title, r.parent_id, cte.level + 1
                FROM {$this->table} r
                INNER JOIN role_cte cte ON r.parent_id = cte.id
            )
            SELECT * FROM role_cte ORDER BY level, parent_id;
        ";
        return $this->db->query($sql)->getResult();
    }
}
