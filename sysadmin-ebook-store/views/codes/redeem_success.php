<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container py-5" style="max-width:650px;">
  <div class="card border-0 shadow-lg rounded-4">
    <div class="card-body p-4 text-center">
      <h3 class="text-success">Code Valid ✅</h3>
      <p class="mb-1">Book: <strong><?= e($code['title']) ?></strong></p>
      <p class="text-muted">Click the secure button below to download your PDF.</p>
      <a class="btn btn-accent btn-lg" href="<?= base_url('index.php?route=download&id=' . (int)$code['id'] . '&token=' . urlencode($token) . '&code=' . urlencode($code['code'])) ?>">Secure Download</a>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
