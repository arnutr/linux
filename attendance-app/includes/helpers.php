<?php

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function post(string $key, ?string $default = null): ?string
{
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
}

function get_client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function get_user_agent(): string
{
    return substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 500);
}

function calculate_distance_km(float $lat1, float $lon1, float $lat2, float $lon2): float
{
    $earthRadius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2)
        + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
        * sin($dLon / 2) * sin($dLon / 2);

    return 2 * $earthRadius * atan2(sqrt($a), sqrt(1 - $a));
}

function random_token(int $bytes = 16): string
{
    return bin2hex(random_bytes($bytes));
}
