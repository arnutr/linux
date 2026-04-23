<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

class MemberController
{
    private User $users;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->users = new User();
        $this->logs = new ActivityLog();
    }

    public function index(): void
    {
        require_admin();
        View::render('members/index', ['members' => $this->users->all()]);
    }

    public function store(): void
    {
        require_admin();
        if (!csrf_validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirect('index.php?route=members');
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL),
            'password' => $_POST['password'] ?? '',
            'role' => in_array($_POST['role'] ?? '', ['admin', 'customer'], true) ? $_POST['role'] : 'customer',
        ];

        if (!$data['name'] || !$data['email'] || strlen($data['password']) < 6) {
            flash('error', 'Please fill valid member data.');
            redirect('index.php?route=members');
        }

        $this->users->create($data);
        $this->logs->log(user()['id'], 'member_create', $data['email']);
        flash('success', 'Member created successfully.');
        redirect('index.php?route=members');
    }

    public function update(): void
    {
        require_admin();
        if (!csrf_validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirect('index.php?route=members');
        }
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL),
            'password' => $_POST['password'] ?? '',
            'role' => in_array($_POST['role'] ?? '', ['admin', 'customer'], true) ? $_POST['role'] : 'customer',
        ];
        $this->users->updateById($id, $data);
        $this->logs->log(user()['id'], 'member_update', 'Member ID ' . $id);
        flash('success', 'Member updated.');
        redirect('index.php?route=members');
    }

    public function delete(): void
    {
        require_admin();
        if (!csrf_validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            redirect('index.php?route=members');
        }
        $id = (int)($_POST['id'] ?? 0);
        if ($id === (int)user()['id']) {
            flash('error', 'You cannot delete your own account.');
            redirect('index.php?route=members');
        }
        $this->users->deleteById($id);
        $this->logs->log(user()['id'], 'member_delete', 'Member ID ' . $id);
        flash('success', 'Member deleted.');
        redirect('index.php?route=members');
    }
}
