import hashlib
import os
import secrets
from datetime import datetime, time
from functools import wraps

from flask import abort, current_app, flash
from flask_login import current_user
from werkzeug.utils import secure_filename

from extensions import db
from models import AttendanceRecord, SuspiciousLog


def allowed_file(filename: str) -> bool:
    ext = filename.rsplit(".", 1)[-1].lower() if "." in filename else ""
    return ext in current_app.config["ALLOWED_IMAGE_EXTENSIONS"]


def save_uploaded_image(file_storage, upload_dir: str):
    if not allowed_file(file_storage.filename):
        raise ValueError("ประเภทไฟล์ไม่ถูกต้อง")

    original = secure_filename(file_storage.filename)
    ext = original.rsplit(".", 1)[-1].lower()
    unique_name = f"{datetime.utcnow().strftime('%Y%m%d%H%M%S')}_{secrets.token_hex(8)}.{ext}"
    abs_dir = os.path.join(current_app.root_path, upload_dir)
    os.makedirs(abs_dir, exist_ok=True)
    file_path = os.path.join(abs_dir, unique_name)
    file_storage.save(file_path)

    with open(file_path, "rb") as f:
        file_hash = hashlib.sha256(f.read()).hexdigest()

    return f"{upload_dir}/{unique_name}", file_hash


def role_required(*roles):
    def decorator(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            if not current_user.is_authenticated:
                abort(401)
            if current_user.role.name not in roles:
                abort(403)
            return func(*args, **kwargs)

        return wrapper

    return decorator


def within_session_window(session, now=None):
    now = now or datetime.utcnow()
    if session.status != "open" or now.date() != session.session_date:
        return False
    return time(session.start_time.hour, session.start_time.minute) <= now.time() <= time(
        session.end_time.hour, session.end_time.minute
    )


def run_suspicious_checks(attendance: AttendanceRecord):
    reasons = []

    ip_count = AttendanceRecord.query.filter(
        AttendanceRecord.ip_address == attendance.ip_address,
        AttendanceRecord.created_at >= datetime.utcnow().replace(hour=0, minute=0, second=0),
    ).count()
    if ip_count >= 5:
        reasons.append("IP เดียวกันเช็คชื่อหลายบัญชีในวันเดียว")

    ua_count = AttendanceRecord.query.filter(
        AttendanceRecord.user_agent == attendance.user_agent,
        AttendanceRecord.created_at >= datetime.utcnow().replace(hour=0, minute=0, second=0),
    ).count()
    if ua_count >= 5:
        reasons.append("อุปกรณ์/เบราว์เซอร์เดียวกันเช็คชื่อหลายบัญชี")

    same_hash = AttendanceRecord.query.filter(
        AttendanceRecord.photo_hash == attendance.photo_hash,
        AttendanceRecord.id != attendance.id,
    ).count()
    if same_hash > 0:
        reasons.append("ภาพเช็คชื่อซ้ำกับบันทึกอื่น")

    for reason in reasons:
        db.session.add(SuspiciousLog(attendance_id=attendance.id, reason=reason))

    if reasons:
        attendance.suspicious = True
        flash("ระบบตรวจพบความเสี่ยงการเช็คชื่อแทน โปรดรอตรวจสอบ", "warning")

    return reasons
