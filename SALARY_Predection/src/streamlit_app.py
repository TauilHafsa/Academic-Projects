import streamlit as st
import requests
import json
import pandas as pd

st.set_page_config(page_title="Prédiction de Revenu Annuel", layout="wide")

st.title("Prédiction de Revenu Annuel au Maroc")
st.subheader("Entrez vos informations personnelles et professionnelles")

# URL de l'API (ajustable en fonction du déploiement)
API_URL = "http://localhost:8000/predict"

# Créer des colonnes pour une meilleure mise en page
col1, col2 = st.columns(2)

with st.form("salary_prediction_form"):
    with col1:
        st.subheader("Informations personnelles")
        
        milieu = st.selectbox(
            "Milieu", 
            ["Urbain", "Rural"],
            help="Votre zone d'habitation"
        )
        
        sexe = st.selectbox(
            "Sexe", 
            ["Homme", "Femme"],
            help="Votre genre"
        )
        
        age = st.number_input(
            "Âge", 
            min_value=18, 
            max_value=100, 
            value=30,
            help="Votre âge en années"
        )
        
        categorie_age = st.selectbox(
            "Catégorie d'âge", 
            ["Jeune", "Adulte", "Senior"],
            help="Votre catégorie d'âge"
        )
        
        niveau_education = st.selectbox(
            "Niveau d'éducation", 
            ["Aucun", "Fondamental", "Secondaire", "Supérieur"],
            help="Votre plus haut niveau d'études complété"
        )
        
        etat_matrimonial = st.selectbox(
            "État matrimonial", 
            ["Célibataire", "Marié", "Divorcé", "Veuf"],
            help="Votre situation matrimoniale actuelle"
        )
        
        personnes_a_charge = st.number_input(
            "Personnes à charge", 
            min_value=0, 
            max_value=20, 
            value=0,
            help="Nombre de personnes dont vous avez la charge financière"
        )
    
    with col2:
        st.subheader("Informations professionnelles")
        
        annees_experience = st.number_input(
            "Années d'expérience", 
            min_value=0.0, 
            max_value=60.0, 
            value=5.0,
            step=0.5,
            help="Votre expérience professionnelle en années"
        )
        
        categorie_socioprofessionnelle = st.selectbox(
            "Catégorie socioprofessionnelle", 
            ["Groupe 1", "Groupe 2", "Groupe 3", "Groupe 4", "Groupe 5", "Groupe 6"],
            help="Votre catégorie socioprofessionnelle"
        )
        
        secteur_activite = st.selectbox(
            "Secteur d'activité", 
            ["Public", "Privé formel", "Privé informel"],
            help="Votre secteur d'activité professionnel"
        )
        
        acces_services_financiers = st.selectbox(
            "Accès services financiers", 
            ["Aucun", "Basique", "Avancé"],
            help="Votre niveau d'accès aux services financiers"
        )
        
        st.subheader("Possessions")
        possession_voiture = st.checkbox("Possède une voiture", value=False)
        possession_logement = st.checkbox("Possède un logement", value=False)
        possession_terrain = st.checkbox("Possède un terrain", value=False)
    
    # Bouton de soumission
    submit_button = st.form_submit_button(
        "Prédire le revenu annuel", 
        use_container_width=True
    )

# Traitement lors de la soumission
if submit_button:
    # Préparer les données pour l'API
    input_data = {
        "milieu": milieu,
        "sexe": sexe,
        "age": age,
        "categorie_age": categorie_age,
        "niveau_education": niveau_education,
        "annees_experience": annees_experience,
        "etat_matrimonial": etat_matrimonial,
        "categorie_socioprofessionnelle": categorie_socioprofessionnelle,
        "possession_voiture": possession_voiture,
        "possession_logement": possession_logement,
        "possession_terrain": possession_terrain,
        "personnes_a_charge": personnes_a_charge,
        "secteur_activite": secteur_activite,
        "acces_services_financiers": acces_services_financiers
    }
    
    # Afficher un spinner pendant la requête
    with st.spinner("Calcul du revenu annuel en cours..."):
        try:
            # Envoyer la requête à l'API
            response = requests.post(API_URL, json=input_data)
            
            # Vérifier la réponse
            if response.status_code == 200:
                result = response.json()
                predicted_salary = result["salaire_annuel"]
                
                # Animation de réussite
                st.balloons()
                
                # Afficher le résultat dans un cadre spécial
                st.success(f"### Revenu annuel prédit: {predicted_salary:,.2f} FCFA")
                
                # Afficher une visualisation
                salary_data = {"Revenu prédit": predicted_salary}
                df = pd.DataFrame(list(salary_data.items()), columns=['Type', 'Montant'])
                st.bar_chart(df.set_index('Type'))
                
                # Ajouter un téléchargement des résultats
                result_json = json.dumps({
                    "inputs": input_data,
                    "prediction": result
                }, indent=4)
                st.download_button(
                    "Télécharger les résultats", 
                    result_json,
                    file_name="prediction_revenu.json",
                    mime="application/json"
                )
            else:
                st.error(f"Erreur lors de la prédiction: {response.text}")
        except requests.exceptions.ConnectionError:
            st.error("Impossible de se connecter à l'API. Vérifiez que le serveur FastAPI est en cours d'exécution.")
        except Exception as e:
            st.error(f"Une erreur s'est produite: {str(e)}")

# Ajouter des informations supplémentaires
with st.expander("Comment fonctionne cette application?"):
    st.write("""
    Cette application utilise un modèle d'apprentissage automatique entraîné sur des données socio-économiques 
    marocaines pour prédire le revenu annuel d'une personne en fonction de diverses caractéristiques personnelles 
    et professionnelles.
    
    Le modèle utilisé est un MLP Regressor (Perceptron multicouche) avec les métriques suivantes:
    - MAE (Erreur absolue moyenne): 2568.45
    - RMSE (Erreur quadratique moyenne): 6033.28
    - R² (Coefficient de détermination): 0.8503
    
    Ces métriques indiquent que le modèle est capable de prédire le revenu avec une précision raisonnable.
    """)

# Pied de page
st.sidebar.markdown("---")
st.sidebar.info("Cette application est fournie à titre informatif uniquement et les prédictions ne doivent pas être considérées comme des conseils financiers ou professionnels officiels.")