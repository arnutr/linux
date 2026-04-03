<?php
// Global application configuration

define('APP_NAME', 'Classroom Attendance System');
define('BASE_URL', 'http://localhost/attendance-app');
define('UPLOAD_DIR', __DIR__ . '/../uploads/checkins/');
define('MAX_UPLOAD_SIZE', 4 * 1024 * 1024); // 4MB
define('ALLOWED_UPLOAD_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_UPLOAD_MIME', ['image/jpeg', 'image/png', 'image/webp']);
define('SESSION_NAME', 'attendance_secure_session');
define('CSRF_TOKEN_NAME', 'csrf_token');

// Database for XAMPP
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'attendance_system';
const DB_USER = 'root';
const DB_PASS = '';
