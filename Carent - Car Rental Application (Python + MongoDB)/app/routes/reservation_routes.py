from flask import Blueprint, render_template, redirect, url_for, flash, request, current_app
from flask_login import login_required, current_user
from app.models.reservation import Reservation
from app.models.car import Car
from app.models.client import Client
from app.utils.decorators import manager_required
from datetime import datetime, timedelta
from bson.objectid import ObjectId

reservation_bp = Blueprint('reservation', __name__, url_prefix='/reservations')

@reservation_bp.route('/')
@login_required
@manager_required
def list():
    # Récupérer les paramètres de filtre
    client_search = request.args.get('client_search', '')
    car_search = request.args.get('car_search', '')
    status_filter = request.args.get('status', '')
    date_from = request.args.get('date_from', '')
    date_to = request.args.get('date_to', '')
    
    # Préparer le filtre MongoDB
    query = {}
    
    # Construction des filtres MongoDB
    if status_filter:
        query["status"] = status_filter
    
    # Convertir les dates si elles sont fournies
    start_date_query = {}
    if date_from:
        try:
            start_date_from = datetime.strptime(date_from, '%Y-%m-%d')
            start_date_query["$gte"] = start_date_from
        except ValueError:
            flash("Format de date invalide pour 'date de'", 'warning')
    
    if date_to:
        try:
            start_date_to = datetime.strptime(date_to, '%Y-%m-%d')
            start_date_query["$lte"] = start_date_to
        except ValueError:
            flash("Format de date invalide pour 'date à'", 'warning')
    
    if start_date_query:
        query["start_date"] = start_date_query
    
    # Filtrage pour client et voiture sera fait en mémoire après la requête initiale
    # car nous avons besoin de chercher dans des collections liées
    
    # Récupérer toutes les réservations selon les filtres de base
    reservation_cursor = current_app.db.reservations.find(query).sort("created_at", -1)
    
    # Pré-filtrage des IDs de client et voiture si nécessaire
    client_ids = []
    car_ids = []
    
    if client_search:
        # Recherche de clients correspondants
        client_filter = {
            "$or": [
                {"first_name": {"$regex": client_search, "$options": "i"}},
                {"last_name": {"$regex": client_search, "$options": "i"}},
                {"email": {"$regex": client_search, "$options": "i"}},
                {"phone": {"$regex": client_search, "$options": "i"}}
            ]
        }
        clients = current_app.db.clients.find(client_filter, {"_id": 1})
        client_ids = [client["_id"] for client in clients]
        
        if not client_ids:
            # Si aucun client ne correspond, retourner une liste vide
            return render_template('reservations/list.html', 
                                  reservations=[], 
                                  pending_count=0, 
                                  now=datetime.now())
    
    if car_search:
        # Recherche de voitures correspondantes
        car_filter = {
            "$or": [
                {"brand": {"$regex": car_search, "$options": "i"}},
                {"model": {"$regex": car_search, "$options": "i"}},
                {"registration": {"$regex": car_search, "$options": "i"}}
            ]
        }
        cars = current_app.db.cars.find(car_filter, {"_id": 1})
        car_ids = [car["_id"] for car in cars]
        
        if not car_ids:
            # Si aucune voiture ne correspond, retourner une liste vide
            return render_template('reservations/list.html', 
                                  reservations=[], 
                                  pending_count=0, 
                                  now=datetime.now())
    
    # Assembler les réservations avec leurs données liées
    reservations = []
    
    for reservation_data in reservation_cursor:
        reservation = Reservation.from_document(reservation_data)
        
        # Filtrer par IDs de client si nécessaire
        if client_ids and reservation.client_id not in client_ids:
            continue
            
        # Filtrer par IDs de voiture si nécessaire
        if car_ids and reservation.car_id not in car_ids:
            continue
        
        # Obtenir les informations du client
        client_data = current_app.db.clients.find_one({"_id": reservation.client_id})
        client = Client.from_document(client_data) if client_data else None
        
        # Obtenir les informations de la voiture
        car_data = current_app.db.cars.find_one({"_id": reservation.car_id})
        car = Car.from_document(car_data) if car_data else None
        
        reservations.append({
            "reservation": reservation,
            "client": client,
            "car": car
        })
    
    # Compter les réservations en attente pour l'affichage du badge
    pending_count = sum(1 for r in reservations if r["reservation"].status == Reservation.STATUS_PENDING)
    
    # Passer la date actuelle pour les vérifications de boutons dans le template
    now = datetime.now()
    
    return render_template('reservations/list.html', 
                          reservations=reservations, 
                          pending_count=pending_count, 
                          now=datetime.now(),
                          request=request)  # Passer l'objet request pour accéder aux arguments dans le template

