<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table          = 'clients';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'type', 'company_name', 'website', 'phone', 'address', 'city', 'state', 'zip', 'country',
        'vat_number', 'gst_number', 'currency', 'currency_symbol', 'labels', 'status', 
        'owner_id', 'created_by'
    ];
    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';

    public function getDetails($id) {
        $client = $this->find($id);
        if (!$client) return null;

        $db = \Config\Database::connect();

        // Count Projects
        $client['total_projects'] = $db->table('projects')->where('client_id', $id)->countAllResults();
        
        // Invoice Stats
        $invoices = $db->table('invoices')->where('client_id', $id)->get()->getResultArray();
        $total_invoice_value = 0;
        $payment_received = 0;
        foreach ($invoices as $inv) {
            $total_invoice_value += $inv['invoice_total'];
            $payment_received += $inv['payment_received'];
        }
        $client['invoice_value'] = $total_invoice_value;
        $client['payment_received'] = $payment_received;
        $client['due'] = $total_invoice_value - $payment_received;

        // Determine Status Badge Color
        $client['status_color'] = 'secondary';
        if ($client['status'] === 'active') $client['status_color'] = 'success';
        if ($client['status'] === 'inactive') $client['status_color'] = 'dark';

        return $client;
    }
}
