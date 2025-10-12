package client.controllers;

import javafx.fxml.FXML;
import javafx.scene.control.TextField;
import javafx.scene.control.Label;
import javafx.stage.Stage;
import java.sql.*;
import client.Config;

public class AddContactController {

    @FXML
    private TextField emailField;

    @FXML
    private Label statusLabel;

    private int currentUserId;

    public void setCurrentUserId(int id) {
        this.currentUserId = id;
    }

    @FXML
    private void handleAddContact() {
        String contactEmail = emailField.getText().trim();

        if (contactEmail.isEmpty()) {
            statusLabel.setText("Veuillez entrer un email");
            return;
        }

        try (Connection conn = DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD)) {
            // First check if the contact exists
            String checkUserSql = "SELECT id_utilisateur FROM utilisateurs WHERE email = ?";
            try (PreparedStatement checkStmt = conn.prepareStatement(checkUserSql)) {
                checkStmt.setString(1, contactEmail);
                ResultSet rs = checkStmt.executeQuery();

                if (!rs.next()) {
                    statusLabel.setText("Cet utilisateur n'existe pas");
                    return;
                }

                int contactId = rs.getInt("id_utilisateur");

                System.out.println("Current User ID: " + currentUserId);
                System.out.println("Contact ID: " + contactId);

                // Check if already contacts
                String checkContactSql = "SELECT * FROM contacts WHERE " +
                        "(utilisateur_1 = ? AND utilisateur_2 = ?) OR " +
                        "(utilisateur_1 = ? AND utilisateur_2 = ?)";

                try (PreparedStatement contactStmt = conn.prepareStatement(checkContactSql)) {
                    contactStmt.setInt(1, currentUserId);
                    contactStmt.setInt(2, contactId);
                    contactStmt.setInt(3, contactId);
                    contactStmt.setInt(4, currentUserId);

                    if (contactStmt.executeQuery().next()) {
                        statusLabel.setText("Ce contact existe déjà");
                        return;
                    }
                }

                // Add new contact
                String insertSql = "INSERT INTO contacts (utilisateur_1, utilisateur_2, statut) VALUES (?, ?, 'en attente')";
                try (PreparedStatement insertStmt = conn.prepareStatement(insertSql)) {
                    insertStmt.setInt(1, currentUserId);
                    insertStmt.setInt(2, contactId);
                    System.out.println("Inserting into contacts: utilisateur_1 = " + currentUserId + ", utilisateur_2 = " + contactId);
                    insertStmt.executeUpdate();

                    // Add notification for the recipient
                    String notifSql = "INSERT INTO notifications (utilisateur_id, contenu, statut) VALUES (?, ?, 'non lu')";
                    try (PreparedStatement notifStmt = conn.prepareStatement(notifSql)) {
                        notifStmt.setInt(1, contactId);
                        notifStmt.setString(2, "Nouvelle demande de contact");
                        notifStmt.executeUpdate();
                    }

                    statusLabel.setText("Contact ajouté avec succès!");
                    statusLabel.setStyle("-fx-text-fill: green;");

                    // Close window after short delay
                    new Thread(() -> {
                        try {
                            Thread.sleep(1500);
                            javafx.application.Platform.runLater(() -> {

                                Stage stage = (Stage) emailField.getScene().getWindow();
                                stage.close();
                            });
                        } catch (InterruptedException e) {
                            e.printStackTrace();
                        }
                    }).start();
                }
            }

        } catch (SQLException e) {
            e.printStackTrace();
            statusLabel.setText("Erreur lors de l'ajout du contact: " + e.getMessage());
        }
    }

    @FXML
    private void handleCancel() {
        Stage stage = (Stage) emailField.getScene().getWindow();
        stage.close();
    }
}
