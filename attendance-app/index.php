<?php
require_once __DIR__ . '/includes/auth.php';
$user = current_user();
if (!$user) {
    header('Location: /attendance-app/public/login.php');
    exit;
}

$target = '/attendance-app/' . $user['role'] . '/dashboard.php';
header('Location: ' . $target);
exit;
