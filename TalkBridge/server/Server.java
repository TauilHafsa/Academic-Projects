package server;

import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;
import java.sql.SQLException;
import client.Config; // Assure-toi que le chemin est correct


public class Server {
    public static void main(String[] args) {
        try {
            Database database = new Database();
            ServerSocket serverSocket = new ServerSocket(Config.SERVER_PORT);
            System.out.println("Serveur démarré sur le port " + Config.SERVER_PORT);

            while (true) {
                Socket clientSocket = serverSocket.accept();
                new Thread(new ClientHandler(clientSocket, database)).start();
            }
        } catch (SQLException e) {
            System.err.println("Erreur de base de données: " + e.getMessage());
        } catch (IOException e) {
            System.err.println("Erreur de serveur: " + e.getMessage());
        }
    }
}
