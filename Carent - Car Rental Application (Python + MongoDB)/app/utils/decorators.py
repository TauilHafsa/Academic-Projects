from functools import wraps
from flask import flash, redirect, url_for
from flask_login import current_user

def admin_required(f):
    """Décorateur pour restreindre l'accès aux administrateurs"""
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not current_user.role == 'admin':
            flash('Accès refusé. Vous devez être administrateur pour accéder à cette page.', 'danger')
            return redirect(url_for('car.list'))
        return f(*args, **kwargs)
    return decorated_function

def manager_required(f):
    """Décorateur pour restreindre l'accès aux managers"""
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not current_user.role == 'manager':
            flash('Accès refusé. Vous devez être manager pour accéder à cette page.', 'danger')
            return redirect(url_for('admin.dashboard'))
        return f(*args, **kwargs)
    return decorated_function