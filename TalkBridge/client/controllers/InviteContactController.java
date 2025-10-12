package client.controllers;

import client.Client;
import common.Protocol;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

import java.io.IOException;

public class InviteContactController {

    @FXML private TextField emailField;
    @FXML private Button inviteButton;
    @FXML private Button cancelButton;
    @FXML private Label statusLabel;

    private Client client;
    private int currentUserId;

    public void setClientAndUser(Client client, int userId) {
        this.client = client;
        this.currentUserId = userId;
    }

    @FXML
    private void initialize() {
        statusLabel.setText("");
        statusLabel.setVisible(false);
    }

    @FXML
    private void handleInvite() {
        String email = emailField.getText().trim();

        if (email.isEmpty()) {
            statusLabel.setText("Veuillez entrer un email.");
            statusLabel.setVisible(true);
            return;
        }

        if (client == null || currentUserId == -1) {
            statusLabel.setText("Client ou utilisateur non initialisé.");
            statusLabel.setVisible(true);
            return;
        }

        try {
            String response = client.sendRequest(Protocol.INVITE_CONTACT_CMD, email);

            if (response.startsWith(Protocol.SUCCESS_PREFIX)) {
                statusLabel.setText("Invitation envoyée à " + email);
                statusLabel.setVisible(true);
                emailField.clear();
            } else {
                statusLabel.setText("Erreur: " + response.substring(Protocol.ERROR_PREFIX.length()));
                statusLabel.setVisible(true);
            }
        } catch (IOException e) {
            statusLabel.setText("Erreur de communication: " + e.getMessage());
            statusLabel.setVisible(true);
        }
    }

    @FXML
    private void handleCancel() {
        Stage stage = (Stage) cancelButton.getScene().getWindow();
        stage.close();
    }
}
