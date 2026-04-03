from flask_wtf import FlaskForm
from flask_wtf.file import FileAllowed, FileField
from wtforms import BooleanField, DateField, EmailField, PasswordField, SelectField, StringField, SubmitField, TimeField
from wtforms.validators import DataRequired, Email, Length, Optional


class LoginForm(FlaskForm):
    email = EmailField("อีเมล", validators=[DataRequired(), Email()])
    password = PasswordField("รหัสผ่าน", validators=[DataRequired(), Length(min=6)])
    submit = SubmitField("เข้าสู่ระบบ")


class UserCreateForm(FlaskForm):
    role = SelectField("บทบาท", choices=[("lecturer", "Lecturer"), ("student", "Student")], validators=[DataRequired()])
    email = EmailField("อีเมล", validators=[DataRequired(), Email()])
    password = PasswordField("รหัสผ่าน", validators=[DataRequired(), Length(min=8)])
    code = StringField("รหัส", validators=[DataRequired(), Length(max=30)])
    full_name = StringField("ชื่อ-สกุล", validators=[DataRequired(), Length(max=120)])
    department = StringField("ภาควิชา", validators=[DataRequired(), Length(max=120)])
    year = StringField("ชั้นปี (เฉพาะนักศึกษา)", validators=[Optional()])
    submit = SubmitField("สร้างบัญชี")


class CourseForm(FlaskForm):
    code = StringField("รหัสวิชา", validators=[DataRequired(), Length(max=30)])
    name = StringField("ชื่อวิชา", validators=[DataRequired(), Length(max=255)])
    submit = SubmitField("บันทึก")


class SessionForm(FlaskForm):
    session_date = DateField("วันที่", validators=[DataRequired()])
    start_time = TimeField("เวลาเริ่ม", validators=[DataRequired()])
    end_time = TimeField("เวลาสิ้นสุด", validators=[DataRequired()])
    require_gps = BooleanField("ต้องระบุพิกัด")
    require_face_photo = BooleanField("ต้องใช้ภาพใบหน้า")
    allowed_ip_prefix = StringField("IP Prefix ที่อนุญาต", validators=[Optional(), Length(max=64)])
    submit = SubmitField("สร้างคาบเรียน")


class CheckinForm(FlaskForm):
    attendance_code = StringField("รหัสเช็คชื่อ", validators=[DataRequired(), Length(max=64)])
    latitude = StringField("Latitude", validators=[Optional()])
    longitude = StringField("Longitude", validators=[Optional()])
    checkin_photo = FileField(
        "ภาพเช็คชื่อ",
        validators=[DataRequired(), FileAllowed(["jpg", "jpeg", "png", "webp"], "รองรับเฉพาะรูปภาพ")],
    )
    submit = SubmitField("ยืนยันเช็คชื่อ")


class StudentProfileForm(FlaskForm):
    full_name = StringField("ชื่อ-สกุล", validators=[DataRequired(), Length(max=120)])
    department = StringField("ภาควิชา", validators=[DataRequired(), Length(max=120)])
    year = StringField("ชั้นปี", validators=[DataRequired(), Length(max=10)])
    profile_image = FileField("รูปโปรไฟล์", validators=[FileAllowed(["jpg", "jpeg", "png", "webp"])])
    submit = SubmitField("อัปเดตโปรไฟล์")
