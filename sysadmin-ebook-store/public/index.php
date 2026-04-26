<?php
session_start();

require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/View.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/MemberController.php';
require_once __DIR__ . '/../controllers/BookController.php';
require_once __DIR__ . '/../controllers/CodeController.php';
require_once __DIR__ . '/../controllers/RedemptionController.php';

$route = $_GET['route'] ?? 'dashboard';
$method = $_SERVER['REQUEST_METHOD'];

$auth = new AuthController();
$dashboard = new DashboardController();
$member = new MemberController();
$book = new BookController();
$code = new CodeController();
$redeem = new RedemptionController();

$routes = [
    'login' => fn() => $method === 'POST' ? $auth->login() : $auth->showLogin(),
    'logout' => fn() => $auth->logout(),

    'dashboard' => fn() => $dashboard->index(),

    'members' => fn() => $member->index(),
    'members/store' => fn() => $member->store(),
    'members/update' => fn() => $member->update(),
    'members/delete' => fn() => $member->delete(),

    'books' => fn() => $book->index(),
    'books/cover' => fn() => $book->cover(),
    'books/store' => fn() => $book->store(),
    'books/update' => fn() => $book->update(),
    'books/delete' => fn() => $book->delete(),

    'codes' => fn() => $code->index(),
    'codes/generate' => fn() => $code->generate(),
    'codes/export' => fn() => $code->exportCsv(),

    'redeem' => fn() => $method === 'POST' ? $redeem->redeem() : $redeem->index(),
    'download' => fn() => $redeem->download(),
];

if (!isset($routes[$route])) {
    http_response_code(404);
    exit('Route not found');
}

$routes[$route]();
