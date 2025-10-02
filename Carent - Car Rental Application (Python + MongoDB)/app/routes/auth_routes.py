from flask import Blueprint, render_template, redirect, url_for, flash, request, current_app
from flask_login import login_user, logout_user, login_required, current_user
from app.models.user import User
from werkzeug.security import check_password_hash
from bson.objectid import ObjectId
from app.utils.decorators import admin_required
from datetime import datetime

auth_bp = Blueprint('auth', __name__, url_prefix='/auth')

@auth_bp.route('/login', methods=['GET', 'POST'])
def login():
    if current_user.is_authenticated:
        return redirect(url_for('index'))
        
    if request.method == 'POST':
        email = request.form.get('email')
        password = request.form.get('password')
        
        # Vérification des informations
        user_data = current_app.db.users.find_one({"email": email})
        if user_data and User.from_document(user_data).check_password(password):
            user = User.from_document(user_data)
            login_user(user)
            
            # Redirection en fonction du rôle
            if user.role == 'admin':
                return redirect(url_for('admin.dashboard'))
            else:
                return redirect(url_for('index'))
        else:
            flash('Adresse email ou mot de passe invalide', 'danger')
    
    return render_template('auth/login.html')

@auth_bp.route('/logout')
@login_required
def logout():
    logout_user()
    flash('Vous avez été déconnecté', 'success')
    return redirect(url_for('auth.login'))

@auth_bp.route('/profile', methods=['GET', 'POST'])
@login_required
def profile():
    if request.method == 'POST':
        username = request.form.get('username')
        email = request.form.get('email')
        
        # Vérifier si l'email est déjà utilisé par un autre utilisateur
        if email != current_user.email:
            existing_user = current_app.db.users.find_one({"email": email, "_id": {"$ne": current_user._id}})
            if existing_user:
                flash('Cette adresse email est déjà utilisée', 'danger')
                return redirect(url_for('auth.profile'))
        
        # Mise à jour des informations
        current_app.db.users.update_one(
            {"_id": current_user._id},
            {"$set": {
                "username": username,
                "email": email,
                "updated_at": datetime.utcnow()
            }}
        )
        
        flash('Profil mis à jour avec succès', 'success')
        return redirect(url_for('auth.profile'))
    
    return render_template('auth/profile.html')

@auth_bp.route('/change-password', methods=['GET', 'POST'])
@login_required
def change_password():
    if request.method == 'POST':
        current_password = request.form.get('current_password')
        new_password = request.form.get('new_password')
        confirm_password = request.form.get('confirm_password')
        
        # Vérifier le mot de passe actuel
        if not current_user.check_password(current_password):
            flash('Mot de passe actuel incorrect', 'danger')
            return redirect(url_for('auth.change_password'))
        
        # Vérifier que les nouveaux mots de passe correspondent
        if new_password != confirm_password:
            flash('Les nouveaux mots de passe ne correspondent pas', 'danger')
            return redirect(url_for('auth.change_password'))
        
        # Mettre à jour le mot de passe
        password_hash = User.hash_password(new_password)
        current_app.db.users.update_one(
            {"_id": current_user._id},
            {"$set": {
                "password_hash": password_hash,
                "updated_at": datetime.utcnow()
            }}
        )
        
        flash('Mot de passe modifié avec succès', 'success')
        return redirect(url_for('auth.profile'))
    
    return render_template('auth/change_password.html')