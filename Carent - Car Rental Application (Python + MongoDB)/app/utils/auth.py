from flask import current_app
from flask_login import LoginManager
from app.models.user import User
from bson.objectid import ObjectId

def setup_login_manager(app):
    """Configure le gestionnaire d'authentification pour l'application Flask"""
    login_manager = LoginManager()
    login_manager.init_app(app)
    login_manager.login_view = 'auth.login'
    login_manager.login_message = 'Veuillez vous connecter pour accéder à cette page.'
    login_manager.login_message_category = 'warning'
    
    @login_manager.user_loader
    def load_user(user_id):
        """Fonction qui charge un utilisateur à partir de son ID"""
        user_data = app.db.users.find_one({"_id": user_id})
        if user_data:
            return User.from_document(user_data)
        return None
    
    return login_manager

def create_admin_if_not_exists(app):
    """Vérifie si un compte administrateur existe, sinon en crée un par défaut"""
    # Vérifier si un admin existe déjà
    admin_exists = app.db.users.find_one({"role": "admin"})
    
    if not admin_exists:
        # Créer un administrateur par défaut
        admin_user = User(
            username="admin",
            email="admin@carent.com",
            role="admin",
            password_hash=User.hash_password("admin123")  # Remplacer par un mot de passe sécurisé en production
        )
        
        # Insérer l'administrateur dans la base de données
        app.db.users.insert_one(admin_user.to_document())
        print("Compte administrateur par défaut créé")