<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

function get_active_sessions_for_student(int $userId): array
{
    $sql = "SELECT s.*, c.course_code, c.course_name, e.section_name
            FROM enrollments e
            JOIN courses c ON c.id = e.course_id
            JOIN attendance_sessions s ON s.course_id = c.id
            WHERE e.student_user_id = :uid
            AND s.session_date = CURDATE()
            ORDER BY s.start_time ASC";
    $stmt = db()->prepare($sql);
    $stmt->execute(['uid' => $userId]);
    return $stmt->fetchAll();
}

function detect_suspicious(int $sessionId, int $studentUserId, string $ip, string $ua): array
{
    $reasons = [];

    $ipStmt = db()->prepare('SELECT COUNT(DISTINCT student_user_id) c FROM attendance_records WHERE attendance_session_id = :sid AND ip_address = :ip AND created_at >= DATE_SUB(NOW(), INTERVAL 20 MINUTE)');
    $ipStmt->execute(['sid' => $sessionId, 'ip' => $ip]);
    if ((int)$ipStmt->fetch()['c'] >= 3) {
        $reasons[] = 'Same IP used by multiple students in short period';
    }

    $uaStmt = db()->prepare('SELECT COUNT(DISTINCT student_user_id) c FROM attendance_records WHERE attendance_session_id = :sid AND user_agent = :ua');
    $uaStmt->execute(['sid' => $sessionId, 'ua' => $ua]);
    if ((int)$uaStmt->fetch()['c'] >= 3) {
        $reasons[] = 'Same device/browser used by many accounts';
    }

    return [
        'flag' => !empty($reasons),
        'reason' => implode('; ', $reasons),
    ];
}

function record_suspicious_log(int $recordId, int $sessionId, int $studentUserId, string $reason, string $severity = 'medium'): void
{
    $stmt = db()->prepare('INSERT INTO suspicious_logs(attendance_record_id, attendance_session_id, student_user_id, reason, severity) VALUES(:rid,:sid,:uid,:reason,:severity)');
    $stmt->execute([
        'rid' => $recordId,
        'sid' => $sessionId,
        'uid' => $studentUserId,
        'reason' => $reason,
        'severity' => $severity,
    ]);
}
