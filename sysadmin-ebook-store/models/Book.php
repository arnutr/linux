<?php
require_once __DIR__ . '/../app/Model.php';

class Book extends Model
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM books ORDER BY id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM books WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare('INSERT INTO books (title, description, cover_image, file_path, price) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$data['title'], $data['description'], $data['cover_image'], $data['file_path'], $data['price']]);
    }

    public function updateById(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE books SET title = ?, description = ?, cover_image = ?, file_path = ?, price = ? WHERE id = ?');
        return $stmt->execute([$data['title'], $data['description'], $data['cover_image'], $data['file_path'], $data['price'], $id]);
    }

    public function deleteById(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM books WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function countAll(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM books')->fetchColumn();
    }
}
