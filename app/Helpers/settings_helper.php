<?php

use App\Models\SettingModel;

if (! function_exists('setting')) {
    /**
     * Get a setting value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        // Simple caching strategy could be added here
        $model = new SettingModel();
        return $model->getValue($key, $default);
    }
}

if (! function_exists('update_setting')) {
    /**
     * Update or create a setting.
     *
     * @param string $key
     * @param mixed  $value
     * @param string $group
     *
     * @return void
     */
    function update_setting(string $key, $value, string $group = 'general')
    {
        $model = new SettingModel();
        $model->setValue($key, $value, $group);
    }
}
