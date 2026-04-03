<?php
require_once __DIR__ . '/../config/config.php';

function secure_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();

    if (!isset($_SESSION['regenerated_at'])) {
        session_regenerate_id(true);
        $_SESSION['regenerated_at'] = time();
    }
}

function csrf_token(): string
{
    secure_session_start();
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verify_csrf_token(?string $token): bool
{
    secure_session_start();
    return isset($_SESSION[CSRF_TOKEN_NAME]) && is_string($token)
        && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}
