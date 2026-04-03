<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../modules/attendance.php';
require_auth(['student']);

$user = current_user();
$sessions = get_active_sessions_for_student($user['id']);
include __DIR__ . '/../templates/header.php';
?>
<div class="row">
  <?php $role = 'student'; include __DIR__ . '/../templates/sidebar.php'; ?>
  <div class="col-md-9 col-lg-10">
    <h3>เช็กชื่อเข้าเรียนวันนี้</h3>
    <div class="row g-3 mt-1">
      <?php foreach ($sessions as $s): ?>
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-body">
              <h5><?= e($s['course_code'] . ' ' . $s['course_name']) ?></h5>
              <div class="small text-muted">กลุ่ม <?= e($s['section_name']) ?></div>
              <div><?= e($s['start_time']) ?> - <?= e($s['end_time']) ?></div>
              <a class="btn btn-primary mt-2" href="/attendance-app/student/checkin.php?session_id=<?= (int)$s['id'] ?>">เช็กชื่อ</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (!$sessions): ?><div class="alert alert-secondary">ไม่มีรอบเช็กชื่อที่เปิดอยู่</div><?php endif; ?>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
