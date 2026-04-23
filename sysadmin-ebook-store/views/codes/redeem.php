<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container py-5" style="max-width:600px;">
  <div class="card border-0 shadow-lg rounded-4">
    <div class="card-body p-4">
      <h3 class="mb-3">Redeem Your Download Code</h3>
      <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
      <form method="post" action="<?= base_url('index.php?route=redeem') ?>">
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <input class="form-control form-control-lg mb-3" name="code" placeholder="SYS-BOOK-XXXX-YYYY" required>
        <button class="btn btn-accent w-100">Validate Code</button>
      </form>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
