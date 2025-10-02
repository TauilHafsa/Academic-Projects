from flask import Blueprint, render_template, redirect, url_for, flash, request, current_app
from flask_login import login_required, current_user
from app.models.car import Car
from app.utils.decorators import manager_required
from datetime import datetime
from bson.objectid import ObjectId

car_bp = Blueprint('car', __name__, url_prefix='/cars')

@car_bp.route('/')
@login_required
@manager_required
def list():
    # Récupération des paramètres de filtrage
    brand = request.args.get('brand', '')
    model = request.args.get('model', '')
    category = request.args.get('category', '')
    fuel_type = request.args.get('fuel_type', '')
    min_price = request.args.get('min_price', '')
    max_price = request.args.get('max_price', '')
    seats = request.args.get('seats', '')
    
    # Construction de la requête MongoDB
    query = {}
    
    if brand:
        query['brand'] = {'$regex': brand, '$options': 'i'}
    if model:
        query['model'] = {'$regex': model, '$options': 'i'}
    if category and category != 'all':
        query['category'] = category
    if fuel_type and fuel_type != 'all':
        query['fuel_type'] = fuel_type
    if min_price and min_price.isdigit():
        if 'daily_rate' not in query:
            query['daily_rate'] = {}
        query['daily_rate']['$gte'] = float(min_price)
    if max_price and max_price.isdigit():
        if 'daily_rate' not in query:
            query['daily_rate'] = {}
        query['daily_rate']['$lte'] = float(max_price)
    if seats and seats.isdigit():
        query['seats'] = int(seats)
    
    # Exécution de la requête
    cars = [Car.from_document(car) for car in current_app.db.cars.find(query)]
    
    # Récupération de toutes les valeurs distinctes pour les filtres
    all_brands = current_app.db.cars.distinct('brand')
    all_categories = current_app.db.cars.distinct('category')
    all_fuel_types = current_app.db.cars.distinct('fuel_type')
    all_seats = sorted(current_app.db.cars.distinct('seats'))
    
    return render_template('cars/list.html', 
                           cars=cars, 
                           all_brands=all_brands,
                           all_categories=all_categories,
                           all_fuel_types=all_fuel_types,
                           all_seats=all_seats,
                           filter_brand=brand,
                           filter_model=model,
                           filter_category=category,
                           filter_fuel_type=fuel_type,
                           filter_min_price=min_price,
                           filter_max_price=max_price,
                           filter_seats=seats)

@car_bp.route('/available')
@login_required
@manager_required
def available():
    # Récupération des paramètres de filtrage
    brand = request.args.get('brand', '')
    model = request.args.get('model', '')
    category = request.args.get('category', '')
    fuel_type = request.args.get('fuel_type', '')
    min_price = request.args.get('min_price', '')
    max_price = request.args.get('max_price', '')
    seats = request.args.get('seats', '')
    
    # Construction de la requête MongoDB
    query = {"available": True}
    
    if brand:
        query['brand'] = {'$regex': brand, '$options': 'i'}
    if model:
        query['model'] = {'$regex': model, '$options': 'i'}
    if category and category != 'all':
        query['category'] = category
    if fuel_type and fuel_type != 'all':
        query['fuel_type'] = fuel_type
    if min_price and min_price.isdigit():
        if 'daily_rate' not in query:
            query['daily_rate'] = {}
        query['daily_rate']['$gte'] = float(min_price)
    if max_price and max_price.isdigit():
        if 'daily_rate' not in query:
            query['daily_rate'] = {}
        query['daily_rate']['$lte'] = float(max_price)
    if seats and seats.isdigit():
        query['seats'] = int(seats)
    
    # Exécution de la requête
    available_cars = [Car.from_document(car) for car in current_app.db.cars.find(query)]
    
    # Récupération de toutes les valeurs distinctes pour les filtres
    all_brands = current_app.db.cars.distinct('brand')
    all_categories = current_app.db.cars.distinct('category')
    all_fuel_types = current_app.db.cars.distinct('fuel_type')
    all_seats = sorted(current_app.db.cars.distinct('seats'))
    
    return render_template('cars/available.html', 
                           cars=available_cars,
                           all_brands=all_brands,
                           all_categories=all_categories,
                           all_fuel_types=all_fuel_types,
                           all_seats=all_seats,
                           filter_brand=brand,
                           filter_model=model,
                           filter_category=category,
                           filter_fuel_type=fuel_type,
                           filter_min_price=min_price,
                           filter_max_price=max_price,
                           filter_seats=seats)

