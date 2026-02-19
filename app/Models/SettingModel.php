<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['key', 'value', 'group', 'updated_at'];

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
     * Update or Create a setting.
     */
    public function setValue(string $key, $value, string $group = 'general')
    {
        $existing = $this->where('key', $key)->first();

        if ($existing) {
            $this->update($existing['id'], ['value' => $value, 'group' => $group]);
        } else {
            $this->insert(['key' => $key, 'value' => $value, 'group' => $group]);
        }
    }
}
