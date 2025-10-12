package client.controllers;

import javafx.application.Platform;
import javafx.scene.layout.HBox;
import javafx.geometry.Pos;
import client.models.User;
// Removed direct server.Database import - Client should not directly access server DB class
import client.Config; // Use Config for DB connection details if accessing directly (Not recommended)
import javafx.fxml.FXML;
import javafx.scene.layout.VBox;
import javafx.scene.control.Label;
import javafx.scene.image.ImageView;
import javafx.scene.image.Image;
import javafx.scene.control.ProgressIndicator;
import javafx.scene.control.Alert;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;
import client.Client; // Import Client class

import java.sql.*; // Required for direct DB access
import java.util.ArrayList;
import java.util.List;
import java.io.InputStream;
import java.io.IOException;

public class ContactsController {

    @FXML private VBox contactsBox;
    @FXML private ProgressIndicator loadingIndicator; // Add loading indicator

    private int currentUserId = -1; // Store the logged-in user's ID
    private Client client; // Declare the client variable

    // Default constructor required by FXML loader if no other constructor is present
    public ContactsController() {
        // Initialization logic can go into initialize() method
    }

    public void setClient(Client client) {
        this.client = client;
    }

    // Method to set the user ID after the controller is created
    public void setUserId(int userId) {
        this.currentUserId = userId;
        System.out.println("ContactsController received User ID: " + this.currentUserId);
        // Load contacts only after userId is set
        if (this.currentUserId != -1) {
            loadContacts();
        } else {
            System.err.println("ContactsController: Invalid User ID received.");
            showError("Erreur", "ID Utilisateur invalide.", "Impossible de charger les contacts.");
        }
    }

    @FXML
    public void initialize() {
        // Initial setup, like clearing the box and showing loading indicator
        contactsBox.getChildren().clear();
        if (loadingIndicator != null) {
            loadingIndicator.setVisible(true);
        }
        // Don't load contacts here, wait for setUserId to be called
    }

    private void loadContacts() {
        if (currentUserId == -1) {
            System.err.println("Cannot load contacts: currentUserId is not set.");
            if (loadingIndicator != null) loadingIndicator.setVisible(false);
            return;
        }

        // Show loading indicator
        if (loadingIndicator != null) loadingIndicator.setVisible(true);
        contactsBox.getChildren().clear(); // Clear previous contacts

        // Run database fetching in a background thread to avoid blocking UI
        new Thread(() -> {
            try {
                // *** NOTE: Direct database access from the client is NOT recommended
                // *** in a real application. This should ideally be a request to the server.
                List<User> contacts = getContactsFromDatabase(currentUserId);

                // Update UI on the JavaFX Application Thread
                Platform.runLater(() -> {
                    if (loadingIndicator != null) loadingIndicator.setVisible(false);
                    if (contacts.isEmpty()) {
                        contactsBox.getChildren().add(new Label("Vous n'avez aucun contact pour le moment."));
                    } else {
                        for (User user : contacts) {
                            contactsBox.getChildren().add(createContactItemBox(user));
                        }
                    }
                });

            } catch (SQLException e) {
                e.printStackTrace();
                Platform.runLater(() -> {
                    if (loadingIndicator != null) loadingIndicator.setVisible(false);
                    showError("Erreur Base de Données", "Impossible de récupérer les contacts.", e.getMessage());
                });
            } catch (Exception e) { // Catch other potential errors
                e.printStackTrace();
                Platform.runLater(() -> {
                    if (loadingIndicator != null) loadingIndicator.setVisible(false);
                    showError("Erreur Inattendue", "Une erreur est survenue.", e.getMessage());
                });
            }
        }).start();
    }

