<?php
require_once __DIR__ . '/../models/DownloadCode.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/ActivityLog.php';

class CodeController
{
    private DownloadCode $codes;
    private Book $books;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->codes = new DownloadCode();
        $this->books = new Book();
        $this->logs = new ActivityLog();
    }

    public function index(): void
    {
        require_admin();
        $filters = [
            'book_id' => $_GET['book_id'] ?? null,
            'status' => $_GET['status'] ?? null,
            'search' => trim($_GET['search'] ?? ''),
        ];
        View::render('codes/index', [
            'codes' => $this->codes->all($filters),
            'books' => $this->books->all(),
            'filters' => $filters,
        ]);
    }

    public function generate(): void
    {
        require_admin();
        if (!csrf_validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirect('index.php?route=codes');
        }

        $bookId = (int)($_POST['book_id'] ?? 0);
        $qty = max(1, min(500, (int)($_POST['quantity'] ?? 1)));
        $usageLimit = max(1, (int)($_POST['usage_limit'] ?? 1));
        $expiry = $_POST['expires_at'] ?: null;
        $this->codes->createMany($bookId, $qty, $usageLimit, $expiry);
        $this->logs->log(user()['id'], 'code_generate', "Book {$bookId}, qty {$qty}");
        flash('success', "Generated {$qty} code(s).");
        redirect('index.php?route=codes');
    }

    public function exportCsv(): void
    {
        require_admin();
        $codes = $this->codes->all();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="download_codes.csv"');

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['โค้ด', 'หนังสือ', 'จำนวนใช้งานสูงสุด', 'ใช้งานแล้ว', 'หมดอายุ']);
        foreach ($codes as $code) {
            fputcsv($out, [$code['code'], $code['title'], $code['usage_limit'], $code['used_count'], $code['expires_at']]);
        }
        fclose($out);
        exit;
    }
}
