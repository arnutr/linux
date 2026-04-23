<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid px-3 pb-4">
  <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
  <div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><p>Total Books</p><h3><?= $totalBooks ?></h3></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><p>Total Codes</p><h3><?= $codeStats['total'] ?></h3></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><p>Used Codes</p><h3><?= $codeStats['used'] ?></h3></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><p>Total Members</p><h3><?= $totalUsers ?></h3></div></div></div>
  </div>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white fw-semibold">Recent Activity</div>
    <div class="table-responsive">
      <table class="table mb-0">
        <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Context</th></tr></thead>
        <tbody>
        <?php foreach ($recentLogs as $log): ?>
          <tr>
            <td><?= e($log['created_at']) ?></td>
            <td><?= e($log['name'] ?? 'Public') ?></td>
            <td><span class="badge text-bg-dark"><?= e($log['action']) ?></span></td>
            <td><?= e($log['context']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
