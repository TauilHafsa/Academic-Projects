package client.controllers;

import javafx.fxml.FXML;
import javafx.scene.control.TextField;

public class DeclineInviteController {

    @FXML
    private TextField senderField;

    @FXML
    private void handleDecline() {
        String sender = senderField.getText();
        System.out.println("Invitation refusée de : " + sender);
        // TODO: refuser l'invitation via client
    }
}
