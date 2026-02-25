<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationSettingModel extends Model
{
    protected $table            = 'notification_settings';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'event', 'category', 'sort',
        'notify_to_terms', 'notify_to_team', 'notify_to_team_members',
        'enable_email', 'enable_web', 'enable_slack',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all notification settings, optionally filtered by category.
     */
    public function getDetails(array $options = [])
    {
        $builder = $this->builder();
        if (!empty($options['category'])) {
            $builder->where('category', $options['category']);
        }
        if (!empty($options['id'])) {
            $builder->where('id', (int)$options['id']);
        }
        return $builder->orderBy('sort', 'ASC')->get();
    }

    /**
     * The list of "notify_to" terms supported.
     */
    public function notifyToTerms(): array
    {
        return [
            'creator', 'assignee', 'receiver',
            'all_staff', 'client', 'team', 'team_members',
        ];
    }

    /**
     * Save a notification setting by id.
     */
    public function saveById(array $data, int $id): bool
    {
        return $this->update($id, $data);
    }
}
