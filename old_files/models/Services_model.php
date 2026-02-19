<?php

namespace App\Models;

class Services_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'services';
        parent::__construct($this->table);
    }

    function get_specific_category($options = array()) {
        $service_services = $this->db->prefixTable('services');
        $where = "";

        $sql = "SELECT * FROM $service_services WHERE deleted=0 $where";
        return $this->db->query($sql);
    }

    function get_details($options = array()) {
        $services_table = $this->db->prefixTable('services');
        $categories_table = $this->db->prefixTable('service_categories');

        // Always start with a safe base
        $where = " AND $services_table.deleted=0";

        // Filters
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $services_table.id=$id";
        }

        $category_id = $this->_get_clean_value($options, "category_id");
        if ($category_id) {
            $where .= " AND $services_table.category_id=$category_id";
        }

        // Optional: status filter (requires services.is_active column)
        $is_active = get_array_value($options, "is_active");
        if ($is_active === "0" || $is_active === "1") {
            $where .= " AND $services_table.is_active=" . ((int)$is_active);
        }

        // Optional: assignment filter (requires services.assignment_mode column)
        $assignment_mode = get_array_value($options, "assignment_mode");
        if ($assignment_mode) {
            if ($assignment_mode === "inherit") {
                $where .= " AND ($services_table.assignment_mode IS NULL OR $services_table.assignment_mode='')";
            } else {
                $assignment_mode = $this->_get_clean_value($assignment_mode);
                $where .= " AND $services_table.assignment_mode='$assignment_mode'";
            }
        }

        // Optional: payment policy filter (requires allow_free_booking + requires_payment columns)
        $payment_policy = get_array_value($options, "payment_policy");
        if ($payment_policy) {
            if ($payment_policy === "required") {
                $where .= " AND ($services_table.price > 0)
                            AND ($services_table.allow_free_booking=0)
                            AND ($services_table.requires_payment=1)";
            } else if ($payment_policy === "free_allowed") {
                $where .= " AND ($services_table.price > 0)
                            AND ($services_table.allow_free_booking=1)";
            } else if ($payment_policy === "free_only") {
                $where .= " AND ($services_table.price <= 0)";
            }
        }

        // Optional search (useful later for reporting/admin UX)
        $search = get_array_value($options, "search");
        if ($search) {
            $search = $this->db->escapeLikeString($search);
            $where .= " AND ($services_table.name LIKE '%$search%' ESCAPE '!'
                        OR $categories_table.name LIKE '%$search%' ESCAPE '!')";
        }

        $sql = "SELECT
                    $services_table.*,
                    $categories_table.name AS category_title
                FROM $services_table
                LEFT JOIN $categories_table
                    ON $categories_table.id = $services_table.category_id
                WHERE 1=1 $where
                ORDER BY $services_table.id DESC";

        return $this->db->query($sql);
    }

    

public function resolve_pricing_policy(int $service_id): array
{
    $service = $this->get_details(["id" => $service_id])->getRow();

    if (!$service) {
        return [
            "requires_payment" => 0,
            "price" => 0.0,
            "source" => "service"
        ];
    }

    // Rule 1: allow_free_booking wins
    if ((int)$service->allow_free_booking === 1) {
        return [
            "requires_payment" => 0,
            "price" => 0.0,
            "source" => "service"
        ];
    }

    // Rule 2: requires_payment + price
    $price = (float)($service->price ?? 0);
    if ((int)$service->requires_payment === 1 && $price > 0) {
        return [
            "requires_payment" => 1,
            "price" => $price,
            "source" => "service"
        ];
    }

    // Rule 3: free fallback
    return [
        "requires_payment" => 0,
        "price" => 0.0,
        "source" => "service"
    ];
}

}
