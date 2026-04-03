# Classroom Attendance Check-in (PHP 8 + MySQL)

Production-ready starter project for secure classroom attendance on XAMPP.

## 1) Project folder structure

```text
attendance-app/
├── index.php
├── README.md
├── config/
├── includes/
├── modules/
├── templates/
├── public/
├── admin/
├── instructor/
├── student/
├── assets/
│   ├── css/
│   └── js/
├── database/
│   ├── schema.sql
│   └── seed.sql
└── uploads/
    └── checkins/
```

## 2) Setup (XAMPP localhost)
1. Copy `attendance-app` into `C:/xampp/htdocs/`.
2. Start **Apache** and **MySQL** from XAMPP Control Panel.
3. Create DB by importing `database/schema.sql` then `database/seed.sql` in phpMyAdmin.
4. Open `config/config.php` and adjust DB credentials if needed.
5. Visit `http://localhost/attendance-app/public/login.php`.

### Demo accounts
- Admin: `admin@demo.local` / `Password@123`
- Instructor: `somchai@demo.local` / `Password@123`
- Student: `napat@demo.local` / `Password@123`

> Note: all seeded accounts use a pre-generated hash compatible with `Password@123`.

## 3) Security features implemented
- Session hardening with `HttpOnly` and `SameSite=Lax` cookies
- CSRF token for all state-changing forms
- Password hashing/verification (`password_hash` + `password_verify`)
- Role-based route protection (`admin`, `instructor`, `student`)
- PDO prepared statements in all DB writes/filters
- Output escaping helper to reduce XSS
- Strict upload checks (size, extension, MIME)
- Upload directory execution blocking using `.htaccess`

## 4) Anti-proxy attendance controls
Attendance is accepted only when:
1. Student is authenticated.
2. Session code (`session_token`) is correct.
3. Session window is currently open.
4. Photo is uploaded and validated.
5. Student has not checked in before for the same session.

The system records and evaluates:
- Timestamp
- IP address
- Browser user-agent
- Geolocation (optional)
- Distance from classroom geofence (if configured)

Suspicious cases are flagged and written to `suspicious_logs`, including:
- Same IP used by many accounts in short period
- Same device/user-agent reused by many students
- Location outside configured geofence

## 5) Features by role
- **Admin**: dashboard cards/chart, user management, password reset, course list, all attendance logs.
- **Instructor**: session creation with token and optional geofence, filtered reports, CSV export, suspicious flags and photo review.
- **Student**: active sessions, token check-in page, live capture/upload photo, attendance history with badges.

## 6) Future improvements
- Face recognition API integration (e.g., AWS Rekognition / Azure Face / OpenCV backend)
- Liveness detection challenge (blink/turn-head)
- Time-based rolling QR tokens (TOTP style)
- Device fingerprinting with stronger risk scoring
- Notification/webhook for high-risk suspicious spikes
- Per-course policy profiles (strict/normal)

## 7) Important notes
- This is a strong anti-cheating baseline, **not perfect identity proofing**.
- Always deploy over HTTPS in production.
- Consider adding audit trails + immutable logs for compliance.
