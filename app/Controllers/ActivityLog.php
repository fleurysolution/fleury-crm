<?php

namespace App\Controllers;

use App\Models\ActivityLogModel;

class ActivityLog extends BaseAppController
{
    /**
     * GET /activity — global activity log (admin view)
     */
    public function index(): string
    {
        $feed = (new ActivityLogModel())->globalFeed(150);

        return $this->render('activity/index', [
            'feed' => $feed,
        ]);
    }

    /**
     * GET /projects/:id/activity — project activity feed (JSON for inline tab)
     */
    public function forProject(int $projectId): \CodeIgniter\HTTP\Response
    {
        $feed = (new ActivityLogModel())->forProject($projectId, 100);
        return $this->response->setJSON($feed);
    }
}
