<?php
/**
 * Centralized session bootstrap with sensible defaults.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 60 * 60 * 24 * 7,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    ]);
    session_start();
}

function current_user_id(): ?int
{
    return $_SESSION['user']['UserID'] ?? null;
}

function current_user_role(): ?string
{
    return $_SESSION['user']['Role'] ?? null;
}
?>

