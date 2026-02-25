<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['key', 'value', 'group', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get a setting value by key.
     */
    public function getValue(string $key, $default = null)
    {
        $setting = $this->where('key', $key)->first();
        return $setting ? $setting['value'] : $default;
    }

    /**
     * Update or Create a setting (upsert).
     */
    public function setValue(string $key, $value, string $group = 'general'): void
    {
        $existing = $this->where('key', $key)->first();
        if ($existing) {
            $this->update($existing['id'], ['value' => $value, 'group' => $group]);
        } else {
            $this->insert(['key' => $key, 'value' => $value, 'group' => $group]);
        }
    }

    /**
     * Get all settings for a group as key => value array.
     */
    public function getGroup(string $group): array
    {
        $rows   = $this->where('group', $group)->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }

    /**
     * Get all settings as flat key => value array.
     */
    public function getAllAsKeyValue(): array
    {
        $rows   = $this->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }

    /**
     * Save multiple settings array at once.
     * @param array $data  ['key1' => 'val1', 'key2' => 'val2', ...]
     */
    public function saveMany(array $data, string $group = 'general'): void
    {
        foreach ($data as $key => $value) {
            $this->setValue($key, $value ?? '', $group);
        }
    }
}
