<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid px-3 pb-4">
  <div class="d-flex justify-content-between mb-3"><h4>Member Management</h4><button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#createMemberModal">Add Member</button></div>
  <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
  <div class="card shadow-sm border-0"><div class="table-responsive">
    <table class="table align-middle mb-0"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th></th></tr></thead><tbody>
      <?php foreach ($members as $m): ?>
        <tr>
          <td><?= e($m['name']) ?></td><td><?= e($m['email']) ?></td><td><span class="badge text-bg-secondary"><?= e($m['role']) ?></span></td>
          <td class="text-end">
            <button class="btn btn-sm btn-outline-primary js-edit-member" data-member='<?= e(json_encode($m)) ?>' data-bs-toggle="modal" data-bs-target="#editMemberModal">Edit</button>
            <form method="post" class="d-inline" action="<?= base_url('index.php?route=members/delete') ?>" onsubmit="return confirm('Delete this member?')">
              <input type="hidden" name="_csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody></table>
  </div></div>
</div>

<div class="modal fade" id="createMemberModal"><div class="modal-dialog"><form method="post" class="modal-content" action="<?= base_url('index.php?route=members/store') ?>">
  <div class="modal-header"><h5 class="modal-title">Create Member</h5></div><div class="modal-body">
    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
    <input class="form-control mb-2" name="name" placeholder="Name" required>
    <input type="email" class="form-control mb-2" name="email" placeholder="Email" required>
    <input type="password" class="form-control mb-2" name="password" placeholder="Password (min 6)" required>
    <select class="form-select" name="role"><option value="customer">Customer</option><option value="admin">Admin</option></select>
  </div><div class="modal-footer"><button class="btn btn-accent">Save</button></div>
</form></div></div>

<div class="modal fade" id="editMemberModal"><div class="modal-dialog"><form method="post" class="modal-content" action="<?= base_url('index.php?route=members/update') ?>">
  <div class="modal-header"><h5 class="modal-title">Edit Member</h5></div><div class="modal-body">
    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" id="em-id">
    <input class="form-control mb-2" name="name" id="em-name" required>
    <input type="email" class="form-control mb-2" name="email" id="em-email" required>
    <input type="password" class="form-control mb-2" name="password" placeholder="Leave blank to keep password">
    <select class="form-select" name="role" id="em-role"><option value="customer">Customer</option><option value="admin">Admin</option></select>
  </div><div class="modal-footer"><button class="btn btn-accent">Update</button></div>
</form></div></div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
