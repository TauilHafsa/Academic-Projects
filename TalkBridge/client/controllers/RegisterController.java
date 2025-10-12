package client.controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.stage.Stage;
import client.Client;

import java.io.IOException;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class RegisterController {
    @FXML private TextField firstNameField;
    @FXML private TextField lastNameField;
    @FXML private TextField emailField;
    @FXML private PasswordField passwordField;
    @FXML private PasswordField confirmPasswordField;
    @FXML private Button registerButton;
    @FXML private Button loginButton;
    @FXML private Label errorLabel;

    private Client client;

    public void setClient(Client client) {
        this.client = client;
    }

    @FXML
    private void initialize() {
        registerButton.setOnAction(event -> handleRegister());
        loginButton.setOnAction(event -> showLoginView());
    }

    private void handleRegister() {
        String firstName = firstNameField.getText().trim();
        String lastName = lastNameField.getText().trim();
        String email = emailField.getText().trim();
        String password = passwordField.getText().trim();
        String confirmPassword = confirmPasswordField.getText().trim();

        if (firstName.isEmpty() || lastName.isEmpty() || email.isEmpty() || password.isEmpty()) {
            errorLabel.setText("Veuillez remplir tous les champs");
            return;
        }

        if (!isValidEmail(email)) {
            errorLabel.setText("Format d'email invalide");
            return;
        }

        if (!password.equals(confirmPassword)) {
            errorLabel.setText("Les mots de passe ne correspondent pas");
            return;
        }

        try {
            String response = client.sendAuthRequest(common.Protocol.REGISTER_CMD, email, password, firstName, lastName);
            if (response.startsWith(common.Protocol.SUCCESS_PREFIX)) {
                showLoginView();
            } else if (response.contains("EMAIL_EXISTS")) {
                errorLabel.setText("Cet email est déjà utilisé");
            } else {
                errorLabel.setText("Erreur lors de l'inscription");
            }
        } catch (IOException e) {
            errorLabel.setText("Erreur de connexion au serveur");
            e.printStackTrace();
        }
    }

    private void showLoginView() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("../views/LoginView.fxml"));
            Parent root = loader.load();

            LoginController controller = loader.getController();
            controller.setClient(client);

            Stage stage = (Stage) loginButton.getScene().getWindow();
            stage.setScene(new Scene(root, 400, 400));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private boolean isValidEmail(String email) {
        String regex = "^[\\w-\\.]+@([\\w-]+\\.)+[\\w-]{2,4}$";
        Pattern pattern = Pattern.compile(regex);
        Matcher matcher = pattern.matcher(email);
        return matcher.matches();
    }
}
