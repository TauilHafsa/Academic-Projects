import pandas as pd
import numpy as np
import joblib
from sklearn.model_selection import train_test_split
from sklearn.pipeline import Pipeline
from sklearn.compose import ColumnTransformer
from sklearn.preprocessing import OneHotEncoder, StandardScaler
from sklearn.impute import SimpleImputer
from sklearn.base import BaseEstimator, TransformerMixin
from sklearn.neural_network import MLPRegressor

# Classe pour supprimer les valeurs aberrantes
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

# Classe pour l'ingénierie des caractéristiques
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

def create_model():
    """Créer et retourner le pipeline du modèle"""
    
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
    
    # Pipeline complet
    pipeline = Pipeline(steps=[
        ('feature_engineering', FeatureEngineering()),
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

if __name__ == "__main__":
    # Chargement des données (remplacer par votre chemin de fichier)
    print("Chargement des données...")
    try:
        df = pd.read_csv("C:/Users/hp/Documents/Code/Python/SALARY_Predection/data/dataset_revenu_marocains.csv")
        df = df.drop(['id','id_transaction', 'date_inscription','couleur_preferee', 'age_mois'], axis=1)
        
        # Séparation features / target
        X = df.drop(columns="revenu_annuel")
        y = df["revenu_annuel"]
        
        # Suppression des valeurs aberrantes
        outlier_remover = OutlierRemover()
        mask = outlier_remover.transform(X).index
        X_clean = X.loc[mask]
        y_clean = y.loc[mask]
        
        # Split train/test
        X_train, X_test, y_train, y_test = train_test_split(X_clean, y_clean, test_size=0.3, random_state=42)
        
        # Création et entraînement du modèle
        print("Création et entraînement du modèle...")
        pipeline = create_model()
        pipeline.fit(X_train, y_train)
        
        # Enregistrement du modèle
        print("Enregistrement du modèle...")
        joblib.dump(pipeline, "best_model.joblib")
        print("Modèle sauvegardé avec succès sous 'best_model.joblib'")
        
    except Exception as e:
        print(f"Erreur: {e}")