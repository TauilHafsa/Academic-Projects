from datetime import datetime
from bson import ObjectId

class Client:
    """Modèle pour les clients"""
    
    def __init__(self, first_name, last_name, email, phone, address=None, 
                 license_number=None, birth_date=None, _id=None):
        self._id = _id if _id else str(ObjectId())
        self.first_name = first_name
        self.last_name = last_name
        self.email = email
        self.phone = phone
        self.address = address
        self.license_number = license_number  # Numéro de permis de conduire
        self.birth_date = birth_date  # Date de naissance
        self.created_at = datetime.utcnow()
        self.updated_at = datetime.utcnow()
    
    def to_document(self):
        """Convertit l'objet en document MongoDB"""
        return {
            "_id": self._id,
            "first_name": self.first_name,
            "last_name": self.last_name,
            "email": self.email,
            "phone": self.phone,
            "address": self.address,
            "license_number": self.license_number,
            "birth_date": self.birth_date,
            "created_at": self.created_at,
            "updated_at": self.updated_at
        }
    
    @classmethod
    def from_document(cls, document):
        """Crée une instance à partir d'un document MongoDB"""
        if not document:
            return None
            
        client = cls(
            first_name=document["first_name"],
            last_name=document["last_name"],
            email=document["email"],
            phone=document["phone"],
            address=document.get("address"),
            license_number=document.get("license_number"),
            birth_date=document.get("birth_date"),
            _id=document["_id"]
        )
        client.created_at = document.get("created_at", datetime.utcnow())
        client.updated_at = document.get("updated_at", datetime.utcnow())
        return client