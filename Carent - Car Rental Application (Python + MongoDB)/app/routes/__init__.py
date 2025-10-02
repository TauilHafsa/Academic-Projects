# Fichier d'initialisation pour le package routes
# Ce fichier permet de regrouper tous les blueprints pour l'enregistrement dans l'application Flask

from app.routes.admin_routes import admin_bp
from app.routes.auth_routes import auth_bp
from app.routes.car_routes import car_bp
from app.routes.client_routes import client_bp
from app.routes.reservation_routes import reservation_bp

# Liste des blueprints à enregistrer dans l'application Flask
blueprints = [
    admin_bp,
    auth_bp,
    car_bp,
    client_bp,
    reservation_bp
]