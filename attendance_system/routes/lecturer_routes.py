import csv
import io
import secrets
from datetime import datetime

from flask import Blueprint, Response, flash, redirect, render_template, request, url_for
from flask_login import current_user, login_required

from extensions import db
from forms import CourseForm, SessionForm
from models import AttendanceRecord, ClassSession, Course, SuspiciousLog, SystemLog
from utils import role_required


lecturer_bp = Blueprint("lecturer", __name__, template_folder="../templates")


@lecturer_bp.route("/dashboard")
@login_required
@role_required("lecturer")
def dashboard():
    courses = Course.query.filter_by(lecturer_id=current_user.lecturer.id).all()
    sessions = (
        ClassSession.query.join(Course)
        .filter(Course.lecturer_id == current_user.lecturer.id)
        .order_by(ClassSession.session_date.desc())
        .limit(50)
        .all()
    )
    suspicious = (
        SuspiciousLog.query.join(AttendanceRecord, SuspiciousLog.attendance_id == AttendanceRecord.id)
        .join(ClassSession, AttendanceRecord.session_id == ClassSession.id)
        .join(Course, ClassSession.course_id == Course.id)
        .filter(Course.lecturer_id == current_user.lecturer.id)
        .order_by(SuspiciousLog.created_at.desc())
        .limit(20)
        .all()
    )
    return render_template("lecturer/dashboard.html", courses=courses, sessions=sessions, suspicious=suspicious)


@lecturer_bp.route("/courses", methods=["GET", "POST"])
@login_required
@role_required("lecturer")
def courses():
    form = CourseForm()
    if form.validate_on_submit():
        course = Course(code=form.code.data.strip(), name=form.name.data.strip(), lecturer_id=current_user.lecturer.id)
        db.session.add(course)
        db.session.add(SystemLog(actor_user_id=current_user.id, action=f"create course {course.code}"))
        db.session.commit()
        flash("สร้างรายวิชาสำเร็จ", "success")
        return redirect(url_for("lecturer.courses"))

    all_courses = Course.query.filter_by(lecturer_id=current_user.lecturer.id).all()
    return render_template("lecturer/courses.html", form=form, courses=all_courses)


@lecturer_bp.route("/courses/<int:course_id>/sessions", methods=["GET", "POST"])
@login_required
@role_required("lecturer")
def sessions(course_id):
    course = Course.query.filter_by(id=course_id, lecturer_id=current_user.lecturer.id).first_or_404()
    form = SessionForm()

    if form.validate_on_submit():
        session = ClassSession(
            course_id=course.id,
            session_date=form.session_date.data,
            start_time=form.start_time.data,
            end_time=form.end_time.data,
            status="closed",
            attendance_code=secrets.token_hex(3).upper(),
            qr_token=secrets.token_urlsafe(24),
            require_gps=form.require_gps.data,
            require_face_photo=form.require_face_photo.data,
            allowed_ip_prefix=form.allowed_ip_prefix.data or None,
        )
        db.session.add(session)
        db.session.commit()
        flash("สร้างคาบเรียนสำเร็จ", "success")
        return redirect(url_for("lecturer.sessions", course_id=course.id))

    all_sessions = ClassSession.query.filter_by(course_id=course.id).order_by(ClassSession.session_date.desc()).all()
    return render_template("lecturer/sessions.html", course=course, form=form, sessions=all_sessions)


@lecturer_bp.route("/sessions/<int:session_id>/toggle", methods=["POST"])
@login_required
@role_required("lecturer")
def toggle_session(session_id):
    session = ClassSession.query.get_or_404(session_id)
    if session.course.lecturer_id != current_user.lecturer.id:
        return redirect(url_for("lecturer.dashboard"))
    session.status = "open" if session.status == "closed" else "closed"
    db.session.commit()
    flash(f"ปรับสถานะเป็น {session.status}", "info")
    return redirect(request.referrer or url_for("lecturer.dashboard"))


@lecturer_bp.route("/sessions/<int:session_id>/attendance")
@login_required
@role_required("lecturer")
def attendance_list(session_id):
    session = ClassSession.query.get_or_404(session_id)
    records = AttendanceRecord.query.filter_by(session_id=session.id).order_by(AttendanceRecord.checkin_time.desc()).all()
    return render_template("lecturer/attendance_list.html", session=session, records=records)


@lecturer_bp.route("/sessions/<int:session_id>/export.csv")
@login_required
@role_required("lecturer")
def export_csv(session_id):
    session = ClassSession.query.get_or_404(session_id)
    rows = AttendanceRecord.query.filter_by(session_id=session.id).all()

    output = io.StringIO()
    writer = csv.writer(output)
    writer.writerow(["student_code", "name", "checkin_time", "status", "late", "suspicious"])
    for row in rows:
        writer.writerow(
            [
                row.student.student_code,
                row.student.full_name,
                row.checkin_time.isoformat(),
                row.status,
                row.is_late,
                row.suspicious,
            ]
        )

    return Response(
        output.getvalue(),
        mimetype="text/csv",
        headers={"Content-Disposition": f"attachment; filename=session_{session.id}_attendance.csv"},
    )


@lecturer_bp.route("/attendance/<int:attendance_id>/status", methods=["POST"])
@login_required
@role_required("lecturer")
def set_attendance_status(attendance_id):
    record = AttendanceRecord.query.get_or_404(attendance_id)
    new_status = request.form.get("status")
    if new_status in {"approved", "rejected", "pending"}:
        record.status = new_status
        db.session.commit()
    return redirect(request.referrer or url_for("lecturer.dashboard"))
