<?php
return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'sysadmin_ebook_store',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name' => 'SYSADMIN E-BOOK STORE',
        'base_url' => '/sysadmin-ebook-store/public',
        'session_timeout' => 1800,
        'uploads_books_path' => dirname(__DIR__) . '/uploads/books/',
        'uploads_covers_path' => dirname(__DIR__) . '/uploads/covers/',
    ],
];
