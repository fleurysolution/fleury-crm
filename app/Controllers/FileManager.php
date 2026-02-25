<?php

namespace App\Controllers;

use App\Models\ProjectFileModel;
use App\Models\ActivityLogModel;
use App\Models\ProjectModel;

class FileManager extends BaseAppController
{
    protected string $uploadPath = WRITEPATH . 'uploads/project_files/';

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Ensure upload directory exists
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * GET /files — global file manager (all projects)
     */
    public function index(): string
    {
        $db    = \Config\Database::connect();
        $files = $db->query(
            'SELECT pf.*, CONCAT(u.first_name, " ", u.last_name) AS uploader_name, p.title AS project_name
             FROM project_files pf
             LEFT JOIN fs_users u ON u.id = pf.uploaded_by
             LEFT JOIN projects p ON p.id = pf.project_id
             WHERE pf.deleted_at IS NULL
             ORDER BY pf.created_at DESC'
        )->getResultArray();

        $projects = $db->table('projects')
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('title', 'ASC')->get()->getResultArray();

        return $this->render('files/index', [
            'files'    => $files,
            'projects' => $projects,
        ]);
    }

    /**
     * GET /projects/:id/files — project files tab (inline, returns JSON)
     */
    public function forProject(int $projectId): \CodeIgniter\HTTP\Response
    {
        $files = (new ProjectFileModel())->forProject($projectId);
        return $this->response->setJSON($files);
    }

    /**
     * POST /projects/:id/files/upload — handle file upload
     */
    public function upload(int $projectId): \CodeIgniter\HTTP\Response
    {
        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'No valid file uploaded.']);
        }

        if ($file->getSize() > 20 * 1024 * 1024) {
            return $this->response->setJSON(['success' => false, 'message' => 'File too large (max 20 MB).']);
        }

        $stored = $file->getRandomName();
        $file->move($this->uploadPath, $stored);

        $fm = new ProjectFileModel();
        $id = $fm->insert([
            'project_id'  => $projectId,
            'entity_type' => $this->request->getPost('entity_type') ?: null,
            'entity_id'   => $this->request->getPost('entity_id')   ?: null,
            'name'        => $file->getClientName(),
            'stored_name' => $stored,
            'path'        => 'project_files/' . $stored,
            'mime_type'   => $file->getClientMimeType(),
            'size'        => $file->getSize(),
            'description' => $this->request->getPost('description') ?: null,
            'uploaded_by' => $this->currentUser['id'] ?? null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        ActivityLogModel::log('file', $fm->db->insertID(), 'created',
            "File uploaded: {$file->getClientName()}", ['project_id' => $projectId]);

        $row = $fm->find($fm->db->insertID()) ?? $fm->find($id);
        return $this->response->setJSON(['success' => true, 'file' => $row]);
    }

    /**
     * GET /files/:id/download — download/view file
     */
    public function download(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $fm   = new ProjectFileModel();
        $file = $fm->find($id);
        if (!$file || $file['deleted_at']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $fullPath = $this->uploadPath . $file['stored_name'];
        if (!file_exists($fullPath)) {
            return $this->response->setStatusCode(404)->setBody('File not found on disk.');
        }

        // Inline preview for images/pdf, download for everything else
        $inline = in_array(
            explode('/', $file['mime_type'] ?? '')[0],
            ['image', 'text']
        ) || $file['mime_type'] === 'application/pdf';

        return $this->response
            ->setHeader('Content-Type', $file['mime_type'] ?? 'application/octet-stream')
            ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $file['name'] . '"')
            ->setHeader('Content-Length', (string)$file['size'])
            ->setBody(file_get_contents($fullPath));
    }

    /**
     * POST /files/:id/delete — soft delete
     */
    public function delete(int $id): \CodeIgniter\HTTP\Response
    {
        $fm   = new ProjectFileModel();
        $file = $fm->find($id);
        if (!$file) return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);

        $fm->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);

        // Remove physical file
        $fullPath = $this->uploadPath . $file['stored_name'];
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        ActivityLogModel::log('file', $id, 'deleted', "File deleted: {$file['name']}", ['project_id' => $file['project_id']]);
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /files/:id/update — update description
     */
    public function update(int $id): \CodeIgniter\HTTP\Response
    {
        (new ProjectFileModel())->update($id, [
            'description' => $this->request->getPost('description'),
        ]);
        return $this->response->setJSON(['success' => true]);
    }
}
