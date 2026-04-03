<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

function current_user(): ?array
{
    secure_session_start();
    return $_SESSION['user'] ?? null;
}

function login_user(string $email, string $password): bool
{
    $stmt = db()->prepare('SELECT id, role, email, password_hash, full_name, status FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || $user['status'] !== 'active') {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    secure_session_start();
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'role' => $user['role'],
        'email' => $user['email'],
        'full_name' => $user['full_name'],
    ];

    return true;
}

function logout_user(): void
{
    secure_session_start();
    $_SESSION = [];
    session_destroy();
}

function require_auth(?array $roles = null): void
{
    $user = current_user();
    if (!$user) {
        redirect('/attendance-app/public/login.php');
    }

    if ($roles && !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('Forbidden');
    }
}