@reservation_bp.route('/create', methods=['GET', 'POST'])
@login_required
@manager_required
def create():
    # Si une voiture est sélectionnée depuis la page des voitures disponibles
    selected_car = None
    car_id = request.args.get('car_id')
    if car_id:
        car_data = current_app.db.cars.find_one({"_id": car_id, "available": True})
        if car_data:
            selected_car = Car.from_document(car_data)
    
    # Récupérer la liste des clients et des voitures disponibles
    clients = [Client.from_document(client) for client in current_app.db.clients.find()]
    available_cars = [Car.from_document(car) for car in current_app.db.cars.find({"available": True})]
    
    if request.method == 'POST':
        # Récupération des données du formulaire
        car_id = request.form.get('car_id')
        client_id = request.form.get('client_id')
        start_date = datetime.strptime(request.form.get('start_date'), '%Y-%m-%d')
        end_date = datetime.strptime(request.form.get('end_date'), '%Y-%m-%d')
        notes = request.form.get('notes', '')
        
        # Vérifier si la voiture est disponible
        car_data = current_app.db.cars.find_one({"_id": car_id, "available": True})
        if not car_data:
            flash('La voiture sélectionnée n\'est pas disponible', 'danger')
            return redirect(url_for('reservation.create'))
        
        # Vérifier si les dates sont valides
        if start_date >= end_date:
            flash('La date de fin doit être postérieure à la date de début', 'danger')
            return redirect(url_for('reservation.create'))
        
        if start_date < datetime.now().replace(hour=0, minute=0, second=0, microsecond=0):
            flash('La date de début ne peut pas être dans le passé', 'danger')
            return redirect(url_for('reservation.create'))
        
        # Vérifier les chevauchements de réservation pour cette voiture
        overlapping = current_app.db.reservations.find_one({
            "car_id": car_id,
            "status": {"$in": [Reservation.STATUS_PENDING, Reservation.STATUS_APPROVED]},
            "$or": [
                {"start_date": {"$lte": end_date}, "end_date": {"$gte": start_date}},
                {"start_date": {"$gte": start_date, "$lte": end_date}},
                {"end_date": {"$gte": start_date, "$lte": end_date}}
            ]
        })
        
        if overlapping:
            flash('Cette voiture est déjà réservée pour cette période', 'danger')
            return redirect(url_for('reservation.create'))
        
        # Calculer le prix total
        car = Car.from_document(car_data)
        days = (end_date - start_date).days + 1
        total_price = car.daily_rate * days
        
        # Créer la réservation
        reservation = Reservation(
            car_id=car_id,
            client_id=client_id,
            start_date=start_date,
            end_date=end_date,
            total_price=total_price,
            notes=notes
        )
        
        # Ajouter les notes si fournies
        if notes:
            reservation.notes = notes
        
        # Insérer dans la base de données
        current_app.db.reservations.insert_one(reservation.to_document())
        
        flash('Réservation créée avec succès', 'success')
        return redirect(url_for('reservation.list'))
    
    return render_template('reservations/create.html', clients=clients, available_cars=available_cars, selected_car=selected_car)

