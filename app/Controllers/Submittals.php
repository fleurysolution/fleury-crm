<?php

namespace App\Controllers;

use App\Models\SubmittalModel;
use App\Models\SubmittalRevisionModel;
use App\Models\ProjectModel;

class Submittals extends BaseAppController
{
    /**
     * GET /projects/:id/submittals — submittal register
     */
    public function index(int $projectId): string
    {
        $project  = (new ProjectModel())->find($projectId);
        if (!$project) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $subModel = new SubmittalModel();
        $submittals = $subModel->forProject($projectId);
        $counts     = $subModel->statusCounts($projectId);

        return $this->render('submittals/index', [
            'project'    => $project,
            'submittals' => $submittals,
            'counts'     => $counts,
        ]);
    }

    /**
     * GET /submittals/:id — detail + revision trail
     */
    public function show(int $id): string
    {
        $subModel  = new SubmittalModel();
        $submittal = $subModel->withUserName()->find($id);
        if (!$submittal) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $project   = (new ProjectModel())->find($submittal['project_id']);
        $revisions = (new SubmittalRevisionModel())->forSubmittal($id);

        $db = \Config\Database::connect();
        $members = $db->table('project_members')->select('fs_users.id as user_id, fs_users.first_name, fs_users.last_name, roles.name as role, CONCAT(fs_users.first_name, " ", fs_users.last_name) AS name')
            ->join('fs_users', 'fs_users.id = project_members.user_id')
            ->join('user_roles', 'user_roles.user_id = fs_users.id', 'left')
            ->join('roles', 'roles.id = user_roles.role_id', 'left')
            ->where('project_members.project_id', $submittal['project_id'])
            ->get()->getResultArray();

        return $this->render('submittals/show', [
            'project'   => $project,
            'submittal' => $submittal,
            'revisions' => $revisions,
            'members'   => $members,
        ]);
    }

    /**
     * POST /projects/:id/submittals — create new submittal
     */
    public function store(int $projectId): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $subModel = new SubmittalModel();
        $data = [
            'project_id'       => $projectId,
            'submittal_number' => $subModel->nextNumber($projectId),
            'title'            => $this->request->getPost('title'),
            'spec_section'     => $this->request->getPost('spec_section'),
            'type'             => $this->request->getPost('type') ?: 'shop_drawing',
            'status'           => 'submitted',
            'submitted_by'     => $this->currentUser['id'],
            'reviewer_id'      => $this->request->getPost('reviewer_id') ?: null,
            'due_date'         => $this->request->getPost('due_date')    ?: null,
            'current_revision' => 0,
        ];

        $subId = $subModel->insert($data);

        // Create initial revision record
        (new SubmittalRevisionModel())->insert([
            'submittal_id' => $subId,
            'revision_no'  => 0,
            'status'       => 'submitted',
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'id' => $subId, 'number' => $data['submittal_number']]);
        }
        return redirect()->to(site_url("submittals/{$subId}"))->with('success', 'Submittal ' . $data['submittal_number'] . ' created.');
    }

    /**
     * POST /submittals/:id/review — add review decision + create new revision
     */
    public function review(int $id): \CodeIgniter\HTTP\Response
    {
        $subModel = new SubmittalModel();
        $sub      = $subModel->find($id);
        if (!$sub) return $this->response->setJSON(['success' => false]);

        $status    = $this->request->getPost('status');
        $forwardTo = $this->request->getPost('forward_to');
        $notes     = $this->request->getPost('notes');
        $sigData   = $this->request->getPost('signature_data');
        $newRev    = (int)$sub['current_revision'] + 1;

        $revData = [
            'submittal_id' => $id,
            'revision_no'  => $newRev,
            'status'       => $status,
            'reviewer_id'  => $this->currentUser['id'],
            'reviewed_at'  => date('Y-m-d H:i:s'),
            'notes'        => $notes,
        ];

        if ($sigData) {
            $revData['signature_data'] = $sigData;
            $revData['signature_ip']   = $this->request->getIPAddress();
            $revData['signed_at']      = date('Y-m-d H:i:s');
        }

        // Record the current reviewer's decision in the revision history
        (new SubmittalRevisionModel())->insert($revData);

        // Update the master submittal record
        $updateData = [
            'current_revision' => $newRev,
        ];
        
        if ($forwardTo) {
            // If forwarded to someone else, status remains "under_review" for the next person
            $updateData['status']      = 'under_review';
            $updateData['reviewer_id'] = $forwardTo;
        } else {
            // Otherwise, apply the final decision to the submittal
            $updateData['status']      = $status;
            $updateData['reviewer_id'] = $this->currentUser['id'];
        }

        $subModel->update($id, $updateData);

        // Send notifications
        $creatorId = $sub['submitted_by'];
        $title = "Submittal #{$sub['submittal_number']} Reviewed: " . ucfirst(str_replace('_', ' ', $status));
        $url = "submittals/{$id}";
        
        if ($forwardTo) {
            // Notify the new reviewer
            \App\Models\NotificationModel::send(
                $forwardTo,
                'submittal_forwarded',
                "Submittal #{$sub['submittal_number']} forwarded to you for review",
                ['url' => $url, 'body' => $notes ?: '']
            );
            // Notify the creator that it was forwarded
            if (current_user_id() !== $creatorId) {
                \App\Models\NotificationModel::send(
                    $creatorId,
                    'submittal_forwarded_creator',
                    "Submittal #{$sub['submittal_number']} was forwarded for further review",
                    ['url' => $url]
                );
            }
        } else {
            // Final decision applied
            if (current_user_id() !== $creatorId) {
                \App\Models\NotificationModel::send(
                    $creatorId,
                    'submittal_reviewed',
                    $title,
                    ['url' => $url, 'body' => $notes ?: 'Decision: ' . $status]
                );
            }
        }

        return $this->response->setJSON(['success' => true, 'status' => $updateData['status'], 'revision' => $newRev]);
    }

    /**
     * POST /submittals/:id/delete — soft delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $subModel = new SubmittalModel();
        $sub      = $subModel->find($id);
        $subModel->delete($id);
        return redirect()->to(site_url("projects/{$sub['project_id']}?tab=submittals"))
            ->with('success', 'Submittal deleted.');
    }
}
