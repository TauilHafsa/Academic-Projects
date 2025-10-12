package client.controllers;

import javafx.fxml.FXML;
import javafx.scene.control.TextField;

public class AcceptInviteController {

    @FXML
    private TextField senderField;

    @FXML
    private void handleAccept() {
        String sender = senderField.getText();
        System.out.println("Invitation acceptée de : " + sender);
        // TODO: accepter l'invitation via client
    }
}
