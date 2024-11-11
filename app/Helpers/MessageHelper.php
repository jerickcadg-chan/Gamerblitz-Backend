<?php

if (!function_exists('alert_created_text')) {
    function alert_created_text($model = null)
    {
        return "Data {$model} berhasil ditambah!";
    }
}

if (!function_exists('alert_updated_text')) {
    function alert_updated_text($model = null)
    {
        return "Data {$model} berhasil diperbarui!";
    }
}

if (!function_exists('alert_deleted_text')) {
    function alert_deleted_text($model = null)
    {
        return "Data {$model} berhasil dihapus!";
    }
}

if (!function_exists('alert_imported_text')) {
    function alert_imported_text($model = null)
    {
        return "Data {$model} berhasil diimport!";
    }
}

if (!function_exists('alert_forbidden')) {
    function alert_forbidden()
    {
        return 'Whooooops! System error!';
    }
}

if (!function_exists('alert_error')) {
    function alert_error($model = null)
    {
        return app()->environment() == 'production' ? 'Internal server error' : substr($model, 0, 300);
    }
}

if (!function_exists('auth_role_is')) {
    function auth_role_is($role)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user->hasRole($role);
    }
}

if (!function_exists('greeting')) {
    function greeting() {
        $hour = now()->format('H');

        if ($hour < 10) {
            return 'Selamat pagi';
        }
        if ($hour < 14) {
            return 'Selamat siang';
        }
        if ($hour < 18) {
            return 'Selamat sore';
        }
        return 'Selamat malam';
    }
}
