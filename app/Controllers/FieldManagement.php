<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\PunchListModel;
use App\Models\SiteDiaryModel;
use App\Models\SiteDiaryLaborModel;
use App\Models\NotificationModel;

class FieldManagement extends BaseAppController
{
    // ==========================================
    // PUNCH LISTS
    // ==========================================

    /**
     * POST /projects/:id/punch
     * Adds a new Punch List Item
     */
    public function storePunchList(int $projectId): \CodeIgniter\HTTP\RedirectResponse
    {
        $plModel = new PunchListModel();

        $data = [
            'project_id'  => $projectId,
            'item_no'     => $this->request->getPost('item_no'),
            'location'    => $this->request->getPost('location'),
            'description' => $this->request->getPost('description'),
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'due_date'    => $this->request->getPost('due_date') ?: null,
            'status'      => 'Open',
            'created_by'  => $this->currentUser['id']
        ];

        $plModel->insert($data);

        // If assigned, send a notification
        if ($data['assigned_to']) {
            NotificationModel::send(
                $data['assigned_to'], 
                'punch_assignment', 
                "You were assigned a new Punch List Item: {$data['item_no']} - {$data['description']}", 
                "projects/{$projectId}?tab=field"
            );
        }

        return redirect()->back()->with('success', 'Punch List Item created.');
    }

    /**
     * POST /punch/:id/status
     * Upgrades the status of a Punch List Item
     */
    public function updatePunchStatus(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $plModel = new PunchListModel();
        $item = $plModel->find($id);

        if (!$item) return redirect()->back()->with('error', 'Item not found.');

        $status = $this->request->getPost('status');
        $updateData = ['status' => $status];

        // If resolved, stamp the time
        if ($status === 'Resolved') {
            $updateData['resolved_at'] = date('Y-m-d H:i:s');
        }

        $plModel->update($id, $updateData);

        // Notify creator if assignee resolves it
        if ($status === 'Resolved' && $item['created_by'] !== $this->currentUser['id']) {
            NotificationModel::send(
                $item['created_by'], 
                'punch_resolved', 
                "Item {$item['item_no']} was marked Resolved.", 
                "projects/{$item['project_id']}?tab=field"
            );
        }

        return redirect()->back()->with('success', "Punch List Item marked as {$status}.");
    }

    /**
     * POST /punch/:id/delete
     */
    public function deletePunchList(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $plModel = new PunchListModel();
        $plModel->delete($id);
        return redirect()->back()->with('success', 'Punch List Item deleted.');
    }


    // ==========================================
    // SITE DIARIES (Daily Logs)
    // ==========================================

    /**
     * POST /projects/:id/diaries
     * Drafts a new Site Diary
     */
    public function createDiary(int $projectId): \CodeIgniter\HTTP\RedirectResponse
    {
        $dModel = new SiteDiaryModel();
        
        // Fetch project to get location (simplified proxy for lat/lng)
        $project = (new ProjectModel())->find($projectId);
        
        // Default coords (New York) - in a real app, geocode the $project['location']
        $lat = 40.71; 
        $lng = -74.00;
        
        $weatherDesc = '';
        $tempStr = '';

        try {
            $client = \Config\Services::curlrequest();
            $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lng}&current=temperature_2m,weather_code&temperature_unit=fahrenheit";
            $response = $client->get($url, ['timeout' => 3]);
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                if (isset($data['current'])) {
                    $temp = $data['current']['temperature_2m'];
                    $code = $data['current']['weather_code'];
                    
                    $tempStr = "{$temp}°F";
                    $weatherDesc = $this->getWmoDescription($code);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if API is down
            log_message('error', 'Weather API failed: ' . $e->getMessage());
        }

        $diaryId = $dModel->insert([
            'project_id'         => $projectId,
            'report_date'        => $this->request->getPost('report_date') ?: date('Y-m-d'),
            'weather_conditions' => $weatherDesc,
            'temperature'        => $tempStr,
            'status'             => 'Draft',
            'created_by'         => $this->currentUser['id']
        ]);

        return redirect()->to(site_url("field/diary/{$diaryId}"))->with('success', 'Daily log drafted with Auto-Weather.');
    }

    /**
     * Helper to map WMO Weather Codes to text
     */
    private function getWmoDescription(int $code): string
    {
        $map = [
            0 => 'Clear sky',
            1 => 'Mainly clear', 2 => 'Partly cloudy', 3 => 'Overcast',
            45 => 'Fog', 48 => 'Depositing rime fog',
            51 => 'Light Drizzle', 53 => 'Moderate Drizzle', 55 => 'Dense Drizzle',
            61 => 'Slight Rain', 63 => 'Moderate Rain', 65 => 'Heavy Rain',
            71 => 'Slight Snow', 73 => 'Moderate Snow', 75 => 'Heavy Snow',
            80 => 'Slight Rain Showers', 81 => 'Moderate Rain Showers', 82 => 'Violent Rain Showers',
            95 => 'Thunderstorm', 96 => 'Thunderstorm with slight hail', 99 => 'Thunderstorm with heavy hail'
        ];
        return $map[$code] ?? 'Unknown';
    }

    /**
     * GET /field/diary/:id
     * The Worksheet for a specific Site Diary
     */
    public function showDiary(int $id): string
    {
        $dModel  = new SiteDiaryModel();
        $lModel  = new SiteDiaryLaborModel();
        
        $diary = $dModel->find($id);
        if (!$diary) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        
        $project = (new ProjectModel())->find($diary['project_id']);
        $laborLines = $lModel->forDiary($id);

        return $this->render('field/diary_show', [
            'project'    => $project,
            'diary'      => $diary,
            'laborLines' => $laborLines
        ]);
    }

    /**
     * POST /field/diary/:id/save
     * Upserts the daily log payload (text areas + labor array)
     */
    public function saveDiaryItems(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $dModel  = new SiteDiaryModel();
        $lModel  = new SiteDiaryLaborModel();
        
        $diary = $dModel->find($id);
        if (!$diary) return redirect()->back()->with('error', 'Diary not found.');

        // 1. Update master table stats
        $dModel->update($id, [
            'weather_conditions'  => $this->request->getPost('weather_conditions'),
            'temperature'         => $this->request->getPost('temperature'),
            'work_performed'      => $this->request->getPost('work_performed'),
            'materials_received'  => $this->request->getPost('materials_received'),
            'safety_observations' => $this->request->getPost('safety_observations')
        ]);

        // 2. Wipe existing labor and rebuild
        $lModel->where('diary_id', $id)->delete();

        $trades = $this->request->getPost('trades') ?? [];
        $counts = $this->request->getPost('worker_counts') ?? [];
        $hours  = $this->request->getPost('hours_worked') ?? [];

        $insertLabor = [];
        foreach ($trades as $index => $tradeName) {
            if (empty(trim((string)$tradeName))) continue;

            $insertLabor[] = [
                'diary_id'         => $id,
                'trade_or_company' => $tradeName,
                'worker_count'     => isset($counts[$index]) ? (int)$counts[$index] : 1,
                'hours_worked'     => isset($hours[$index]) && $hours[$index] !== '' ? (float)$hours[$index] : null
            ];
        }

        if (!empty($insertLabor)) {
            $lModel->insertBatch($insertLabor);
        }

        // 3. Status handling
        $action = $this->request->getPost('status_action');
        if ($action === 'submit') {
            $dModel->update($id, ['status' => 'Submitted']);
            return redirect()->to(site_url("projects/{$diary['project_id']}?tab=field"))->with('success', 'Daily Log submitted and locked.');
        }

        return redirect()->back()->with('success', 'Draft progress saved successfully.');
    }
}
