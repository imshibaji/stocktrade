<?php

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return session()->get('isLoggedIn') === true;
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        return session()->get('user');
    }
}

if (!function_exists('current_user_id')) {
    function current_user_id(): ?int
    {
        $user = session()->get('user');
        return $user['id'] ?? null;
    }
}
