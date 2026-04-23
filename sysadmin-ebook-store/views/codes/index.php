<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid px-3 pb-4">
  <h4 class="mb-3">Download Link Management</h4>
  <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

  <div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <form class="row g-2" method="get" action="<?= base_url('index.php') ?>">
      <input type="hidden" name="route" value="codes">
      <div class="col-md-3"><select class="form-select" name="book_id"><option value="">All Books</option><?php foreach ($books as $book): ?><option value="<?= $book['id'] ?>" <?= ($filters['book_id']==$book['id'])?'selected':'' ?>><?= e($book['title']) ?></option><?php endforeach; ?></select></div>
      <div class="col-md-2"><select class="form-select" name="status"><option value="">All Status</option><option value="used" <?= ($filters['status']==='used')?'selected':'' ?>>Used</option><option value="unused" <?= ($filters['status']==='unused')?'selected':'' ?>>Unused</option></select></div>
      <div class="col-md-4"><input class="form-control" name="search" placeholder="Search code" value="<?= e($filters['search']) ?>"></div>
      <div class="col-md-3 d-flex gap-2"><button class="btn btn-dark w-100">Filter</button><a class="btn btn-outline-success w-100" href="<?= base_url('index.php?route=codes/export') ?>">Export CSV</a></div>
    </form>
  </div></div>

  <div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <h6>Bulk Generate Codes</h6>
    <form class="row g-2" method="post" action="<?= base_url('index.php?route=codes/generate') ?>">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
      <div class="col-md-3"><select class="form-select" name="book_id" required><?php foreach ($books as $book): ?><option value="<?= $book['id'] ?>"><?= e($book['title']) ?></option><?php endforeach; ?></select></div>
      <div class="col-md-2"><input type="number" class="form-control" min="1" max="500" name="quantity" value="10" required></div>
      <div class="col-md-2"><input type="number" class="form-control" min="1" name="usage_limit" value="1" required></div>
      <div class="col-md-3"><input type="date" class="form-control" name="expires_at"></div>
      <div class="col-md-2"><button class="btn btn-accent w-100">Generate</button></div>
    </form>
  </div></div>

  <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Code</th><th>Book</th><th>Usage</th><th>Expires</th><th>Status</th></tr></thead><tbody>
    <?php foreach ($codes as $c): $used=(int)$c['used_count']>=(int)$c['usage_limit']; ?>
      <tr><td><code><?= e($c['code']) ?></code></td><td><?= e($c['title']) ?></td><td><?= (int)$c['used_count'] ?>/<?= (int)$c['usage_limit'] ?></td><td><?= e($c['expires_at'] ?: '-') ?></td><td><?= $used ? '<span class="badge text-bg-danger">Used</span>' : '<span class="badge text-bg-success">Unused</span>' ?></td></tr>
    <?php endforeach; ?>
  </tbody></table></div></div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
