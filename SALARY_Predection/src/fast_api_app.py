from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import pandas as pd
import joblib
import numpy as np
import os

app = FastAPI(title="API de prédiction de revenu annuel")

# Définir la variable model comme global
model = None

# Tenter de charger le modèle de différents emplacements
MODEL_PATHS = [
    "simple_model.joblib",   # Nouveau modèle simplifié
    "../model/simple_model.joblib",
    "./simple_model.joblib", # Chemin alternatif
    "best_model.joblib",     # Ancien modèle (si réussi)
    "../model/best_model.joblib", 
    "./model/best_model.joblib",
    "model/best_model.joblib",
]

# Essayer chaque chemin jusqu'à trouver le modèle
for path in MODEL_PATHS:
    if os.path.exists(path):
        try:
            model = joblib.load(path)
            print(f"Modèle chargé avec succès depuis: {path}")
            break
        except Exception as e:
            print(f"Erreur lors du chargement du modèle depuis {path}: {e}")

# Vérifier si le modèle a été chargé
if model is None:
    print("ERREUR CRITIQUE: Impossible de charger le modèle. L'API ne fonctionnera pas correctement.")

class SalaryInput(BaseModel):
    milieu: str
    sexe: str
    age: int
    categorie_age: str
    niveau_education: str
    annees_experience: float
    etat_matrimonial: str
    categorie_socioprofessionnelle: str
    possession_voiture: bool
    possession_logement: bool
    possession_terrain: bool
    personnes_a_charge: int
    secteur_activite: str
    acces_services_financiers: str

    class Config:
        json_schema_extra = {
            "example": {
                "milieu": "Urbain",
                "sexe": "Homme",
                "age": 35,
                "categorie_age": "Adulte",
                "niveau_education": "Supérieur",
                "annees_experience": 10.0,
                "etat_matrimonial": "Marié",
                "categorie_socioprofessionnelle": "Groupe 2",
                "possession_voiture": True,
                "possession_logement": False,
                "possession_terrain": True,
                "personnes_a_charge": 2,
                "secteur_activite": "Privé formel",
                "acces_services_financiers": "Basique"
            }
        }

@app.get("/")
def read_root():
    return {"message": "API de prédiction de revenu annuel", 
            "documentation": "/docs",
            "statut_modele": "Chargé" if model is not None else "Non chargé"}

@app.get("/health")
def health_check():
    """Endpoint pour vérifier l'état de l'API et du modèle"""
    status = "healthy" if model is not None else "unhealthy"
    return {
        "status": status,
        "model_loaded": model is not None
    }

@app.post("/predict")
def predict_salary(data: SalaryInput):
    # Vérifier si le modèle est chargé
    if model is None:
        raise HTTPException(
            status_code=500, 
            detail="Le modèle n'est pas chargé. Vérifiez les logs de démarrage du serveur."
        )
    
    try:
        # Conversion des booléens en entiers (0/1) comme dans les données d'entraînement
        possession_voiture = 1 if data.possession_voiture else 0
        possession_logement = 1 if data.possession_logement else 0
        possession_terrain = 1 if data.possession_terrain else 0
        
        # Créer un DataFrame avec une seule ligne (comme attendu par le modèle)
        input_df = pd.DataFrame({
            "milieu": [data.milieu],
            "sexe": [data.sexe],
            "age": [data.age],
            "categorie_age": [data.categorie_age],
            "niveau_education": [data.niveau_education],
            "annees_experience": [data.annees_experience],
            "etat_matrimonial": [data.etat_matrimonial],
            "categorie_socioprofessionnelle": [data.categorie_socioprofessionnelle],
            "possession_voiture": [possession_voiture],
            "possession_logement": [possession_logement],
            "possession_terrain": [possession_terrain],
            "personnes_a_charge": [data.personnes_a_charge],
            "secteur_activite": [data.secteur_activite],
            "acces_services_financiers": [data.acces_services_financiers]
        })
        
        # Faire la prédiction
        prediction = model.predict(input_df)
        
        # Retourner le résultat
        return {
            "salaire_annuel": float(round(prediction[0], 2)),
            "statut": "succès"
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erreur de prédiction: {str(e)}")

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)