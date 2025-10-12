


package client.controllers;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;// Tu charges une nouvelle page/interface.
import javafx.scene.Parent;// Je charge la page FXML et je récupère la racine
import javafx.scene.Scene;//afin de pouvoir définir ce qui doit être affiché à l'écran (les boutons, champs de texte, etc.).
import javafx.scene.control.*;//Permet d'utiliser les éléments de l'interface utilisateur tels que les boutons, champs de texte, labels, etc.
import javafx.stage.Stage;//Permet de gérer les fenêtres (Stage) de l'application JavaFX
import client.Client;
import client.models.User;
import common.Protocol; // façon d’écrire les messages entre ton application et le serveur.

import java.io.IOException;

public class LoginController {
    @FXML private TextField emailField;
    @FXML private PasswordField passwordField;
    @FXML private Button loginButton;
    @FXML private Button registerButton;
    @FXML private Label errorLabel;

    private Client client;

    public void setClient(Client client) {
        this.client = client;
        if (this.client == null) {
            System.err.println("LoginController: Client instance is null!");
            errorLabel.setText("Erreur: Client non initialisé.");
            loginButton.setDisable(true);
            registerButton.setDisable(true);
        }
    }

    @FXML
    private void initialize() {
        loginButton.setOnAction(event -> handleLogin());
        registerButton.setOnAction(event -> showRegisterView());
        errorLabel.setText(""); // Clear error label initially
    }


    private void handleLogin() {
        String email = emailField.getText().trim();
        String password = passwordField.getText().trim(); // NOTE: Sending plain text password
        errorLabel.setText(""); // Clear previous errors

        if (email.isEmpty() || password.isEmpty()) {
            errorLabel.setText("Veuillez remplir tous les champs");
            return;
        }

        if (client == null) {
            errorLabel.setText("Erreur: Connexion client non établie.");
            return;
        }


        try {
            // Ensure connection before attempting login
            // client.connectToServer(Config.SERVER_ADDRESS, Config.SERVER_PORT); // Reconnect if necessary, or handle connection state

            String loginResponse = client.sendAuthRequest(Protocol.LOGIN_CMD, email, password);
            System.out.println("Login Response: " + loginResponse);

            if (loginResponse != null && loginResponse.startsWith(Protocol.SUCCESS_PREFIX)) {
                // Login successful, now fetch user data
                System.out.println("Attempting to fetch user data for: " + email);
                String userDataResponse = client.sendAuthRequest(Protocol.GET_USER_BY_EMAIL, email);
                System.out.println("User Data Response: " + userDataResponse);

                if (userDataResponse != null && userDataResponse.startsWith(Protocol.SUCCESS_PREFIX)) {
                    String[] userData = userDataResponse.substring(Protocol.SUCCESS_PREFIX.length()).split(Protocol.SEPARATOR);
                    if (userData.length == 3) {
                        try {
                            int id = Integer.parseInt(userData[0]);
                            String firstName = userData[1]; // Assuming server sends nom as first name
                            String lastName = userData[2];  // Assuming server sends prenom as last name
                            User user = new User(id, email, firstName, lastName, true); // Assume online after login

                            // Inform the client instance about the logged-in user ID
                            client.setCurrentUserId(id);

                            showHomeView(user); // Pass the created User object
                        } catch (NumberFormatException e) {
                            errorLabel.setText("Erreur: Données utilisateur invalides reçues.");
                            System.err.println("Error parsing user ID: " + userData[0]);
                        }
                    } else {
                        errorLabel.setText("Erreur: Format de données utilisateur incorrect.");
                        System.err.println("Incorrect user data format: " + userDataResponse);
                    }
                } else {
                    String errorMsg = (userDataResponse != null) ? userDataResponse.substring(Protocol.ERROR_PREFIX.length()) : "Réponse invalide";
                    errorLabel.setText("Erreur récupération utilisateur: " + errorMsg);
                    System.err.println("Failed to get user data: " + userDataResponse);
                    // Consider logging out or handling this state
                }
            } else {
                String errorMsg = (loginResponse != null) ? loginResponse.substring(Protocol.ERROR_PREFIX.length()) : "Réponse invalide";
                errorLabel.setText("Échec connexion: " + errorMsg);
                System.err.println("Login failed: " + loginResponse);
            }
        } catch (IOException e) {
            errorLabel.setText("Erreur de connexion: " + e.getMessage());
            e.printStackTrace();
            // Maybe try reconnecting or disable login button temporarily
        } catch (Exception e) { // Catch unexpected errors
            errorLabel.setText("Erreur inattendue: " + e.getMessage());
            e.printStackTrace();
        }
    }

    private void showRegisterView() {
        if (client == null) {
            errorLabel.setText("Erreur: Connexion client non établie.");
            return;
        }
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("../views/RegisterView.fxml"));
            Parent root = loader.load();

            RegisterController controller = loader.getController();
            controller.setClient(client); // Pass the *same* client instance

            Stage stage = (Stage) registerButton.getScene().getWindow();
            stage.setScene(new Scene(root, 400, 500)); // Adjust size if needed
            stage.setTitle("Chat Application - Inscription");
        } catch (IOException e) {
            e.printStackTrace();
            errorLabel.setText("Impossible d'ouvrir la vue d'inscription.");
        }
    }

    private void showHomeView(User currentUser) {
        if (client == null) {
            errorLabel.setText("Erreur: Connexion client non établie.");
            return;
        }
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("../views/HomeView.fxml"));
            Parent root = loader.load();

            HomeController homeController = loader.getController();
            // Pass the User object AND the Client instance to HomeController
            homeController.setUserAndClient(currentUser, client);

            Stage stage = (Stage) loginButton.getScene().getWindow();
            stage.setScene(new Scene(root, 800, 600)); // Adjust size if needed
            stage.setTitle("Chat Application - Accueil");
        } catch (IOException e) {
            e.printStackTrace();
            errorLabel.setText("Impossible d'ouvrir la vue d'accueil.");
        }
    }
}
