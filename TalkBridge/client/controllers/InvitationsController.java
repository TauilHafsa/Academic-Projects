// InvitationsController.java
package client.controllers;

import client.Client;
import common.Protocol;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.scene.control.ListView;

import java.io.IOException;
import java.sql.SQLException;
import java.util.List;

public class InvitationsController {

    @FXML private ListView<String> invitationsListView;
    private ObservableList<String> invitations = FXCollections.observableArrayList();
    private int userId;
    private Client client;

    @FXML
    public void initialize() {
        invitationsListView.setItems(invitations);
    }

    public void setUserId(int userId) {
        this.userId = userId;
    }

    public void setClient(Client client) {
        this.client = client;
        loadInvitations(); // Load invitations after client is set
    }

    private void loadInvitations() {
        // Clear existing invitations
        invitations.clear();
        System.out.println("[DEBUG] Loading invitations for user ID: " + userId); // Log user ID

        // Fetch invitations from the server
        try {
            String response = client.sendRequest(Protocol.GET_INVITATIONS_CMD);
            System.out.println("[DEBUG] Received response: " + response); // Log raw response

            if (response.startsWith(Protocol.SUCCESS_PREFIX)) {
                String invitationsString = response.substring(Protocol.SUCCESS_PREFIX.length());
                String[] invitationsData = invitationsString.split(Protocol.SEPARATOR);
                System.out.println("[DEBUG] Number of invitations received: " + invitationsData.length); // Log number of invitations

                for (String email : invitationsData) {
                    System.out.println("[DEBUG] Adding invitation: " + email); // Log each invitation
                    invitations.add(email);
                }
            } else {
                System.err.println("Error loading invitations: " + response);
                // Handle error (e.g., show an alert)
            }
        } catch (IOException e) {
            System.err.println("Error communicating with server: " + e.getMessage());
            // Handle error (e.g., show an alert)
        }
    }

    @FXML
    private void handleAcceptInvitation() {
        String selectedInvitation = invitationsListView.getSelectionModel().getSelectedItem();
        if (selectedInvitation != null) {
            try {
                String response = client.sendRequest(Protocol.ACCEPT_INVITATION_CMD, selectedInvitation);
                if (response.startsWith(Protocol.SUCCESS_PREFIX)) {
                    System.out.println("Invitation accepted from: " + selectedInvitation);
                    invitations.remove(selectedInvitation);
                } else {
                    System.err.println("Error accepting invitation: " + response);
                    // Handle error (e.g., show an alert)
                }
            } catch (IOException e) {
                System.err.println("Error communicating with server: " + e.getMessage());
                // Handle error (e.g., show an alert)
            }
        }
    }

    @FXML
    private void handleDeclineInvitation() {
        String selectedInvitation = invitationsListView.getSelectionModel().getSelectedItem();
        if (selectedInvitation != null) {
            try {
                String response = client.sendRequest(Protocol.DECLINE_INVITATION_CMD, selectedInvitation);
                if (response.startsWith(Protocol.SUCCESS_PREFIX)) {
                    System.out.println("Invitation declined from: " + selectedInvitation);
                    invitations.remove(selectedInvitation);
                } else {
                    System.err.println("Error declining invitation: " + response);
                    // Handle error (e.g., show an alert)
                }
            } catch (IOException e) {
                System.err.println("Error communicating with server: " + e.getMessage());
                // Handle error (e.g., show an alert)
            }
        }
    }
}
