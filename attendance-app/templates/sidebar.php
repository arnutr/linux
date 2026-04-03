<?php
/** @var string $role */
?>
<div class="col-md-3 col-lg-2 mb-3">
  <div class="list-group shadow-sm">
    <?php if ($role === 'admin'): ?>
      <a href="/attendance-app/admin/dashboard.php" class="list-group-item list-group-item-action">แดชบอร์ดผู้ดูแล</a>
      <a href="/attendance-app/admin/users.php" class="list-group-item list-group-item-action">จัดการผู้ใช้</a>
      <a href="/attendance-app/admin/courses.php" class="list-group-item list-group-item-action">จัดการรายวิชา</a>
      <a href="/attendance-app/admin/attendance.php" class="list-group-item list-group-item-action">บันทึกการเข้าเรียนทั้งหมด</a>
    <?php elseif ($role === 'instructor'): ?>
      <a href="/attendance-app/instructor/dashboard.php" class="list-group-item list-group-item-action">แดชบอร์ดอาจารย์</a>
      <a href="/attendance-app/instructor/sessions.php" class="list-group-item list-group-item-action">จัดการรอบเช็กชื่อ</a>
      <a href="/attendance-app/instructor/reports.php" class="list-group-item list-group-item-action">รายงานการเข้าเรียน</a>
    <?php else: ?>
      <a href="/attendance-app/student/dashboard.php" class="list-group-item list-group-item-action">แดชบอร์ดนักศึกษา</a>
      <a href="/attendance-app/student/history.php" class="list-group-item list-group-item-action">ประวัติการเข้าเรียน</a>
    <?php endif; ?>
  </div>
</div>
