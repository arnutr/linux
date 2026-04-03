<?php
require_once __DIR__ . '/../includes/auth.php';
require_auth(['instructor']);
$user=current_user();$pdo=db();
$courseId=(int)($_GET['course_id']??0);$date=$_GET['date']??'';
$where=' WHERE c.instructor_user_id=:uid ';$params=['uid'=>$user['id']];
if($courseId){$where.=' AND c.id=:cid ';$params['cid']=$courseId;}
if($date){$where.=' AND s.session_date=:d ';$params['d']=$date;}
$sql='SELECT ar.*,u.full_name,c.course_code,s.session_date FROM attendance_records ar JOIN users u ON u.id=ar.student_user_id JOIN attendance_sessions s ON s.id=ar.attendance_session_id JOIN courses c ON c.id=s.course_id '.$where.' ORDER BY s.session_date DESC';
$stmt=$pdo->prepare($sql);$stmt->execute($params);$rows=$stmt->fetchAll();
$coursesStmt=$pdo->prepare('SELECT id,course_code,section_name FROM courses WHERE instructor_user_id=:uid');$coursesStmt->execute(['uid'=>$user['id']]);$courses=$coursesStmt->fetchAll();
if(isset($_GET['export']) && $_GET['export']==='csv'){
  header('Content-Type: text/csv');header('Content-Disposition: attachment; filename="attendance_report.csv"');
  $out=fopen('php://output','w');fputcsv($out,['date','course','student','status','ip','suspicious']);
  foreach($rows as $r){fputcsv($out,[$r['session_date'],$r['course_code'],$r['full_name'],$r['status'],$r['ip_address'],$r['suspicious_reason']]);}
  fclose($out);exit;
}
include __DIR__ . '/../templates/header.php';
?>
<div class="row"><?php $role='instructor'; include __DIR__ . '/../templates/sidebar.php'; ?><div class="col-md-9 col-lg-10">
<h3>รายงานการเข้าเรียน</h3>
<form class="row g-2 mb-3"><div class="col-md-4"><select class="form-select" name="course_id"><option value="">ทุกวิชา</option><?php foreach($courses as $c):?><option value="<?=$c['id']?>" <?=$courseId===$c['id']?'selected':''?>><?=e($c['course_code'].' '.$c['section_name'])?></option><?php endforeach;?></select></div>
<div class="col-md-3"><input type="date" class="form-control" name="date" value="<?=e($date)?>"></div><div class="col-md-5"><button class="btn btn-primary">Filter</button> <a class="btn btn-outline-success" href="?<?=http_build_query(array_merge($_GET,['export'=>'csv']))?>">Export CSV</a></div></form>
<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Date</th><th>Course</th><th>Student</th><th>Status</th><th>Photo</th><th>Flag</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=e($r['session_date'])?></td><td><?=e($r['course_code'])?></td><td><?=e($r['full_name'])?></td><td><span class="badge text-bg-<?= $r['status']==='late'?'warning':'success' ?>"><?=e($r['status'])?></span></td><td><?php if($r['checkin_photo']):?><a href="/attendance-app/uploads/checkins/<?=e($r['checkin_photo'])?>" target="_blank">View</a><?php endif;?></td><td><?= $r['suspicious_flag']?'<span class="badge text-bg-danger">'.e($r['suspicious_reason']).'</span>':'-'?></td></tr><?php endforeach;?></tbody></table></div>
</div></div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
