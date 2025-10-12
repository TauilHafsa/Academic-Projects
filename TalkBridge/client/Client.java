package client;

import client.Config;
import client.controllers.LoginController;
import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.io.*;
import java.net.Socket;
import java.net.SocketException;
import common.Protocol; // Import Protocol

public class Client extends Application {
    private Socket socket;
    private PrintWriter out;
    private BufferedReader in;
    private int currentUserId = -1; // Store logged-in user ID

    @Override
    public void start(Stage primaryStage) throws Exception {
        connectToServer(Config.SERVER_ADDRESS, Config.SERVER_PORT);

        FXMLLoader loader = new FXMLLoader(getClass().getResource("views/LoginView.fxml"));
        Parent root = loader.load();

        LoginController controller = loader.getController();
        controller.setClient(this); // Pass the client instance

        primaryStage.setTitle("Chat Application - Connexion");
        primaryStage.setScene(new Scene(root, 400, 400));
        primaryStage.show();
    }

    public void connectToServer(String host, int port) throws IOException {
        try {
            socket = new Socket(host, port);
            out = new PrintWriter(socket.getOutputStream(), true);
            in = new BufferedReader(new InputStreamReader(socket.getInputStream()));
            System.out.println("Connected to server: " + host + ":" + port);
        } catch (IOException e)
        {
            System.err.println("Failed to connect to server: " + e.getMessage());
            throw e; // Re-throw to indicate connection failure
        }
    }

    // Method to set the current user ID after successful login
    public void setCurrentUserId(int userId) {
        this.currentUserId = userId;
        System.out.println("Client user ID set to: " + userId);
    }

    // Getter for current user ID
    public int getCurrentUserId() {
        return this.currentUserId;
    }

    // Generic method to send requests and receive responses
    public String sendRequest(String command, String... params) throws IOException {
        if (socket == null || socket.isClosed() || out == null || in == null) {
            throw new IOException("Not connected to server or connection closed.");
        }

        StringBuilder requestBuilder = new StringBuilder(command);
        for (String param : params) {
            requestBuilder.append(Protocol.SEPARATOR).append(param);
        }
        String request = requestBuilder.toString();

        System.out.println("Sending request: " + request);
        out.println(request);
        out.flush(); // Ensure data is sent immediately

        try {
            // Read response from server
            String response = in.readLine();
            if (response == null) {
                // Server likely closed the connection
                throw new IOException("Server closed the connection unexpectedly.");
            }
            System.out.println("Received response: " + response);
            return response;
        } catch (SocketException e) {
            System.err.println("Socket error during read: " + e.getMessage());
            // Attempt to reconnect or handle gracefully
            closeConnection(); // Close potentially broken connection
            throw new IOException("Connection error: " + e.getMessage(), e);
        } catch (IOException e) {
            System.err.println("Error reading response: " + e.getMessage());
            closeConnection(); // Close potentially broken connection
            throw e; // Re-throw the exception
        }
    }

    // Specific method for authentication requests (can be merged with sendRequest later if desired)
    public String sendAuthRequest(String command, String... params) throws IOException {
        // For now, just calls the generic sendRequest method
        return sendRequest(command, params);
    }

    // Specific method for deleting a contact
    public String sendDeleteContactRequest(String contactEmail) throws IOException {
        if (currentUserId == -1) {
            throw new IOException("User not logged in. Cannot delete contact.");
        }
        // The server uses the logged-in user ID associated with the ClientHandler thread
        return sendRequest(Protocol.DELETE_CONTACT_CMD, contactEmail);
    }

    public String sendMessage(int receiverId, String content, String filePath) throws IOException {
        if (currentUserId == -1) {
            throw new IOException("User not logged in. Cannot send message.");
        }
        return sendRequest(Protocol.SEND_MESSAGE_CMD, String.valueOf(receiverId), content, filePath);
    }

    public String getMessages(int receiverId) throws IOException {
        if (currentUserId == -1) {
            throw new IOException("User not logged in. Cannot get messages.");
        }
        return sendRequest(Protocol.GET_MESSAGES_CMD, String.valueOf(receiverId));
    }

    // Ajoutez cette méthode à la classe Client
    public String getUserNameById(int userId) throws IOException {
        if (userId == -1) {
            throw new IOException("User ID is not valid.");
        }

        // Envoi d'une requête pour obtenir le nom d'utilisateur en fonction de son ID
        return sendRequest(Protocol.GET_USERNAME_CMD, String.valueOf(userId));
    }


    private void closeConnection() {
        System.out.println("Closing client connection...");
        try {
            if (out != null) out.close();
            if (in != null) in.close();
            if (socket != null && !socket.isClosed()) socket.close();
            System.out.println("Client connection closed.");
        } catch (IOException e) {
            System.err.println("Error closing client resources: " + e.getMessage());
        } finally {
            out = null;
            in = null;
            socket = null;
            currentUserId = -1; // Reset user ID on disconnect
        }
    }

    @Override
    public void stop() throws Exception {
        closeConnection();
        super.stop();
    }

    public static void main(String[] args) {
        launch(args);
    }

    public byte[] downloadFile(String fileName) throws IOException {
        // Envoyer la requête de téléchargement
        out.println(Protocol.DOWNLOAD_FILE + fileName);
        out.flush();

        // Lire la réponse du serveur
        String response = in.readLine();

        if (response.startsWith(Protocol.SUCCESS_PREFIX)) {
            // Lire la taille du fichier
            int fileSize = Integer.parseInt(in.readLine());
            byte[] fileData = new byte[fileSize];

            // Lire les données binaires
            DataInputStream dataIn = new DataInputStream(socket.getInputStream());
            dataIn.readFully(fileData); // Lit exactement fileSize bytes

            return fileData;
        } else {
            throw new IOException(response.substring(Protocol.ERROR_PREFIX.length()));
        }
    }
}
