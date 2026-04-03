CREATE DATABASE IF NOT EXISTS attendance_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE attendance_system;

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30) NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role_id INT NOT NULL,
  is_active_user BOOLEAN DEFAULT TRUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE lecturers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  lecturer_code VARCHAR(30) NOT NULL UNIQUE,
  full_name VARCHAR(120) NOT NULL,
  department VARCHAR(120) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  student_code VARCHAR(30) NOT NULL UNIQUE,
  full_name VARCHAR(120) NOT NULL,
  department VARCHAR(120) NOT NULL,
  year INT NOT NULL,
  profile_image VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL UNIQUE,
  name VARCHAR(255) NOT NULL,
  lecturer_id INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (lecturer_id) REFERENCES lecturers(id)
);

CREATE TABLE enrollments (
  student_id INT NOT NULL,
  course_id INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(student_id, course_id),
  FOREIGN KEY (student_id) REFERENCES students(id),
  FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE class_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  session_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  status VARCHAR(20) DEFAULT 'closed',
  attendance_code VARCHAR(64) NOT NULL,
  qr_token VARCHAR(128) NOT NULL,
  require_gps BOOLEAN DEFAULT FALSE,
  require_face_photo BOOLEAN DEFAULT TRUE,
  allowed_ip_prefix VARCHAR(64) NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE attendance_records (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  course_id INT NOT NULL,
  session_id INT NOT NULL,
  checkin_time DATETIME NOT NULL,
  photo_path VARCHAR(255) NOT NULL,
  photo_hash VARCHAR(64) NOT NULL,
  ip_address VARCHAR(45) NOT NULL,
  user_agent VARCHAR(255) NOT NULL,
  latitude VARCHAR(30),
  longitude VARCHAR(30),
  is_late BOOLEAN DEFAULT FALSE,
  status VARCHAR(20) DEFAULT 'pending',
  suspicious BOOLEAN DEFAULT FALSE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_student_session(student_id, session_id),
  FOREIGN KEY (student_id) REFERENCES students(id),
  FOREIGN KEY (course_id) REFERENCES courses(id),
  FOREIGN KEY (session_id) REFERENCES class_sessions(id)
);

CREATE TABLE suspicious_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  attendance_id INT NOT NULL,
  reason VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (attendance_id) REFERENCES attendance_records(id)
);

CREATE TABLE system_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  actor_user_id INT NULL,
  action VARCHAR(255) NOT NULL,
  level VARCHAR(20) DEFAULT 'info',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (actor_user_id) REFERENCES users(id)
);

-- sample roles
INSERT INTO roles(name) VALUES ('admin'), ('lecturer'), ('student');

-- sample users (password: Password123!)
INSERT INTO users(email, password_hash, role_id) VALUES
('admin@example.com', '$2b$12$8kCbgl2nfp9ccWzv4Zy25uQgrfVuwK59fTliijJfU1xW8SuX4B2nC', 1),
('lecturer1@example.com', '$2b$12$8kCbgl2nfp9ccWzv4Zy25uQgrfVuwK59fTliijJfU1xW8SuX4B2nC', 2),
('student1@example.com', '$2b$12$8kCbgl2nfp9ccWzv4Zy25uQgrfVuwK59fTliijJfU1xW8SuX4B2nC', 3);

INSERT INTO lecturers(user_id, lecturer_code, full_name, department)
VALUES (2, 'LEC001', 'Dr. Somchai Teacher', 'Computer Science');

INSERT INTO students(user_id, student_code, full_name, department, year, profile_image)
VALUES (3, 'STU001', 'Nattapong Student', 'Computer Science', 2, 'static/uploads/profiles/default.png');

INSERT INTO courses(code, name, lecturer_id)
VALUES ('CS101', 'Introduction to Programming', 1);

INSERT INTO enrollments(student_id, course_id) VALUES (1, 1);

INSERT INTO class_sessions(course_id, session_date, start_time, end_time, status, attendance_code, qr_token)
VALUES (1, CURDATE(), '09:00:00', '10:30:00', 'open', 'ABC123', 'sample_qr_token_1');
