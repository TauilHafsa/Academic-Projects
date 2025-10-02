from app import create_app
from app.models.user import User
import os

app = create_app()

# Create default admin during application startup
with app.app_context():
    admin_count = app.db.users.count_documents({"role": "admin"})
    if admin_count == 0:
        default_admin = User(
            username="admin",
            email="admin@carent.com",
            role="admin",
            password_hash=User.hash_password("admin123")
        )
        app.db.users.insert_one(default_admin.to_document())
        print("Compte administrateur par défaut créé!")

if __name__ == '__main__':
    port = int(os.environ.get('PORT', 5000))
    app.run(host='0.0.0.0', port=port, debug=True)