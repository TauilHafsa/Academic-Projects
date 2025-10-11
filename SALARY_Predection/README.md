
# Annual Income Prediction App — Morocco

A complete **Machine Learning web application** that predicts the **annual income of Moroccan citizens** based on socio-demographic and professional characteristics.

---

## 🧠 Overview

This project was developed as part of the **Artificial Intelligence Engineering Curriculum (2024–2025)**.  
It demonstrates a **full Machine Learning pipeline**, covering every stage from data generation to web deployment.

### Project Highlights
- Synthetic dataset generation based on official Moroccan statistics (HCP)
- Data cleaning, preprocessing, and exploratory analysis
- Model training and hyperparameter tuning
- Model evaluation and performance comparison
- Deployment using **FastAPI** (backend) and **Streamlit** (frontend)

---

## 📁 Project Structure

```

.
├── model/
│   └── best_model.joblib                # Saved predictive model
├── notebooks/
│   └── model_selection.ipynb            # Jupyter notebook for model creation and tuning
├── data/
│   └── dataset_revenu_marocains.csv     # Training dataset
├── src/
│   ├── train_model.py                   # Model training script
│   ├── fast_api_app.py                  # FastAPI backend
│   └── streamlit_app.py                 # Streamlit web interface
└── README.md                            # This file

```

---

## ⚙️ Main Components

1. **Machine Learning Model**  
   A **Multi-Layer Perceptron (MLP Regressor)** trained to predict annual income.

2. **REST API**  
   Built with **FastAPI** to expose the trained model as an endpoint.

3. **Web Interface**  
   Developed with **Streamlit**, providing an intuitive UI for real-time income prediction.

---

## 🧩 Requirements

- Python 3.7 or higher
- Core dependencies:
```

fastapi
uvicorn
streamlit
scikit-learn
pandas
numpy
joblib

````

Install dependencies:
```bash
pip install -r requirements.txt
````

---

## 🚀 Getting Started

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/your-username/morocco-income-prediction.git
cd morocco-income-prediction
```

### 2️⃣ Train the Model (Optional)

A pre-trained model is already included, but you can retrain it:

```bash
python src/train_model.py
```

### 3️⃣ Run the FastAPI Backend

```bash
cd src
uvicorn fast_api_app:app --reload
```

* API: [http://localhost:8000](http://localhost:8000)
* Docs: [http://localhost:8000/docs](http://localhost:8000/docs)

### 4️⃣ Launch the Streamlit App

In a separate terminal:

```bash
cd src
streamlit run streamlit_app.py
```

Access the web interface at [http://localhost:8501](http://localhost:8501)

---

## 🔗 API Endpoint

**POST /predict**

Accepts a JSON payload describing the individual's features and returns the predicted annual income.

**Example Request:**

```json
{
  "milieu": "Urbain",
  "sexe": "Homme",
  "age": 35,
  "categorie_age": "Adulte",
  "niveau_education": "Supérieur",
  "annees_experience": 10.0,
  "etat_matrimonial": "Marié",
  "categorie_socioprofessionnelle": "Groupe 2",
  "possession_voiture": true,
  "possession_logement": false,
  "possession_terrain": true,
  "personnes_a_charge": 2,
  "secteur_activite": "Privé formel",
  "acces_services_financiers": "Basique"
}
```

**Example Response:**

```json
{
  "salaire_annuel": 38547.25,
  "status": "success"
}
```

---

## 📊 Model Performance

| Metric                                |   Score |
| :------------------------------------ | ------: |
| **MAE** (Mean Absolute Error)         | 2568.45 |
| **RMSE** (Root Mean Squared Error)    | 6033.28 |
| **R²** (Coefficient of Determination) |  0.8503 |

---

## 📦 Deliverables

* `generate_dataset.py` – Data generation script
* `dataset_revenu_marocains.csv` – Generated dataset
* `mini_projet_AI_Noms.ipynb` – Jupyter notebook
* `best_model.joblib` – Saved model
* `fast_api_app.py` – FastAPI backend
* `streamlit_app.py` – Streamlit interface
* `README.md` – Project documentation

---

## 👨‍💻 Authors

Developed by: 
- TAUIL Hafsa
- SOULMANI Mohamed Salim
- KHALISS Hicham

Under the supervision of **Y. El Younoussi**


---

## 🏁 License

This project is released under the **MIT License**.
You are free to use, modify, and distribute it with proper attribution.

---



