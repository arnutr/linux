from flask import Blueprint, flash, redirect, render_template, request, url_for
from flask_login import current_user, login_required, login_user, logout_user

from forms import LoginForm
from models import User


auth_bp = Blueprint("auth", __name__)


@auth_bp.route("/")
def index():
    if current_user.is_authenticated:
        if current_user.has_role("admin"):
            return redirect(url_for("admin.dashboard"))
        if current_user.has_role("lecturer"):
            return redirect(url_for("lecturer.dashboard"))
        return redirect(url_for("student.dashboard"))
    return redirect(url_for("auth.login"))


@auth_bp.route("/login", methods=["GET", "POST"])
def login():
    form = LoginForm()
    if form.validate_on_submit():
        user = User.query.filter_by(email=form.email.data.lower().strip()).first()
        if user and user.check_password(form.password.data) and user.is_active_user:
            login_user(user, remember=True)
            next_url = request.args.get("next")
            return redirect(next_url or url_for("auth.index"))
        flash("อีเมลหรือรหัสผ่านไม่ถูกต้อง", "danger")
    return render_template("auth/login.html", form=form)


@auth_bp.route("/logout")
@login_required
def logout():
    logout_user()
    flash("ออกจากระบบเรียบร้อย", "success")
    return redirect(url_for("auth.login"))
