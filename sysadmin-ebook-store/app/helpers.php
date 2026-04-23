<?php
function config(string $key)
{
    static $cfg;
    if (!$cfg) {
        $cfg = require __DIR__ . '/../config/config.php';
    }
    $parts = explode('.', $key);
    $value = $cfg;
    foreach ($parts as $part) {
        $value = $value[$part] ?? null;
    }
    return $value;
}

function base_url(string $path = ''): string
{
    return rtrim(config('app.base_url'), '/') . '/' . ltrim($path, '/');
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_validate(?string $token): bool
{
    return hash_equals($_SESSION['_csrf_token'] ?? '', (string)$token);
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(): void
{
    if (!is_logged_in()) {
        redirect('index.php?route=login');
    }

    $last = $_SESSION['last_activity'] ?? 0;
    if (time() - $last > (int) config('app.session_timeout')) {
        session_destroy();
        redirect('index.php?route=login&expired=1');
    }

    $_SESSION['last_activity'] = time();
}

function require_admin(): void
{
    require_auth();
    if ((user()['role'] ?? 'customer') !== 'admin') {
        http_response_code(403);
        exit('Forbidden');
    }
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
}
