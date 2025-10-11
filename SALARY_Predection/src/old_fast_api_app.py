from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import pandas as pd
import joblib
import numpy as np
import os
from sklearn.base import BaseEstimator, TransformerMixin

# Définir les mêmes classes de transformation que celles utilisées pendant l'entraînement
# Cette classe DOIT être identique à celle définie dans train_model.py
class OutlierRemover(BaseEstimator, TransformerMixin):
    def __init__(self, z_thresh=3):
        self.z_thresh = z_thresh

    def fit(self, X, y=None):
        return self

    def transform(self, X, y=None):
        # Uniquement sur les colonnes numériques
        numeric_cols = X.select_dtypes(include=np.number).columns
        mask = (np.abs((X[numeric_cols] - X[numeric_cols].mean()) / X[numeric_cols].std()) < self.z_thresh).all(axis=1)
        return X[mask]

# Cette classe DOIT être identique à celle définie dans train_model.py
class FeatureEngineering(BaseEstimator, TransformerMixin):
    def fit(self, X, y=None):
        return self

    def transform(self, X):
        X_transformed = X.copy()

        # Conversion en types numériques
        for col in ['annees_experience', 'age']:
            if col in X_transformed.columns:
                X_transformed[col] = pd.to_numeric(X_transformed[col], errors='coerce')

        # Éviter les divisions par zéro
        X_transformed['annees_experience'] = X_transformed['annees_experience'].replace(0, np.nan)
        X_transformed['age'] = X_transformed['age'].replace(0, np.nan)

        # Créer de nouvelles caractéristiques (ratio)
        if 'annees_experience' in X_transformed.columns and 'age' in X_transformed.columns:
            X_transformed['ratio_experience_age'] = X_transformed['annees_experience'] / X_transformed['age']

        return X_transformed

app = FastAPI(title="API de prédiction de revenu annuel")

# Définir la variable model comme global
model = None

# Tenter de charger le modèle de différents emplacements
MODEL_PATHS = [
    "best_model.joblib",  # Chemin actuel
    "../model/best_model.joblib",  # Chemin alternatif 1
    "./model/best_model.joblib",   # Chemin alternatif 2
    "model/best_model.joblib",     # Chemin alternatif 3
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