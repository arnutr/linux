from datetime import datetime

from flask import Blueprint, flash, redirect, render_template, request, url_for
from flask_login import current_user, login_required

from extensions import db
from forms import CheckinForm, StudentProfileForm
from models import AttendanceRecord, ClassSession, Course, SystemLog
from utils import role_required, run_suspicious_checks, save_uploaded_image, within_session_window


student_bp = Blueprint("student", __name__, template_folder="../templates")


@student_bp.route("/dashboard")
@login_required
@role_required("student")
def dashboard():
    records = AttendanceRecord.query.filter_by(student_id=current_user.student.id).order_by(AttendanceRecord.checkin_time.desc()).all()
    return render_template("student/dashboard.html", courses=current_user.student.courses, records=records)


@student_bp.route("/enroll/<int:course_id>", methods=["POST"])
@login_required
@role_required("student")
def enroll(course_id):
    course = Course.query.get_or_404(course_id)
    if course not in current_user.student.courses:
        current_user.student.courses.append(course)
        db.session.add(SystemLog(actor_user_id=current_user.id, action=f"enroll in {course.code}"))
        db.session.commit()
        flash("ลงทะเบียนวิชาเรียบร้อย", "success")
    return redirect(url_for("student.dashboard"))


@student_bp.route("/sessions/<int:session_id>/checkin", methods=["GET", "POST"])
@login_required
@role_required("student")
def checkin(session_id):
    session = ClassSession.query.get_or_404(session_id)
    form = CheckinForm()

    if form.validate_on_submit():
        existing = AttendanceRecord.query.filter_by(student_id=current_user.student.id, session_id=session.id).first()
        if existing:
            flash("คุณเช็คชื่อคาบนี้แล้ว", "warning")
            return redirect(url_for("student.dashboard"))

        if not within_session_window(session, datetime.utcnow()):
            flash("นอกช่วงเวลาเช็คชื่อ หรือคาบยังไม่เปิด", "danger")
            return redirect(url_for("student.dashboard"))

        if form.attendance_code.data.strip() != session.attendance_code:
            flash("รหัสเช็คชื่อไม่ถูกต้อง", "danger")
            return redirect(url_for("student.checkin", session_id=session.id))

        if session.require_gps and (not form.latitude.data or not form.longitude.data):
            flash("คาบนี้ต้องเปิดตำแหน่ง GPS ก่อนเช็คชื่อ", "danger")
            return redirect(url_for("student.checkin", session_id=session.id))

        if session.allowed_ip_prefix and not request.remote_addr.startswith(session.allowed_ip_prefix):
            flash("เครือข่ายปัจจุบันไม่ได้รับอนุญาต", "danger")
            return redirect(url_for("student.checkin", session_id=session.id))

        photo_path, photo_hash = save_uploaded_image(form.checkin_photo.data, "static/uploads/checkins")
        now = datetime.utcnow()
        is_late = now.time() > session.start_time

        attendance = AttendanceRecord(
            student_id=current_user.student.id,
            course_id=session.course_id,
            session_id=session.id,
            checkin_time=now,
            photo_path=photo_path,
            photo_hash=photo_hash,
            ip_address=request.headers.get("X-Forwarded-For", request.remote_addr or "unknown")[:45],
            user_agent=(request.user_agent.string or "unknown")[:255],
            latitude=form.latitude.data,
            longitude=form.longitude.data,
            is_late=is_late,
            status="pending",
        )
        db.session.add(attendance)
        db.session.commit()

        run_suspicious_checks(attendance)
        db.session.commit()

        flash("เช็คชื่อสำเร็จ", "success")
        return redirect(url_for("student.dashboard"))

    return render_template("student/checkin.html", session=session, form=form)


@student_bp.route("/profile", methods=["GET", "POST"])
@login_required
@role_required("student")
def profile():
    student = current_user.student
    form = StudentProfileForm(obj=student)

    if form.validate_on_submit():
        student.full_name = form.full_name.data.strip()
        student.department = form.department.data.strip()
        student.year = int(form.year.data)

        if form.profile_image.data:
            photo_path, _ = save_uploaded_image(form.profile_image.data, "static/uploads/profiles")
            student.profile_image = photo_path

        db.session.add(SystemLog(actor_user_id=current_user.id, action="update student profile"))
        db.session.commit()
        flash("อัปเดตโปรไฟล์แล้ว", "success")
        return redirect(url_for("student.profile"))

    return render_template("student/profile.html", form=form, student=student)
