package server;

import java.io.File;
import java.sql.*;
import client.Config;
import common.Protocol;

import java.util.List;
import java.util.ArrayList;

public class Database {
    private Connection connection;

    public Database() throws SQLException {
        // Ensure MySQL driver is loaded
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            throw new SQLException("MySQL JDBC Driver not found.", e);
        }
        this.connection = DriverManager.getConnection(
                Config.DB_URL,
                Config.DB_USER,
                Config.DB_PASSWORD
        );
    }

    public boolean authenticateUser(String email, String password) throws SQLException {
        String query = "SELECT * FROM utilisateurs WHERE email = ? AND mot_de_passe = ?"; // Assuming plain text password for now
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setString(1, email);
            stmt.setString(2, password);
            ResultSet rs = stmt.executeQuery();
            return rs.next(); // Returns true if a user with the given credentials exists
        }
    }

    public boolean registerUser(String email, String password, String firstName, String lastName) throws SQLException {
        // Check if email already exists
        String checkQuery = "SELECT 1 FROM utilisateurs WHERE email = ?";
        try (PreparedStatement checkStmt = connection.prepareStatement(checkQuery)) {
            checkStmt.setString(1, email);
            if (checkStmt.executeQuery().next()) {
                // Email already exists
                return false;
            }
        }

        // Insert new user
        String insertQuery = "INSERT INTO utilisateurs (email, mot_de_passe, nom, prenom) VALUES (?, ?, ?, ?)";
        try (PreparedStatement stmt = connection.prepareStatement(insertQuery)) {
            stmt.setString(1, email);
            stmt.setString(2, password); // Storing password in plain text - consider hashing
            stmt.setString(3, firstName); // Assuming 'nom' is firstName
            stmt.setString(4, lastName);  // Assuming 'prenom' is lastName
            return stmt.executeUpdate() > 0; // Returns true if the insertion was successful
        }
    }

    public int getUserIdByEmail(String email) throws SQLException {
        String query = "SELECT id_utilisateur FROM utilisateurs WHERE email = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setString(1, email);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) {
                return rs.getInt("id_utilisateur");
            } else {
                throw new SQLException("User not found with email: " + email);
            }
        }
    }

    public String getUserEmailById(int userId) throws SQLException {
        String query = "SELECT email FROM utilisateurs WHERE id_utilisateur = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, userId);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) {
                return rs.getString("email");
            } else {
                throw new SQLException("User not found with ID: " + userId);
            }
        }
    }

    // Method to get username by ID
    public String getUsernameById(int userId) throws SQLException {
        String query = "SELECT nom, prenom FROM utilisateurs WHERE id_utilisateur = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, userId);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) {
                String firstName = rs.getString("nom");
                String lastName = rs.getString("prenom");
                return firstName + " " + lastName; // Combine first and last names
            } else {
                throw new SQLException("User not found with ID: " + userId);
            }
        }
    }


    public void addContact(int userId, String contactEmail) throws SQLException {
        int contactId = getUserIdByEmail(contactEmail);

        if (userId == contactId) {
            throw new SQLException("Cannot add yourself as a contact");
        }

        // Check if already contacts or request pending
        String checkQuery = "SELECT 1 FROM contacts WHERE " +
                "(utilisateur_1 = ? AND utilisateur_2 = ?) OR " +
                "(utilisateur_1 = ? AND utilisateur_2 = ?)";

        try (PreparedStatement stmt = connection.prepareStatement(checkQuery)) {
            stmt.setInt(1, userId);
            stmt.setInt(2, contactId);
            stmt.setInt(3, contactId);
            stmt.setInt(4, userId);

            if (stmt.executeQuery().next()) {
                throw new SQLException("Contact relationship already exists or is pending.");
            }
        }

        // Add contact request
        String insertQuery = "INSERT INTO contacts (utilisateur_1, utilisateur_2, statut) VALUES (?, ?, 'accepté')";
        try (PreparedStatement stmt = connection.prepareStatement(insertQuery)) {
            stmt.setInt(1, userId); // The user sending the request
            stmt.setInt(2, contactId); // The user receiving the request
            System.out.println("[DEBUG] Adding contact: utilisateur_1 = " + userId + ", utilisateur_2 = " + contactId + ", statut = 'accepté'");
            stmt.executeUpdate();
        }

        // Add notification for the recipient
        String notifQuery = "INSERT INTO notifications (utilisateur_id, contenu, statut) VALUES (?, ?, 'non lu')";
        try (PreparedStatement stmt = connection.prepareStatement(notifQuery)) {
            stmt.setInt(1, contactId);
            stmt.setString(2, "Nouvelle demande de contact de la part de l'utilisateur ID: " + userId); // More informative message
            stmt.executeUpdate();
        }
    }

    public boolean deleteContact(int userId, String contactEmail) throws SQLException {
        int contactId = getUserIdByEmail(contactEmail);

        // Delete the contact relationship regardless of who initiated it
        String deleteQuery = "DELETE FROM contacts WHERE " +
                "(utilisateur_1 = ? AND utilisateur_2 = ?) OR " +
                "(utilisateur_1 = ? AND utilisateur_2 = ?)";
        try (PreparedStatement stmt = connection.prepareStatement(deleteQuery)) {
            stmt.setInt(1, userId);
            stmt.setInt(2, contactId);
            stmt.setInt(3, contactId);
            stmt.setInt(4, userId);
            int rowsAffected = stmt.executeUpdate();
            return rowsAffected > 0; // Return true if a relationship was deleted
        }
    }

    public void storeInvitation(int userId, String email) throws SQLException {
        String query = "INSERT INTO invitations (sender_id, receiver_email) VALUES (?, ?)";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, userId);
            stmt.setString(2, email);
            stmt.executeUpdate();
        }
    }

    public void sendMessage(int senderId, int receiverId, String content, String filePath) throws SQLException {
        String query = "INSERT INTO Messages (expediteur_id, destinataire_id, contenu, fichier_joint) VALUES (?, ?, ?, ?)";

        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, senderId);
            stmt.setInt(2, receiverId);
            stmt.setString(3, content);
            stmt.setString(4, filePath);
            stmt.executeUpdate();
        }
    }

    public List<String> getMessages(int senderId, int receiverId) throws SQLException {
        List<String> messages = new ArrayList<>();
        String query = "SELECT expediteur_id, contenu, fichier_joint FROM Messages WHERE " +
                "((expediteur_id = ? AND destinataire_id = ?) OR " +
                "(expediteur_id = ? AND destinataire_id = ?)) " +
                "ORDER BY date_envoi";

        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, senderId);
            stmt.setInt(2, receiverId);
            stmt.setInt(3, receiverId);
            stmt.setInt(4, senderId);
            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                int expediteurId = rs.getInt("expediteur_id");
                String content = rs.getString("contenu");
                String filePath = rs.getString("fichier_joint");
                String cleanContent = (content != null) ? content.trim() : "";

                // Gestion du fichier joint
                if (filePath != null && !filePath.trim().isEmpty()) {
                    String fileName = new File(filePath).getName();
                    if (!cleanContent.isEmpty()) {
                        cleanContent += " ";
                    }
                    cleanContent += "[Fichier: " + fileName + "]";
                }

                if (!cleanContent.isEmpty()) {
                    messages.add(expediteurId + Protocol.MESSAGE_SEPARATOR + cleanContent);
                }
            }
        }
        return messages;
    }

    public Connection getConnection() {
        return connection;
    }
}
