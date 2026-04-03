# Course Attendance Check-in System (Flask + MySQL)

Production-style web application for attendance check-in with anti-proxy mechanisms.

## 1) Project Structure

```
attendance_system/
├── app.py
├── config.py
├── extensions.py
├── models.py
├── forms.py
├── utils.py
├── requirements.txt
├── database.sql
├── routes/
│   ├── __init__.py
│   ├── auth_routes.py
│   ├── admin_routes.py
│   ├── lecturer_routes.py
│   └── student_routes.py
├── templates/
│   ├── base.html
│   ├── auth/login.html
│   ├── admin/dashboard.html
│   ├── lecturer/*.html
│   └── student/*.html
└── static/
    ├── js/checkin.js
    └── uploads/
        ├── checkins/
        └── profiles/
```

## 2) Key Features Implemented

- Role-based authentication: Admin, Lecturer, Student
- Secure password hashing
- CSRF protection with Flask-WTF
- Course/session management (lecturer)
- Check-in with image upload, time-window check, one-time check-in
- Captures timestamp, IP, user-agent, latitude/longitude
- Suspicious detection:
  - same IP used by many accounts
  - same browser/device footprint used by many accounts
  - same image hash reused
- Lecturer tools:
  - open/close attendance
  - approve/reject record
  - CSV export
  - suspicious log view
- Student tools:
  - profile update + profile image upload
  - optional webcam capture and geolocation from browser

## 3) Security Controls

- Password hashing via Werkzeug
- Input validation with WTForms
- File extension validation + secure file names
- Upload file size limit (`MAX_CONTENT_LENGTH`)
- CSRF-protected forms
- Route protection by role decorators

## 4) Setup Instructions (XAMPP or standard MySQL)

### A. Create database

1. Start MySQL (XAMPP Control Panel or local MySQL service)
2. Run SQL file:
   ```bash
   mysql -u root -p < database.sql
   ```

### B. Install Python dependencies

```bash
python -m venv .venv
source .venv/bin/activate  # Windows: .venv\Scripts\activate
pip install -r requirements.txt
```

### C. Configure environment

Create `.env`:

```env
SECRET_KEY=replace-this
DATABASE_URL=mysql+pymysql://root:YOUR_PASSWORD@127.0.0.1:3306/attendance_system
SESSION_COOKIE_SECURE=false
```

### D. Run app

```bash
export FLASK_APP=app.py
python app.py
```

Open: `http://127.0.0.1:5000`

## 5) Sample Login Accounts

> Note: sample hashes in `database.sql` are prefilled.

- Admin: `admin@example.com` / `Password123!`
- Lecturer: `lecturer1@example.com` / `Password123!`
- Student: `student1@example.com` / `Password123!`

## 6) Optional Advanced Features

- **Face recognition**: `face-recognition` included in requirements, but route fallback is enabled if not configured.
- **QR token**: each class session generates `qr_token`; can be used to build QR-based attendance URL.
- **Charts/PDF**: requirements include `matplotlib` + `reportlab` for extension.

## 7) Notes for Production Hardening

- Move secrets to environment and rotate regularly
- Use HTTPS + secure cookies (`SESSION_COOKIE_SECURE=true`)
- Add reverse-proxy aware IP extraction and WAF rules
- Add audit and alert pipeline for suspicious logs
- Add async job for face verification (Celery/RQ)

