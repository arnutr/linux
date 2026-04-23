<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container min-vh-100 d-flex align-items-center justify-content-center">
  <div class="card login-card shadow border-0">
    <div class="card-body p-4">
      <h4 class="mb-3">Admin Login</h4>
      <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
      <form method="post" action="<?= base_url('index.php?route=login') ?>">
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
        <button class="btn btn-accent w-100">Login</button>
      </form>
      <p class="text-muted small mt-3 mb-0">Default admin: admin@store.local / admin123</p>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
