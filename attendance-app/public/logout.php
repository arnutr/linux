<?php
require_once __DIR__ . '/../includes/auth.php';
logout_user();
header('Location: /attendance-app/public/login.php');
exit;
