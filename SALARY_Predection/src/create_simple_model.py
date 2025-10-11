import joblib
import pandas as pd
import numpy as np
from sklearn.neural_network import MLPRegressor
from sklearn.preprocessing import OneHotEncoder, StandardScaler
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
from sklearn.impute import SimpleImputer
import os

# Fonction pour créer un modèle simple sans les transformateurs personnalisés
def create_simple_model():
    # Prétraitement pour les colonnes numériques
    numeric_transformer = Pipeline(steps=[
        ('imputer', SimpleImputer(strategy='median')),
        ('scaler', StandardScaler())
    ])
    
    # Prétraitement pour les colonnes catégorielles
    categorical_transformer = Pipeline(steps=[
        ('imputer', SimpleImputer(strategy='most_frequent')),
        ('onehot', OneHotEncoder(handle_unknown='ignore'))
    ])
    
    # Liste des colonnes numériques et catégorielles
    numeric_features = ['age', 'annees_experience', 'personnes_a_charge', 'possession_voiture', 
                      'possession_logement', 'possession_terrain']
    categorical_features = ['milieu', 'sexe', 'categorie_age', 'niveau_education', 
                           'etat_matrimonial', 'categorie_socioprofessionnelle', 
                           'secteur_activite', 'acces_services_financiers']
    
    # Assemblage des prétraitements
    preprocessor = ColumnTransformer(
        transformers=[
            ('num', numeric_transformer, numeric_features),
            ('cat', categorical_transformer, categorical_features)
        ])
    
    # Pipeline simple sans les transformateurs personnalisés
    pipeline = Pipeline(steps=[
        ('preprocessor', preprocessor),
        ('regressor', MLPRegressor(
            hidden_layer_sizes=(100, 100),
            activation='relu',
            solver='adam',
            alpha=0.0001,
            learning_rate='constant',
            learning_rate_init=0.001,
            max_iter=300,
            random_state=42
        ))
    ])
    
    return pipeline

# Ecrire un script qui va créer un nouveau modèle simplifié à partir d'exemples
if __name__ == "__main__":
    # Chemin où se trouve votre dataset d'entraînement
    # Remplacez par le chemin correct vers votre dataset
    data_path = "../data/dataset_revenu_marocains.csv"
    
    # Tester si le fichier existe
    if not os.path.exists(data_path):
        print(f"Le fichier {data_path} n'existe pas.")
        print("Veuillez entrer le chemin correct vers votre dataset:")
        data_path = input("> ")
    
    try:
        # Charger le dataset
        print(f"Chargement des données depuis {data_path}...")
        df = pd.read_csv(data_path)
        df = df.drop(['id','id_transaction', 'date_inscription', 'couleur_preferee', 'age_mois'], axis=1, errors='ignore')
        
        # Séparation features / target
        X = df.drop(columns="revenu_annuel")
        y = df["revenu_annuel"]
        
        # Création et entraînement du modèle simplifié
        print("Création et entraînement du modèle simplifié...")
        pipeline = create_simple_model()
        pipeline.fit(X, y)
        
        # Enregistrement du modèle
        print("Enregistrement du modèle...")
        joblib.dump(pipeline, "simple_model.joblib")
        print("Modèle sauvegardé avec succès sous 'simple_model.joblib'")
        
    except Exception as e:
        print(f"Erreur: {e}")