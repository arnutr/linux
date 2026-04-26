<?php
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/ActivityLog.php';

class BookController
{
    private Book $books;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->books = new Book();
        $this->logs = new ActivityLog();
    }

    public function index(): void
    {
        require_admin();
        View::render('books/index', ['books' => $this->books->all()]);
    }

    public function cover(): void
    {
        require_admin();
        $file = basename($_GET['file'] ?? '');
        if ($file === '') {
            http_response_code(404);
            exit('Cover not found');
        }

        $path = rtrim(config('app.uploads_covers_path'), '/') . '/' . $file;
        if (!is_file($path)) {
            http_response_code(404);
            exit('Cover not found');
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    private function uploadFile(string $field, string $dir, array $allowed): ?string
    {
        if (empty($_FILES[$field]['name'])) {
            return null;
        }
        $file = $_FILES[$field];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $name = uniqid($field . '_', true) . '.' . $ext;
        $target = rtrim($dir, '/') . '/' . $name;
        move_uploaded_file($file['tmp_name'], $target);
        return $name;
    }

    public function store(): void
    {
        require_admin();
        if (!csrf_validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirect('index.php?route=books');
        }

        $cover = $this->uploadFile('cover_image', config('app.uploads_covers_path'), ['jpg', 'jpeg', 'png', 'webp']);
        $file = $this->uploadFile('file_pdf', config('app.uploads_books_path'), ['pdf']);

        if (!$file) {
            flash('error', 'PDF file is required.');
            redirect('index.php?route=books');
        }

        $this->books->create([
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'cover_image' => $cover,
            'file_path' => $file,
            'price' => (float)($_POST['price'] ?? 0),
        ]);

        $this->logs->log(user()['id'], 'book_create', $_POST['title'] ?? '');
        flash('success', 'Book added successfully.');
        redirect('index.php?route=books');
    }

    public function update(): void
    {
        require_admin();
        if (!csrf_validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirect('index.php?route=books');
        }

        $id = (int)($_POST['id'] ?? 0);
        $existing = $this->books->find($id);
        if (!$existing) {
            flash('error', 'Book not found.');
            redirect('index.php?route=books');
        }

        $cover = $this->uploadFile('cover_image', config('app.uploads_covers_path'), ['jpg', 'jpeg', 'png', 'webp']) ?: $existing['cover_image'];
        $file = $this->uploadFile('file_pdf', config('app.uploads_books_path'), ['pdf']) ?: $existing['file_path'];

        $this->books->updateById($id, [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'cover_image' => $cover,
            'file_path' => $file,
            'price' => (float)($_POST['price'] ?? 0),
        ]);
        $this->logs->log(user()['id'], 'book_update', 'Book ID ' . $id);
        flash('success', 'Book updated.');
        redirect('index.php?route=books');
    }

    public function delete(): void
    {
        require_admin();
        if (!csrf_validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirect('index.php?route=books');
        }

        $id = (int)($_POST['id'] ?? 0);
        $this->books->deleteById($id);
        $this->logs->log(user()['id'], 'book_delete', 'Book ID ' . $id);
        flash('success', 'Book deleted.');
        redirect('index.php?route=books');
    }
}
