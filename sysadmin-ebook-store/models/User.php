<?php
require_once __DIR__ . '/../app/Model.php';

class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, name, email, role, created_at FROM users ORDER BY id DESC')->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$data['name'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT), $data['role']]);
    }

    public function updateById(int $id, array $data): bool
    {
        $fields = 'name = ?, email = ?, role = ?';
        $params = [$data['name'], $data['email'], $data['role']];

        if (!empty($data['password'])) {
            $fields .= ', password = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $params[] = $id;
        $stmt = $this->db->prepare("UPDATE users SET {$fields} WHERE id = ?");
        return $stmt->execute($params);
    }

    public function deleteById(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function countAll(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}
