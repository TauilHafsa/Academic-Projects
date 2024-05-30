#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#ifdef _WIN32
#include <winsock2.h>
#include <ws2tcpip.h>
#else
#include <netinet/in.h>
#include <sys/socket.h>
#endif

#define CONNECTION_HOST "127.0.0.1"
#define LISTENING_PORT 5094
#define BUFFER_SIZE 1024

int main() {
#ifdef _WIN32
    WSADATA wsa;
    if (WSAStartup(MAKEWORD(2, 2), &wsa) != 0) {
        fprintf(stderr, "(CLIENT) Echec d'initialisation de winSock\n");
        exit(EXIT_FAILURE);
    }
#endif


    int socketFD = socket(AF_INET, SOCK_STREAM, 0);
    if (socketFD == -1) {
        fprintf(stderr, "(CLIENT) Echec d'initialisation du socket\n");
        exit(EXIT_FAILURE);
    }

    struct sockaddr_in socketAddress;
    socketAddress.sin_family = AF_INET;
    socketAddress.sin_port = htons(LISTENING_PORT);
    socketAddress.sin_addr.s_addr = inet_addr(CONNECTION_HOST);
    if (socketAddress.sin_addr.s_addr == INADDR_NONE) {
        fprintf(stderr, "(CLIENT) Adresse invalide ou non prise en charge\n");
        exit(EXIT_FAILURE);
    }

    int socketAddressLength = sizeof(socketAddress);
    int connectionStatus = connect(socketFD, (struct sockaddr*)&socketAddress, socketAddressLength);
    if (connectionStatus == -1) {
        fprintf(stderr, "(CLIENT) Echec de la connexion au serveur\n");
        exit(EXIT_FAILURE);
    }

    int attempts = 0;
    int is_admin = -1;

    printf("****************************BIENVENUE *****************************\n");
    printf("RQ : vous avez le droit d'entrer comme -ADMIN ou INVITE seulement :)\n\n");
    while (attempts < 3) {
        char username[50];
        printf("S'il vous plait Saisir votre login : ");
        fgets(username, sizeof(username), stdin);
        username[strcspn(username, "\n")] = '\0'; // Supprimer le caractère de nouvelle ligne

        char password[50];
        printf("S'il vous plait Saisir votre password : ");
        fgets(password, sizeof(password), stdin);
        password[strcspn(password, "\n")] = '\0'; // Supprimer le caractère de nouvelle ligne
        
        int sentBytes = send(socketFD, username, strlen(username), 0);
        if (sentBytes == -1) {
            fprintf(stderr, "(CLIENT) Echec d'envoi du nom d'utilisateur au serveur\n");
            exit(EXIT_FAILURE);
        }

        sentBytes = send(socketFD, password, strlen(password), 0);
        if (sentBytes == -1) {
            fprintf(stderr, "1 (CLIENT) Echec d'envoi du mot de passe au serveur\n");
            exit(EXIT_FAILURE);
        }
        printf("Message envoyer\n");

        char buffer[BUFFER_SIZE] = {0};
        int receivedBytes = recv(socketFD, buffer, BUFFER_SIZE, 0);
        if (receivedBytes == -1) {
            fprintf(stderr, "2 (CLIENT) Echec de reception de la reponse du serveur\n");
            exit(EXIT_FAILURE);
        }
        if (strcmp(buffer, "Invalide") == 0) {
            attempts++;
            printf("Le login ou le mot de passe est incorrect. Il vous reste %d essais.\n", 3 - attempts);
        } else {
            if (receivedBytes < 200) {
                is_admin = 0 ;
            }
            if (receivedBytes > 200) {
                is_admin = 1 ;
            }
            printf("%s\n", buffer);
            break;
        }
    }
    if (attempts == 3) {
        printf("Nombre maximum de tentatives atteint. Arret du systeme.\n");
        exit(EXIT_FAILURE);
    }
    
    char map_choix;
    while (map_choix != 'Q'){
        int choix;
        map_choix = 'Q';
        
        if (is_admin == 1){

            do {
                printf("Entrer Votre choix (entre 1 et 6) : ");
                if (scanf("%d", &choix) != 1 || choix < 1 || choix > 6) {
                    printf("Veuillez entrer un choix entre 1 et 6.\n");
                    while (getchar() != '\n'); // Clear input buffer
                    choix = 7;
                }
            } while (choix < 1 || choix > 6);
            
            if (choix == 1){
                map_choix = 'A';
            }
            else if (choix == 2){
                map_choix = 'R';
            }
            else if (choix == 3){
                map_choix = 'S';
            }
            else if (choix == 4){
                map_choix = 'M';
            }
            else if (choix == 5){
                map_choix = 'D';
            }
            else  {
                map_choix = 'Q';
            }
        }
        else if (is_admin == 0){
            do {
                printf("Entrer Votre choix (entre 1 et 3) : ");
                if (scanf("%d", &choix) != 1 || choix < 1 || choix > 3) {
                    printf("Veuillez entrer un choix entre 1 et 3.\n");
                    while (getchar() != '\n'); // Clear input buffer
                }
            } while (choix < 1 || choix > 3);
            
            if (choix == 1){
                map_choix = 'R';
            }
            else if (choix == 2){
                map_choix = 'D';
            }
            else {
                map_choix = 'Q';
            }
        }

        send(socketFD, &map_choix, sizeof(map_choix), 0);

        // Après la saisie du choix dans le menu
        if (map_choix == 'A'){
             // Ajouter un contact
            char nom[50], prenom[50], GSM[20], email[50], rue[50], ville[50], pays[50];
            printf("Donner le nom du contact : ");
            scanf("%s", nom);
            send(socketFD, nom, strlen(nom)+1, 0);

            printf("Entrez le prunom du contact: ");
            scanf("%s", prenom);
            send(socketFD, prenom, strlen(prenom)+1, 0);

            printf("Entrez l'email: ");
            scanf("%s", email);
            send(socketFD, email, strlen(email)+1, 0);

            printf("Entrez le GSM: ");
            scanf("%s", GSM);
            send(socketFD, GSM, strlen(GSM)+1, 0);


            printf("Saisie de l'adresse:\n");
            printf("veuillez entrer le nom de la rue: ");
            scanf("%s", rue);
            send(socketFD, rue, strlen(rue)+1, 0);

            printf("veuillez entrer la ville du contact: ");
            scanf("%s", ville);
            send(socketFD, ville, strlen(ville)+1, 0);

            printf("veuillez entrer le pays du contact: ");
            scanf("%s", pays);
            send(socketFD, pays, strlen(pays)+1, 0);
            //char r_tmp [50];
            // Recevoir la réponse du serveur
            //recv(socketFD, r_tmp, sizeof(r_tmp), 0);
            //printf("%s\n", r_tmp);
            printf("contact saisie %s#%s$%s#%s#%s$%s#%s \n Ajout avec succes \n", nom, prenom, email, GSM, rue, ville, pays);
        }
        else if (map_choix == 'R') {
            char email[100];
            printf("Veuillez entrer l'email du contact a rechercher : ");
            getchar(); // Pour absorber le caractère de nouvelle ligne laissé par scanf
            fgets(email, sizeof(email), stdin);
            email[strcspn(email, "\n")] = '\0'; // Supprimer le caractère de nouvelle ligne

            // Envoi de l'email au serveur
            int sentBytes = send(socketFD, email, strlen(email), 0);
            if (sentBytes == -1) {
                fprintf(stderr, "(CLIENT) Echec d'envoi de l'email de recherche au serveur\n");
                exit(EXIT_FAILURE);
            }

            // Attente de la réponse du serveur
            char searchResult[BUFFER_SIZE];
            int receivedBytes = recv(socketFD, searchResult, BUFFER_SIZE, 0);
            if (receivedBytes == -1) {
                fprintf(stderr, "3 (CLIENT) Echec de reception de la reponse du serveur\n");
                exit(EXIT_FAILURE);
            }

            // Vérification si la réponse est vide
            if (receivedBytes == 0) {
                printf("Aucune reponse reçue du serveur.\n");
                exit(EXIT_FAILURE);
            }

            // Assurez-vous d'ajouter un caractère nul à la fin de la chaîne de caractères reçue pour la terminer correctement.
            searchResult[receivedBytes] = '\0';

            // Affichage du résultat de la recherche
            printf("Resultat de la recherche est: \n%s\n", searchResult);
        }
        else if (map_choix == 'S') {
            char emails[100];
            printf("Veuillez entrer l'email du contact a supprimer : ");
            getchar(); // Pour absorber le caractère de nouvelle ligne laissé par scanf
            fgets(emails, sizeof(emails), stdin);
            emails[strcspn(emails, "\n")] = '\0'; // Supprimer le caractère de nouvelle ligne

            // Envoi de l'email au serveur
            int sentBytess = send(socketFD, emails, strlen(emails), 0);

            if (sentBytess == -1) {
                fprintf(stderr, "N (CLIENT) Echec d'envoi de l'email de recherche au serveur\n");
                exit(EXIT_FAILURE);
            }

            // Attente de la réponse du serveur
            char searchResults[BUFFER_SIZE];
            int receivedBytess = recv(socketFD, searchResults, BUFFER_SIZE, 0);
            if (receivedBytess == -1) {
                fprintf(stderr, "3 (CLIENT) Echec de reception de la reponse du serveur\n");
                exit(EXIT_FAILURE);
            }

            // Vérification si la réponse est vide
            if (receivedBytess == 0) {
                printf("Aucune reponse reçue du serveur.\n");
                exit(EXIT_FAILURE);
            }

            // Assurez-vous d'ajouter un caractère nul à la fin de la chaîne de caractères reçue pour la terminer correctement.
            searchResults[receivedBytess] = '\0';

            // Affichage du résultat de la recherche
            printf("%s\n", searchResults);
        }
        else if (map_choix == 'M') {
            char email[50];
        char buffer[BUFFER_SIZE];

        // Demander à l'utilisateur d'entrer l'e-mail du contact à modifier
        printf("Donnez email du contact a modifier : ");
        scanf("%s", email);
        send(socketFD, email, strlen(email)+1, 0);

        // Recevoir la réponse du serveur
        //recv(socketFD, buffer, sizeof(buffer), 0);
        //printf("%s\n", buffer);

        // Vérifier si la modification a échoué
        if (strcmp(buffer, "Echec de la modification du contact\n") == 0) {
            break;
        }
        char contactFound[2] ;
        recv(socketFD, contactFound, 1, 0);
        int numcontactFound = atoi(contactFound);
        if (numcontactFound ){
            // Demander les nouvelles informations du contact à modifier
            printf("Donnez le nouveau nom du contact : ");
            scanf("%s", buffer);
            send(socketFD, buffer, strlen(buffer)+1, 0);

            printf("Entrez le nouveau prenom du contact : ");
            scanf("%s", buffer);
            send(socketFD, buffer, strlen(buffer)+1, 0);

            printf("Entrez le nouveau numero de telephone : ");
            scanf("%s", buffer);
            send(socketFD, buffer, strlen(buffer)+1, 0);

            printf("Entrez le nouvel e-mail : ");
            scanf("%s", buffer);
            send(socketFD, buffer, strlen(buffer)+1, 0);

            printf("Saisissez la nouvelle adresse :\n");
            printf("Veuillez entrer le nouveau nom de la rue : ");
            scanf("%s", buffer);
            send(socketFD, buffer, strlen(buffer)+1, 0);

            printf("Veuillez entrer la nouvelle ville du contact : ");
            scanf("%s", buffer);
            send(socketFD, buffer, strlen(buffer)+1, 0);

            printf("Veuillez entrer le nouveau pays du contact : ");
            scanf("%s", buffer);
            send(socketFD, buffer, strlen(buffer)+1, 0);
            
            char a [100];
            // Recevoir la réponse du serveur
            recv(socketFD, a, sizeof(a), 0);
            printf("%s\n", a);

            }
        else{
            printf("Cette email n'existe pas :( \n");
            char a [100];
            // Recevoir la réponse du serveur
            recv(socketFD, a, sizeof(a), 0);
        }
        }
        else if (map_choix == 'D') {
                #define BUFFER_SIZE2 1024
                char buffer2[BUFFER_SIZE2];

                int bytesReceived2 = recv(socketFD, buffer2, BUFFER_SIZE2-1, 0);
                if (bytesReceived2 > 0) {
                    buffer2[bytesReceived2] = '\0'; // Null-terminate the received data
                    printf("%s\n", buffer2);
                } else {
                    printf("Erreur :(\n");
                }
            }
        else {;}
    }

#ifdef _WIN32
    closesocket(socketFD);
    WSACleanup();
#else
    close(socketFD);
#endif
    return 0;
}