# Le reste du code reste inchangé
@car_bp.route('/create', methods=['GET', 'POST'])
@login_required
@manager_required
def create():
    if request.method == 'POST':
        # Récupération des données du formulaire
        brand = request.form.get('brand')
        model = request.form.get('model')
        year = int(request.form.get('year'))
        registration = request.form.get('registration')
        daily_rate = float(request.form.get('daily_rate'))
        category = request.form.get('category')
        fuel_type = request.form.get('fuel_type')
        seats = int(request.form.get('seats'))
        image = request.form.get('image')
        
        # Création d'une nouvelle voiture
        car = Car(
            brand=brand,
            model=model,
            year=year,
            registration=registration,
            daily_rate=daily_rate,
            category=category,
            fuel_type=fuel_type,
            seats=seats,
            image=image
        )
        
        # Insertion dans la base de données
        current_app.db.cars.insert_one(car.to_document())
        
        flash('Voiture ajoutée avec succès', 'success')
        return redirect(url_for('car.list'))
    
    return render_template('cars/create.html')

@car_bp.route('/<car_id>/edit', methods=['GET', 'POST'])
@login_required
@manager_required
def edit(car_id):
    car_data = current_app.db.cars.find_one({"_id": car_id})
    if not car_data:
        flash('Voiture introuvable', 'danger')
        return redirect(url_for('car.list'))
        
    car = Car.from_document(car_data)
    
    if request.method == 'POST':
        # Récupération des données du formulaire
        car.brand = request.form.get('brand')
        car.model = request.form.get('model')
        car.year = int(request.form.get('year'))
        car.registration = request.form.get('registration')
        car.daily_rate = float(request.form.get('daily_rate'))
        car.category = request.form.get('category')
        car.fuel_type = request.form.get('fuel_type')
        car.seats = int(request.form.get('seats'))
        car.available = 'available' in request.form
        car.image = request.form.get('image')
        car.updated_at = datetime.utcnow()
        
        # Mise à jour dans la base de données
        current_app.db.cars.update_one(
            {"_id": car_id},
            {"$set": car.to_document()}
        )
        
        flash('Voiture modifiée avec succès', 'success')
        return redirect(url_for('car.list'))
    
    return render_template('cars/edit.html', car=car)

@car_bp.route('/<car_id>/delete', methods=['POST'])
@login_required
@manager_required
def delete(car_id):
    # Vérifier si la voiture existe
    car = current_app.db.cars.find_one({"_id": car_id})
    if not car:
        flash('Voiture introuvable', 'danger')
        return redirect(url_for('car.list'))
    
    # Vérifier si la voiture a des réservations
    has_reservations = current_app.db.reservations.find_one({
        "car_id": car_id,
        "status": {"$nin": ["rejected", "cancelled"]}
    })
    
    if has_reservations:
        flash('Impossible de supprimer cette voiture car elle possède des réservations actives', 'danger')
        return redirect(url_for('car.list'))
    
    # Supprimer la voiture
    current_app.db.cars.delete_one({"_id": car_id})
    
    flash('Voiture supprimée avec succès', 'success')
    return redirect(url_for('car.list'))

@car_bp.route('/<car_id>/details')
@login_required
@manager_required
def details(car_id):
    car_data = current_app.db.cars.find_one({"_id": car_id})
    if not car_data:
        flash('Voiture introuvable', 'danger')
        return redirect(url_for('car.list'))
        
    car = Car.from_document(car_data)
    
    # Récupérer les réservations actives pour cette voiture
    reservations = [r for r in current_app.db.reservations.find({
    "car_id": car_id,
    "status": {"$in": ["pending", "approved"]}
})]
    
    return render_template('cars/details.html', car=car, reservations=reservations)