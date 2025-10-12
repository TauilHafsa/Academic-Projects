package client.controllers;

import client.models.User;
import client.Client;
import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.control.Button;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.Parent;
import javafx.stage.Modality;
import javafx.stage.Stage;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.VBox;
import javafx.geometry.Insets;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.scene.input.MouseEvent;

import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import client.Config;
import java.util.List;
import java.util.ArrayList;

public class HomeController {

    @FXML private Label userLabel;
    @FXML private Button logoutButton;
    @FXML private Button showContactsButton;
    @FXML private Button addContactButton;
    @FXML private Button removeContactButton;
    @FXML private Button inviteContactButton;
    @FXML private Button viewInvitationsButton;
    @FXML private BorderPane mainContentArea;
    @FXML private Button addGroupButton;
    @FXML private Button removeGroupButton;
    @FXML private VBox conversationList;

    private int currentUserId;
    private User user;
    private Client client;
    private ObservableList<String> conversations = FXCollections.observableArrayList();

    public void setUserAndClient(User user, Client client) {
        this.user = user;
        this.client = client;
        if (user != null && client != null) {
            this.currentUserId = user.getId();
            client.setCurrentUserId(user.getId());
            userLabel.setText("Bienvenue, " + user.getName());
            System.out.println("HomeController initialized with User ID: " + this.currentUserId);
            loadConversations();
            handleShowContacts();
        } else {
            showError("Erreur", "Utilisateur ou client invalide", "Impossible de charger les informations utilisateur.");
        }
    }

    @FXML
    private void initialize() {
        logoutButton.setOnAction(event -> handleLogout());
        showContactsButton.setOnAction(event -> handleShowContacts());
        addContactButton.setOnAction(event -> handleOpenAddContact());
        removeContactButton.setOnAction(event -> handleRemoveContact());
        inviteContactButton.setOnAction(event -> handleInviteContact());
        viewInvitationsButton.setOnAction(event -> handleViewInvitations());
        addGroupButton.setOnAction(event -> handleAddGroup());
    }

    @FXML
    private void handleLogout() {
        Alert alert = new Alert(AlertType.CONFIRMATION);
        alert.setTitle("Déconnexion");
        alert.setHeaderText("Êtes-vous sûr de vouloir vous déconnecter ?");
        alert.setContentText("Vous serez redirigé vers la page de connexion.");

        if (alert.showAndWait().get() == javafx.scene.control.ButtonType.OK) {
            try {
                if (client != null) {
                    client.setCurrentUserId(-1);
                }
                this.client = null;
                this.user = null;
                this.currentUserId = -1;

                Stage stage = (Stage) logoutButton.getScene().getWindow();
                FXMLLoader loader = new FXMLLoader(getClass().getResource("/client/views/LoginView.fxml"));
                Parent root = loader.load();

                Client newClient = new Client();
                LoginController loginController = loader.getController();
                loginController.setClient(newClient);

                stage.setScene(new Scene(root));
                stage.setTitle("Chat Application - Connexion");
                stage.show();
            } catch (Exception e) {
                e.printStackTrace();
                showError("Erreur de Déconnexion", "Impossible de retourner à l'écran de connexion.", e.getMessage());
            }
        }
    }

    @FXML
    private void handleShowContacts() {
        loadViewIntoMainArea(
                "/client/views/ContactsView.fxml",
                controller -> {
                    if (controller instanceof ContactsController) {
                        ContactsController contactsController = (ContactsController) controller;
                        contactsController.setUserId(currentUserId);
                        contactsController.setClient(client);
                        System.out.println("Loading Contacts View for User ID: " + currentUserId);
                    }
                });
    }

    private void loadViewIntoMainArea(String fxmlPath, ControllerInitializer initializer) {
        if (client == null || currentUserId == -1) {
            showError("Erreur", "Non connecté", "Vous devez être connecté pour effectuer cette action.");
            return;
        }
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource(fxmlPath));
            Parent root = loader.load();

            Object controller = loader.getController();
            if (initializer != null) {
                initializer.initialize(controller);
            }

