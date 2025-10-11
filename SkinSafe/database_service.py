"""
Service pour les opérations de base de données
"""
from models import db, Utilisateur, Analyse, Notification
from sqlalchemy.exc import SQLAlchemyError
from datetime import datetime
import logging

class DatabaseService:
    """Service pour gérer les opérations de base de données"""
    
    @staticmethod
    def create_user(nom, email, mot_de_passe):
        """Créer un nouvel utilisateur"""
        try:
            # Vérifier si l'email existe déjà
            existing_user = Utilisateur.query.filter_by(email=email).first()
            if existing_user:
                return None, "Email déjà utilisé"
            
            user = Utilisateur(nom=nom, email=email, mot_de_passe=mot_de_passe)
            db.session.add(user)
            db.session.commit()
            return user, "Utilisateur créé avec succès"
        except SQLAlchemyError as e:
            db.session.rollback()
            logging.error(f"Erreur lors de la création de l'utilisateur: {e}")
            return None, "Erreur lors de la création"
    
    @staticmethod
    def authenticate_user(email, mot_de_passe):
        """Authentifier un utilisateur"""
        try:
            user = Utilisateur.query.filter_by(email=email).first()
            if user and user.check_password(mot_de_passe):
                return user, "Authentification réussie"
            return None, "Email ou mot de passe incorrect"
        except SQLAlchemyError as e:
            logging.error(f"Erreur lors de l'authentification: {e}")
            return None, "Erreur de connexion"
    
    @staticmethod
    def get_user_by_id(user_id):
        """Récupérer un utilisateur par son ID"""
        try:
            return Utilisateur.query.get(user_id)
        except SQLAlchemyError as e:
            logging.error(f"Erreur lors de la récupération de l'utilisateur: {e}")
            return None
    
    @staticmethod
    def create_analysis(id_utilisateur, image_path, resultat, probabilite):
        """Créer une nouvelle analyse"""
        try:
            analysis = Analyse(
                id_utilisateur=id_utilisateur,
                image_path=image_path,
                resultat=resultat,
                probabilite=probabilite
            )
            db.session.add(analysis)
            db.session.commit()
            return analysis, "Analyse créée avec succès"
        except SQLAlchemyError as e:
            db.session.rollback()
            logging.error(f"Erreur lors de la création de l'analyse: {e}")
            return None, "Erreur lors de la sauvegarde"
    
    @staticmethod
    def get_user_analyses(user_id, limit=None):
        """Récupérer les analyses d'un utilisateur"""
        try:
            query = Analyse.query.filter_by(id_utilisateur=user_id).order_by(Analyse.date_analyse.desc())
            if limit:
                query = query.limit(limit)
            return query.all()
        except SQLAlchemyError as e:
            logging.error(f"Erreur lors de la récupération des analyses: {e}")
            return []
    
    @staticmethod
    def get_analysis_by_id(analysis_id):
        """Récupérer une analyse par son ID"""
        try:
            return Analyse.query.get(analysis_id)
        except SQLAlchemyError as e:
            logging.error(f"Erreur lors de la récupération de l'analyse: {e}")
            return None
    
    @staticmethod
    def create_notification(id_utilisateur, id_analyse, message):
        """Créer une notification"""
        try:
            notification = Notification(
                id_utilisateur=id_utilisateur,
                id_analyse=id_analyse,
                message=message
            )
            db.session.add(notification)
            db.session.commit()
            return notification, "Notification créée"
        except SQLAlchemyError as e:
            db.session.rollback()
            logging.error(f"Erreur lors de la création de la notification: {e}")
            return None, "Erreur notification"
    
    @staticmethod
    def get_user_notifications(user_id, limit=10):
        """Récupérer les notifications d'un utilisateur"""
        try:
            return Notification.query.filter_by(id_utilisateur=user_id)\
                .order_by(Notification.date_envoi.desc())\
                .limit(limit).all()
        except SQLAlchemyError as e:
            logging.error(f"Erreur lors de la récupération des notifications: {e}")
            return []
    
    @staticmethod
    def get_dashboard_stats(user_id):
        """Récupérer les statistiques pour le dashboard"""
        try:
            total_analyses = Analyse.query.filter_by(id_utilisateur=user_id).count()
            recent_analyses = Analyse.query.filter_by(id_utilisateur=user_id)\
                .order_by(Analyse.date_analyse.desc()).limit(5).all()
            
            # Statistiques par résultat
            results_stats = db.session.query(
                Analyse.resultat, 
                db.func.count(Analyse.id_analyse).label('count')
            ).filter_by(id_utilisateur=user_id).group_by(Analyse.resultat).all()
            
            return {
                'total_analyses': total_analyses,
                'recent_analyses': [a.to_dict() for a in recent_analyses],
                'results_stats': {stat.resultat: stat.count for stat in results_stats}
            }
        except SQLAlchemyError as e:
            logging.error(f"Erreur lors de la récupération des statistiques: {e}")
            return {
                'total_analyses': 0,
                'recent_analyses': [],
                'results_stats': {}
            }
    
    @staticmethod
    def delete_analysis(analysis_id, user_id):
        """Supprimer une analyse"""
        try:
            analysis = Analyse.query.filter_by(id_analyse=analysis_id, id_utilisateur=user_id).first()
            if analysis:
                db.session.delete(analysis)
                db.session.commit()
                return True, "Analyse supprimée"
            return False, "Analyse non trouvée"
        except SQLAlchemyError as e:
            db.session.rollback()
            logging.error(f"Erreur lors de la suppression: {e}")
            return False, "Erreur lors de la suppression"