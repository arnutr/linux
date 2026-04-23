# SYSADMIN E-BOOK STORE & DOWNLOAD SYSTEM

Production-ready PHP 8 + MySQL web app for e-book catalog, secure code redemption, and protected PDF downloads.

## Features
- Admin auth (session, timeout, hashed passwords)
- Member CRUD (admin/customer roles)
- Book CRUD + cover/PDF upload
- Download code generation with usage limit + expiration
- Public redemption + secure file streaming download
- Dashboard stats + recent activity log
- Code filtering/search + CSV export
- Bootstrap 5 responsive SaaS-style UI

## Folder Structure
- `app/`, `config/`, `controllers/`, `models/`, `views/`
- `public/` web root
- `uploads/books`, `uploads/covers`
- `sql/schema.sql`

## Setup (XAMPP)
1. Copy folder into `htdocs/sysadmin-ebook-store`.
2. Import `sql/schema.sql` into MySQL.
3. Ensure Apache modules `rewrite` enabled.
4. Update DB config in `config/config.php` if needed.
5. Open: `http://localhost/sysadmin-ebook-store/public/index.php?route=login`

## Default Login
- Email: `admin@store.local`
- Password: `admin123`

## Security Notes
- Prepared statements (PDO)
- CSRF token for all forms
- Input validation/sanitization
- Session timeout enforcement
- Files are served via PHP controller (`readfile`) and stored outside `public`
