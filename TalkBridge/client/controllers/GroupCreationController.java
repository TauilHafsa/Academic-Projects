package client.controllers;

import client.Client;
import client.Config;
import client.models.User;
import common.Protocol;
import javafx.beans.property.BooleanProperty;
import javafx.beans.property.SimpleBooleanProperty;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.scene.control.TextField;
import javafx.scene.control.ListCell;
import javafx.scene.control.CheckBox;
import javafx.util.Callback;
import javafx.stage.Stage;
import javafx.scene.layout.HBox;
import javafx.geometry.Pos;

import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class GroupCreationController {

    @FXML private TextField groupNameField;
    @FXML private ListView<ContactItem> contactsListView;
    @FXML private Button createButton;

    private Client client;
    private int currentUserId;
    private ObservableList<ContactItem> contacts = FXCollections.observableArrayList();

    public class ContactItem {
        private User user;
        private BooleanProperty selected;

        public ContactItem(User user) {
            this.user = user;
            this.selected = new SimpleBooleanProperty(false);
        }

        public User getUser() {
            return user;
        }

        public BooleanProperty selectedProperty() {
            return selected;
        }

        public boolean isSelected() {
            return selected.get();
        }

        public void setSelected(boolean selected) {
            this.selected.set(selected);
        }
    }

    @FXML
    public void initialize() {
        contactsListView.setItems(contacts);
        contactsListView.setCellFactory(new Callback<ListView<ContactItem>, ListCell<ContactItem>>() {
            @Override
            public ListCell<ContactItem> call(ListView<ContactItem> param) {
                return new ContactListCell();
            }
        });
    }

    static class ContactListCell extends ListCell<ContactItem> {
        @Override
        protected void updateItem(ContactItem item, boolean empty) {
            super.updateItem(item, empty);
            if (empty || item == null) {
                setText(null);
                setGraphic(null);
            } else {
                HBox hbox = new HBox();
                hbox.setAlignment(Pos.CENTER_LEFT);
                CheckBox checkBox = new CheckBox(item.getUser().getFirstName() + " " + item.getUser().getLastName() + " (" + item.getUser().getEmail() + ")");
                checkBox.selectedProperty().bindBidirectional(item.selectedProperty());
                hbox.getChildren().add(checkBox);
                setGraphic(hbox);
            }
        }
    }

    public void setClientAndUser(Client client, int userId) {
        this.client = client;
        this.currentUserId = userId;
        loadContacts();
    }

    private void loadContacts() {
        contacts.clear();
        List<User> contactList = getContactsFromDatabase(currentUserId);
        for (User user : contactList) {
            contacts.add(new ContactItem(user));
        }
    }

    @FXML
    private void handleCreateGroup() {
        String groupName = groupNameField.getText().trim();

        if (groupName.isEmpty()) {
            System.out.println("Group name cannot be empty.");
            return;
        }

        List<Integer> selectedContactIds = new ArrayList<>();
        for (ContactItem contactItem : contacts) {
            if (contactItem.isSelected()) {
                selectedContactIds.add(contactItem.getUser().getId());
            }
        }

        if (selectedContactIds.isEmpty()) {
            System.out.println("No contacts selected for the group.");
            return;
        }

        try {
            createGroupInDatabase(groupName, selectedContactIds);
            closeWindow();
        } catch (SQLException e) {
            System.err.println("Error creating group in database: " + e.getMessage());
        }
    }

    private void createGroupInDatabase(String groupName, List<Integer> selectedContactIds) throws SQLException {
        String insertGroupQuery = "INSERT INTO Groupes (nom_groupe, createur_id) VALUES (?, ?)";
        String getGroupIdQuery = "SELECT id_groupe FROM Groupes WHERE nom_groupe = ? AND createur_id = ?";
        String insertMemberQuery = "INSERT INTO Membre_Groupe (groupe_id, utilisateur_id, role) VALUES (?, ?, ?)";

        try (Connection conn = DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
             PreparedStatement insertGroupStmt = conn.prepareStatement(insertGroupQuery);
             PreparedStatement getGroupIdStmt = conn.prepareStatement(getGroupIdQuery);
             PreparedStatement insertMemberStmt = conn.prepareStatement(insertMemberQuery)) {

            // Insert the new group
            insertGroupStmt.setString(1, groupName);
            insertGroupStmt.setInt(2, currentUserId);
            insertGroupStmt.executeUpdate();

            // Get the group ID
            getGroupIdStmt.setString(1, groupName);
            getGroupIdStmt.setInt(2, currentUserId);
            ResultSet rs = getGroupIdStmt.executeQuery();
            int groupId = -1;
            if (rs.next()) {
                groupId = rs.getInt("id_groupe");
            }

            // Add the creator to the group as admin
            insertMemberStmt.setInt(1, groupId);
            insertMemberStmt.setInt(2, currentUserId);
            insertMemberStmt.setString(3, "admin"); // Role admin pour le créateur
            insertMemberStmt.executeUpdate();

            // Add the selected contacts to the group
            for (int contactId : selectedContactIds) {
                insertMemberStmt.setInt(1, groupId);
                insertMemberStmt.setInt(2, contactId);
                insertMemberStmt.setString(3, "membre"); // Role membre pour les autres
                insertMemberStmt.executeUpdate();
            }
        }
    }

    private List<User> getContactsFromDatabase(int userId) {
        List<User> contacts = new ArrayList<>();
        String query = "SELECT u.id_utilisateur, u.email, u.nom, u.prenom " +
                "FROM Utilisateurs u " +
                "JOIN Contacts c ON (u.id_utilisateur = c.utilisateur_2 AND c.utilisateur_1 = ?) OR (u.id_utilisateur = c.utilisateur_1 AND c.utilisateur_2 = ?) " +
                "WHERE u.id_utilisateur != ?";

        try (Connection conn = DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setInt(1, userId);
            stmt.setInt(2, userId);
            stmt.setInt(3, userId);

            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                int id = rs.getInt("id_utilisateur");
                String email = rs.getString("email");
                String firstName = rs.getString("nom");
                String lastName = rs.getString("prenom");

                User contact = new User(id, email, firstName, lastName, false);
                contacts.add(contact);
            }

        } catch (SQLException e) {
            System.err.println("Error fetching contacts from database: " + e.getMessage());
        }

        return contacts;
    }

    private int getUserIdByEmail(String email) throws SQLException {
        String query = "SELECT id_utilisateur FROM utilisateurs WHERE email = ?";
        try (Connection conn = DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {
            stmt.setString(1, email);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) {
                return rs.getInt("id_utilisateur");
            } else {
                throw new SQLException("User not found with email: " + email);
            }
        }
    }

    private void closeWindow() {
        Stage stage = (Stage) createButton.getScene().getWindow();
        if (stage != null) {
            stage.close();
        }
    }
}
