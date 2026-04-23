<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid px-3 pb-4">
  <div class="d-flex justify-content-between mb-3"><h4>Book Management</h4><button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#createBookModal">Add eBook</button></div>
  <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
  <div class="row g-3">
    <?php foreach ($books as $b): ?>
      <div class="col-md-4"><div class="card h-100 shadow-sm border-0 rounded-4">
        <?php if ($b['cover_image']): ?><img class="book-cover" src="<?= base_url('../uploads/covers/' . $b['cover_image']) ?>" alt="cover"><?php endif; ?>
        <div class="card-body"><h5><?= e($b['title']) ?></h5><p class="small text-muted"><?= e($b['description']) ?></p><strong>$<?= number_format((float)$b['price'], 2) ?></strong></div>
        <div class="card-footer bg-white border-0 d-flex justify-content-between">
          <button class="btn btn-sm btn-outline-primary js-edit-book" data-book='<?= e(json_encode($b)) ?>' data-bs-toggle="modal" data-bs-target="#editBookModal">Edit</button>
          <form method="post" action="<?= base_url('index.php?route=books/delete') ?>" onsubmit="return confirm('Delete this book?')">
            <input type="hidden" name="_csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button>
          </form>
        </div>
      </div></div>
    <?php endforeach; ?>
  </div>
</div>

<div class="modal fade" id="createBookModal"><div class="modal-dialog"><form method="post" enctype="multipart/form-data" class="modal-content" action="<?= base_url('index.php?route=books/store') ?>">
  <div class="modal-header"><h5>Add eBook</h5></div><div class="modal-body">
    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
    <input class="form-control mb-2" name="title" placeholder="Title" required>
    <textarea class="form-control mb-2" name="description" placeholder="Description"></textarea>
    <input type="number" step="0.01" class="form-control mb-2" name="price" placeholder="Price" required>
    <label class="form-label">Cover image</label><input type="file" class="form-control mb-2" name="cover_image" accept="image/*">
    <label class="form-label">PDF file</label><input type="file" class="form-control" name="file_pdf" accept="application/pdf" required>
  </div><div class="modal-footer"><button class="btn btn-accent">Save</button></div>
</form></div></div>

<div class="modal fade" id="editBookModal"><div class="modal-dialog"><form method="post" enctype="multipart/form-data" class="modal-content" action="<?= base_url('index.php?route=books/update') ?>">
  <div class="modal-header"><h5>Edit eBook</h5></div><div class="modal-body">
    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" id="eb-id">
    <input class="form-control mb-2" name="title" id="eb-title" required>
    <textarea class="form-control mb-2" name="description" id="eb-description"></textarea>
    <input type="number" step="0.01" class="form-control mb-2" name="price" id="eb-price" required>
    <label class="form-label">Replace cover</label><input type="file" class="form-control mb-2" name="cover_image" accept="image/*">
    <label class="form-label">Replace PDF</label><input type="file" class="form-control" name="file_pdf" accept="application/pdf">
  </div><div class="modal-footer"><button class="btn btn-accent">Update</button></div>
</form></div></div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