@reservation_bp.route('/<reservation_id>/edit', methods=['GET', 'POST'])
@login_required
@manager_required
def edit(reservation_id):
    # Récupérer la réservation
    reservation_data = current_app.db.reservations.find_one({"_id": reservation_id})
    if not reservation_data:
        flash('Réservation introuvable', 'danger')
        return redirect(url_for('reservation.list'))
    
    reservation = Reservation.from_document(reservation_data)
    
    # Si la réservation n'est plus en attente ou approuvée, empêcher la modification
    if reservation.status not in [Reservation.STATUS_PENDING, Reservation.STATUS_APPROVED]:
        flash('Cette réservation ne peut plus être modifiée', 'danger')
        return redirect(url_for('reservation.list'))
    
    # Récupérer les informations client et voiture
    client_data = current_app.db.clients.find_one({"_id": reservation.client_id})
    client = Client.from_document(client_data) if client_data else None
    
    car_data = current_app.db.cars.find_one({"_id": reservation.car_id})
    car = Car.from_document(car_data) if car_data else None
    
    if request.method == 'POST':
        # Récupération des données du formulaire
        start_date = datetime.strptime(request.form.get('start_date'), '%Y-%m-%d')
        end_date = datetime.strptime(request.form.get('end_date'), '%Y-%m-%d')
        
        # Vérifier si les dates sont valides
        if start_date >= end_date:
            flash('La date de fin doit être postérieure à la date de début', 'danger')
            return redirect(url_for('reservation.edit', reservation_id=reservation_id))
        
        if start_date < datetime.now().replace(hour=0, minute=0, second=0, microsecond=0):
            flash('La date de début ne peut pas être dans le passé', 'danger')
            return redirect(url_for('reservation.edit', reservation_id=reservation_id))
        
        # Vérifier les chevauchements de réservation pour cette voiture
        overlapping = current_app.db.reservations.find_one({
            "car_id": reservation.car_id,
            "_id": {"$ne": reservation_id},
            "status": {"$in": [Reservation.STATUS_PENDING, Reservation.STATUS_APPROVED]},
            "$or": [
                {"start_date": {"$lte": end_date}, "end_date": {"$gte": start_date}},
                {"start_date": {"$gte": start_date, "$lte": end_date}},
                {"end_date": {"$gte": start_date, "$lte": end_date}}
            ]
        })
        
        if overlapping:
            flash('Cette voiture est déjà réservée pour cette période', 'danger')
            return redirect(url_for('reservation.edit', reservation_id=reservation_id))
        
        # Mettre à jour les dates et recalculer le prix
        reservation.start_date = start_date
        reservation.end_date = end_date
        
        days = (end_date - start_date).days + 1
        reservation.total_price = car.daily_rate * days
        reservation.updated_at = datetime.utcnow()
        
        # Mettre à jour dans la base de données
        current_app.db.reservations.update_one(
            {"_id": reservation_id},
            {"$set": reservation.to_document()}
        )
        
        flash('Réservation modifiée avec succès', 'success')
        return redirect(url_for('reservation.list'))
    
    # Passer la date actuelle pour les vérifications dans le template
    now = datetime.now()
    
    return render_template(
        'reservations/edit.html', 
        reservation=reservation,
        client=client,
        car=car,
        now=datetime.now()
    )

@reservation_bp.route('/<reservation_id>/approve', methods=['POST'])
@login_required
@manager_required
def approve(reservation_id):
    # Récupérer la réservation
    reservation_data = current_app.db.reservations.find_one({"_id": reservation_id})
    if not reservation_data:
        flash('Réservation introuvable', 'danger')
        return redirect(url_for('reservation.list'))
    
    reservation = Reservation.from_document(reservation_data)
    
    # Vérifier si la réservation est en attente
    if reservation.status != Reservation.STATUS_PENDING:
        flash('Cette réservation ne peut pas être approuvée', 'danger')
        return redirect(url_for('reservation.list'))
    
    # Approuver la réservation
    reservation.status = Reservation.STATUS_APPROVED
    reservation.manager_id = current_user._id
    reservation.updated_at = datetime.utcnow()
    
    # Mettre à jour dans la base de données
    current_app.db.reservations.update_one(
        {"_id": reservation_id},
        {"$set": reservation.to_document()}
    )
    
    # Mettre à jour la disponibilité de la voiture si la réservation commence aujourd'hui
    if reservation.start_date <= datetime.now().replace(hour=0, minute=0, second=0, microsecond=0):
        current_app.db.cars.update_one(
            {"_id": reservation.car_id},
            {"$set": {"available": False}}
        )
    
    flash('Réservation approuvée avec succès', 'success')
    return redirect(url_for('reservation.list'))

@reservation_bp.route('/<reservation_id>/reject', methods=['POST'])
@login_required
@manager_required
def reject(reservation_id):
    # Récupérer la réservation
    reservation_data = current_app.db.reservations.find_one({"_id": reservation_id})
    if not reservation_data:
        flash('Réservation introuvable', 'danger')
        return redirect(url_for('reservation.list'))
    
    reservation = Reservation.from_document(reservation_data)
    
    # Vérifier si la réservation est en attente
    if reservation.status != Reservation.STATUS_PENDING:
        flash('Cette réservation ne peut pas être rejetée', 'danger')
        return redirect(url_for('reservation.list'))
    
    # Rejeter la réservation
    reservation.status = Reservation.STATUS_REJECTED
    reservation.manager_id = current_user._id
    reservation.updated_at = datetime.utcnow()
    
    # Mettre à jour dans la base de données
    current_app.db.reservations.update_one(
        {"_id": reservation_id},
        {"$set": reservation.to_document()}
    )
    
    flash('Réservation rejetée', 'success')
    return redirect(url_for('reservation.list'))

