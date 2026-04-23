<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/DownloadCode.php';
require_once __DIR__ . '/../models/ActivityLog.php';

class DashboardController
{
    public function index(): void
    {
        require_admin();
        $users = new User();
        $books = new Book();
        $codes = new DownloadCode();
        $logs = new ActivityLog();

        View::render('dashboard/index', [
            'totalUsers' => $users->countAll(),
            'totalBooks' => $books->countAll(),
            'codeStats' => $codes->stats(),
            'recentLogs' => $logs->recent(),
        ]);
    }
}
