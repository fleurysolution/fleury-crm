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
use App\Models\ChangeEventModel;
use App\Models\ChangeOrderModel;
use App\Models\MeetingModel;
use App\Models\ContractModel;
use App\Models\BudgetModel;
use App\Models\BidPackageModel;
use App\Models\BidModel;
use App\Models\ProjectBudgetItemModel;
use App\Models\CostCodeModel;
use App\Services\AutomationService;

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
    protected ChangeEventModel $events;
    protected ChangeOrderModel $orders;
    protected MeetingModel $meetings_model;
    protected ContractModel $contracts;
    protected BudgetModel $budget_model;
    protected BidPackageModel $bid_packages;
    protected BidModel           $bid_model;
    protected ProjectBudgetItemModel $budget_items_model;
    protected CostCodeModel      $cost_codes_model;
    protected AutomationService  $automation;


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
        $this->events     = new ChangeEventModel();
        $this->orders     = new ChangeOrderModel();
        $this->meetings_model = new MeetingModel();
        $this->contracts      = new ContractModel();
        $this->budget_model   = new BudgetModel();
        $this->bid_packages   = new BidPackageModel();
        $this->bid_model      = new BidModel();
        $this->budget_items_model = new ProjectBudgetItemModel();
        $this->cost_codes_model   = new CostCodeModel();
        $this->automation     = new AutomationService();
    }

    // ── LIST ─────────────────────────────────────────────────────────────
    public function index(): string
    {
        $viewMode = $this->request->getGet('view') ?? 'grid';
        $filter   = $this->request->getGet('status') ?? 'all';
        $clientId = $this->request->getGet('client_id');
        $branchId = $this->request->getGet('branch_id');
        $start    = $this->request->getGet('start_date');
        $end      = $this->request->getGet('end_date');
        $search   = $this->request->getGet('search');

        $q = $this->projects->withDetails();

        if ($filter !== 'all') {
            $q->where('projects.status', $filter);
        }
        if (!empty($clientId)) {
            $q->where('projects.client_id', $clientId);
        }
        if (!empty($branchId)) {
            $q->where('projects.branch_id', $branchId);
        }
        if (!empty($start)) {
            $q->where('projects.start_date >=', $start);
        }
        if (!empty($end)) {
            $q->where('projects.end_date <=', $end);
        }
        if (!empty($search)) {
            $q->groupStart()
              ->like('projects.title', $search)
              ->orLike('projects.description', $search)
              ->orLike('clients.company_name', $search)
              ->groupEnd();
        }

        $projects = $q->orderBy('projects.created_at','DESC')->findAll();

        $counts = [];
        foreach (['draft','active','on_hold','completed','archived'] as $s) {
            $counts[$s] = $this->projects->where('status', $s)->countAllResults();
        }

        $clients  = model(ClientModel::class)->select('id,company_name')->orderBy('company_name', 'ASC')->findAll();
        $branches = model(\App\Models\Office_model::class)->where('deleted', 0)->orderBy('name', 'ASC')->findAll();

        return $this->render('projects/index', [
            'title'    => 'Projects',
            'projects' => $projects,
            'filter'   => $filter,
            'counts'   => $counts,
            'viewMode' => $viewMode,
            'clients'  => $clients,
            'branches' => $branches,
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
            'project_stage'=> $this->request->getPost('project_stage') ?? 'pre_construction',
            'priority'    => $this->request->getPost('priority') ?? 'medium',
            'sector'         => $this->request->getPost('sector') ?: null,
            'total_floors'   => $this->request->getPost('total_floors') ?: null,
            'site_acreage'   => $this->request->getPost('site_acreage') ?: null,
            'gross_sqft'     => $this->request->getPost('gross_sqft') ?: null,
            'duration_months' => $this->request->getPost('duration_months') ?: null,
            'labor_productivity_factor' => $this->request->getPost('labor_productivity_factor') ?: 1.0,
            'standard_owner_id' => $this->request->getPost('standard_owner_id') ?: null,
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'end_date'    => $this->request->getPost('end_date') ?: null,
            'budget'      => $this->request->getPost('budget') ?: null,
            'contract_type'=> $this->request->getPost('contract_type') ?: null,
            'versioned_budget_baseline' => $this->request->getPost('versioned_budget_baseline') ?: null,
            'currency'    => $this->request->getPost('currency') ?? 'USD',
            'description' => $this->request->getPost('description') ?: null,
            'color'       => $this->request->getPost('color') ?? '#4a90e2',
            'latitude'    => $this->request->getPost('latitude') ?: null,
            'longitude'   => $this->request->getPost('longitude') ?: null,
            'geofence_radius' => $this->request->getPost('geofence_radius') ?: 100,
            'created_by'  => session()->get('user_id'),
            'tenant_id'   => session()->get('tenant_id'),
            'branch_id'   => session()->get('branch_id'),
        ];

        if (!trim($data['title'])) {
            return redirect()->back()->withInput()->with('error', 'Project title is required.');
        }

        $id = $this->projects->insert($data);
        $this->automation->trigger('projects', 'create', array_merge(['id' => $id], $data), session()->get('tenant_id'));

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
        
        // Fetch Procurement & Bid Data for Phase 5
        $procurementModel = new \App\Models\ProcurementModel();
        $bidComparisonModel = new \App\Models\BidComparisonModel();
        
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
            'procurement_items' => ($tab === 'procurement') ? $procurementModel->where('project_id', $id)->findAll() : [],
            'bid_comparisons'   => ($tab === 'procurement') ? $bidComparisonModel->where('project_id', $id)->findAll() : [],
            'change_events' => ($tab === 'change_management') ? $this->events->getForProject($id, $project['tenant_id']) : [],
            'change_orders' => ($tab === 'change_management' || $tab === 'finance') ? $this->orders->getForProject($id, $project['tenant_id']) : [],
            'project_contracts' => ($tab === 'change_management') ? $this->contracts->forProject($id) : [],
            'meetings'      => ($tab === 'meetings') ? $this->meetings_model->getForProject($id, $project['tenant_id']) : [],
            'budget_data'   => ($tab === 'finance' || $tab === 'finance_wip' || $tab === 'production_control') ? $this->budget_model->getProjectFinancials($id, $project['tenant_id']) : null,
            'control_metrics' => ($tab === 'production_control') ? (new \App\Services\ProjectControlService())->getPerformanceMetrics($id) : null,
            'bid_packages'  => ($tab === 'bidding') ? $this->bid_packages->getForProject($id, $project['tenant_id']) : [],
            'bids_per_package' => ($tab === 'bidding') ? $this->getBidsPerPackage($id) : [],
            'drawings_list' => ($tab === 'drawings') ? $this->drawings->where('project_id', $id)->where('tenant_id', $project['tenant_id'])->findAll() : [],
            'budget_items'  => ($tab === 'finance') ? $this->budget_items_model->getForProject($id, $project['tenant_id']) : [],
            'cost_codes'    => ($tab === 'finance') ? $this->cost_codes_model->forProject($id) : [],
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
            'project_stage'=> $this->request->getPost('project_stage') ?? 'active',
            'priority'    => $this->request->getPost('priority') ?? 'medium',
            'sector'         => $this->request->getPost('sector') ?: null,
            'total_floors'   => $this->request->getPost('total_floors') ?: null,
            'site_acreage'   => $this->request->getPost('site_acreage') ?: null,
            'gross_sqft'     => $this->request->getPost('gross_sqft') ?: null,
            'duration_months' => $this->request->getPost('duration_months') ?: null,
            'labor_productivity_factor' => $this->request->getPost('labor_productivity_factor') ?: 1.0,
            'standard_owner_id' => $this->request->getPost('standard_owner_id') ?: null,
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'end_date'    => $this->request->getPost('end_date') ?: null,
            'budget'      => $this->request->getPost('budget') ?: null,
            'contract_type'=> $this->request->getPost('contract_type') ?: null,
            'versioned_budget_baseline' => $this->request->getPost('versioned_budget_baseline') ?: null,
            'currency'    => $this->request->getPost('currency') ?? 'USD',
            'description' => $this->request->getPost('description') ?: null,
            'color'       => $this->request->getPost('color') ?? '#4a90e2',
            'latitude'    => $this->request->getPost('latitude') ?: null,
            'longitude'   => $this->request->getPost('longitude') ?: null,
            'geofence_radius' => $this->request->getPost('geofence_radius') ?: 100,
        ];
        $this->projects->update($id, $data);
        $this->automation->trigger('projects', 'update', array_merge(['id' => $id], $data), session()->get('tenant_id'));

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
        $files = $this->request->getFiles();
        $webcamPhotos = $this->request->getPost('webcam_photos') ?: [];

        // Handle File Uploads
        if (!empty($files['photos'])) {
            $uploadFiles = is_array($files['photos']) ? $files['photos'] : [$files['photos']];
            $titles = $this->request->getPost('file_titles') ?: [];
            $descriptions = $this->request->getPost('file_descriptions') ?: [];

            foreach ($uploadFiles as $index => $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $name = $file->getRandomName();
                    $file->move($dir, $name);
                    
                    // Resize image for optimization (Max width 1200px)
                    $image = \Config\Services::image()
                        ->withFile($dir . $name)
                        ->resize(1200, 1200, true, 'width')
                        ->save($dir . $name);

                    $this->photos->insert([
                        'project_id'  => $projectId,
                        'photo_path'  => "uploads/projects/{$projectId}/progress/{$name}",
                        'title'       => $titles[$index] ?? '',
                        'description' => $descriptions[$index] ?? '',
                        'caption'     => $file->getClientName(),
                        'uploaded_by' => session()->get('user_id')
                    ]);
                }
            }
        }

        // Handle Webcam Photos (Base64)
        if (!empty($webcamPhotos)) {
            $titles = $this->request->getPost('cap_titles') ?: [];
            $descriptions = $this->request->getPost('cap_descriptions') ?: [];

            foreach ($webcamPhotos as $index => $data) {
                if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                    $data = substr($data, strpos($data, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif
                    $data = base64_decode($data);
                    
                    if ($data !== false) {
                        $name = time() . '_' . uniqid() . '.' . $type;
                        $fullPath = $dir . DIRECTORY_SEPARATOR . $name;
                        file_put_contents($fullPath, $data);
                        
                        // Resize webcam snap too
                        \Config\Services::image()
                            ->withFile($fullPath)
                            ->resize(1200, 1200, true, 'width')
                            ->save($fullPath);

                        $this->photos->insert([
                            'project_id'  => $projectId,
                            'photo_path'  => "uploads/projects/{$projectId}/progress/{$name}",
                            'title'       => $titles[$index] ?? '',
                            'description' => $descriptions[$index] ?? '',
                            'caption'     => 'Webcam Capture ' . ($index + 1),
                            'uploaded_by' => session()->get('user_id')
                        ]);
                    }
                }
            }
        }

        return $this->response->setJSON(['success' => true]);
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
        // Close session early to avoid locking other requests during slow PDF generation
        session_write_close();

        $start = $this->request->getGet('start_date');
        $end   = $this->request->getGet('end_date');

        $query = $this->photos->where('project_id', $projectId);
        if ($start) $query->where('created_at >=', $start . ' 00:00:00');
        if ($end)   $query->where('created_at <=', $end . ' 23:59:59');
        
        $photos = $query->orderBy('created_at', 'DESC')->findAll();

        if (empty($photos)) {
            return redirect()->back()->with('error', 'No photos available for the selected range.');
        }

        // Project details with client information
        $projectDetails = $this->projects->withDetails()->find($projectId);

        $t1 = microtime(true);
        $data = [
            'project'     => $projectDetails,
            'photos'      => $photos,
            'date'        => date('F j, Y'),
            'appSettings' => $this->appSettings,
        ];

        $html = view('projects/progress_report_pdf', $data);
        $t3 = microtime(true);

        $tempDir = WRITEPATH . 'temp';
        if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', FCPATH); // Allow local files
        $options->set('isHtml5ParserEnabled', true);
        $options->set('enable_font_subsetting', false); 
        $options->set('defaultFont', 'Helvetica');
        $options->set('tempDir', $tempDir);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setBasePath(FCPATH);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $t4 = microtime(true);

        log_message('error', sprintf(
            "PDF Perf [%d photos]: View: %.3fs, Render: %.3fs, Total: %.3fs",
            count($photos),
            $t3 - $t1,
            $t4 - $t3,
            $t4 - $t1
        ));

        $filename = "Progress_Report_" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $projectDetails['title']) . "_" . date('Ymd') . ".pdf";
        $dompdf->stream($filename, ["Attachment" => false]);
    }

    public function distributeReport(int $projectId)
    {
        session_write_close();
        $recipients = $this->request->getPost('recipients') ?: [];
        $customEmails = $this->request->getPost('custom_emails');
        $message = $this->request->getPost('message');

        if ($customEmails) {
            $parts = explode(',', $customEmails);
            foreach ($parts as $p) {
                $email = trim($p);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = $email;
                }
            }
        }

        if (empty($recipients)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No valid recipients selected.']);
        }

        // Generate PDF content
        $project = $this->projects->withDetails()->find($projectId);
        $start = $this->request->getPost('start_date');
        $end   = $this->request->getPost('end_date');

        $query = $this->photos->where('project_id', $projectId);
        if ($start) $query->where('created_at >=', $start . ' 00:00:00');
        if ($end)   $query->where('created_at <=', $end . ' 23:59:59');
        $photos = $query->orderBy('created_at', 'DESC')->findAll();

        $html = view('projects/progress_report_pdf', [
            'project'     => $project,
            'photos'      => $photos,
            'date'        => date('F j, Y'),
            'appSettings' => $this->appSettings
        ]);

        $tempDir = WRITEPATH . 'temp';
        if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', FCPATH);
        $options->set('enable_font_subsetting', false);
        $options->set('tempDir', $tempDir);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setBasePath(FCPATH);
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        // Send Email
        $emailService = \Config\Services::email();
        $emailService->setTo($recipients);
        $emailService->setSubject("Site Progress Report: " . $project['title']);
        $emailService->setMessage($message ?: "Please find the attached site progress report for " . $project['title']);
        $emailService->attach($pdfOutput, 'Progress_Report_' . time() . '.pdf', 'application/pdf');

        if ($emailService->send()) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to send emails.']);
        }
    }

    private function getBidsPerPackage(int $projectId): array
    {
        $packages = $this->bid_packages->where('project_id', $projectId)->findAll();
        $bids_per_package = [];
        foreach ($packages as $pkg) {
            $bids_per_package[$pkg['id']] = $this->bid_model->getForPackage($pkg['id']);
        }
        return $bids_per_package;
    }
}
