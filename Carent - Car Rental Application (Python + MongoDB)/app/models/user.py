from flask_login import UserMixin
from datetime import datetime
import bcrypt
from bson import ObjectId

class User(UserMixin):
    """Modèle pour les utilisateurs (Administrateurs et Managers)"""
    
    def __init__(self, username, email, role='manager', password_hash=None, _id=None):
        self._id = _id if _id else str(ObjectId())
        self.username = username
        self.email = email
        self.role = role  # 'admin' ou 'manager'
        self.password_hash = password_hash
        self.created_at = datetime.utcnow()
        self.updated_at = datetime.utcnow()
    
    def get_id(self):
        return self._id
    
    @staticmethod
    def hash_password(password):
        """Hash un mot de passe avec bcrypt"""
        return bcrypt.hashpw(password.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')
    
    def check_password(self, password):
        """Vérifie si le mot de passe fourni correspond au hash stocké"""
        return bcrypt.checkpw(password.encode('utf-8'), self.password_hash.encode('utf-8'))
    
    def to_document(self):
        """Convertit l'objet en document MongoDB"""
        return {
            "_id": self._id,
            "username": self.username,
            "email": self.email,
            "role": self.role,
            "password_hash": self.password_hash,
            "created_at": self.created_at,
            "updated_at": self.updated_at
        }
    
    @classmethod
    def from_document(cls, document):
        """Crée une instance à partir d'un document MongoDB"""
        if not document:
            return None
            
        user = cls(
            username=document["username"],
            email=document["email"],
            role=document["role"],
            password_hash=document["password_hash"],
            _id=document["_id"]
        )
        user.created_at = document.get("created_at", datetime.utcnow())
        user.updated_at = document.get("updated_at", datetime.utcnow())
        return user