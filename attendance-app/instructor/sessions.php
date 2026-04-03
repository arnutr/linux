<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
require_auth(['instructor']);
$user=current_user();$pdo=db();$msg='';
if($_SERVER['REQUEST_METHOD']==='POST' && verify_csrf_token($_POST['csrf_token']??null)){
  $token=random_token(4);
  $stmt=$pdo->prepare('INSERT INTO attendance_sessions(course_id,session_date,start_time,end_time,late_after,session_token,geo_lat,geo_lng,geo_radius_m,created_by_user_id)
  VALUES(:course_id,:session_date,:start_time,:end_time,:late_after,:token,:lat,:lng,:radius,:uid)');
  $stmt->execute([
    'course_id'=>(int)$_POST['course_id'],'session_date'=>$_POST['session_date'],'start_time'=>$_POST['start_time'],'end_time'=>$_POST['end_time'],'late_after'=>$_POST['late_after'],'token'=>$token,
    'lat'=>$_POST['geo_lat']?:null,'lng'=>$_POST['geo_lng']?:null,'radius'=>$_POST['geo_radius_m']?:null,'uid'=>$user['id']
  ]);
  $msg='Created session token: '.$token;
}
$coursesStmt=$pdo->prepare('SELECT id,course_code,course_name,section_name FROM courses WHERE instructor_user_id=:uid');$coursesStmt->execute(['uid'=>$user['id']]);$courses=$coursesStmt->fetchAll();
$listStmt=$pdo->prepare('SELECT s.*,c.course_code,c.section_name FROM attendance_sessions s JOIN courses c ON c.id=s.course_id WHERE c.instructor_user_id=:uid ORDER BY s.session_date DESC');$listStmt->execute(['uid'=>$user['id']]);$rows=$listStmt->fetchAll();
include __DIR__ . '/../templates/header.php';
?>
<div class="row"><?php $role='instructor'; include __DIR__ . '/../templates/sidebar.php'; ?><div class="col-md-9 col-lg-10">
<h3>สร้างรอบเช็กชื่อ</h3>
<?php if($msg):?><div class="alert alert-success"><?=e($msg)?></div><?php endif;?>
<form method="post" class="card p-3 mb-3"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<div class="row g-2"><div class="col-md-4"><select class="form-select" name="course_id" required><?php foreach($courses as $c):?><option value="<?=$c['id']?>"><?=e($c['course_code'].' '.$c['section_name'])?></option><?php endforeach;?></select></div>
<div class="col-md-2"><input type="date" class="form-control" name="session_date" required></div><div class="col-md-2"><input type="time" class="form-control" name="start_time" required></div><div class="col-md-2"><input type="time" class="form-control" name="late_after" required></div><div class="col-md-2"><input type="time" class="form-control" name="end_time" required></div></div>
<div class="row g-2 mt-1"><div class="col-md-4"><input class="form-control" step="any" name="geo_lat" placeholder="Geo Lat (optional)"></div><div class="col-md-4"><input class="form-control" step="any" name="geo_lng" placeholder="Geo Lng (optional)"></div><div class="col-md-4"><input class="form-control" step="0.1" name="geo_radius_m" placeholder="Radius meter (optional)"></div></div>
<button class="btn btn-primary mt-2">Create session</button></form>
<table class="table table-striped"><thead><tr><th>Date</th><th>Course</th><th>Window</th><th>Token</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=e($r['session_date'])?></td><td><?=e($r['course_code'].' '.$r['section_name'])?></td><td><?=e($r['start_time'].'-'.$r['end_time'])?></td><td><code><?=e($r['session_token'])?></code></td></tr><?php endforeach;?></tbody></table>
</div></div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
