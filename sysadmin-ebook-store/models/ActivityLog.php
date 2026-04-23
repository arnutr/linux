<?php
require_once __DIR__ . '/../app/Model.php';

class ActivityLog extends Model
{
    public function log(?int $userId, string $action, string $context = ''): void
    {
        $stmt = $this->db->prepare('INSERT INTO activity_logs (user_id, action, context) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $action, $context]);
    }

    public function recent(int $limit = 10): array
    {
        $stmt = $this->db->prepare('SELECT al.*, u.name FROM activity_logs al LEFT JOIN users u ON u.id = al.user_id ORDER BY al.id DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
