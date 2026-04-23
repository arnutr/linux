CREATE DATABASE IF NOT EXISTS sysadmin_ebook_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sysadmin_ebook_store;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE books (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  cover_image VARCHAR(255) DEFAULT NULL,
  file_path VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE download_codes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  book_id INT UNSIGNED NOT NULL,
  code VARCHAR(64) NOT NULL UNIQUE,
  usage_limit INT UNSIGNED NOT NULL DEFAULT 1,
  used_count INT UNSIGNED NOT NULL DEFAULT 0,
  expires_at DATE DEFAULT NULL,
  used_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_codes_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

CREATE TABLE activity_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  action VARCHAR(100) NOT NULL,
  context VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO users (name, email, password, role) VALUES
('System Admin', 'admin@store.local', '$2y$12$Th2AtvPd4Yj117jyzYwSaeuWSxnKxy3uWNhg7A8j07b6/2A2KYMEC', 'admin'),
('John Customer', 'customer@example.com', '$2y$12$Th2AtvPd4Yj117jyzYwSaeuWSxnKxy3uWNhg7A8j07b6/2A2KYMEC', 'customer');

INSERT INTO books (title, description, file_path, price) VALUES
('Linux Hardening Handbook', 'Practical hardening and auditing steps.', 'linux-hardening.pdf', 19.99),
('Bash Automation Mastery', 'Write robust shell automation scripts.', 'bash-automation.pdf', 14.50);

INSERT INTO download_codes (book_id, code, usage_limit, used_count, expires_at) VALUES
(1, 'SYS-BOOK-A1B2-C3D4', 1, 0, DATE_ADD(CURDATE(), INTERVAL 30 DAY)),
(2, 'SYS-BOOK-E5F6-G7H8', 1, 0, NULL);
