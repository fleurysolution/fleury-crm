<?php

namespace App\Services;

use App\Models\PageModel;

class PageService
{
    public function findActiveBySlug(string $slug): ?array
    {
        return model(PageModel::class)->findActiveBySlug($slug);
    }

    public function canView(array $page, ?array $user): bool
    {
        $internal = !empty($page['internal_use_only']);
        if (!$internal) return true;

        // internal -> must be logged in
        if (!$user) return false;

        // admin -> always
        if (!empty($user['is_admin'])) return true;

        $teamOnly    = !empty($page['visible_to_team_members_only']);
        $clientsOnly = !empty($page['visible_to_clients_only']);

        if (!$teamOnly && !$clientsOnly) return true;

        if ($teamOnly && ($user['user_type'] ?? '') !== 'staff') return false;
        if ($clientsOnly && ($user['user_type'] ?? '') !== 'client') return false;

        return true;
    }
}