@reservation_bp.route('/<reservation_id>/cancel', methods=['POST'])
@login_required
@manager_required
def cancel(reservation_id):
    # Récupérer la réservation
    reservation_data = current_app.db.reservations.find_one({"_id": reservation_id})
    if not reservation_data:
        flash('Réservation introuvable', 'danger')
        return redirect(url_for('reservation.list'))
    
    reservation = Reservation.from_document(reservation_data)
    
    # Vérifier si la réservation peut être annulée
    if reservation.status not in [Reservation.STATUS_PENDING, Reservation.STATUS_APPROVED]:
        flash('Cette réservation ne peut pas être annulée', 'danger')
        return redirect(url_for('reservation.list'))
    
    # Stocker le statut original avant de le modifier
    original_status = reservation.status
    
    # Annuler la réservation
    reservation.status = Reservation.STATUS_CANCELLED
    reservation.manager_id = current_user._id
    reservation.updated_at = datetime.utcnow()
    
    # Mettre à jour dans la base de données
    current_app.db.reservations.update_one(
        {"_id": reservation_id},
        {"$set": reservation.to_document()}
    )
    
    # Remettre la voiture comme disponible si nécessaire
    if original_status == Reservation.STATUS_APPROVED:
        current_app.db.cars.update_one(
            {"_id": reservation.car_id},
            {"$set": {"available": True}}
        )
    
    flash('Réservation annulée', 'success')
    return redirect(url_for('reservation.list'))

@reservation_bp.route('/<reservation_id>/complete', methods=['POST'])
@login_required
@manager_required
def complete(reservation_id):
    # Récupérer la réservation
    reservation_data = current_app.db.reservations.find_one({"_id": reservation_id})
    if not reservation_data:
        flash('Réservation introuvable', 'danger')
        return redirect(url_for('reservation.list'))
    
    reservation = Reservation.from_document(reservation_data)
    
    # Vérifier si la réservation est approuvée
    if reservation.status != Reservation.STATUS_APPROVED:
        flash('Cette réservation ne peut pas être terminée', 'danger')
        return redirect(url_for('reservation.list'))
    
    # Vérifier si la date de fin est passée
    if reservation.end_date > datetime.now().replace(hour=0, minute=0, second=0, microsecond=0):
        flash('Cette réservation n\'est pas encore terminée', 'danger')
        return redirect(url_for('reservation.list'))
    
    # Marquer la réservation comme terminée
    reservation.status = Reservation.STATUS_COMPLETED
    reservation.updated_at = datetime.utcnow()
    
    # Mettre à jour dans la base de données
    current_app.db.reservations.update_one(
        {"_id": reservation_id},
        {"$set": reservation.to_document()}
    )
    
    # Remettre la voiture comme disponible
    current_app.db.cars.update_one(
        {"_id": reservation.car_id},
        {"$set": {"available": True}}
    )
    
    flash('Réservation marquée comme terminée', 'success')
    return redirect(url_for('reservation.list'))

@reservation_bp.route('/<reservation_id>/details')
@login_required
@manager_required
def details(reservation_id):
    # Récupérer la réservation
    reservation_data = current_app.db.reservations.find_one({"_id": reservation_id})
    if not reservation_data:
        flash('Réservation introuvable', 'danger')
        return redirect(url_for('reservation.list'))
    
    reservation = Reservation.from_document(reservation_data)
    
    # Récupérer les informations client et voiture
    client_data = current_app.db.clients.find_one({"_id": reservation.client_id})
    client = Client.from_document(client_data) if client_data else None
    
    car_data = current_app.db.cars.find_one({"_id": reservation.car_id})
    car = Car.from_document(car_data) if car_data else None
    
    # Récupérer les informations du manager si disponible
    manager = None
    if reservation.manager_id:
        manager_data = current_app.db.users.find_one({"_id": reservation.manager_id})
        if manager_data:
            from app.models.user import User
            manager = User.from_document(manager_data)
    
    # Passer la date actuelle pour les vérifications dans le template
    now = datetime.now()
    
    return render_template(
        'reservations/details.html',
        reservation=reservation,
        client=client,
        car=car,
        manager=manager,
        now=now
    )

@reservation_bp.route('/<reservation_id>/delete', methods=['POST'])
@login_required
@manager_required
def delete(reservation_id):
    # Vérifier si la réservation existe
    reservation_data = current_app.db.reservations.find_one({"_id": reservation_id})
    if not reservation_data:
        flash('Réservation introuvable', 'danger')
        return redirect(url_for('reservation.list'))
    
    # Supprimer la réservation
    current_app.db.reservations.delete_one({"_id": reservation_id})
    
    flash('Réservation supprimée avec succès', 'success')
    return redirect(url_for('reservation.list'))