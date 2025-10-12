package client.controllers;

import client.Client;
import common.Protocol;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

import java.io.IOException;

public class RemoveContactController {

    @FXML private TextField contactEmailField;
    @FXML private Button confirmRemoveButton;
    @FXML private Button cancelButton;
    @FXML private Label statusLabel;

    private Client client;
    private int currentUserId;

    public void setClientAndUser(Client client, int userId) {
        this.client = client;
        this.currentUserId = userId;
        if (this.client == null) {
            System.err.println("RemoveContactController: Client instance is null!");
            updateStatusLabel("Erreur: Client non initialisé.", true);
            confirmRemoveButton.setDisable(true);
        }
        if (this.currentUserId == -1) {
            System.err.println("RemoveContactController: User ID is invalid!");
            updateStatusLabel("Erreur: Utilisateur non valide.", true);
            confirmRemoveButton.setDisable(true);
        }
    }

    @FXML
    private void initialize() {
        updateStatusLabel("", false); // Clear status and hide initially
    }

    // Helper method to update status label text and visibility/managed state
    private void updateStatusLabel(String text, boolean isVisible) {
        statusLabel.setText(text);
        statusLabel.setVisible(isVisible);
        statusLabel.setManaged(isVisible); // Make it take up space only when visible
        if (text.startsWith("Erreur")) {
            statusLabel.setStyle("-fx-text-fill: red; -fx-font-weight: bold;");
        } else if (text.startsWith("Contact supprimé")) {
            statusLabel.setStyle("-fx-text-fill: green; -fx-font-weight: bold;");
        } else {
            statusLabel.setStyle("-fx-text-fill: red; -fx-font-weight: bold;"); // Default error style if needed
        }
    }


    @FXML
    private void handleConfirmRemove() {
        String contactEmail = contactEmailField.getText().trim();
        updateStatusLabel("", false); // Clear previous status

        if (contactEmail.isEmpty()) {
            updateStatusLabel("Veuillez entrer l'email du contact.", true);
            return;
        }

        if (client == null || currentUserId == -1) {
            updateStatusLabel("Erreur: Client ou utilisateur non initialisé.", true);
            return;
        }


        try {
            String response = client.sendDeleteContactRequest(contactEmail);

            if (response != null && response.startsWith(Protocol.SUCCESS_PREFIX)) {
                updateStatusLabel("Contact supprimé avec succès !", true);
                contactEmailField.setDisable(true);
                confirmRemoveButton.setDisable(true);
                closeWindowAfterDelay(1500);

            } else {
                String errorMessage = (response != null) ? response.substring(Protocol.ERROR_PREFIX.length()) : "Réponse invalide du serveur";
                updateStatusLabel("Erreur: " + errorMessage, true);
            }

        } catch (IOException e) {
            updateStatusLabel("Erreur de communication: " + e.getMessage(), true);
            e.printStackTrace();
        } catch (Exception e) {
            updateStatusLabel("Erreur inattendue: " + e.getMessage(), true);
            e.printStackTrace();
        }
    }

    @FXML
    private void handleCancel() {
        closeWindow();
    }

    private void closeWindow() {
        Stage stage = (Stage) cancelButton.getScene().getWindow();
        if (stage != null) {
            stage.close();
        }
    }

    private void closeWindowAfterDelay(long delayMillis) {
        new Thread(() -> {
            try {
                Thread.sleep(delayMillis);
                javafx.application.Platform.runLater(this::closeWindow);
            } catch (InterruptedException e) {
                Thread.currentThread().interrupt();
                e.printStackTrace();
            }
        }).start();
    }
}
