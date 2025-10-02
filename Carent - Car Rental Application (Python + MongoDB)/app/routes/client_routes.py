from flask import Blueprint, render_template, redirect, url_for, flash, request, current_app
from flask_login import login_required, current_user
from app.models.client import Client
from app.utils.decorators import manager_required
from datetime import datetime
from bson.objectid import ObjectId

client_bp = Blueprint('client', __name__, url_prefix='/clients')

@client_bp.route('/', methods=['GET'])
@login_required
@manager_required
def list():
    # Récupérer les paramètres de filtrage
    search_query = request.args.get('search', '')
    filter_by = request.args.get('filter_by', 'all')
    
    # Construire la requête MongoDB
    query = {}
    
    # Si une recherche est présente
    if search_query:
        if filter_by == 'all':
            # Recherche sur plusieurs champs
            query = {
                "$or": [
                    {"first_name": {"$regex": search_query, "$options": "i"}},
                    {"last_name": {"$regex": search_query, "$options": "i"}},
                    {"email": {"$regex": search_query, "$options": "i"}},
                    {"phone": {"$regex": search_query, "$options": "i"}},
                    {"address": {"$regex": search_query, "$options": "i"}},
                    {"license_number": {"$regex": search_query, "$options": "i"}}
                ]
            }
        elif filter_by == 'name':
            query = {
                "$or": [
                    {"first_name": {"$regex": search_query, "$options": "i"}},
                    {"last_name": {"$regex": search_query, "$options": "i"}}
                ]
            }
        else:
            # Recherche sur le champ spécifié
            query = {filter_by: {"$regex": search_query, "$options": "i"}}
    
    # Récupérer tous les clients correspondant à la requête
    clients = [Client.from_document(client) for client in current_app.db.clients.find(query)]
    
    # Pour chaque client, compter le nombre de réservations
    for client in clients:
        # Compter le nombre de réservations pour ce client
        client.reservations_count = current_app.db.reservations.count_documents({"client_id": client._id})
    
    return render_template('clients/list.html', 
                           clients=clients, 
                           search_query=search_query,
                           filter_by=filter_by)
                           
@client_bp.route('/create', methods=['GET', 'POST'])
@login_required
@manager_required
def create():
    if request.method == 'POST':
        # Récupération des données du formulaire
        first_name = request.form.get('first_name')
        last_name = request.form.get('last_name')
        email = request.form.get('email')
        phone = request.form.get('phone')
        address = request.form.get('address')
        license_number = request.form.get('license_number')
        birth_date = request.form.get('birth_date')
        
        # Convertir la date de naissance en objet datetime si fournie
        if birth_date:
            birth_date = datetime.strptime(birth_date, '%Y-%m-%d')
        
        # Vérifier si l'email est déjà utilisé
        existing_client = current_app.db.clients.find_one({"email": email})
        if existing_client:
            flash('Un client avec cette adresse email existe déjà', 'danger')
            return redirect(url_for('client.create'))
        
        # Création d'un nouveau client
        client = Client(
            first_name=first_name,
            last_name=last_name,
            email=email,
            phone=phone,
            address=address,
            license_number=license_number,
            birth_date=birth_date
        )
        
        # Insertion dans la base de données
        current_app.db.clients.insert_one(client.to_document())
        
        flash('Client ajouté avec succès', 'success')
        return redirect(url_for('client.list'))
    
    return render_template('clients/create.html')

@client_bp.route('/<client_id>/edit', methods=['GET', 'POST'])
@login_required
@manager_required
def edit(client_id):
    client_data = current_app.db.clients.find_one({"_id": client_id})
    if not client_data:
        flash('Client introuvable', 'danger')
        return redirect(url_for('client.list'))
        
    client = Client.from_document(client_data)
    
    if request.method == 'POST':
        # Récupération des données du formulaire
        client.first_name = request.form.get('first_name')
        client.last_name = request.form.get('last_name')
        client.email = request.form.get('email')
        client.phone = request.form.get('phone')
        client.address = request.form.get('address')
        client.license_number = request.form.get('license_number')
        
        birth_date = request.form.get('birth_date')
        if birth_date:
            client.birth_date = datetime.strptime(birth_date, '%Y-%m-%d')
        
        client.updated_at = datetime.utcnow()
        
        # Vérifier si l'email est déjà utilisé par un autre client
        existing_client = current_app.db.clients.find_one({
            "email": client.email,
            "_id": {"$ne": client_id}
        })
        
        if existing_client:
            flash('Un autre client utilise déjà cette adresse email', 'danger')
            return redirect(url_for('client.edit', client_id=client_id))
        
        # Mise à jour dans la base de données
        current_app.db.clients.update_one(
            {"_id": client_id},
            {"$set": client.to_document()}
        )
        
        flash('Client modifié avec succès', 'success')
        return redirect(url_for('client.list'))
    
    return render_template('clients/edit.html', client=client)

@client_bp.route('/<client_id>/delete', methods=['POST'])
@login_required
@manager_required
def delete(client_id):
    # Vérifier si le client existe
    client = current_app.db.clients.find_one({"_id": client_id})
    if not client:
        flash('Client introuvable', 'danger')
        return redirect(url_for('client.list'))
    
    # Vérifier si le client a des réservations
    has_reservations = current_app.db.reservations.find_one({
        "client_id": client_id,
        "status": {"$nin": ["rejected", "cancelled", "completed"]}
    })
    
    if has_reservations:
        flash('Impossible de supprimer ce client car il possède des réservations actives', 'danger')
        return redirect(url_for('client.list'))
    
    # Supprimer le client
    current_app.db.clients.delete_one({"_id": client_id})
    
    flash('Client supprimé avec succès', 'success')
    return redirect(url_for('client.list'))

@client_bp.route('/<client_id>/details')
@login_required
@manager_required
def details(client_id):
    client_data = current_app.db.clients.find_one({"_id": client_id})
    if not client_data:
        flash('Client introuvable', 'danger')
        return redirect(url_for('client.list'))
        
    client = Client.from_document(client_data)
    
    # Récupérer les réservations de ce client
    reservations = []
    for reservation_data in current_app.db.reservations.find({"client_id": client_id}):
        from app.models.reservation import Reservation
        reservation = Reservation.from_document(reservation_data)
        
        # Obtenir les informations de la voiture associée
        car_data = current_app.db.cars.find_one({"_id": reservation.car_id})
        if car_data:
            from app.models.car import Car
            car = Car.from_document(car_data)
            reservations.append({
                "reservation": reservation,
                "car": car
            })
    
    return render_template('clients/details.html', client=client, reservations=reservations)