from flask import Flask
from pymongo import MongoClient
from app.config import Config
from flask_login import LoginManager

# Initialisation de l'application
def create_app(config_class=Config):
    app = Flask(__name__)
    app.config.from_object(config_class)
    
    # Connexion à MongoDB
    client = MongoClient(app.config['MONGODB_URI'])
    db = client[app.config['MONGODB_DB']]
    app.db = db
    
    # Configuration de Flask-Login
    login_manager = LoginManager()
    login_manager.login_view = 'auth.login'
    login_manager.init_app(app)
    
    # Import des modèles pour le login_manager
    from app.models.user import User
    
    @login_manager.user_loader
    def load_user(user_id):
        user_data = app.db.users.find_one({"_id": user_id})
        if user_data:
            return User.from_document(user_data)
        return None
    
    # Enregistrement des blueprints
    from app.routes.auth_routes import auth_bp
    from app.routes.car_routes import car_bp
    from app.routes.client_routes import client_bp
    from app.routes.reservation_routes import reservation_bp
    from app.routes.admin_routes import admin_bp
    
    app.register_blueprint(auth_bp)
    app.register_blueprint(car_bp)
    app.register_blueprint(client_bp)
    app.register_blueprint(reservation_bp)
    app.register_blueprint(admin_bp)
    
    # Routes de base
    @app.route('/')
    def index():
        from flask import render_template
        return render_template('index.html')
    
    return app