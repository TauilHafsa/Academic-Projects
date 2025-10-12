package client.controllers;

import client.Client;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import javafx.stage.FileChooser;

import java.io.File;
import java.io.IOException;
import java.util.Arrays;
import java.util.List;
import java.util.stream.Collectors;

import common.Protocol;

public class ChatController {

    @FXML private TextArea chatArea;
    @FXML private TextField messageField;
    @FXML private Button sendButton;
    @FXML private Button attachButton;

    private Client client;
    private int receiverId;
    private int currentUserId;
    private String filePath;

    public void setClient(Client client) {
        this.client = client;
        loadMessages();
    }

    public void setReceiverId(int receiverId) {
        this.receiverId = receiverId;
    }

    public void setCurrentUserId(int currentUserId) {
        this.currentUserId = currentUserId;
    }

    @FXML
    public void initialize() {
        sendButton.setOnAction(event -> sendMessage());
        attachButton.setOnAction(event -> attachFile());
    }

    public void loadMessages() {
        try {
            String response = client.getMessages(receiverId);
            if (response.startsWith(Protocol.SUCCESS_PREFIX)) {
                String messagesString = response.substring(Protocol.SUCCESS_PREFIX.length());
                chatArea.clear();

                if (!messagesString.isEmpty()) {
                    String[] messages = messagesString.split(Protocol.SEPARATOR);

                    for (String message : messages) {
                        if (message == null || message.trim().isEmpty()) continue;

                        String[] parts = message.split(Protocol.MESSAGE_SEPARATOR, 2);
                        if (parts.length == 2) {
                            try {
                                int senderId = Integer.parseInt(parts[0]);
                                String content = parts[1].trim();
                                String senderName = (senderId == currentUserId) ? "Me" : client.getUserNameById(senderId);

                                // Nettoyer le contenu des artefacts restants
                                content = content.replaceAll("\\|\\|", "").replace("[Fichier: null]", "");

                                if (!content.isEmpty()) {
                                    chatArea.appendText(senderName + ": " + content + "\n");
                                }
                            } catch (Exception e) {
                                System.err.println("Error processing message: " + message);
                            }
                        }
                    }
                }
            } else {
                chatArea.appendText("Error: " + response.substring(Protocol.ERROR_PREFIX.length()) + "\n");
            }
        } catch (IOException e) {
            chatArea.appendText("Error loading messages: " + e.getMessage() + "\n");
        }
    }

    private void sendMessage() {
        String message = messageField.getText().trim();
        if (!message.isEmpty() || filePath != null) {
            try {
                // Nettoyer le message avant envoi
                String cleanMessage = message.replace("Attached file:", "").trim();
                client.sendMessage(receiverId, cleanMessage, filePath);

                // Afficher le message immédiatement
                String displayMessage = "Me: " + cleanMessage;
                if (filePath != null) {
                    displayMessage += " [Fichier: " + new File(filePath).getName() + "]";
                }
                chatArea.appendText(displayMessage + "\n");

                messageField.clear();
                filePath = null;
            } catch (IOException e) {
                chatArea.appendText("Error sending message: " + e.getMessage() + "\n");
            }
        }
    }

    private void attachFile() {
        FileChooser fileChooser = new FileChooser();
        File file = fileChooser.showOpenDialog(null);
        if (file != null) {
            filePath = file.getAbsolutePath();
            messageField.setText("Pièce jointe: " + file.getName());
        }
    }
    private String getSenderName(int senderId) throws IOException {
        if (senderId == currentUserId) {
            return "Me";
        } else {
            // Fetch the sender's name from the server
            return client.getUserNameById(senderId);
        }
    }
}