    // Creates the visual representation (HBox) for a single contact
    private HBox createContactItemBox(User user) {
        HBox contactItem = new HBox(10);
        contactItem.setAlignment(Pos.CENTER_LEFT);
        contactItem.setStyle("-fx-padding: 5px; -fx-border-color: #cccccc; -fx-border-width: 0 0 1px 0;"); // Add padding and border

        // Default User Image
        ImageView userImage = new ImageView();
        try {
            // Load a default image from resources (adjust path as needed)
            InputStream imageStream = getClass().getResourceAsStream("/client/views/default_user.png");
            if (imageStream != null) {
                userImage.setImage(new Image(imageStream));
            } else {
                System.err.println("Default user image not found.");
                // Optionally set a placeholder color or leave empty
            }
        } catch (Exception e) {
            System.err.println("Error loading default user image: " + e.getMessage());
        }
        userImage.setFitHeight(40); // Smaller image
        userImage.setFitWidth(40);

        // User Info (Name)
        VBox userInfo = new VBox();
        Label nameLabel = new Label(user.getName()); // Use getName() from User model
        nameLabel.setStyle("-fx-font-size: 14px; -fx-font-weight: bold;");

        // Add last message
        String lastMessage = getLastMessage(currentUserId, user.getId());
        Label lastMessageLabel = new Label(lastMessage);
        lastMessageLabel.setStyle("-fx-font-size: 12px; -fx-text-fill: #888888;");

        userInfo.getChildren().addAll(nameLabel, lastMessageLabel);

        contactItem.getChildren().addAll(userImage, userInfo);

        // Add click listener to the HBox (optional - for opening chat)
        contactItem.setOnMouseClicked(event -> {
            System.out.println("Clicked on contact: " + user.getName() + " (ID: " + user.getId() + ")");
            openChatView(user);
        });


        return contactItem;
    }

    private void openChatView(User user) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/client/views/ChatView.fxml"));
            Parent root = loader.load();

            ChatController chatController = loader.getController();
            chatController.setClient(client); // Corrected line
            chatController.setReceiverId(user.getId());
            chatController.setCurrentUserId(currentUserId);
            chatController.loadMessages(); // Load messages after setting IDs

            Stage stage = new Stage();
            stage.setTitle("Chat with " + user.getName());
            stage.setScene(new Scene(root, 600, 400));
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
            showError("Error", "Could not open chat window", e.getMessage());
        }
    }

    // Fetches contacts directly from the database (Not Recommended for Client)
    private List<User> getContactsFromDatabase(int userId) throws SQLException {
        List<User> contacts = new ArrayList<>();
        // Query to get accepted contacts where the current user is either utilisateur_1 or utilisateur_2
        String query = "SELECT u.id_utilisateur, u.email, u.nom, u.prenom " +
                "FROM Utilisateurs u " +
                "JOIN Contacts c ON (u.id_utilisateur = c.utilisateur_2 AND c.utilisateur_1 = ?) OR (u.id_utilisateur = c.utilisateur_1 AND c.utilisateur_2 = ?) " +
                "WHERE u.id_utilisateur != ?"; // Ensure status is accepted and don't select the user themselves

        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;

        try {
            // Establish connection using Config details
            conn = DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
            stmt = conn.prepareStatement(query);
            stmt.setInt(1, userId); // Where user is utilisateur_1
            stmt.setInt(2, userId); // Where user is utilisateur_2
            stmt.setInt(3, userId); // Don't select the user themselves

            System.out.println("Executing contacts query for user ID: " + userId);
            rs = stmt.executeQuery();

            while (rs.next()) {
                int id = rs.getInt("id_utilisateur");
                String email = rs.getString("email");
                String firstName = rs.getString("nom");    // Assuming 'nom' is first name
                String lastName = rs.getString("prenom"); // Assuming 'prenom' is last name

                // Create User object (assuming 'online' status is not available/needed here)
                User contact = new User(id, email, firstName, lastName, false);
                contacts.add(contact);
                System.out.println("Found contact: " + contact.getName());
            }
            System.out.println("Total contacts found: " + contacts.size());

        } finally {
            // Ensure resources are closed
            if (rs != null) try { rs.close(); } catch (SQLException e) { e.printStackTrace(); }
            if (stmt != null) try { stmt.close(); } catch (SQLException e) { e.printStackTrace(); }
            if (conn != null) try { conn.close(); } catch (SQLException e) { e.printStackTrace(); }
        }

        return contacts;
    }

    private String getLastMessage(int senderId, int receiverId) {
        String lastMessage = "No messages yet";
        String query = "SELECT contenu FROM Messages WHERE " +
                "((expediteur_id = ? AND destinataire_id = ?) OR " +
                "(expediteur_id = ? AND destinataire_id = ?)) " +
                "AND destinataire_type = 'utilisateur' " +
                "ORDER BY date_envoi DESC LIMIT 1";

        try (Connection conn = DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {
            stmt.setInt(1, senderId);
            stmt.setInt(2, receiverId);
            stmt.setInt(3, receiverId);
            stmt.setInt(4, senderId);

            ResultSet rs = stmt.executeQuery();
            if (rs.next()) {
                lastMessage = rs.getString("contenu");
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return lastMessage;
    }

    // Helper method for showing errors
    private void showError(String title, String header, String content) {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle(title);
        alert.setHeaderText(header);
        alert.setContentText(content);
        alert.showAndWait();
    }
}
