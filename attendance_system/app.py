from flask import Flask

from config import Config
from extensions import csrf, db, login_manager, migrate
from routes.admin_routes import admin_bp
from routes.auth_routes import auth_bp
from routes.lecturer_routes import lecturer_bp
from routes.student_routes import student_bp


def create_app() -> Flask:
    app = Flask(__name__)
    app.config.from_object(Config)

    db.init_app(app)
    migrate.init_app(app, db)
    login_manager.init_app(app)
    csrf.init_app(app)

    app.register_blueprint(auth_bp)
    app.register_blueprint(admin_bp, url_prefix="/admin")
    app.register_blueprint(lecturer_bp, url_prefix="/lecturer")
    app.register_blueprint(student_bp, url_prefix="/student")

    from models import User  # noqa: F401

    @app.context_processor
    def inject_globals():
        return {"APP_NAME": "ระบบเช็คชื่อเข้าเรียน"}

    return app


if __name__ == "__main__":
    app = create_app()
    app.run(debug=True)
