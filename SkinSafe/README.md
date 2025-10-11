
# 🩺 SkinSafe - Melanoma Detection with Deep Learning

**SkinSafe** is an AI-powered application designed to assist in the early detection of melanoma by classifying skin lesions as benign or malignant from medical images. Developed using Python (Flask) for the backend and leveraging a Vision Transformer (ViT) Deep Learning model, it aims to provide a reliable and accessible diagnostic aid.

---

## 📌 Table of Contents

- [🎯 Objective](#-objective)
- [✨ Key Features](#-key-features)
- [📈 Core Technologies](#-core-technologies)
- [📁 Project Structure](#-project-structure)
- [🚀 Getting Started](#-getting-started)
- [🎨 Design Patterns](#-design-patterns)
- [🤝 Authors](#-authors)
- [📃 License](#-license)

---

## 🎯 Objective

To automate melanoma detection using a Deep Learning model trained on annotated skin lesion images. The goal is to provide a highly accurate diagnostic probability for malignancy via a secure Flask API, facilitating early detection to improve patient outcomes.

---

## ✨ Key Features

-   **Image Submission & Preprocessing**: Upload lesion images (DICOM/JPEG) with automated conversion and preparation for AI.
-   **Malignancy Prediction**: A Vision Transformer (ViT) model predicts the probability of a lesion being malignant.
-   **Diagnostic History**: Users can view and manage their past analysis results.
-   **User Management**: Secure user registration, login, and profile management.
-   **Notification System**: Alert users, especially for high-risk diagnoses.

---

## 📈 Core Technologies

-   **Python**: Backend logic and AI model.
-   **Flask**: RESTful API framework.
-   **Vision Transformer (ViT)**: Deep Learning model for image classification.
-   **HTML/CSS/JS**: Frontend for the web interface (templates).
-   **SQL Database**: For user data, analysis history, etc. (indicated by `skinsafe_db.sql`).

---

## 📁 Project Structure

/SkinSafe/
|
├── app.ipynb               # Fichier principal Flask
├── skinsafevit.ipynb       # Modele entraine
├── static/                 # Dossier pour les images uploadees
│   └── ...
├── templates/              
│   ├── index.html          # Page d acceuil
│   ├── dashboard.html      # Page de tableau de board
│   ├── history.html        # Page d historique
│   ├── login.html 
│   ├── register.html 
│   └── error.html          
├── patterns/               # Implementation des design patterns
│   ├── __init__.py         
│   ├── singleton.py        
│   ├── observer.py         
│   ├── adapter.py          
│   ├── strategy.py        
│   ├── command.py         
│   └── facade.py         
├── utils/                  # Utilitaires supplémentaires
│   └── config.py           # Configuration
├── models.py
├── .env
├── skinsafe_db.sql
└── requirements.txt        # Dépendances Python

---

## 🚀 Getting Started

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/your-username/skinsafe.git
    cd skinsafe
    ```

2.  **Set up Database**:
    -   Ensure a SQL database (e.g., SQLite, PostgreSQL, MySQL) is configured.
    -   Execute `skinsafe_db.sql` to create the necessary tables.

3.  **Create Virtual Environment & Install Dependencies**:
    ```bash
    python -m venv venv
    source venv/bin/activate  # On Windows: `.\venv\Scripts\activate`
    pip install -r requirements.txt
    ```

4.  **Configure Environment Variables**:
    -   Create a `.env` file based on `utils/config.py` for sensitive information (e.g., database connection strings, secret keys).

5.  **Run the Application**:
    ```bash
    jupyter nbconvert --to script app.ipynb # If running as a standard script
    python app.py # Or execute app.ipynb directly if using Jupyter environment
    ```
    _Note: `app.ipynb` suggests a Jupyter Notebook. For production, it's typically converted to a `.py` script or run directly within a Jupyter environment. Adjust `python app.py` accordingly._

---

## 🎨 Design Patterns

The project incorporates several design patterns for improved structure, flexibility, and maintainability:

-   **Singleton**: Ensures a single instance of the DL model.
-   **Observer**: Manages backend-frontend notifications.
-   **Adapter**: Converts various image formats for the AI pipeline.
-   **Strategy**: Dynamically selects image preprocessing algorithms.
-   **Command**: Encapsulates user actions for better event management.
-   **Facade**: Simplifies interaction with the complex AI pipeline.

---

## 🤝 Authors

-   [Hafsa Tauil](https://github.com/TauilHafsa)

---

## 📃 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for more details.


---



