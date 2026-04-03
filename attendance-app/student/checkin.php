<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../modules/upload.php';
require_once __DIR__ . '/../modules/attendance.php';

require_auth(['student']);
$user = current_user();
$pdo = db();
$sessionId = (int)($_GET['session_id'] ?? 0);
$msg = null;

$stmt = $pdo->prepare('SELECT s.*, c.course_name FROM attendance_sessions s JOIN courses c ON c.id = s.course_id WHERE s.id = :id');
$stmt->execute(['id' => $sessionId]);
$session = $stmt->fetch();
if (!$session) {
    exit('Session not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $msg = ['type' => 'danger', 'text' => 'Invalid CSRF token'];
    } else {
        $dup = $pdo->prepare('SELECT id FROM attendance_records WHERE attendance_session_id = :sid AND student_user_id = :uid LIMIT 1');
        $dup->execute(['sid' => $sessionId, 'uid' => $user['id']]);
        if ($dup->fetch()) {
            $msg = ['type' => 'warning', 'text' => 'คุณเช็กชื่อรอบนี้แล้ว'];
        } else {
            $qrToken = post('qr_token', '');
            $now = date('H:i:s');
            if ($qrToken !== $session['session_token']) {
                $msg = ['type' => 'danger', 'text' => 'Session code ไม่ถูกต้อง'];
            } elseif ($now < $session['start_time'] || $now > $session['end_time']) {
                $msg = ['type' => 'danger', 'text' => 'รอบเช็กชื่อไม่เปิด'];
            } else {
                $upload = handle_checkin_upload($_FILES['checkin_photo'] ?? []);
                if (!$upload['ok']) {
                    $msg = ['type' => 'danger', 'text' => $upload['error']];
                } else {
                    $lat = is_numeric($_POST['latitude'] ?? null) ? (float)$_POST['latitude'] : null;
                    $lng = is_numeric($_POST['longitude'] ?? null) ? (float)$_POST['longitude'] : null;
                    $distance = null;
                    $status = $now > $session['late_after'] ? 'late' : 'present';
                    $susReason = [];

                    if ($lat !== null && $lng !== null && $session['geo_lat'] !== null && $session['geo_lng'] !== null && $session['geo_radius_m'] !== null) {
                        $distanceKm = calculate_distance_km($lat, $lng, (float)$session['geo_lat'], (float)$session['geo_lng']);
                        $distance = round($distanceKm * 1000, 2);
                        if ($distance > (float)$session['geo_radius_m']) {
                            $susReason[] = 'Location outside geofence';
                        }
                    }

                    $sus = detect_suspicious($sessionId, $user['id'], get_client_ip(), get_user_agent());
                    if ($sus['flag']) {
                        $susReason[] = $sus['reason'];
                    }

                    $reasonText = implode('; ', array_filter($susReason));
                    $ins = $pdo->prepare('INSERT INTO attendance_records(attendance_session_id, student_user_id, checkin_time, status, checkin_photo, ip_address, user_agent, latitude, longitude, distance_from_class, qr_token_used, suspicious_flag, suspicious_reason)
                        VALUES(:sid,:uid,NOW(),:status,:photo,:ip,:ua,:lat,:lng,:distance,:token,:flag,:reason)');
                    $ins->execute([
                        'sid' => $sessionId,
                        'uid' => $user['id'],
                        'status' => $status,
                        'photo' => $upload['filename'],
                        'ip' => get_client_ip(),
                        'ua' => get_user_agent(),
                        'lat' => $lat,
                        'lng' => $lng,
                        'distance' => $distance,
                        'token' => $qrToken,
                        'flag' => $reasonText !== '' ? 1 : 0,
                        'reason' => $reasonText ?: null,
                    ]);
                    $recordId = (int)$pdo->lastInsertId();
                    if ($reasonText !== '') {
                        record_suspicious_log($recordId, $sessionId, $user['id'], $reasonText, 'medium');
                    }
                    $msg = ['type' => 'success', 'text' => 'เช็กชื่อสำเร็จ'];
                }
            }
        }
    }
}

include __DIR__ . '/../templates/header.php';
?>
<div class="row">
  <?php $role = 'student'; include __DIR__ . '/../templates/sidebar.php'; ?>
  <div class="col-md-9 col-lg-10">
    <h4>เช็กชื่อ: <?= e($session['course_name']) ?></h4>
    <?php if ($msg): ?><div class="alert alert-<?= e($msg['type']) ?>"><?= e($msg['text']) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="card p-3 shadow-sm" id="checkinForm">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" id="latitude" name="latitude">
      <input type="hidden" id="longitude" name="longitude">
      <div class="mb-3">
        <label class="form-label">รหัส Session / QR token</label>
        <input class="form-control" name="qr_token" required>
      </div>
      <div class="mb-3">
        <label class="form-label">รูปถ่ายเช็กชื่อ (สด/อัปโหลด)</label>
        <input class="form-control" type="file" accept="image/*" capture="user" id="checkin_photo" name="checkin_photo" required>
        <img id="preview" class="img-thumbnail mt-2 d-none" style="max-width:220px" alt="preview">
      </div>
      <button class="btn btn-success">ยืนยันเช็กชื่อ</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
