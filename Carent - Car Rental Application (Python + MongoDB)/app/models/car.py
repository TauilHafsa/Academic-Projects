from datetime import datetime
from bson import ObjectId

class Car:
    """Modèle pour les voitures disponibles à la location"""
    
    def __init__(self, brand, model, year, registration, daily_rate, available=True, 
                 image=None, category="standard", fuel_type="essence", seats=5, _id=None):
        self._id = _id if _id else str(ObjectId())
        self.brand = brand
        self.model = model
        self.year = year
        self.registration = registration  # Plaque d'immatriculation
        self.daily_rate = daily_rate  # Tarif journalier
        self.available = available  # Disponibilité
        self.image = image  # URL de l'image
        self.category = category  # Catégorie: économique, standard, luxe, SUV, etc.
        self.fuel_type = fuel_type  # Type de carburant
        self.seats = seats  # Nombre de places
        self.created_at = datetime.utcnow()
        self.updated_at = datetime.utcnow()
    
    def to_document(self):
        """Convertit l'objet en document MongoDB"""
        return {
            "_id": self._id,
            "brand": self.brand,
            "model": self.model,
            "year": self.year,
            "registration": self.registration,
            "daily_rate": self.daily_rate,
            "available": self.available,
            "image": self.image,
            "category": self.category,
            "fuel_type": self.fuel_type,
            "seats": self.seats,
            "created_at": self.created_at,
            "updated_at": self.updated_at
        }
    
    @classmethod
    def from_document(cls, document):
        """Crée une instance à partir d'un document MongoDB"""
        if not document:
            return None
            
        car = cls(
            brand=document["brand"],
            model=document["model"],
            year=document["year"],
            registration=document["registration"],
            daily_rate=document["daily_rate"],
            available=document.get("available", True),
            image=document.get("image"),
            category=document.get("category", "standard"),
            fuel_type=document.get("fuel_type", "essence"),
            seats=document.get("seats", 5),
            _id=document["_id"]
        )
        car.created_at = document.get("created_at", datetime.utcnow())
        car.updated_at = document.get("updated_at", datetime.utcnow())
        return car