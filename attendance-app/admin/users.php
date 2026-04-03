<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/session.php';
require_auth(['admin']);
$pdo = db();
$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST' && verify_csrf_token($_POST['csrf_token']??null)) {
  $uid=(int)($_POST['user_id']??0);
  $newPassword = 'Reset@1234';
  $hash=password_hash($newPassword,PASSWORD_DEFAULT);
  $stmt=$pdo->prepare('UPDATE users SET password_hash=:h WHERE id=:id');
  $stmt->execute(['h'=>$hash,'id'=>$uid]);
  $msg='Password reset to Reset@1234';
}
$rows=$pdo->query('SELECT id,full_name,email,role,status,created_at FROM users ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../templates/header.php';
?>
<div class="row"><?php $role='admin'; include __DIR__ . '/../templates/sidebar.php'; ?><div class="col-md-9 col-lg-10">
<h3>จัดการผู้ใช้</h3>
<?php if($msg):?><div class="alert alert-info"><?=e($msg)?></div><?php endif;?>
<table class="table table-bordered"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><?=$r['id']?></td><td><?=e($r['full_name'])?></td><td><?=e($r['email'])?></td><td><?=e($r['role'])?></td><td>
<form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="user_id" value="<?=$r['id']?>"><button class="btn btn-sm btn-outline-warning">Reset Password</button></form>
</td></tr><?php endforeach;?>
</tbody></table>
</div></div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
