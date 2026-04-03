<?php
require_once __DIR__ . '/../includes/auth.php';
require_auth(['instructor']);
$user=current_user();
$pdo=db();
$stats=[
 'my_courses'=>(int)$pdo->prepare('SELECT COUNT(*) FROM courses WHERE instructor_user_id = :uid')->execute(['uid'=>$user['id']])
];
$stmt=$pdo->prepare('SELECT COUNT(*) FROM courses WHERE instructor_user_id=:uid');$stmt->execute(['uid'=>$user['id']]);$courses=(int)$stmt->fetchColumn();
$stmt=$pdo->prepare('SELECT COUNT(*) FROM attendance_sessions s JOIN courses c ON c.id=s.course_id WHERE c.instructor_user_id=:uid');$stmt->execute(['uid'=>$user['id']]);$sessions=(int)$stmt->fetchColumn();
$stmt=$pdo->prepare('SELECT COUNT(*) FROM attendance_records ar JOIN attendance_sessions s ON s.id=ar.attendance_session_id JOIN courses c ON c.id=s.course_id WHERE c.instructor_user_id=:uid AND ar.suspicious_flag=1');$stmt->execute(['uid'=>$user['id']]);$flags=(int)$stmt->fetchColumn();
include __DIR__ . '/../templates/header.php';
?>
<div class="row"><?php $role='instructor'; include __DIR__ . '/../templates/sidebar.php'; ?><div class="col-md-9 col-lg-10">
<h3>แดชบอร์ดอาจารย์</h3>
<div class="row g-3"><div class="col-md-4"><div class="card"><div class="card-body">วิชาที่สอน <div class="fs-4"><?=$courses?></div></div></div></div><div class="col-md-4"><div class="card"><div class="card-body">รอบเช็กชื่อ <div class="fs-4"><?=$sessions?></div></div></div></div><div class="col-md-4"><div class="card"><div class="card-body">Flag ผิดปกติ <div class="fs-4"><?=$flags?></div></div></div></div></div>
</div></div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
