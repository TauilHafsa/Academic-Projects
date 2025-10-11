
# 🚗 Carent - Car Rental Application (Python + MongoDB)

**Carent** is a car rental agency management application developed in Python with MongoDB. It allows **managers** to handle cars, clients, and reservations, while **administrators** can manage manager accounts.

---

## 📌 Table of Contents

- [🎯 Objective](#-objective)
- [👥 User Profiles](#-user-profiles)
- [🧩 Key Features](#-key-features)
- [🗃️ MongoDB Schema Design](#-mongodb-schema-design)
- [🛠️ Technologies Used](#-technologies-used)
- [📁 Project Structure](#-project-structure)
- [🚀 Getting Started](#-getting-started)
- [📌 Implementation Steps](#-implementation-steps)
- [✅ Features to Test](#-features-to-test)
- [✨ Authors](#-authors)
- [📃 License](#-license)

---

## 🎯 Objective

To create a comprehensive application for managing a car rental agency, including:

- Vehicle management
- Client management
- Car reservations
- Manager account management

---

## 👥 User Profiles

### 🧑‍💼 Manager
- Authentication required
- Access to:
  - Cars: Create, Read, Update, Delete (CRUD)
  - Clients: CRUD
  - Reservations: View / Accept / Decline

### 🛠️ Administrator
- Authentication required
- Access to:
  - Manager Account Management: CRUD

---

## 🧩 Key Features

| Feature                              | Access    | Description                                   |
|--------------------------------------|-----------|-----------------------------------------------|
| 🔍 View Available Cars               | Manager   | See all currently available cars              |
| 📅 Reserve a Car                     | Manager   | Create a reservation for a client             |
| 📖 View Reservations                 | Manager   | List all current and past reservations        |
| ✅ Accept / Decline Reservation      | Manager   | Change the status of a reservation            |
| 🚗 Car Management                    | Manager   | Add, Modify, Delete, List cars                |
| 👥 Client Management                 | Manager   | Add, Modify, Delete, List clients             |
| 🧑‍💻 Manager Account Management      | Admin     | Add, Modify, Delete manager accounts          |

---

## 🛠️ Technologies Used

- **Python**: Application logic
- **MongoDB**: NoSQL Database
- **pymongo**: Python driver for MongoDB
- **bcrypt**: Password hashing
- **getpass**, **datetime**: Utility tools
- **Flask**: Web interface framework

---

## 📁 Project Structure
```
carent/
├── app/
│ ├── init.py
│ ├── config.py
│ ├── models/
│ │ ├── init.py
│ │ ├── car.py
│ │ ├── client.py
│ │ ├── reservation.py
│ │ └── user.py
│ ├── routes/
│ │ ├── init.py
│ │ ├── admin_routes.py
│ │ ├── auth_routes.py
│ │ ├── car_routes.py
│ │ ├── client_routes.py
│ │ └── reservation_routes.py
│ ├── static/
│ ├── templates/
│ │ ├── admin/
│ │ │ ├── managers/
│ │ │ │ ├── list.html
│ │ │ │ ├── create.html
│ │ │ ├── dashboard.html
│ │ ├── auth/
│ │ │ ├── change_password.html
│ │ │ ├── login.html
│ │ │ ├── profile.html
│ │ ├── cars/
│ │ │ ├── list.html
│ │ │ ├── create.html
│ │ │ ├── available.html
│ │ ├── clients/
│ │ │ ├── list.html
│ │ │ ├── create.html
│ │ ├── reservations/
│ │ │ ├── partials/
│ │ │ │ ├── reservation_table.html
│ │ │ ├── create.html
│ │ │ ├── details.html
│ │ │ ├── list.html
│ │ ├── base.html
│ │ └── index.html
│ └── utils/
│ ├── init.py
│ ├── auth.py
│ └── decorators.py
├── requirements.txt
└── run.py
```
---

## 🚀 Getting Started

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/your-username/carent.git
    cd carent
    ```

2.  **Install dependencies**:
    ```bash
    pip install -r requirements.txt
    ```
    (Note: I've updated this to `pip install -r requirements.txt` as it's standard practice, assuming you'll populate `requirements.txt` with `pymongo`, `bcrypt`, `Flask`, etc.)

3.  **Configure MongoDB**:
    Ensure your MongoDB instance is running. Update connection details in `app/config.py` if necessary (e.g., `MONGO_URI`).

4.  **Run the application**:
    ```bash
    python run.py
    ```
    (Note: I've updated this to `python run.py` based on your project structure indicating `run.py` as the main entry point.)

---

## 📌 Implementation Steps

1.  🔐 Authentication (Admin & Manager)
2.  🚗 Car CRUD operations
3.  👤 Client CRUD operations
4.  📅 Reservation Management
5.  🛠️ Manager Account Management (Admin only)
6.  ✅ Web Interface Implementation (Flask)

---

## ✅ Features to Test

-   [ ] Secure login for Admin and Manager roles
-   [ ] Add, modify, and delete vehicles
-   [ ] Client registration and management
-   [ ] Car reservation process
-   [ ] Decline/Accept reservation functionality
-   [ ] Create/Manage manager accounts by an administrator

---

## ✨ Authors

-   [Mohamed Salim Soulmani](https://github.com/iseeubad)
-   [Hafsa Tauil](https://github.com/TauilHafsa)
-   [Salma Dahman](https://github.com/Salmadahman)

---

## 📃 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for more details.


---
