import os
from dotenv import load_dotenv

load_dotenv()

class Config:
    """Configuration de base pour l'application Carent"""
    SECRET_KEY = os.environ.get('SECRET_KEY') or 'clé-secrète-par-défaut'
    MONGODB_URI = os.environ.get('MONGODB_URI') or 'mongodb://localhost:27017'
    MONGODB_DB = os.environ.get('MONGODB_DB') or 'carent_db'