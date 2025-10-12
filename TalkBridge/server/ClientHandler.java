package server;

import java.io.*;
import java.net.Socket;
import java.sql.SQLException;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import common.Protocol;
import java.util.List;
import java.util.ArrayList;

public class ClientHandler implements Runnable {
    private final Socket clientSocket;
    private final Database database;
    private PrintWriter out;
    private BufferedReader in;
    private int currentUserId = -1; // Store the logged-in user's ID

    public ClientHandler(Socket socket, Database database) {
        this.clientSocket = socket;
        this.database = database;
    }

    @Override
    public void run() {
        try {
            out = new PrintWriter(clientSocket.getOutputStream(), true);
            in = new BufferedReader(new InputStreamReader(clientSocket.getInputStream()));

            String inputLine;
            while ((inputLine = in.readLine()) != null) {
                handleRequest(inputLine);
            }
        } catch (IOException e) {
            System.out.println("Client déconnecté: " + e.getMessage());
        } finally {
            // Log when handler is ending
            System.out.println("ClientHandler for user ID " + currentUserId + " ending.");
            try {
                if (in != null) in.close();
                if (out != null) out.close();
                if (clientSocket != null && !clientSocket.isClosed()) clientSocket.close();
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }

    private void handleRequest(String request) throws IOException {
        System.out.println("Raw request received: \"" + request + "\""); // Keep this

        String[] parts = request.split(Protocol.SEPARATOR);
        if (parts.length == 0) {
            System.err.println("Received empty request after split.");
            return;
        }

        String command = parts[0].trim();
        // ***** START DEBUG LOGGING *****
        System.out.println("[DEBUG] Command received after trim: '" + command + "' (Length: " + command.length() + ")");
        System.out.println("[DEBUG] Comparing with Protocol.DELETE_CONTACT_CMD: '" + Protocol.DELETE_CONTACT_CMD + "' (Length: " + Protocol.DELETE_CONTACT_CMD.length() + ")");
        boolean isEqual = command.equals(Protocol.DELETE_CONTACT_CMD);
        System.out.println("[DEBUG] command.equals(Protocol.DELETE_CONTACT_CMD): " + isEqual);
        // ***** END DEBUG LOGGING *****

        System.out.println("Processing command: '" + command + "' from user ID: " + currentUserId); // Modified log

        try {
            switch (command) {
                case Protocol.LOGIN_CMD:
                    System.out.println("[DEBUG] Matched LOGIN_CMD");
                    if (parts.length >= 3) handleLogin(parts[1], parts[2]);
                    break;
                case Protocol.REGISTER_CMD:
                    System.out.println("[DEBUG] Matched REGISTER_CMD");
                    if (parts.length >= 5) handleRegister(parts[1], parts[2], parts[3], parts[4]);
                    break;
                case Protocol.GET_USER_BY_EMAIL:
                    System.out.println("[DEBUG] Matched GET_USER_BY_EMAIL");
                    if (parts.length >= 2) handleGetUserByEmail(parts[1]);
                    break;
                case Protocol.DELETE_CONTACT_CMD:
                    System.out.println("[DEBUG] Matched DELETE_CONTACT_CMD"); // Add log here
                    if (currentUserId != -1 && parts.length >= 2) {
                        handleDeleteContact(parts[1]);
                    } else if (currentUserId == -1) {
                        System.err.println("DELETE_CONTACT attempt failed: User not authenticated.");
                        out.println(Protocol.ERROR_PREFIX + "Non authentifié");
                        out.flush();
                    } else {
                        System.err.println("DELETE_CONTACT attempt failed: Invalid parts length: " + parts.length);
                        out.println(Protocol.ERROR_PREFIX + "Commande DELETE_CONTACT invalide (nombre d'arguments)");
                        out.flush();
                    }
                    break;
                case Protocol.INVITE_CONTACT_CMD:
                    System.out.println("[DEBUG] Matched INVITE_CONTACT_CMD");
                    if (currentUserId != -1 && parts.length >= 2) {
                        handleInviteContact(parts[1]);
                    } else {
                        out.println(Protocol.ERROR_PREFIX + "Non authentifié");
                        out.flush();
                    }
                    break;
                case Protocol.GET_INVITATIONS_CMD:
                    System.out.println("[DEBUG] Matched GET_INVITATIONS_CMD");
                    if (currentUserId != -1) {
                        handleGetInvitations();
                    } else {
                        out.println(Protocol.ERROR_PREFIX + "Non authentifié");
                        out.flush();
                    }
                    break;
                case Protocol.ACCEPT_INVITATION_CMD:
                    System.out.println("[DEBUG] Matched ACCEPT_INVITATION_CMD");
                    if (currentUserId != -1 && parts.length >= 2) {
                        handleAcceptInvitation(parts[1]);
                    } else {
                        out.println(Protocol.ERROR_PREFIX + "Non authentifié");
                        out.flush();
                    }
                    break;
                case Protocol.DECLINE_INVITATION_CMD:
                    System.out.println("[DEBUG] Matched DECLINE_INVITATION_CMD");
                    if (currentUserId != -1 && parts.length >= 2) {
                        handleDeclineInvitation(parts[1]);
                    } else {
                        out.println(Protocol.ERROR_PREFIX + "Non authentifié");
                        out.flush();
                    }
                    break;
                case Protocol.SEND_MESSAGE_CMD:
                    System.out.println("[DEBUG] Matched SEND_MESSAGE_CMD");
                    if (currentUserId != -1 && parts.length >= 4) {
                        handleSendMessage(Integer.parseInt(parts[1]), parts[2], parts[3]);
                    } else {
                        out.println(Protocol.ERROR_PREFIX + "Non authentifié ou arguments manquants pour SEND_MESSAGE");
                        out.flush();
                    }
                    break;
                case Protocol.GET_MESSAGES_CMD:
                    System.out.println("[DEBUG] Matched GET_MESSAGES_CMD");
                    if (currentUserId != -1 && parts.length >= 2) {
                        handleGetMessages(Integer.parseInt(parts[1]));
                    } else {
                        out.println(Protocol.ERROR_PREFIX + "Non authentifié ou arguments manquants pour GET_MESSAGES");
                        out.flush();
                    }
                    break;
                case Protocol.GET_USERNAME_CMD:
                    System.out.println("[DEBUG] Matched GET_USERNAME_CMD");
                    if (parts.length >= 2) {
                        handleGetUsername(Integer.parseInt(parts[1]));
                    } else {
                        out.println(Protocol.ERROR_PREFIX + "Invalid arguments for GET_USERNAME");
                        out.flush();
                    }
                    break;
                default:
                    // ***** MORE DEBUG LOGGING IN DEFAULT *****
                    System.err.println("[DEBUG] Command '" + command + "' did NOT match any case. Falling into default.");
                    // Log the character codes for debugging potential hidden characters
                    StringBuilder commandCodes = new StringBuilder();
                    for (char c : command.toCharArray()) {
                        commandCodes.append((int) c).append(" ");
                    }
                    System.err.println("[DEBUG] Character codes for received command: " + commandCodes.toString().trim());
                    StringBuilder expectedCodes = new StringBuilder();
                    for (char c : Protocol.DELETE_CONTACT_CMD.toCharArray()) {
                        expectedCodes.append((int) c).append(" ");
                    }
                    System.err.println("[DEBUG] Character codes for expected command: " + expectedCodes.toString().trim());
                    // ***** END DEBUG LOGGING IN DEFAULT *****
                    out.println(Protocol.ERROR_PREFIX + "Commande inconnue: " + command);
                    out.flush();
            }
        } catch (SQLException e) {
            System.err.println("Database error processing command '" + command + "': " + e.getMessage());
            out.println(Protocol.ERROR_PREFIX + "Erreur de base de données: " + e.getMessage());
            out.flush();
            e.printStackTrace();
        } catch (Exception e) {
            System.err.println("Unexpected server error processing command '" + command + "': " + e.getMessage());
            out.println(Protocol.ERROR_PREFIX + "Erreur serveur: " + e.getMessage());
            out.flush();
            e.printStackTrace();
        }
    }

    // --- handleLogin, handleRegister, handleGetUserByEmail remain the same ---

    private void handleLogin(String email, String password) throws SQLException, IOException {
        try {
            if (database.authenticateUser(email, password)) {
                this.currentUserId = database.getUserIdByEmail(email); // Store user ID upon successful login
                System.out.println("Login successful for user: " + email + ", ID: " + this.currentUserId);
                out.println(Protocol.SUCCESS_PREFIX + "Connexion réussie");
                out.flush();
            } else {
                System.out.println("Login failed for user: " + email);
                out.println(Protocol.ERROR_PREFIX + "Email ou mot de passe incorrect");
                out.flush();
            }
        } catch (SQLException e) {
            System.err.println("SQLException during login for " + email + ": " + e.getMessage());
            throw e; // Re-throw
        }
    }

    private void handleRegister(String email, String password, String firstName, String lastName)
            throws SQLException, IOException {
        try {
            if (database.registerUser(email, password, firstName, lastName)) {
                out.println(Protocol.SUCCESS_PREFIX + "Inscription réussie");
                out.flush();
            } else {
                // Assuming registration fails only if email exists based on Database.java logic
                out.println(Protocol.ERROR_PREFIX + "EMAIL_EXISTS"); // Send specific error code
                out.flush();
            }
        } catch (SQLException e) {
            System.err.println("SQLException during registration for " + email + ": " + e.getMessage());
            throw e; // Re-throw
        }
    }

    private void handleGetUserByEmail(String email) throws SQLException, IOException {
        try {
            String query = "SELECT id_utilisateur, nom, prenom FROM utilisateurs WHERE email = ?";
            try (PreparedStatement stmt = database.getConnection().prepareStatement(query)) {
                stmt.setString(1, email);
                ResultSet rs = stmt.executeQuery();
                if (rs.next()) {
                    int id = rs.getInt("id_utilisateur");
                    String firstName = rs.getString("nom");
                    String lastName = rs.getString("prenom");
                    out.println(Protocol.SUCCESS_PREFIX + id + Protocol.SEPARATOR + firstName + Protocol.SEPARATOR + lastName);
                    out.flush();
                } else {
                    out.println(Protocol.ERROR_PREFIX + "Utilisateur non trouvé");
                    out.flush();
                }
            }
        } catch (SQLException e) {
            System.err.println("SQLException during getUserData for " + email + ": " + e.getMessage());
            out.println(Protocol.ERROR_PREFIX + "Erreur lors de la récupération de l'utilisateur: " + e.getMessage());
            out.flush();
            // Don't re-throw here as we sent an error response
        }
    }


    private void handleDeleteContact(String contactEmail) throws IOException { // Removed SQLException from signature, handle inside
        if (this.currentUserId == -1) {
            System.err.println("handleDeleteContact called but user not authenticated.");
            out.println(Protocol.ERROR_PREFIX + "Non authentifié");
            out.flush();
        }
        System.out.println("Attempting to delete contact '" + contactEmail + "' for user ID: " + this.currentUserId);
        try {
            boolean deleted = database.deleteContact(this.currentUserId, contactEmail);
            if (deleted) {
                System.out.println("Contact " + contactEmail + " successfully deleted for user ID: " + this.currentUserId);
                out.println(Protocol.SUCCESS_PREFIX + "Contact supprimé");
                out.flush();
            } else {
                // This 'else' might mean the contact didn't exist in the table for this pair
                System.out.println("Contact " + contactEmail + " not found in contacts table for user ID: " + this.currentUserId + ", or already deleted.");
                // Check if the target user email actually exists at all
                try {
                    database.getUserIdByEmail(contactEmail); // Check if contact user exists
                    // If above line doesn't throw, the user exists but is not a contact of user ID " + this.currentUserId);
                    out.println(Protocol.ERROR_PREFIX + "Vous n'êtes pas en contact avec cet utilisateur");
                } catch (SQLException userNotFoundEx) {
                    // If getUserIdByEmail throws, the user email itself doesn't exist
                    System.out.println("User " + contactEmail + " does not exist in the database.");
                    out.println(Protocol.ERROR_PREFIX + "Utilisateur non trouvé: " + contactEmail);
                }
                out.flush();
            }
        } catch (SQLException e) {
            System.err.println("SQLException deleting contact '" + contactEmail + "' for user ID " + this.currentUserId + ": " + e.getMessage());
            // Provide more specific error messages based on SQL exception if possible
            if (e.getMessage().startsWith("User not found with email")) {
                out.println(Protocol.ERROR_PREFIX + "Utilisateur non trouvé: " + contactEmail);
            } else {
                out.println(Protocol.ERROR_PREFIX + "Erreur lors de la suppression: " + e.getMessage());
            }
            out.flush();
            e.printStackTrace(); // Log server-side error stack trace
        } catch (Exception e) { // Catch any other unexpected errors during delete
            System.err.println("Unexpected error deleting contact '" + contactEmail + "' for user ID " + this.currentUserId + ": " + e.getMessage());
            out.println(Protocol.ERROR_PREFIX + "Erreur inattendue lors de la suppression.");
            out.flush();
        }
    }

    private void handleInviteContact(String email) throws IOException {
        System.out.println("[DEBUG] handleInviteContact called with email: " + email);
        try {
            // Check if the user exists
            try {
                database.getUserIdByEmail(email);
            } catch (SQLException e) {
                // User does not exist, but we still want to store the invitation
                System.out.println("User" + email + " does not exist, storing invitation anyway.");
            }

            // Store the invitation in the database
            database.storeInvitation(this.currentUserId, email);
            out.println(Protocol.SUCCESS_PREFIX + "Invitation envoyée à " + email);
            out.flush();
        } catch (SQLException e) {
            System.err.println("SQLException storing invitation: " + e.getMessage());
            out.println(Protocol.ERROR_PREFIX + "Erreur lors de l'envoi de l'invitation: " + e.getMessage());
            out.flush();
        }
    }

    private void handleGetInvitations() throws IOException {
        try {
            String userEmail = null;
            try {
                userEmail = database.getUserEmailById(currentUserId);
                System.out.println("[DEBUG] User email: " + userEmail); // Log user email
            } catch (SQLException e) {
                System.err.println("Error getting user email: " + e.getMessage());
                out.println(Protocol.ERROR_PREFIX + "Erreur lors de la récupération de l'utilisateur: " + e.getMessage());
                out.flush();
                return;
            }

            String query = "SELECT sender_id FROM invitations WHERE receiver_email = ?";
            StringBuilder responseBuilder = new StringBuilder(Protocol.SUCCESS_PREFIX);
            PreparedStatement stmt = null;
            ResultSet rs = null;

            try {
                stmt = database.getConnection().prepareStatement(query);
                stmt.setString(1, userEmail);
                System.out.println("[DEBUG] SQL Query: " + query); // Log SQL query
                rs = stmt.executeQuery();
                int rowCount = 0;
                while (rs.next()) {
                    rowCount++;
                    int senderId = rs.getInt("sender_id");
                    try {
                        String senderEmail = database.getUserEmailById(senderId);
                        responseBuilder.append(senderEmail).append(Protocol.SEPARATOR);
                        System.out.println("[DEBUG] Invitation found - Sender ID: " + senderId + ", Sender Email: " + senderEmail); // Log invitation details
                    } catch (SQLException e) {
                        System.err.println("Error getting sender email: " + e.getMessage());
                        // Handle the error, e.g., log it and continue with the next invitation
                        continue;
                    }
                }
                System.out.println("[DEBUG] Number of invitations found: " + rowCount); // Log total invitations
            } finally {
                if (rs != null) try { rs.close(); } catch (SQLException e) { e.printStackTrace(); }
                if (stmt != null) try { stmt.close(); } catch (SQLException e) { e.printStackTrace(); }
            }

            // Remove the trailing separator if there are any invitations
            if (responseBuilder.length() > Protocol.SUCCESS_PREFIX.length()) {
                responseBuilder.delete(responseBuilder.length() - Protocol.SEPARATOR.length(), responseBuilder.length());
            }

            String response = responseBuilder.toString();
            System.out.println("[DEBUG] Response to client: " + response); // Log response to client
            out.println(response);
            out.flush();

        } catch (SQLException e) {
            System.err.println("SQLException getting invitations: " + e.getMessage());
            out.println(Protocol.ERROR_PREFIX + "Erreur lors de la récupération des invitations: " + e.getMessage());
            out.flush();
        }
    }

    private void handleAcceptInvitation(String senderEmail) throws IOException {
        System.out.println("[DEBUG] handleAcceptInvitation called with senderEmail: " + senderEmail);
        try {
            int senderId = database.getUserIdByEmail(senderEmail);
            System.out.println("[DEBUG] Sender ID: " + senderId);

            // Add contact to the Contacts table with 'accepté' status
            database.addContact(currentUserId, senderEmail);
            System.out.println("[DEBUG] Contact added successfully.");

            // Remove invitation from the Invitations table
            String deleteQuery = "DELETE FROM invitations WHERE sender_id = ? AND receiver_email = ?";
            try (PreparedStatement stmt = database.getConnection().prepareStatement(deleteQuery)) {
                stmt.setInt(1, senderId);
                stmt.setString(2, database.getUserEmailById(currentUserId));
                int rowsAffected = stmt.executeUpdate();
                System.out.println("[DEBUG] Invitations deleted: " + rowsAffected);
            }

            out.println(Protocol.SUCCESS_PREFIX + "Invitation acceptée et contact ajouté.");
            out.flush();

        } catch (SQLException e) {
            System.err.println("SQLException accepting invitation: " + e.getMessage());
            out.println(Protocol.ERROR_PREFIX + "Erreur lors de l'acceptation de l'invitation: " + e.getMessage());
            out.flush();
        }
    }

    private void handleDeclineInvitation(String senderEmail) throws IOException {
        System.out.println("[DEBUG] handleDeclineInvitation called with senderEmail: " + senderEmail);
        try {
            int senderId = database.getUserIdByEmail(senderEmail);
            System.out.println("[DEBUG] Sender ID: " + senderId);

            // Update contact to the Contacts table with 'bloqué' status
            // You might need to adjust this query based on your table structure
            int contactId = database.getUserIdByEmail(senderEmail);

            // Delete the contact relationship regardless of who initiated it
            String updateQuery = "UPDATE contacts SET statut = 'bloqué' WHERE " +
                    "((utilisateur_1 = ? AND utilisateur_2 = ?) OR " +
                    "(utilisateur_1 = ? AND utilisateur_2 = ?))";
            try (PreparedStatement stmt = database.getConnection().prepareStatement(updateQuery)) {
                stmt.setInt(1, currentUserId);
                stmt.setInt(2, contactId);
                stmt.setInt(3, contactId);
                stmt.setInt(4, currentUserId);
                int rowsAffected = stmt.executeUpdate();
                System.out.println("[DEBUG] Contacts updated: " + rowsAffected);
            }

            // Remove invitation from the Invitations table
            String deleteQuery = "DELETE FROM invitations WHERE sender_id = ? AND receiver_email = ?";
            try (PreparedStatement stmt = database.getConnection().prepareStatement(deleteQuery)) {
                stmt.setInt(1, senderId);
                stmt.setString(2, database.getUserEmailById(currentUserId));
                int rowsAffected = stmt.executeUpdate();
                System.out.println("[DEBUG] Invitations deleted: " + rowsAffected);
            }

            out.println(Protocol.SUCCESS_PREFIX + "Invitation refusée et contact bloqué.");
            out.flush();

        } catch (SQLException e) {
            System.err.println("SQLException declining invitation: " + e.getMessage());
            out.println(Protocol.ERROR_PREFIX + "Erreur lors du refus de l'invitation: " + e.getMessage());
            out.flush();
        }
    }

    private void handleSendMessage(int receiverId, String content, String filePath) throws IOException {
        try {
            // Validation des données
            if ((content == null || content.trim().isEmpty()) &&
                    (filePath == null || filePath.trim().isEmpty())) {
                out.println(Protocol.ERROR_PREFIX + "Message content or file is required");
                out.flush();
                return;
            }

            // Nettoyage des données
            String cleanContent = (content != null) ? content.replace(Protocol.SEPARATOR, "").trim() : "";
            String cleanFilePath = (filePath != null && !filePath.trim().isEmpty()) ? filePath : null;

            database.sendMessage(currentUserId, receiverId, cleanContent, cleanFilePath);
            out.println(Protocol.SUCCESS_PREFIX + "Message sent");
            out.flush();
        } catch (SQLException e) {
            out.println(Protocol.ERROR_PREFIX + "Database error: " + e.getMessage());
            out.flush();
        }
    }

    private void handleGetMessages(int receiverId) throws IOException {
        try {
            List<String> messages = database.getMessages(currentUserId, receiverId);
            StringBuilder responseBuilder = new StringBuilder(Protocol.SUCCESS_PREFIX);
            for (String message : messages) {
                responseBuilder.append(message).append(Protocol.SEPARATOR);
            }
            if (responseBuilder.length() > Protocol.SUCCESS_PREFIX.length()) {
                responseBuilder.delete(responseBuilder.length() - Protocol.SEPARATOR.length(), responseBuilder.length());
            }
            out.println(responseBuilder.toString());
            out.flush();
        } catch (SQLException e) {
            System.err.println("SQLException getting messages: " + e.getMessage());
            out.println(Protocol.ERROR_PREFIX + "Erreur lors de la récupération des messages: " + e.getMessage());
            out.flush();
        }
    }

    private void handleGetUsername(int userId) throws IOException {
        try {
            String username = database.getUsernameById(userId);
            out.println( username);
            out.flush();
        } catch (SQLException e) {
            System.err.println("SQLException getting username: " + e.getMessage());
            out.println(Protocol.ERROR_PREFIX + "Erreur lors de la récupération du nom d'utilisateur: " + e.getMessage());
            out.flush();
        }
    }
}
