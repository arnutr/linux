from flask import Blueprint, flash, redirect, render_template, url_for
from flask_login import current_user, login_required

from extensions import db
from forms import UserCreateForm
from models import Course, Lecturer, Role, Student, SystemLog, User
from utils import role_required


admin_bp = Blueprint("admin", __name__, template_folder="../templates")


@admin_bp.route("/dashboard", methods=["GET", "POST"])
@login_required
@role_required("admin")
def dashboard():
    form = UserCreateForm()
    if form.validate_on_submit():
        role = Role.query.filter_by(name=form.role.data).first()
        if not role:
            flash("ไม่พบบทบาทผู้ใช้", "danger")
            return redirect(url_for("admin.dashboard"))

        user = User(email=form.email.data.lower().strip(), role_id=role.id)
        user.set_password(form.password.data)
        db.session.add(user)
        db.session.flush()

        if form.role.data == "student":
            db.session.add(
                Student(
                    user_id=user.id,
                    student_code=form.code.data,
                    full_name=form.full_name.data,
                    department=form.department.data,
                    year=int(form.year.data or 1),
                )
            )
        else:
            db.session.add(
                Lecturer(
                    user_id=user.id,
                    lecturer_code=form.code.data,
                    full_name=form.full_name.data,
                    department=form.department.data,
                )
            )

        db.session.add(SystemLog(actor_user_id=current_user.id, action=f"create user {user.email}"))
        db.session.commit()
        flash("สร้างบัญชีสำเร็จ", "success")
        return redirect(url_for("admin.dashboard"))

    stats = {
        "users": User.query.count(),
        "students": Student.query.count(),
        "lecturers": Lecturer.query.count(),
        "courses": Course.query.count(),
    }
    logs = SystemLog.query.order_by(SystemLog.created_at.desc()).limit(20).all()

    return render_template("admin/dashboard.html", form=form, stats=stats, logs=logs)
