<?php
require_once __DIR__ . '/../includes/auth.php';
require_auth(['admin']);
$sql='SELECT ar.*,u.full_name,c.course_code,s.session_date FROM attendance_records ar
JOIN users u ON u.id=ar.student_user_id
JOIN attendance_sessions s ON s.id=ar.attendance_session_id
JOIN courses c ON c.id=s.course_id
ORDER BY ar.created_at DESC LIMIT 500';
$rows=db()->query($sql)->fetchAll();
include __DIR__ . '/../templates/header.php';
?>
<div class="row"><?php $role='admin'; include __DIR__ . '/../templates/sidebar.php'; ?><div class="col-md-9 col-lg-10">
<h3>บันทึกการเข้าเรียนทั้งหมด</h3>
<div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Date</th><th>Student</th><th>Course</th><th>Status</th><th>IP</th><th>Suspicious</th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><?=e($r['session_date'])?></td><td><?=e($r['full_name'])?></td><td><?=e($r['course_code'])?></td><td><?=e($r['status'])?></td><td><?=e($r['ip_address'])?></td><td><?=e($r['suspicious_reason']??'-')?></td></tr><?php endforeach;?>
</tbody></table></div>
</div></div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