            if (mainContentArea != null) {
                mainContentArea.setCenter(root);
            } else {
                System.err.println("mainContentArea is null in HomeController. Cannot load view.");
                openModalWindow(fxmlPath, "View", initializer);
            }

        } catch (IOException e) {
            e.printStackTrace();
            showError("Erreur de Chargement", "Impossible de charger la vue: " + fxmlPath, e.getMessage());
        } catch (Exception e) {
            e.printStackTrace();
            showError("Erreur Inattendue", "Une erreur est survenue lors du chargement de la vue.", e.getMessage());
        }
    }

    @FXML

    private void handleOpenAddContact() {
        openModalWindow(
                "/client/views/AddContactView.fxml",
                "Ajouter un contact",
                controller -> {
                    if (controller instanceof AddContactController) {
                        AddContactController addController = (AddContactController) controller;
                        addController.setCurrentUserId(currentUserId);
                        // Ajouter un callback pour rafraîchir après l'ajout
                        addController.setOnContactAdded(() -> loadConversations());
                        System.out.println("Opening Add Contact for User ID: " + currentUserId);
                    }
                });
    }

    @FXML
    private void handleRemoveContact() {
        openModalWindow(
                "/client/views/RemoveContactView.fxml",
                "Supprimer un contact",
                controller -> {
                    if (controller instanceof RemoveContactController) {
                        ((RemoveContactController) controller).setClientAndUser(client, currentUserId);
                        System.out.println("Opening Remove Contact for User ID: " + currentUserId);
                    }
                });
    }

    @FXML
    private void handleInviteContact() {
        openModalWindow(
                "/client/views/InviteContactView.fxml",
                "Inviter un contact",
                controller -> {
                    if (controller instanceof InviteContactController) {
                        ((InviteContactController) controller).setClientAndUser(client, currentUserId);
                        System.out.println("Opening Invite Contact for User ID: " + currentUserId);
                    }
                });
    }

    @FXML
    private void handleViewInvitations() {
        openModalWindow(
                "/client/views/InvitationsVeiw.fxml",
                "Voir les invitations",
                controller -> {
                    if (controller instanceof InvitationsController) {
                        ((InvitationsController) controller).setUserId(currentUserId);
                        ((InvitationsController) controller).setClient(client);
                        System.out.println("Opening Invitations View for User ID: " + currentUserId);
                    }
                });
    }

    @FXML
    private void handleAddGroup() {
        openModalWindow(
                "/client/views/GroupCreationView.fxml",
                "Créer un groupe",
                controller -> {
                    if (controller instanceof GroupCreationController) {
                        ((GroupCreationController) controller).setClientAndUser(client, currentUserId);
                        System.out.println("Opening Group Creation View for User ID: " + currentUserId);
                    }
                });
    }

    private void openModalWindow(String fxmlPath, String title, ControllerInitializer initializer) {
        if (client == null || currentUserId == -1) {
            showError("Erreur", "Non connecté", "Vous devez être connecté pour effectuer cette action.");
            return;
        }
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource(fxmlPath));
            Parent root = loader.load();

            Object controller = loader.getController();
            if (initializer != null) {
                initializer.initialize(controller);
            }

            Stage stage = new Stage();
            stage.setTitle(title);
            stage.initModality(Modality.APPLICATION_MODAL);
            if (logoutButton != null && logoutButton.getScene() != null) {
                stage.initOwner(logoutButton.getScene().getWindow());
            }
            stage.setScene(new Scene(root));
            stage.showAndWait();
        } catch (IOException e) {
            e.printStackTrace();
            showError("Erreur", "Impossible d'ouvrir la fenêtre: " + title, e.getMessage());
        } catch (Exception e) {
            e.printStackTrace();
            showError("Erreur", "Une erreur inattendue est survenue.", e.getMessage());
        }
    }

    @FunctionalInterface
    interface ControllerInitializer {
        void initialize(Object controller);
    }

    private void showError(String title, String header, String content) {
        Alert alert = new Alert(AlertType.ERROR);
        alert.setTitle(title);
        alert.setHeaderText(header);
        alert.setContentText(content);
        alert.showAndWait();
    }

    private void loadConversations() {
        // Clear existing conversations
        conversationList.getChildren().clear();

        // Load contacts
        List<User> contactList = getContactsFromDatabase(currentUserId);
        for (User contact : contactList) {
            Label contactLabel = new Label(contact.getFirstName() + " " + contact.getLastName());
            contactLabel.setPadding(new Insets(5, 10, 5, 10));
            conversationList.getChildren().add(contactLabel);
        }

        // Load groups
        List<String> groupList = getGroupsFromDatabase(currentUserId);
        for (String groupName : groupList) {
            Label groupLabel = new Label("Groupe: " + groupName);
            groupLabel.setPadding(new Insets(5, 10, 5, 10));
            groupLabel.setStyle("-fx-font-weight: bold; -fx-text-fill: #2b579a;");
            conversationList.getChildren().add(groupLabel);
        }
    }

    private List<User> getContactsFromDatabase(int userId) {
        List<User> contacts = new ArrayList<>();
        String query =
                "SELECT u.id_utilisateur, u.email, u.nom, u.prenom "
                        + "FROM Utilisateurs u "
                        + "JOIN Contacts c ON (u.id_utilisateur = c.utilisateur_2 AND c.utilisateur_1 = ?) OR (u.id_utilisateur = c.utilisateur_1 AND c.utilisateur_2 = ?) "
                        + "WHERE u.id_utilisateur != ?";

        try (Connection conn =
                     DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
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

    private List<String> getGroupsFromDatabase(int userId) {
        List<String> groups = new ArrayList<>();
        String query =
                "SELECT g.nom_groupe FROM Groupes g "
                        + "JOIN Membre_Groupe mg ON g.id_groupe = mg.groupe_id "
                        + "WHERE mg.utilisateur_id = ?";

        try (Connection conn =
                     DriverManager.getConnection(Config.DB_URL, Config.DB_USER, Config.DB_PASSWORD);
             PreparedStatement stmt = conn.prepareStatement(query)) {

            stmt.setInt(1, userId);

            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                String groupName = rs.getString("nom_groupe");
                groups.add(groupName);
            }

        } catch (SQLException e) {
            System.err.println("Error fetching groups from database: " + e.getMessage());
        }

        return groups;
    }

    private void handleContactClick(User contact) {
        System.out.println("Contact clicked: " + contact.getName());
    }

    private void handleGroupClick(String groupName) {
        System.out.println("Group clicked: " + groupName);
    }

    @FXML
    private void handleShowGroups() {
        loadViewIntoMainArea(
                "/client/views/GroupsView.fxml",
                controller -> {
                    if (controller instanceof GroupsController) {
                        GroupsController groupsController = (GroupsController) controller;
                        groupsController.setUserId(currentUserId);
                        groupsController.setClient(client);
                        System.out.println("Loading Groups View for User ID: " + currentUserId);
                    }
                });
    }

}
