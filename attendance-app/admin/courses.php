<?php
require_once __DIR__ . '/../includes/auth.php';
require_auth(['admin']);
$rows = db()->query('SELECT c.*, u.full_name instructor_name FROM courses c LEFT JOIN users u ON u.id=c.instructor_user_id ORDER BY c.id DESC')->fetchAll();
include __DIR__ . '/../templates/header.php';
?>
<div class="row"><?php $role='admin'; include __DIR__ . '/../templates/sidebar.php'; ?><div class="col-md-9 col-lg-10">
<h3>รายวิชา</h3>
<table class="table table-striped"><thead><tr><th>Code</th><th>Name</th><th>Section</th><th>Instructor</th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><?=e($r['course_code'])?></td><td><?=e($r['course_name'])?></td><td><?=e($r['section_name'])?></td><td><?=e($r['instructor_name']??'-')?></td></tr><?php endforeach;?>
</tbody></table>
</div></div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
