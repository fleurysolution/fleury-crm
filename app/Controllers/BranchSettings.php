<?php

namespace App\Controllers;

use App\Models\Region_model;
use App\Models\Office_model;
use App\Models\Division_model;

class BranchSettings extends BaseController
{
    protected Region_model $regionModel;
    protected Office_model $officeModel;
    protected Division_model $divisionModel;

    public function __construct()
    {
        helper('settings');
        $this->regionModel   = new Region_model();
        $this->officeModel   = new Office_model();
        $this->divisionModel = new Division_model();
    }

    // =====================
    // Pages
    // =====================

    public function regions()
    {
        $data = [
            'title' => 'Regions',
            'tab' => 'branches',
            'regions' => $this->regionModel->getAllActive(),
        ];

        return view('settings/branch/regions', $data);
    }

    public function offices()
    {
        $data = [
            'title' => 'Offices',
            'tab' => 'branches',
            'regions' => $this->regionModel->getAllActive(),
            'offices' => $this->officeModel->getAllWithRegion(),
        ];

        return view('settings/branch/branches', $data);
    }

    public function divisions()
    {
        $data = [
            'title' => 'Divisions',
            'tab' => 'branches',
            'regions' => $this->regionModel->getAllActive(),
            'divisions' => $this->divisionModel->getAllWithRegionOffice(),
        ];

        return view('settings/branch/divisions', $data);
    }

    // =====================
    // Regions
    // =====================

    public function saveRegion()
    {
        $id = (int) $this->request->getPost('id');

        $data = [
            'name'        => (string) $this->request->getPost('name'),
            'code'        => (string) $this->request->getPost('region_code'),
            'description' => (string) $this->request->getPost('description'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if ($id === 0) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        $savedId = $this->regionModel->saveRegion($data, $id);

        return $this->response->setJSON([
            'success' => true,
            'id'      => $savedId ?: $id,
            'message' => 'Region saved successfully',
        ]);
    }

    public function deleteRegion(int $id)
    {
        $this->regionModel->softDelete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Region deleted',
        ]);
    }

    public function getRegion(int $id)
    {
        return $this->response->setJSON($this->regionModel->findActiveById($id));
    }

    // =====================
    // Offices
    // =====================

    public function saveOffice()
    {
        $id = (int) $this->request->getPost('id');

        $data = [
            'region_id' => (int) $this->request->getPost('region_id'),
            'name'      => (string) $this->request->getPost('name'),
            'address'   => (string) $this->request->getPost('address'),
            'email'     => (string) $this->request->getPost('email'),
            'phone'     => (string) $this->request->getPost('phone'),
            'updated_at'=> date('Y-m-d H:i:s'),
        ];

        if ($id === 0) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        $savedId = $this->officeModel->saveOffice($data, $id);

        return $this->response->setJSON([
            'success' => true,
            'id'      => $savedId ?: $id,
            'message' => 'Office saved successfully',
        ]);
    }

    public function deleteOffice(int $id)
    {
        $this->officeModel->softDelete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Office deleted',
        ]);
    }

    public function getOffice(int $id)
    {
        return $this->response->setJSON($this->officeModel->findActiveById($id));
    }

    public function officesByRegion(int $regionId)
    {
        return $this->response->setJSON($this->officeModel->getByRegion($regionId));
    }

    // =====================
    // Divisions
    // =====================

    public function saveDivision()
    {
        $id = (int) $this->request->getPost('id');

        $regionId = (int) $this->request->getPost('region_id');
        $officeId = (int) $this->request->getPost('office_id');

        // Optional safety: ensure office belongs to the selected region
        $office = $this->officeModel->findActiveById($officeId);
        if ($office && (int)$office['region_id'] !== $regionId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Selected office does not belong to selected region.',
            ]);
        }

        $data = [
            'region_id'   => $regionId,
            'office_id'   => $officeId,
            'name'        => (string) $this->request->getPost('name'),
            'description' => (string) $this->request->getPost('description'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if ($id === 0) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        $savedId = $this->divisionModel->saveDivision($data, $id);

        return $this->response->setJSON([
            'success' => true,
            'id'      => $savedId ?: $id,
            'message' => 'Division saved successfully',
        ]);
    }

    public function deleteDivision(int $id)
    {
        $this->divisionModel->softDelete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Division deleted',
        ]);
    }

    public function getDivision(int $id)
    {
        return $this->response->setJSON($this->divisionModel->findActiveById($id));
    }

    public function divisionsByOffice(int $officeId)
    {
        return $this->response->setJSON($this->divisionModel->getByOffice($officeId));
    }
}
