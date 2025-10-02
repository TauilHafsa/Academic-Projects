from flask import Blueprint, render_template, redirect, url_for, flash, request, current_app
from flask_login import login_required, current_user
from app.models.user import User
from app.utils.decorators import admin_required
from datetime import datetime

admin_bp = Blueprint('admin', __name__, url_prefix='/admin')

@admin_bp.route('/dashboard')
@login_required
@admin_required
def dashboard():
    # Compteurs pour le tableau de bord
    total_managers = current_app.db.users.count_documents({"role": "manager"})
    total_cars = current_app.db.cars.count_documents({})
    total_clients = current_app.db.clients.count_documents({})
    total_reservations = current_app.db.reservations.count_documents({})
    pending_reservations = current_app.db.reservations.count_documents({"status": "pending"})
    
    # Récupérer les dernières réservations
    recent_reservations = []
    for reservation_data in current_app.db.reservations.find().sort("created_at", -1).limit(5):
        from app.models.reservation import Reservation
        reservation = Reservation.from_document(reservation_data)
        
        # Obtenir les informations du client
        client_data = current_app.db.clients.find_one({"_id": reservation.client_id})
        if client_data:
            from app.models.client import Client
            client = Client.from_document(client_data)
            recent_reservations.append({
                "reservation": reservation,
                "client": client
            })
    
    return render_template(
        'admin/dashboard.html',
        total_managers=total_managers,
        total_cars=total_cars,
        total_clients=total_clients,
        total_reservations=total_reservations,
        pending_reservations=pending_reservations,
        recent_reservations=recent_reservations
    )

@admin_bp.route('/managers')
@login_required
@admin_required
def list_managers():
    managers = [User.from_document(user) for user in current_app.db.users.find({"role": "manager"})]
    return render_template('admin/managers/list.html', managers=managers)

@admin_bp.route('/managers/create', methods=['GET', 'POST'])
@login_required
@admin_required
def create_manager():
    if request.method == 'POST':
        # Récupération des données du formulaire
        username = request.form.get('username')
        email = request.form.get('email')
        password = request.form.get('password')
        
        # Vérifier si l'email est déjà utilisé
        existing_user = current_app.db.users.find_one({"email": email})
        if existing_user:
            flash('Cette adresse email est déjà utilisée', 'danger')
            return redirect(url_for('admin.create_manager'))
        
        # Créer un nouveau manager
        password_hash = User.hash_password(password)
        manager = User(
            username=username,
            email=email,
            role='manager',
            password_hash=password_hash
        )
        
        # Insertion dans la base de données
        current_app.db.users.insert_one(manager.to_document())
        
        flash('Manager créé avec succès', 'success')
        return redirect(url_for('admin.list_managers'))
    
    return render_template('admin/managers/create.html')

@admin_bp.route('/managers/<manager_id>/edit', methods=['GET', 'POST'])
@login_required
@admin_required
def edit_manager(manager_id):
    manager_data = current_app.db.users.find_one({"_id": manager_id, "role": "manager"})
    if not manager_data:
        flash('Manager introuvable', 'danger')
        return redirect(url_for('admin.list_managers'))
        
    manager = User.from_document(manager_data)
    
    if request.method == 'POST':
        # Récupération des données du formulaire
        username = request.form.get('username')
        email = request.form.get('email')
        new_password = request.form.get('new_password')
        
        # Vérifier si l'email est déjà utilisé par un autre utilisateur
        if email != manager.email:
            existing_user = current_app.db.users.find_one({"email": email, "_id": {"$ne": manager_id}})
            if existing_user:
                flash('Cette adresse email est déjà utilisée', 'danger')
                return redirect(url_for('admin.edit_manager', manager_id=manager_id))
        
        # Mettre à jour les informations
        manager.username = username
        manager.email = email
        manager.updated_at = datetime.utcnow()
        
        # Mettre à jour le mot de passe si fourni
        if new_password:
            manager.password_hash = User.hash_password(new_password)
        
        # Mise à jour dans la base de données
        current_app.db.users.update_one(
            {"_id": manager_id},
            {"$set": manager.to_document()}
        )
        
        flash('Manager modifié avec succès', 'success')
        return redirect(url_for('admin.list_managers'))
    
    return render_template('admin/managers/edit.html', manager=manager)

@admin_bp.route('/managers/<manager_id>/delete', methods=['POST'])
@login_required
@admin_required
def delete_manager(manager_id):
    # Vérifier si le manager existe
    manager = current_app.db.users.find_one({"_id": manager_id, "role": "manager"})
    if not manager:
        flash('Manager introuvable', 'danger')
        return redirect(url_for('admin.list_managers'))
    
    # Empêcher la suppression de son propre compte
    if manager_id == current_user._id:
        flash('Vous ne pouvez pas supprimer votre propre compte', 'danger')
        return redirect(url_for('admin.list_managers'))
    
    # Vérifier si le manager a des réservations associées
    has_reservations = current_app.db.reservations.find_one({"manager_id": manager_id})
    if has_reservations:
        flash('Ce manager a des réservations associées et ne peut pas être supprimé', 'warning')
        return redirect(url_for('admin.list_managers'))
    
    # Supprimer le manager
    current_app.db.users.delete_one({"_id": manager_id})
    
    flash('Manager supprimé avec succès', 'success')
    return redirect(url_for('admin.list_managers'))