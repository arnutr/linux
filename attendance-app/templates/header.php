<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
$user = current_user();
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(APP_NAME) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/attendance-app/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Attendance</a>
    <div class="d-flex align-items-center gap-3 text-white small">
      <?php if ($user): ?>
      <span><?= e($user['full_name']) ?> (<?= e($user['role']) ?>)</span>
      <a class="btn btn-sm btn-light" href="/attendance-app/public/logout.php">Logout</a>
      <?php endif; ?>
      <button id="themeToggle" class="btn btn-sm btn-outline-light" type="button">Dark</button>
    </div>
  </div>
</nav>
<div class="container-fluid py-3">
