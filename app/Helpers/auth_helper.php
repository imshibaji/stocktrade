<?php

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return session()->get('isLoggedIn') === true;
    }
}

if (!function_exists('is_impersonating')) {
    function is_impersonating(): bool
    {
        return session()->get('impersonating_user_id') !== null;
    }
}

if (!function_exists('impersonated_user_id')) {
    function impersonated_user_id(): ?int
    {
        $id = session()->get('impersonating_user_id');
        return $id !== null ? (int) $id : null;
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        $impersonatedId = impersonated_user_id();
        if ($impersonatedId !== null) {
            $model = new \App\Models\UserModel();
            return $model->find($impersonatedId);
        }
        return session()->get('user');
    }
}

if (!function_exists('current_user_id')) {
    function current_user_id(): ?int
    {
        $impersonatedId = impersonated_user_id();
        if ($impersonatedId !== null) {
            return $impersonatedId;
        }
        $user = session()->get('user');
        return $user['id'] ?? null;
    }
}
