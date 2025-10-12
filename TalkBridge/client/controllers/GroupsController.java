package client.controllers;

import client.Client;
import client.Config;
import javafx.fxml.FXML;
import javafx.scene.control.ListView;
import javafx.scene.control.Label;
import javafx.application.Platform;
import javafx.scene.input.MouseEvent;
import javafx.scene.control.TextField;
import javafx.scene.control.Button;
import javafx.scene.control.TextArea;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class GroupsController {
    @FXML private ListView<String> groupsListView;
    @FXML private Label titleLabel;
    @FXML private ListView<String> groupMembersListView; // Keep list view for group members
    @FXML private TextArea chatArea; // Add text area for chat
    @FXML private TextField messageField;
    @FXML private Button sendButton;

    private int userId;
    private Client client;
    private String selectedGroup;
    private int selectedGroupId;

    public void initialize() {
        titleLabel.setText("Mes Groupes");

        // Add listener to groupsListView
        groupsListView.setOnMouseClicked(this::handleGroupClick);

        // Add action to sendButton
        sendButton.setOnAction(event -> sendMessageToGroup());
    }

    public void setUserId(int userId) {
        this.userId = userId;
        loadGroups();
    }

    public void setClient(Client client) {
        this.client = client;
    }

    private void loadGroups() {
        Platform.runLater(() -> {
            try {
                List<String> groups = getGroupsFromDatabase(userId);
                groupsListView.getItems().clear(); // Clear the list before adding new items
                if (groups != null) {
                    groupsListView.getItems().addAll(groups);
                } else {
                    System.out.println("GroupsController: getGroupsFromDatabase returned null");
                }
            } catch (Exception e) {
                System.err.println("GroupsController: Exception in loadGroups: " + e.getMessage());
                e.printStackTrace();
            }
        });
    }

    private List<String> getGroupsFromDatabase(int userId) {
        List<String> groups = new ArrayList<>();
        String query = "SELECT g.nom_groupe FROM Groupes g " +
                "JOIN Membre_Groupe mg ON g.id_groupe = mg.groupe_id " +
                "WHERE mg.utilisateur_id = ?";

        try (Connection conn = DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setInt(1, userId);
            System.out.println("GroupsController: SQL Query: " + query);
            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                String groupName = rs.getString("nom_groupe");
                System.out.println("GroupsController: Group found: " + groupName);
                groups.add(groupName);
            }

        } catch (SQLException e) {
            System.err.println("Error fetching groups from database: " + e.getMessage());
            return null;
        }

        return groups;
    }

    @FXML
    private void handleGroupClick(MouseEvent event) {
        selectedGroup = groupsListView.getSelectionModel().getSelectedItem();
        if (selectedGroup != null) {
            try {
                selectedGroupId = getGroupIdFromDatabase(selectedGroup);
                loadGroupMembers(selectedGroup); // Load members instead of messages
                loadGroupMessages(selectedGroupId); // Load messages
            } catch (SQLException e) {
                System.err.println("Error getting group ID: " + e.getMessage());
            }
        }
    }

    private void loadGroupMembers(String groupName) {
        Platform.runLater(() -> {
            try {
                List<String> members = getGroupMembers(groupName);
                groupMembersListView.getItems().clear();
                groupMembersListView.getItems().addAll(members);
            } catch (Exception e) {
                System.err.println("GroupsController: Exception in loadGroupMembers: " + e.getMessage());
                e.printStackTrace();
            }
        });
    }

    private List<String> getGroupMembers(String groupName) {
        List<String> members = new ArrayList<>();
        String query = "SELECT u.id_utilisateur, u.email, u.nom, u.prenom FROM Utilisateurs u " +
                "JOIN Membre_Groupe mg ON u.id_utilisateur = mg.utilisateur_id " +
                "JOIN Groupes g ON mg.groupe_id = g.id_groupe " +
                "WHERE g.nom_groupe = ?";

        try (Connection conn = DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString(1, groupName);
            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                int memberId = rs.getInt("id_utilisateur");
                String memberEmail = rs.getString("email");
                String memberFirstName = rs.getString("nom");
                String memberLastName = rs.getString("prenom");

                String displayName = determineDisplayName(memberId, memberEmail, memberFirstName, memberLastName);
                members.add(displayName);
            }

        } catch (SQLException e) {
            System.err.println("Error fetching group members from database: " + e.getMessage());
        }

        return members;
    }

    private String determineDisplayName(int memberId, String memberEmail, String memberFirstName, String memberLastName) {
        // Check if the member is a contact
        String displayName = memberEmail; // Default to email
        // TODO: Implement logic to check if member is a contact and get the name
        return displayName;
    }

    private void loadGroupMessages(int groupId) {
        Platform.runLater(() -> {
            try {
                List<String> messages = getGroupMessages(groupId);
                chatArea.clear();
                for (String message : messages) {
                    chatArea.appendText(message + "\n");
                }
            } catch (Exception e) {
                System.err.println("GroupsController: Exception in loadGroupMessages: " + e.getMessage());
                e.printStackTrace();
            }
        });
    }

    private List<String> getGroupMessages(int groupId) {
        List<String> messages = new ArrayList<>();
        String query = "SELECT u.nom, u.prenom, m.contenu FROM Messages m " +
                "JOIN Utilisateurs u ON m.expediteur_id = u.id_utilisateur " +
                "WHERE m.destinataire_id = ? AND m.destinataire_type = 'groupe'";

        try (Connection conn = DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setInt(1, groupId);
            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                String senderName = rs.getString("nom") + " " + rs.getString("prenom");
                String content = rs.getString("contenu");
                messages.add(senderName + ": " + content);
            }

        } catch (SQLException e) {
            System.err.println("Error fetching group messages from database: " + e.getMessage());
        }

        return messages;
    }

    private void sendMessageToGroup() {
        String message = messageField.getText().trim();
        if (message.isEmpty() || selectedGroup == null) {
            System.out.println("Message is empty or no group selected.");
            return;
        }

        try {
            if (selectedGroupId != -1) {
                // Send message to the group
                sendMessage(selectedGroupId, message);
                messageField.clear();
                loadGroupMessages(selectedGroupId); // Reload messages to display new message
            } else {
                System.out.println("Group ID not found for group: " + selectedGroup);
            }
        } catch (Exception e) {
            System.err.println("Error sending message to group: " + e.getMessage());
        }
    }

    private void sendMessage(int groupId, String message) {
        String query = "INSERT INTO Messages (expediteur_id, destinataire_id, contenu, destinataire_type) VALUES (?, ?, ?, 'groupe')";

        try (Connection conn = DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setInt(1, userId);
            stmt.setInt(2, groupId);
            stmt.setString(3, message);
            stmt.executeUpdate();

        } catch (SQLException e) {
            System.err.println("Error sending message to group: " + e.getMessage());
        }
    }

    private int getGroupIdFromDatabase(String groupName) throws SQLException {
        String query = "SELECT id_groupe FROM Groupes WHERE nom_groupe = ?";
        try (Connection conn = DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setString(1, groupName);
            ResultSet rs = stmt.executeQuery();

            if (rs.next()) {
                return rs.getInt("id_groupe");
            } else {
                return -1;
            }

        }
    }
}
