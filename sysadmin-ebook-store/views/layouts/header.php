<?php $route = $_GET['route'] ?? 'dashboard'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e(config('app.name')) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
</head>
<body>
<?php if (is_logged_in()): ?>
<div class="d-flex" id="appWrap">
  <aside class="sidebar p-3">
    <h5 class="text-white mb-4">SYSADMIN STORE</h5>
    <nav class="nav flex-column gap-1">
      <a class="nav-link <?= str_contains($route, 'dashboard') ? 'active' : '' ?>" href="<?= base_url('index.php?route=dashboard') ?>"><i class="bi bi-grid me-2"></i>Dashboard</a>
      <a class="nav-link <?= str_contains($route, 'members') ? 'active' : '' ?>" href="<?= base_url('index.php?route=members') ?>"><i class="bi bi-people me-2"></i>Members</a>
      <a class="nav-link <?= str_contains($route, 'books') ? 'active' : '' ?>" href="<?= base_url('index.php?route=books') ?>"><i class="bi bi-book me-2"></i>Books</a>
      <a class="nav-link <?= str_contains($route, 'codes') ? 'active' : '' ?>" href="<?= base_url('index.php?route=codes') ?>"><i class="bi bi-key me-2"></i>Codes</a>
      <a class="nav-link" href="<?= base_url('index.php?route=redeem') ?>"><i class="bi bi-download me-2"></i>Redeem Page</a>
      <a class="nav-link" href="<?= base_url('index.php?route=logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>
  </aside>
  <main class="content-area">
    <nav class="navbar bg-white shadow-sm rounded-3 mb-4 px-3 mt-3">
      <span class="fw-semibold">Welcome, <?= e(user()['name'] ?? 'Guest') ?></span>
    </nav>
<?php endif; ?>
