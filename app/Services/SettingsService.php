<?php

namespace App\Services;

use App\Models\SettingModel;

class SettingsService
{
    private bool $booted = false;
    private int $ttlSeconds = 3600;

    public function boot(): void
    {
        if ($this->booted) return;

        $model = model(SettingModel::class);
        $rows  = $model->findAll();

        foreach ($rows as $row) {
            cache()->save($this->cacheKey($row['key']), $row['value'], $this->ttlSeconds);
        }

        $this->booted = true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $val = cache($this->cacheKey($key));
        return $val === null ? $default : $val;
    }

    public function set(string $key, string $value): bool
    {
        $model = model(SettingModel::class);
        $model->upsert($key, $value);
        cache()->save($this->cacheKey($key), $value, $this->ttlSeconds);
        return true;
    }

    private function cacheKey(string $key): string
    {
        return 'fs_setting_' . $key;
    }
}