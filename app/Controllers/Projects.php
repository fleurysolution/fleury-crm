<?php

namespace App\Controllers;

use App\Controllers\BaseAppController;
use App\Models\ProjectModel;
use App\Models\PhaseModel;
use App\Models\MilestoneModel;
use App\Models\ProjectMemberModel;
use App\Models\TaskModel;
use App\Models\ClientModel;
use App\Models\UserModel;

class Projects extends BaseAppController
{
    protected ProjectModel       $projects;
    protected PhaseModel         $phases;
    protected MilestoneModel     $milestones;
    protected ProjectMemberModel $members;
    protected TaskModel          $tasks;
    protected \App\Models\RfiModel           $rfi;
    protected \App\Models\SubmittalModel     $submittals;
    protected \App\Models\DrawingModel       $drawings;
    protected \App\Models\ProjectProgressPhotoModel $photos;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request,
                                   \CodeIgniter\HTTP\ResponseInterface $response,
                                   \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->projects   = new ProjectModel();
        $this->phases     = new PhaseModel();
        $this->milestones = new MilestoneModel();
        $this->members    = new ProjectMemberModel();
        $this->tasks      = new TaskModel();
        $this->rfi        = new \App\Models\RfiModel(); // Added
        $this->submittals = new \App\Models\SubmittalModel(); // Added
        $this->drawings   = new \App\Models\DrawingModel(); // Added
        $this->photos     = new \App\Models\ProjectProgressPhotoModel(); // Added
    }

    // ── LIST ─────────────────────────────────────────────────────────────
    public function index(): string
    {
        $filter   = $this->request->getGet('status') ?? 'all';
        $q        = $this->projects->withDetails();
        if ($filter !== 'all') {
            $q->where('projects.status', $filter);
        }
        $projects = $q->orderBy('projects.created_at','DESC')->findAll();

        $counts = [];
        foreach (['draft','active','on_hold','completed','archived'] as $s) {
            $counts[$s] = $this->projects->where('status', $s)->countAllResults();
        }

        return $this->render('projects/index', [
            'title'    => 'Projects',
            'projects' => $projects,
            'filter'   => $filter,
            'counts'   => $counts,
        ]);
    }

    // ── CREATE FORM ──────────────────────────────────────────────────────
    public function create(): string
    {
        $clients = model(ClientModel::class)->select('id,company_name')->findAll();
                $users   = model(UserModel::class)->select('id, CONCAT(first_name, " ", last_name) AS name')->findAll();

        return $this->render('projects/create', [
            'title'   => 'New Project',
            'clients' => $clients,
            'users'   => $users,
        ]);
    }

    // ── STORE ────────────────────────────────────────────────────────────
    public function store()
    {
        $data = [
            'title'       => $this->request->getPost('title'),
            'client_id'   => $this->request->getPost('client_id') ?: null,
            'pm_user_id'  => $this->request->getPost('pm_user_id') ?: null,
            'status'      => $this->request->getPost('status') ?? 'draft',
            'priority'    => $this->request->getPost('priority') ?? 'medium',
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'end_date'    => $this->request->getPost('end_date') ?: null,
            'budget'      => $this->request->getPost('budget') ?: null,
            'currency'    => $this->request->getPost('currency') ?? 'USD',
            'description' => $this->request->getPost('description') ?: null,
            'color'       => $this->request->getPost('color') ?? '#4a90e2',
            'created_by'  => session()->get('user_id'),
        ];

        if (!trim($data['title'])) {
            return redirect()->back()->withInput()->with('error', 'Project title is required.');
        }

        $id = $this->projects->insert($data);

        // Auto-add creator as PM member
        $this->members->insert([
            'project_id' => $id,
            'user_id'    => session()->get('user_id'),
            'role'       => 'pm',
        ]);

        // Create a default "Planning" phase
        $this->phases->insert(['project_id' => $id, 'title' => 'Planning', 'sort_order' => 1]);

        return redirect()->to(site_url("projects/{$id}"))->with('message', 'Project created successfully.');
    }

    // ── SHOW (workspace) ─────────────────────────────────────────────────
    public function show(int $id): string
    {
        $project = $this->projects->withDetails()->where('projects.id', $id)->first();
        if (!$project) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Project #$id not found");
        }

        $tab      = $this->request->getGet('tab') ?? 'overview';
        $stats    = $this->projects->getStats($id);
        $phases   = $this->phases->forProject($id);
        $members  = $this->members->getMembers($id);
        $milestones = $this->milestones->forProject($id);
        $rfi        = $this->rfi->where('project_id', $id)->findAll(); // Added
        $submittals = $this->submittals->where('project_id', $id)->findAll(); // Added
        $drawings   = $this->drawings->where('project_id', $id)->findAll(); // Added
        $photos     = $this->photos->where('project_id', $id)->orderBy('created_at', 'DESC')->findAll(); // Added

        return $this->render('projects/show', [
            'title'      => $project['title'],
            'project'    => $project,
            'tab'        => $tab,
            'stats'      => $stats,
            'phases'     => $phases,
            'members'    => $members,
            'milestones' => $milestones,
            'rfi'        => $rfi, // Added
            'submittals' => $submittals, // Added
            'drawings'   => $drawings, // Added
            'photos'     => $photos, // Added
        ]);
    }

    // ── EDIT FORM ────────────────────────────────────────────────────────
    public function edit(int $id): string
    {
        $project = $this->projects->find($id);
        if (!$project) throw new \CodeIgniter\Exceptions\PageNotFoundException();
        $clients = model(ClientModel::class)->select('id,company_name')->findAll();
        $users   = model(UserModel::class)->select('id, CONCAT(first_name, " ", last_name) AS name')->findAll();
        return $this->render('projects/edit', [
            'title'   => 'Edit Project',
            'project' => $project,
            'clients' => $clients,
            'users'   => $users,
        ]);
    }

    // ── UPDATE ───────────────────────────────────────────────────────────
    public function update(int $id)
    {
        $data = [
            'title'       => $this->request->getPost('title'),
            'client_id'   => $this->request->getPost('client_id') ?: null,
            'pm_user_id'  => $this->request->getPost('pm_user_id') ?: null,
            'status'      => $this->request->getPost('status') ?? 'active',
            'priority'    => $this->request->getPost('priority') ?? 'medium',
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'end_date'    => $this->request->getPost('end_date') ?: null,
            'budget'      => $this->request->getPost('budget') ?: null,
            'currency'    => $this->request->getPost('currency') ?? 'USD',
            'description' => $this->request->getPost('description') ?: null,
            'color'       => $this->request->getPost('color') ?? '#4a90e2',
        ];
        $this->projects->update($id, $data);
        return redirect()->to(site_url("projects/{$id}"))->with('message', 'Project updated.');
    }

    // ── ARCHIVE ──────────────────────────────────────────────────────────
    public function archive(int $id)
    {
        $this->projects->update($id, ['status' => 'archived']);
        return redirect()->to(site_url('projects'))->with('message', 'Project archived.');
    }

    // ── MEMBERS ──────────────────────────────────────────────────────────
    public function addMember(int $id)
    {
        $userId = (int)$this->request->getPost('user_id');
        $role   = $this->request->getPost('role') ?? 'member';

        if (!$this->members->isMember($id, $userId)) {
            $this->members->insert(['project_id' => $id, 'user_id' => $userId, 'role' => $role]);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->to(site_url("projects/{$id}?tab=members"))->with('message', 'Member added.');
    }

    // ── PHASE CRUD (AJAX) ─────────────────────────────────────────────────
    public function storePhase(int $projectId)
    {
        $id = $this->phases->insert([
            'project_id' => $projectId,
            'title'      => $this->request->getPost('title'),
            'color'      => $this->request->getPost('color') ?? '#6c757d',
            'sort_order' => (int)$this->request->getPost('sort_order'),
        ]);
        return $this->response->setJSON(['success' => true, 'id' => $id]);
    }

    // ── MILESTONE CRUD (AJAX) ─────────────────────────────────────────────
    public function storeMilestone(int $projectId)
    {
        $id = $this->milestones->insert([
            'project_id'       => $projectId,
            'title'            => $this->request->getPost('title'),
            'description'      => $this->request->getPost('description'),
            'due_date'         => $this->request->getPost('due_date'),
            'is_client_facing' => (int)$this->request->getPost('is_client_facing'),
            'created_by'       => session()->get('user_id'),
        ]);
        $ms = $this->milestones->find($id);
        return $this->response->setJSON(['success' => true, 'milestone' => $ms]);
    }

    public function updateMilestone(int $id)
    {
        $this->milestones->update($id, [
            'title'    => $this->request->getPost('title'),
            'due_date' => $this->request->getPost('due_date'),
            'status'   => $this->request->getPost('status'),
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    // ── PROGRESS PHOTOS (AJAX) ───────────────────────────────────────────
    public function uploadProgressPhotos(int $projectId)
    {
        $files = $this->request->getFiles();
        if (!$files || empty($files['photos'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'No files uploaded.']);
        }

        $dir = FCPATH . "uploads/projects/{$projectId}/progress/";
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $uploaded = [];
        $uploadFiles = is_array($files['photos']) ? $files['photos'] : [$files['photos']];

        foreach ($uploadFiles as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $name = $file->getRandomName();
                $file->move($dir, $name);

                $id = $this->photos->insert([
                    'project_id'  => $projectId,
                    'photo_path'  => "uploads/projects/{$projectId}/progress/{$name}",
                    'caption'     => $file->getClientName(),
                    'uploaded_by' => session()->get('user_id')
                ]);
                $uploaded[] = $this->photos->find($id);
            }
        }

        if (empty($uploaded)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to process files.']);
        }

        return $this->response->setJSON(['success' => true, 'photos' => $uploaded]);
    }

    public function deleteProgressPhoto(int $projectId, int $photoId)
    {
        $photo = $this->photos->find($photoId);
        if ($photo && $photo['project_id'] == $projectId) {
            if (file_exists(FCPATH . $photo['photo_path'])) {
                unlink(FCPATH . $photo['photo_path']);
            }
            $this->photos->delete($photoId);
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Photo not found.']);
    }

    // ── PDF GENERATOR ────────────────────────────────────────────────────
    public function generateProgressReport(int $projectId)
    {
        $project = $this->projects->find($projectId);
        if (!$project) {
            return redirect()->back()->with('error', 'Project not found.');
        }

        $photos = $this->photos->where('project_id', $projectId)->orderBy('created_at', 'DESC')->findAll();

        if (empty($photos)) {
            return redirect()->back()->with('error', 'No photos available to generate a report.');
        }

        $data = [
            'project' => $project,
            'photos'  => $photos,
            'date'    => date('F j, Y'),
        ];

        $html = view('projects/progress_report_pdf', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Progress_Report_" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $project['title']) . "_" . date('Ymd') . ".pdf";
        $dompdf->stream($filename, ["Attachment" => false]);
    }
}
