<?php
require_once __DIR__ . '/../includes/auth.php';
require_auth(['student']);
$user = current_user();

$stmt = db()->prepare('SELECT ar.*, c.course_code, c.course_name, s.session_date
FROM attendance_records ar
JOIN attendance_sessions s ON s.id = ar.attendance_session_id
JOIN courses c ON c.id = s.course_id
WHERE ar.student_user_id = :uid
ORDER BY ar.created_at DESC');
$stmt->execute(['uid' => $user['id']]);
$rows = $stmt->fetchAll();

include __DIR__ . '/../templates/header.php';
?>
<div class="row">
  <?php $role = 'student'; include __DIR__ . '/../templates/sidebar.php'; ?>
  <div class="col-md-9 col-lg-10">
    <h3>ประวัติการเข้าเรียน</h3>
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead><tr><th>วันที่</th><th>วิชา</th><th>เวลา</th><th>สถานะ</th><th>สงสัย</th></tr></thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= e($r['session_date']) ?></td>
              <td><?= e($r['course_code'] . ' ' . $r['course_name']) ?></td>
              <td><?= e($r['checkin_time']) ?></td>
              <td><span class="badge text-bg-<?= $r['status']==='late'?'warning':'success' ?>"><?= e($r['status']) ?></span></td>
              <td><?= $r['suspicious_flag'] ? '<span class="badge text-bg-danger">flag</span>' : '-' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
