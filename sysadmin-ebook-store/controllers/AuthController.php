<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

class AuthController
{
    private User $users;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->users = new User();
        $this->logs = new ActivityLog();
    }

    public function showLogin(): void
    {
        if (is_logged_in()) {
            redirect('index.php?route=dashboard');
        }
        View::render('auth/login');
    }

    public function login(): void
    {
        if (!csrf_validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirect('index.php?route=login');
        }

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            flash('error', 'Invalid credentials.');
            redirect('index.php?route=login');
        }

        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            flash('error', 'Email or password incorrect.');
            redirect('index.php?route=login');
        }

        unset($user['password']);
        $_SESSION['user'] = $user;
        $_SESSION['last_activity'] = time();
        $this->logs->log($user['id'], 'login', 'Admin login successful');
        redirect('index.php?route=dashboard');
    }

    public function logout(): void
    {
        $uid = user()['id'] ?? null;
        $this->logs->log($uid, 'logout', 'User logged out');
        session_destroy();
        redirect('index.php?route=login');
    }
}
