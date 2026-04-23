<?php
require_once __DIR__ . '/../models/DownloadCode.php';
require_once __DIR__ . '/../models/ActivityLog.php';

class RedemptionController
{
    private DownloadCode $codes;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->codes = new DownloadCode();
        $this->logs = new ActivityLog();
    }

    public function index(): void
    {
        View::render('codes/redeem');
    }

    public function redeem(): void
    {
        if (!csrf_validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirect('index.php?route=redeem');
        }

        $codeInput = trim($_POST['code'] ?? '');
        $code = $this->codes->findValidCode($codeInput);
        if (!$code) {
            flash('error', 'Code not found.');
            redirect('index.php?route=redeem');
        }

        $expired = !empty($code['expires_at']) && strtotime($code['expires_at']) < time();
        $usedUp = (int)$code['used_count'] >= (int)$code['usage_limit'];

        if ($expired || $usedUp) {
            flash('error', $expired ? 'Code has expired.' : 'Code usage limit reached.');
            redirect('index.php?route=redeem');
        }

        $_SESSION['redeem_code_id'] = $code['id'];
        $_SESSION['redeem_token'] = bin2hex(random_bytes(24));
        View::render('codes/redeem_success', ['code' => $code, 'token' => $_SESSION['redeem_token']]);
    }

    public function download(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $token = $_GET['token'] ?? '';
        if ($id !== (int)($_SESSION['redeem_code_id'] ?? 0) || !hash_equals($_SESSION['redeem_token'] ?? '', $token)) {
            http_response_code(403);
            exit('Invalid download token');
        }

        $code = $this->codes->findValidCode($_GET['code'] ?? '');
        if (!$code || (int)$code['id'] !== $id) {
            http_response_code(404);
            exit('Code not found');
        }

        $file = config('app.uploads_books_path') . $code['file_path'];
        if (!is_file($file)) {
            http_response_code(404);
            exit('File not found');
        }

        $this->codes->markUsed($id);
        $this->logs->log(null, 'code_redeem', 'Code ' . $code['code']);

        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($file));
        header('Content-Disposition: attachment; filename="' . basename($code['file_path']) . '"');
        readfile($file);

        unset($_SESSION['redeem_code_id'], $_SESSION['redeem_token']);
        exit;
    }
}
