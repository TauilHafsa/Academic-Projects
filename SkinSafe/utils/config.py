
import os
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
import mysql.connector
from mysql.connector import Error

class DatabaseConfig:
    """Configuration de la base de données"""
    
    # Configuration MySQL
    MYSQL_HOST = os.getenv('MYSQL_HOST', '127.0.0.1')
    MYSQL_PORT = int(os.getenv('MYSQL_PORT', 3306))
    MYSQL_USER = os.getenv('MYSQL_USER', 'root')
    MYSQL_PASSWORD = os.getenv('MYSQL_PASSWORD', '')
    MYSQL_DATABASE = os.getenv('MYSQL_DATABASE', 'skinsafe_db')
    
    # URL de connexion SQLAlchemy
    DATABASE_URL = f"mysql+pymysql://{MYSQL_USER}:{MYSQL_PASSWORD}@{MYSQL_HOST}:{MYSQL_PORT}/{MYSQL_DATABASE}"
    
    # Configuration Flask-SQLAlchemy
    SQLALCHEMY_DATABASE_URI = DATABASE_URL
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    SQLALCHEMY_ENGINE_OPTIONS = {
        'pool_pre_ping': True,
        'pool_recycle': 300,
        'connect_args': {'charset': 'utf8mb4'}
    }

# Configuration SQLAlchemy directe
engine = create_engine(DatabaseConfig.DATABASE_URL, echo=True)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

def get_db_connection():
    """Créer une connexion directe à MySQL"""
    try:
        connection = mysql.connector.connect(
            host=DatabaseConfig.MYSQL_HOST,
            port=DatabaseConfig.MYSQL_PORT,
            database=DatabaseConfig.MYSQL_DATABASE,
            user=DatabaseConfig.MYSQL_USER,
            password=DatabaseConfig.MYSQL_PASSWORD,
            charset='utf8mb4',
            autocommit=True
        )
        return connection
    except Error as e:
        print(f"Erreur de connexion à MySQL: {e}")
        return None

def test_connection():
    """Tester la connexion à la base de données"""
    connection = get_db_connection()
    if connection and connection.is_connected():
        print("✅ Connexion à la base de données réussie!")
        connection.close()
        return True
    else:
        print("❌ Échec de la connexion à la base de données!")
        return False