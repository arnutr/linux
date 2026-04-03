from datetime import datetime

from flask_login import UserMixin
from werkzeug.security import check_password_hash, generate_password_hash

from extensions import db, login_manager


enrollments = db.Table(
    "enrollments",
    db.Column("student_id", db.Integer, db.ForeignKey("students.id"), primary_key=True),
    db.Column("course_id", db.Integer, db.ForeignKey("courses.id"), primary_key=True),
    db.Column("created_at", db.DateTime, default=datetime.utcnow),
)


class TimestampMixin:
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)


class Role(db.Model, TimestampMixin):
    __tablename__ = "roles"

    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(30), unique=True, nullable=False)


class User(UserMixin, db.Model, TimestampMixin):
    __tablename__ = "users"

    id = db.Column(db.Integer, primary_key=True)
    email = db.Column(db.String(120), unique=True, nullable=False, index=True)
    password_hash = db.Column(db.String(255), nullable=False)
    role_id = db.Column(db.Integer, db.ForeignKey("roles.id"), nullable=False)
    is_active_user = db.Column(db.Boolean, default=True)

    role = db.relationship("Role")
    student = db.relationship("Student", back_populates="user", uselist=False)
    lecturer = db.relationship("Lecturer", back_populates="user", uselist=False)

    def set_password(self, raw_password: str) -> None:
        self.password_hash = generate_password_hash(raw_password)

    def check_password(self, raw_password: str) -> bool:
        return check_password_hash(self.password_hash, raw_password)

    def has_role(self, role_name: str) -> bool:
        return self.role and self.role.name.lower() == role_name.lower()


@login_manager.user_loader
def load_user(user_id: str):
    return db.session.get(User, int(user_id))


class Lecturer(db.Model, TimestampMixin):
    __tablename__ = "lecturers"

    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey("users.id"), unique=True, nullable=False)
    lecturer_code = db.Column(db.String(30), unique=True, nullable=False)
    full_name = db.Column(db.String(120), nullable=False)
    department = db.Column(db.String(120), nullable=False)

    user = db.relationship("User", back_populates="lecturer")
    courses = db.relationship("Course", back_populates="lecturer", lazy="dynamic")


class Student(db.Model, TimestampMixin):
    __tablename__ = "students"

    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey("users.id"), unique=True, nullable=False)
    student_code = db.Column(db.String(30), unique=True, nullable=False)
    full_name = db.Column(db.String(120), nullable=False)
    department = db.Column(db.String(120), nullable=False)
    year = db.Column(db.Integer, nullable=False)
    profile_image = db.Column(db.String(255), nullable=False, default="uploads/profiles/default.png")

    user = db.relationship("User", back_populates="student")
    courses = db.relationship("Course", secondary=enrollments, back_populates="students")


class Course(db.Model, TimestampMixin):
    __tablename__ = "courses"

    id = db.Column(db.Integer, primary_key=True)
    code = db.Column(db.String(30), unique=True, nullable=False)
    name = db.Column(db.String(255), nullable=False)
    lecturer_id = db.Column(db.Integer, db.ForeignKey("lecturers.id"), nullable=False)

    lecturer = db.relationship("Lecturer", back_populates="courses")
    sessions = db.relationship("ClassSession", back_populates="course", cascade="all,delete-orphan")
    students = db.relationship("Student", secondary=enrollments, back_populates="courses")


class ClassSession(db.Model, TimestampMixin):
    __tablename__ = "class_sessions"

    id = db.Column(db.Integer, primary_key=True)
    course_id = db.Column(db.Integer, db.ForeignKey("courses.id"), nullable=False)
    session_date = db.Column(db.Date, nullable=False)
    start_time = db.Column(db.Time, nullable=False)
    end_time = db.Column(db.Time, nullable=False)
    status = db.Column(db.String(20), default="closed")
    attendance_code = db.Column(db.String(64), nullable=False)
    qr_token = db.Column(db.String(128), nullable=False)
    require_gps = db.Column(db.Boolean, default=False)
    require_face_photo = db.Column(db.Boolean, default=True)
    allowed_ip_prefix = db.Column(db.String(64), nullable=True)

    course = db.relationship("Course", back_populates="sessions")
    attendance_records = db.relationship(
        "AttendanceRecord", back_populates="session", cascade="all,delete-orphan"
    )


class AttendanceRecord(db.Model, TimestampMixin):
    __tablename__ = "attendance_records"
    __table_args__ = (
        db.UniqueConstraint("student_id", "session_id", name="uq_student_session"),
    )

    id = db.Column(db.Integer, primary_key=True)
    student_id = db.Column(db.Integer, db.ForeignKey("students.id"), nullable=False)
    course_id = db.Column(db.Integer, db.ForeignKey("courses.id"), nullable=False)
    session_id = db.Column(db.Integer, db.ForeignKey("class_sessions.id"), nullable=False)
    checkin_time = db.Column(db.DateTime, nullable=False, default=datetime.utcnow)
    photo_path = db.Column(db.String(255), nullable=False)
    photo_hash = db.Column(db.String(64), nullable=False)
    ip_address = db.Column(db.String(45), nullable=False)
    user_agent = db.Column(db.String(255), nullable=False)
    latitude = db.Column(db.String(30), nullable=True)
    longitude = db.Column(db.String(30), nullable=True)
    is_late = db.Column(db.Boolean, default=False)
    status = db.Column(db.String(20), default="pending")
    suspicious = db.Column(db.Boolean, default=False)

    student = db.relationship("Student")
    session = db.relationship("ClassSession", back_populates="attendance_records")


class SuspiciousLog(db.Model, TimestampMixin):
    __tablename__ = "suspicious_logs"

    id = db.Column(db.Integer, primary_key=True)
    attendance_id = db.Column(db.Integer, db.ForeignKey("attendance_records.id"), nullable=False)
    reason = db.Column(db.String(255), nullable=False)


class SystemLog(db.Model):
    __tablename__ = "system_logs"

    id = db.Column(db.Integer, primary_key=True)
    actor_user_id = db.Column(db.Integer, db.ForeignKey("users.id"), nullable=True)
    action = db.Column(db.String(255), nullable=False)
    level = db.Column(db.String(20), default="info")
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
