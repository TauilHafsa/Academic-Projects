"""
Modèles SQLAlchemy pour la base de données SkinSafe
"""
from flask_sqlalchemy import SQLAlchemy
from datetime import datetime
from werkzeug.security import generate_password_hash, check_password_hash

db = SQLAlchemy()

class Utilisateur(db.Model):
    """Modèle pour la table utilisateur"""
    __tablename__ = 'utilisateur'
    
    id_utilisateur = db.Column(db.Integer, primary_key=True, autoincrement=True)
    nom = db.Column(db.String(100), nullable=False)
    email = db.Column(db.String(150), nullable=False, unique=True, index=True)
    mot_de_passe = db.Column(db.String(255), nullable=False)
    date_inscription = db.Column(db.DateTime, default=datetime.utcnow)
    
    # Relations
    analyses = db.relationship('Analyse', backref='utilisateur', cascade='all, delete-orphan')
    notifications = db.relationship('Notification', backref='utilisateur', cascade='all, delete-orphan')
    
    def __init__(self, nom, email, mot_de_passe):
        self.nom = nom
        self.email = email
        self.set_password(mot_de_passe)
    
    def set_password(self, password):
        """Hasher le mot de passe"""
        self.mot_de_passe = generate_password_hash(password)
    
    def check_password(self, password):
        """Vérifier le mot de passe"""
        return check_password_hash(self.mot_de_passe, password)
    
    def to_dict(self):
        """Convertir en dictionnaire"""
        return {
            'id_utilisateur': self.id_utilisateur,
            'nom': self.nom,
            'email': self.email,
            'date_inscription': self.date_inscription.isoformat() if self.date_inscription else None
        }
    
    def __repr__(self):
        return f'<Utilisateur {self.nom}>'

class Analyse(db.Model):
    """Modèle pour la table analyse"""
    __tablename__ = 'analyse'
    
    id_analyse = db.Column(db.Integer, primary_key=True, autoincrement=True)
    id_utilisateur = db.Column(db.Integer, db.ForeignKey('utilisateur.id_utilisateur', ondelete='CASCADE'), nullable=False, index=True)
    date_analyse = db.Column(db.DateTime, default=datetime.utcnow, index=True)
    image_path = db.Column(db.String(255), nullable=False)
    resultat = db.Column(db.String(100), nullable=False)
    probabilite = db.Column(db.Float, nullable=False)
    
    # Relations
    notifications = db.relationship('Notification', backref='analyse', cascade='all, delete-orphan')
    
    def __init__(self, id_utilisateur, image_path, resultat, probabilite):
        self.id_utilisateur = id_utilisateur
        self.image_path = image_path
        self.resultat = resultat
        self.probabilite = max(0.0, min(1.0, probabilite))  # Contrainte 0-1
    
    def to_dict(self):
        """Convertir en dictionnaire"""
        return {
            'id_analyse': self.id_analyse,
            'id_utilisateur': self.id_utilisateur,
            'date_analyse': self.date_analyse.isoformat() if self.date_analyse else None,
            'image_path': self.image_path,
            'resultat': self.resultat,
            'probabilite': self.probabilite
        }
    
    def __repr__(self):
        return f'<Analyse {self.id_analyse}: {self.resultat}>'

class Notification(db.Model):
    """Modèle pour la table notification"""
    __tablename__ = 'notification'
    
    id_notification = db.Column(db.Integer, primary_key=True, autoincrement=True)
    id_utilisateur = db.Column(db.Integer, db.ForeignKey('utilisateur.id_utilisateur', ondelete='CASCADE'), nullable=False, index=True)
    id_analyse = db.Column(db.Integer, db.ForeignKey('analyse.id_analyse', ondelete='CASCADE'), nullable=False)
    message = db.Column(db.Text, nullable=False)
    date_envoi = db.Column(db.DateTime, default=datetime.utcnow, index=True)
    
    def __init__(self, id_utilisateur, id_analyse, message):
        self.id_utilisateur = id_utilisateur
        self.id_analyse = id_analyse
        self.message = message
    
    def to_dict(self):
        """Convertir en dictionnaire"""
        return {
            'id_notification': self.id_notification,
            'id_utilisateur': self.id_utilisateur,
            'id_analyse': self.id_analyse,
            'message': self.message,
            'date_envoi': self.date_envoi.isoformat() if self.date_envoi else None
        }
    
    def __repr__(self):
        return f'<Notification {self.id_notification}>'