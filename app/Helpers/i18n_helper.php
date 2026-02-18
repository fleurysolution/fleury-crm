<?php

if (!function_exists('t')) {
    function t(string $key, ?string $fallback = null, array $args = []): string
    {
        $v = lang('custom.' . $key, $args);
        if ($v !== 'custom.' . $key) return $v;

        $v = lang('default.' . $key, $args);
        if ($v !== 'default.' . $key) return $v;

        return $fallback ?? $key;
    }
}
