from pymongo import MongoClient
from bson.objectid import ObjectId
from datetime import datetime

# Connexion à MongoDB
client = MongoClient("mongodb://localhost:27017/")
db = client["carent_db"]
cars_collection = db["cars"]

# Liste des voitures à insérer
cars = [
    {
        "_id": str(ObjectId()),
        "brand": "Renault",
        "model": "Clio",
        "year": 2022,
        "registration": "ABC-123",
        "daily_rate": 45,
        "available": True,
        "image": "https://cdn.group.renault.com/ren/master/renault-new-clio.jpg",
        "category": "économique",
        "fuel_type": "essence",
        "seats": 5,
        "created_at": datetime.utcnow(),
        "updated_at": datetime.utcnow()
    },
    {
        "_id": str(ObjectId()),
        "brand": "Peugeot",
        "model": "208",
        "year": 2021,
        "registration": "DEF-456",
        "daily_rate": 50,
        "available": True,
        "image": "https://cdn.peugeot.com/peugeot-208.jpg",
        "category": "standard",
        "fuel_type": "diesel",
        "seats": 5,
        "created_at": datetime.utcnow(),
        "updated_at": datetime.utcnow()
    },
    {
        "_id": str(ObjectId()),
        "brand": "Dacia",
        "model": "Duster",
        "year": 2023,
        "registration": "GHI-789",
        "daily_rate": 60,
        "available": True,
        "image": "https://cdn.dacia.com/dacia-duster.jpg",
        "category": "SUV",
        "fuel_type": "essence",
        "seats": 5,
        "created_at": datetime.utcnow(),
        "updated_at": datetime.utcnow()
    }
]

# Insertion dans la base de données
cars_collection.insert_many(cars)
print(f"{len(cars)} voitures insérées avec succès.")
