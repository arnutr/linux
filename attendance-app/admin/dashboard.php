<?php
require_once __DIR__ . '/../includes/auth.php';
require_auth(['admin']);
$pdo = db();
$stats = [
  'users' => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
  'courses' => (int)$pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn(),
  'sessions' => (int)$pdo->query('SELECT COUNT(*) FROM attendance_sessions')->fetchColumn(),
  'flags' => (int)$pdo->query('SELECT COUNT(*) FROM attendance_records WHERE suspicious_flag=1')->fetchColumn(),
];
include __DIR__ . '/../templates/header.php';
?>
<div class="row">
<?php $role='admin'; include __DIR__ . '/../templates/sidebar.php'; ?>
<div class="col-md-9 col-lg-10">
  <h3>ภาพรวมระบบ</h3>
  <div class="row g-3">
    <?php foreach ($stats as $k=>$v): ?><div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted"><?= e(strtoupper($k)) ?></div><div class="fs-4"><?= $v ?></div></div></div></div><?php endforeach; ?>
  </div>
  <canvas id="summaryChart" class="mt-4" height="90"></canvas>
</div>
</div>
<script>window.chartData = <?= json_encode(array_values($stats)) ?>;</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
