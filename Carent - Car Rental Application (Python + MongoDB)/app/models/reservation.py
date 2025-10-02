from datetime import datetime
from bson import ObjectId

class Reservation:
    """Modèle pour les réservations de voitures"""
    
    STATUS_PENDING = "pending"
    STATUS_APPROVED = "approved"
    STATUS_REJECTED = "rejected"
    STATUS_CANCELLED = "cancelled"
    STATUS_COMPLETED = "completed"
    
    def __init__(self, car_id, client_id, start_date, end_date, 
                 status=STATUS_PENDING, total_price=0, manager_id=None, notes="",  _id=None):
        self._id = _id if _id else str(ObjectId())
        self.car_id = car_id
        self.client_id = client_id
        self.start_date = start_date  # Date de début de location
        self.end_date = end_date      # Date de fin de location
        self.status = status          # État de la réservation
        self.total_price = total_price  # Prix total de la location
        self.manager_id = manager_id  # Manager qui a traité la réservation
        self.notes = notes # Notes supplémentaires sur la réservation
        self.created_at = datetime.utcnow()
        self.updated_at = datetime.utcnow()
    
    def to_document(self):
        """Convertit l'objet en document MongoDB"""
        return {
            "_id": self._id,
            "car_id": self.car_id,
            "client_id": self.client_id,
            "start_date": self.start_date,
            "end_date": self.end_date,
            "status": self.status,
            "total_price": self.total_price,
            "manager_id": self.manager_id,
            "notes": self.notes,
            "created_at": self.created_at,
            "updated_at": self.updated_at
        }
    
    @classmethod
    def from_document(cls, document):
        """Crée une instance à partir d'un document MongoDB"""
        if not document:
            return None
            
        reservation = cls(
            car_id=document["car_id"],
            client_id=document["client_id"],
            start_date=document["start_date"],
            end_date=document["end_date"],
            status=document.get("status", cls.STATUS_PENDING),
            total_price=document.get("total_price", 0),
            manager_id=document.get("manager_id"),
            notes=document.get("notes", ""),
            _id=document["_id"]
        )
        reservation.created_at = document.get("created_at", datetime.utcnow())
        reservation.updated_at = document.get("updated_at", datetime.utcnow())
        return reservation