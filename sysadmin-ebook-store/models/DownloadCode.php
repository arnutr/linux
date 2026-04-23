<?php
require_once __DIR__ . '/../app/Model.php';

class DownloadCode extends Model
{
    public function all(array $filters = []): array
    {
        $sql = 'SELECT dc.*, b.title FROM download_codes dc JOIN books b ON b.id = dc.book_id WHERE 1=1';
        $params = [];

        if (!empty($filters['book_id'])) {
            $sql .= ' AND dc.book_id = ?';
            $params[] = (int)$filters['book_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= $filters['status'] === 'used' ? ' AND dc.used_count >= dc.usage_limit' : ' AND dc.used_count < dc.usage_limit';
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND dc.code LIKE ?';
            $params[] = '%' . $filters['search'] . '%';
        }

        $sql .= ' ORDER BY dc.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function createMany(int $bookId, int $qty, int $usageLimit, ?string $expiryDate): void
    {
        $stmt = $this->db->prepare('INSERT INTO download_codes (book_id, code, usage_limit, expires_at) VALUES (?, ?, ?, ?)');
        for ($i = 0; $i < $qty; $i++) {
            $stmt->execute([$bookId, $this->generateCode(), $usageLimit, $expiryDate ?: null]);
        }
    }

    private function generateCode(): string
    {
        return sprintf('SYS-BOOK-%s-%s', strtoupper(bin2hex(random_bytes(2))), strtoupper(bin2hex(random_bytes(2))));
    }

    public function findValidCode(string $code): ?array
    {
        $stmt = $this->db->prepare('SELECT dc.*, b.title, b.file_path FROM download_codes dc JOIN books b ON b.id = dc.book_id WHERE dc.code = ? LIMIT 1');
        $stmt->execute([$code]);
        return $stmt->fetch() ?: null;
    }

    public function markUsed(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE download_codes SET used_count = used_count + 1, used_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function stats(): array
    {
        return [
            'total' => (int)$this->db->query('SELECT COUNT(*) FROM download_codes')->fetchColumn(),
            'used' => (int)$this->db->query('SELECT COUNT(*) FROM download_codes WHERE used_count >= usage_limit')->fetchColumn(),
            'unused' => (int)$this->db->query('SELECT COUNT(*) FROM download_codes WHERE used_count < usage_limit')->fetchColumn(),
        ];
    }
}
