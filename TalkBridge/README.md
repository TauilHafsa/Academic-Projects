# 💬 TalkBridge — Secure Java Communication Platform

TalkBridge is a **secure client–server communication application** built with **JavaFX** and **Java Sockets**.  
It enables real-time messaging, file sharing, and group discussions, using strong **Serpent encryption** combined with **Diffie–Hellman key exchange** to ensure end-to-end security.

---

## 🏗️ Project Architecture

### ⚙️ Model Overview
The project follows a **Client–Server architecture**:

- **Server:** Handles client connections, authentication, message routing, group and contact management.
- **Client:** Provides a user-friendly JavaFX interface for communication, file sharing, and notifications.

### 📂 Directory Structure

```
TalkBridge/
├── client/
│   ├── controllers/        # JavaFX controllers for UI logic
│   │   ├── ChatController.java
│   │   ├── ContactsController.java
│   │   ├── LoginController.java
│   │   └── ... (other controllers)
│   ├── models/             # Core data models
│   │   ├── User.java
│   │   ├── Message.java
│   │   └── Group.java
│   ├── views/              # JavaFX FXML views
│   │   ├── ChatView.fxml
│   │   ├── LoginView.fxml
│   │   ├── GroupCreationView.fxml
│   │   └── ... (other views)
│   ├── style.css
│   ├── Client.java
│   └── Config.java
│
├── server/
│   ├── Server.java
│   ├── ClientHandler.java
│   ├── Database.java
│   ├── FileManager.java
│   ├── EmailManager.java
│   └── ServerUtils.java
│
├── common/                 # Shared resources (if applicable)
├── libs/                   # External dependencies
│   ├── mysql-connector-j-8.0.33.jar
│   ├── jakarta.mail-2.0.1.jar
│   └── javax.mail.jar
│
├── Main.java
└── README.md
```

---

## 🔐 Security Features

### 🧮 Encryption
- **Algorithm:** Serpent  
- **Key Exchange:** Diffie–Hellman (DH)
- **Purpose:** All messages, files, and group communications are encrypted before transmission.

### 🛡️ Security Workflow
1. Clients establish a secure DH key exchange during connection.
2. A shared session key is derived.
3. Messages and files are encrypted using Serpent.
4. The server relays encrypted data — it cannot decrypt client communications.

---

## 💡 Core Features

### 🖥️ Server Side
- **Multi-threaded connection handling**
- **Authentication** via email
- **Message management** for offline clients
- **Contact management** (add, remove, invite)
- **Group management** (create, delete, roles)
- **File and media transfer support**
- **Graceful disconnect management**

### 🧑‍💻 Client Side
- **Secure login and session management**
- **Real-time chat and notifications**
- **File, image, and video sharing**
- **Contact and group management**
- **Group discussions with admin roles**
- **Persistent chat history**
- **Clean logout process**

---

## 🧰 Technologies Used

| Layer | Technology |
|-------|-------------|
| Language | Java 17+ |
| GUI | JavaFX (FXML) |
| Networking | Java Socket API (TCP/IP) |
| Concurrency | Java Threads |
| Database | MySQL |
| Encryption | Serpent + Diffie–Hellman |
| Email Notifications | Jakarta Mail API |
| File Transfer | Java I/O Streams |

---

## ⚙️ Setup Instructions

### 1. Prerequisites
- JDK 17 or higher
- MySQL server running
- JavaFX SDK (if not bundled with JDK)
- Required JARs in `libs/` (Jakarta Mail, MySQL Connector, etc.)

### 2. Database Configuration
Edit the file `Config.java` with your database credentials:
```java
public static final String DB_URL = "jdbc:mysql://localhost:3306/talkbridge";
public static final String DB_USER = "root";
public static final String DB_PASS = "your_password";
```

### 3. Run the Server
```bash
javac server/*.java
java server.Server
```

### 4. Run the Client
```bash
javac client/*.java
java client.Client
```

---

## 📬 Communication Protocol

All client–server exchanges follow a **custom JSON-based protocol**.

Example:
```json
{
  "type": "message",
  "sender": "alice@example.com",
  "receiver": "bob@example.com",
  "content": "Encrypted text here",
  "timestamp": "2025-10-11T18:00:00"
}
```

---

## 🧑‍💻 Contributors

- **Team:** TAUIL Hafsa / EL FAHSI Chaymae / EL BEKKARI Houda 
- **Encryption Integration:** Serpent / Diffie–Hellman


## 📜 License
This project is provided for **educational and research purposes** under an open-source license (MIT or GPLv3, as you prefer).

