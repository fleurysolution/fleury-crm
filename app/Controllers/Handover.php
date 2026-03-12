<?php

namespace App\Controllers;

use App\Models\AssetRegistryModel;
use App\Models\ProjectModel;

class Handover extends ErpController
{
    public function saveAsset(int $projectId)
    {
        $assetModel = new AssetRegistryModel();
        
        $id = $this->request->getPost('id');
        $data = [
            'project_id'       => $projectId,
            'asset_name'       => $this->request->getPost('asset_name'),
            'asset_tag'        => $this->request->getPost('asset_tag'),
            'category'         => $this->request->getPost('category'),
            'manufacturer'     => $this->request->getPost('manufacturer'),
            'model_number'     => $this->request->getPost('model_number'),
            'serial_number'    => $this->request->getPost('serial_number'),
            'install_date'     => $this->request->getPost('install_date'),
            'warranty_expiry'  => $this->request->getPost('warranty_expiry'),
            'manual_url'       => $this->request->getPost('manual_url'),
            'notes'            => $this->request->getPost('notes'),
        ];

        if ($id) {
            $assetModel->update($id, $data);
        } else {
            $assetModel->insert($data);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function printQr(int $projectId)
    {
        $project = (new ProjectModel())->find($projectId);
        $assets  = (new AssetRegistryModel())->where('project_id', $projectId)->findAll();
        
        return view('projects/handover/print_qr', compact('project', 'assets'));
    }

    public function generateQr(int $assetId)
    {
        // Placeholder for QR code generation logic
        // In a real app, we'd use a library like endroid/qr-code
        return "QR_DATA_FOR_ASSET_$assetId";
    }
}
