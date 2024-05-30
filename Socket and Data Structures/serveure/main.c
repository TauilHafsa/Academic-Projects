#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <pthread.h>
#include <unistd.h> // Add this line

#ifdef _WIN32
#include <winsock2.h>
#include <ws2tcpip.h>
#else
#include <netinet/in.h>
#include <sys/socket.h>
#include <unistd.h>
#endif

#define LISTENING_PORT 5094
#define PENDING_QUEUE_MAXLENGTH 5
#define BUFFER_SIZE 1024

typedef struct {
    char username[50];
    char password[50];
    char profile[50];
} Account;

typedef struct {
    char nom[50];
    char prenom[50];
    char email[100];
    char GSM[20];
    char rue[100];
    char ville[50];
    char pays[50];
} Contact;

typedef struct {
    int connectedSocketFD;
} ThreadData;

int verifyCredentials(const char* username, const char* password, Account* accounts, int numAccounts) {
    for (int i = 0; i < numAccounts; i++) {
        if (strcmp(username, accounts[i].username) == 0 && strcmp(password, accounts[i].password) == 0) {
            return i; // Return the index of the matching account
        }
    }
    return -1; // No matching account found
}

void searchContactByEmail(const char* email, FILE* contactsFile, char* result) {
    Contact contact;
    char line[BUFFER_SIZE];

    // Utiliser fgets pour lire chaque ligne du fichier de contacts
    while (fgets(line, BUFFER_SIZE, contactsFile) != NULL) {
        // Utiliser sscanf pour extraire les champs de contact de la ligne lue
        sscanf(line, "%s %s %s %s %s %s %s", contact.nom, contact.prenom, contact.email, contact.GSM, contact.rue, contact.ville, contact.pays);

        // Vérifier si l'email correspond à celui recherché
        if (strcmp(email, contact.email) == 0) {
            // Si c'est le cas, copier les informations du contact dans le résultat et quitter la fonction
            sprintf(result, "Nom: %s\nPrenom: %s\nEmail: %s\nGSM: %s\nRue: %s\nVille: %s\nPays: %s\n", contact.nom, contact.prenom, contact.email, contact.GSM, contact.rue, contact.ville, contact.pays);
            return;
        }
    }

    // Si aucun contact correspondant n'est trouvé, indiquer que le contact n'a pas été trouvé
    strcpy(result, "Contact non trouve.");
}

void searchContactByEmailDelete(const char* email, FILE* contactsFile, char* result) {
    Contact contact;
    int removed = 0;
    int line_number = 0;

    // Open the contactsFile in read mode
    FILE *temp = fopen("cantact_temp.txt", "w");
    if (temp == NULL) {
        printf("Temporary file opening failed.");
        exit(1);
    }

    // Read each line from contactsFile and copy to temp, excluding the line with the provided email
    while (fscanf(contactsFile, "%s %s %s %s %s %s %s",
                  contact.nom, contact.prenom, contact.email, contact.GSM,
                  contact.rue, contact.ville, contact.pays) != EOF) {
        line_number++;
        if (strcmp(email, contact.email) != 0) {
            fprintf(temp, "%s %s %s %s %s %s %s\n",
                    contact.nom, contact.prenom, contact.email, contact.GSM,
                    contact.rue, contact.ville, contact.pays);
        } else {
            removed = 1;
        }
    }

    if (!removed) {
        strcpy(result, "Email not found in the contacts.");
        fclose(contactsFile);
        fclose(temp);
        remove("cantact_temp.txt");
        return;
    }

    // Close the original contactsFile
    fclose(contactsFile);

    // Close the temporary file
    fclose(temp);

    // Remove the original contactsFile
    if (remove("contacts.txt") != 0) {
        strcpy(result, "Error removing file");
        // Handle the error condition here, if necessary
    }

    // Rename the temporary file to original contactsFile
    FILE *new_contactsFile = fopen("contacts.txt", "w");
    if (new_contactsFile == NULL) {
        strcpy(result, "Error creating new contacts file");
        // Handle the error condition here, if necessary
    } else {
        FILE *temp = fopen("cantact_temp.txt", "r");
        if (temp == NULL) {
            strcpy(result, "Error opening temporary file");
            // Handle the error condition here, if necessary
        } else {
            char line[1000];
            while (fgets(line, sizeof(line), temp)) {
                fputs(line, new_contactsFile);
            }
            fclose(temp);
            fclose(new_contactsFile);
            remove("cantact_temp.txt");
            strcpy(result, "Contact removed successfully.");
        }
    }
}

char* displayAllContacts(FILE* contactsFile) {
    Contact contact;
    char line[BUFFER_SIZE];
    char* allContacts = malloc(BUFFER_SIZE * sizeof(char));
    strcpy(allContacts, "");
    int i = 0;

    while (fgets(line, BUFFER_SIZE, contactsFile) != NULL) {
        sscanf(line, "%s %s %s %s %s %s %s", contact.nom, contact.prenom, contact.email, contact.GSM, contact.rue, contact.ville, contact.pays);

        char contactInfo[BUFFER_SIZE];
        sprintf(contactInfo, "##### Contact %d #####: \nNom: %s\nPrenom: %s\nEmail: %s\nGSM: %s\nRue: %s\nVille: %s\nPays: %s\n\n", ++i, contact.nom, contact.prenom, contact.email, contact.GSM, contact.rue, contact.ville, contact.pays);
        strcat(allContacts, contactInfo);
    }
    return allContacts;
}

void addContact(int connectedSocketFD) {
    Contact newContact;
    char buffer[BUFFER_SIZE];

    // Request user to enter contact details
    //send(connectedSocketFD, "Donner le nom du contact : ", strlen("Donner le nom du contact : "), 0);
    recv(connectedSocketFD, newContact.nom, sizeof(newContact.nom), 0);
    newContact.nom[strcspn(newContact.nom, "\n")] = '\0';
    //send(connectedSocketFD, "Entrez le prenom du contact: ", strlen("Entrez le prenom du contact: "), 0);
    recv(connectedSocketFD, newContact.prenom, sizeof(newContact.prenom), 0);
    newContact.prenom[strcspn(newContact.prenom, "\n")] = '\0';

    //send(connectedSocketFD, "Entrez l'email: ", strlen("Entrez l'email: "), 0);
    recv(connectedSocketFD, newContact.email, sizeof(newContact.email), 0);
    newContact.email[strcspn(newContact.email, "\n")] = '\0';

    //send(connectedSocketFD, "Entrez le GSM: ", strlen("Entrez le GSM: "), 0);
    recv(connectedSocketFD, newContact.GSM, sizeof(newContact.GSM), 0);
    newContact.GSM[strcspn(newContact.GSM, "\n")] = '\0';

    //send(connectedSocketFD, "Saisie de l'adresse:\n", strlen("Saisie de l'adresse:\n"), 0);
    //send(connectedSocketFD, "veuillez entrer le nom de la rue: ", strlen("veuillez entrer le nom de la rue: "), 0);
    recv(connectedSocketFD, newContact.rue, sizeof(newContact.rue), 0);
    newContact.rue[strcspn(newContact.rue, "\n")] = '\0';

    //send(connectedSocketFD, "veuillez entrer la ville du contact: ", strlen("veuillez entrer la ville du contact: "), 0);
    recv(connectedSocketFD, newContact.ville, sizeof(newContact.ville), 0);
    newContact.ville[strcspn(newContact.ville, "\n")] = '\0';

    //send(connectedSocketFD, "veuillez entrer le pays du contact: ", strlen("veuillez entrer le pays du contact: "), 0);
    recv(connectedSocketFD, newContact.pays, sizeof(newContact.pays), 0);
    newContact.pays[strcspn(newContact.pays, "\n")] = '\0';

    // Store contact information in a text file
    FILE *contactsFile = fopen("contacts.txt", "a"); // Open file in append mode
    if (contactsFile == NULL) {
        //fprintf(stderr, "Erreur d'ouverture du fichier de contacts\n");
        //send(connectedSocketFD, "Echec d'ajout de contact\n", strlen("Echec d'ajout de contact\n"), 0);
        return;
    }

    fprintf(contactsFile, "%s %s %s %s %s %s %s\n", newContact.nom, newContact.prenom, newContact.email, newContact.GSM, newContact.rue, newContact.ville, newContact.pays);
    fclose(contactsFile);
    //printf("\n%s %s %s %s %s %s %s", newContact.nom, newContact.prenom, newContact.email, newContact.GSM, newContact.rue, newContact.ville, newContact.pays);
    // Construct message with contact details
    //sprintf(buffer, "contact saisi %s %s %s %s %s %s %s\n", newContact.nom, newContact.prenom, newContact.email, newContact.GSM, newContact.rue, newContact.ville, newContact.pays);

    // Send the message back to the client
    //send(connectedSocketFD, buffer, strlen(buffer), 0);
    //send(connectedSocketFD, "Ajout avec succes\n", strlen("Ajout avec succes\n"), 0);
    //fclose(contactsFile);
}

void modifierContact(int connectedSocketFD, FILE* contactsFile) {
    Contact newContact;
    char buffer[BUFFER_SIZE];
    char tempFile[] = "temp.txt";

    // Request user to enter contact details
    recv(connectedSocketFD, newContact.email, sizeof(newContact.email), 0);
    newContact.email[strcspn(newContact.email, "\n")] = '\0';

    // Ouvrir un fichier temporaire en mode écriture
    FILE* tempFilePtr = fopen(tempFile, "w");
    if (tempFilePtr == NULL) {
        fprintf(stderr, "Échec d'ouverture du fichier temporaire\n");
        exit(EXIT_FAILURE);
    }

    int contactFound = 0;
    // Utiliser fgets pour lire chaque ligne du fichier de contacts
    while (fgets(buffer, BUFFER_SIZE, contactsFile) != NULL) {
        Contact currentContact;

        // Utiliser sscanf pour extraire les champs du contact de la ligne lue
        sscanf(buffer, "%s %s %s %s %s %s %s", currentContact.nom, currentContact.prenom,
            currentContact.email, currentContact.GSM, currentContact.rue, currentContact.ville, currentContact.pays);
        
        // Vérifier si l'email correspond à celui recherche
        if (strcmp(currentContact.email, newContact.email) == 0) {
            contactFound = 1;
            char str[2]; // Assuming contactFound will always be either 0 or 1
            sprintf(str, "%d", contactFound);
            send(connectedSocketFD, str, strlen(str), 0);
            
            char a[200];
            char b[200];
            char c[200];
            char d[200];
            char e[200];
            char f[200];
            char g[200];


            // Demander les nouvelles informations du contact à modifier
            recv(connectedSocketFD, a, sizeof(a), 0);
            recv(connectedSocketFD, b, sizeof(b), 0);
            recv(connectedSocketFD, d, sizeof(d), 0);
            recv(connectedSocketFD, c, sizeof(c), 0);
            recv(connectedSocketFD, e, sizeof(e), 0);
            recv(connectedSocketFD, f, sizeof(f), 0);
            recv(connectedSocketFD, g, sizeof(g), 0);
            a[strcspn(a, "\n")] = '\0';
            b[strcspn(b, "\n")] = '\0';
            c[strcspn(c, "\n")] = '\0';
            d[strcspn(d, "\n")] = '\0';
            e[strcspn(e, "\n")] = '\0';
            f[strcspn(f, "\n")] = '\0';
            g[strcspn(g, "\n")] = '\0';

            // Écrire la ligne dans le fichier temporaire
            fprintf(tempFilePtr, "%s %s %s %s %s %s %s\n", a,b,c,d,e,f,g);
        }
        else {
        // Écrire la ligne dans le fichier temporaire
        fprintf(tempFilePtr, "%s %s %s %s %s %s %s\n", currentContact.nom, currentContact.prenom, currentContact.email, currentContact.GSM, currentContact.rue, currentContact.ville, currentContact.pays);
    }}

    // Fermer les fichiers
    fclose(contactsFile);
    fclose(tempFilePtr);

    if (contactFound) {
        // Supprimer le fichier original
        remove("contacts.txt");

        FILE *new_contactsFile = fopen("contacts.txt", "w");
        if (new_contactsFile == NULL) {
            printf("Error");
        } else {
            FILE *temp = fopen("temp.txt", "r");
            if (temp == NULL) {
                send(connectedSocketFD, "Error.\n", strlen("Error.\n"), 0);
            } else {
                char line[1000];
                while (fgets(line, sizeof(line), temp)) {
                    fputs(line, new_contactsFile);
                }
                fclose(temp);
                fclose(new_contactsFile);
                remove("temp.txt");
                send(connectedSocketFD, "Contact modified successfully.\n", strlen("Contact modified successfully.\n"), 0);
            }
        }
    } else {
        send(connectedSocketFD, "Error\n", strlen("Error\n"), 0);
        remove("temp.txt");
    }
}

void *client_handler(void *arg) {
    ThreadData *data = (ThreadData *)arg;

    

    int connectedSocketFD = data->connectedSocketFD;
    
    char map_choix = ' ';
    
    while (map_choix != 'Q') {

        FILE *contactsFile;
        contactsFile = fopen("contacts.txt", "r");
        if (contactsFile == NULL) {
            fprintf(stderr, "(SERVEUR) Echec d'ouverture du fichier des contacts\n");
            exit(EXIT_FAILURE);
        }

        recv(connectedSocketFD, &map_choix, sizeof(map_choix), 0);
        if (map_choix == 'A') {
            printf("----> Le client a demande d'ajouter un contact. \n");
            addContact(connectedSocketFD);
        } else if (map_choix == 'R') {
            printf("----> Le client a demande de rechercher un contact. \n");
            char email[100];
            ssize_t receivedBytes = recv(connectedSocketFD, email, sizeof(email) - 1, 0);
            if (receivedBytes > 0) {
                email[receivedBytes] = '\0'; // Ensure correct null-termination
            } else if (receivedBytes == 0) {
                printf("Connection closed by peer\n");
            } else {
                perror("recv");
            }
            char searchResult[BUFFER_SIZE]; 
            searchContactByEmail(email, contactsFile, searchResult);

            // Envoi de la réponse au client
            send(connectedSocketFD, searchResult, strlen(searchResult), 0);

        } else if (map_choix == 'M') {
            printf("----> Le client a demande de modifier un contact. \n");
            modifierContact(connectedSocketFD, contactsFile);
        } else if (map_choix == 'S') {
            printf("----> Le client a demande de supprimer un contact. \n");
            char emails[100];
            ssize_t receivedBytess = recv(connectedSocketFD, emails, sizeof(emails) - 1, 0);
            if (receivedBytess > 0) {
                emails[receivedBytess] = '\0'; // Ensure correct null-termination
            }
            char searchResults[BUFFER_SIZE];
            searchContactByEmailDelete(emails, contactsFile, searchResults);

            // Envoi de la réponse au client
            send(connectedSocketFD, searchResults, strlen(searchResults), 0);
        } else if (map_choix == 'D') {
            printf("----> Le client a demande d'afficher tous les contacts. \n");
            //FILE *contactsFile;
            //contactsFile = fopen("contacts.txt", "r");
            char *contactInfo = displayAllContacts(contactsFile);

            send(connectedSocketFD, contactInfo, strlen(contactInfo) + 1, 0);
        } else if (map_choix == 'Q') {
            printf("----> Le client a demande de quitter:(  \n");
            // Handle the case where map_choix is 'Q'
            fclose(contactsFile);
            close(connectedSocketFD);
            return NULL;
        } else {
            ;
        }
    }
    //fclose(contactsFile);
    close(connectedSocketFD);
    return NULL;
}

int main() {
#ifdef _WIN32
    WSADATA wsa;
    if (WSAStartup(MAKEWORD(2, 2), &wsa) != 0) {
        fprintf(stderr, "(SERVEUR) Echec d'initialisation de winSock\n");
        exit(EXIT_FAILURE);
    }
#endif

    int socketFD = socket(AF_INET, SOCK_STREAM, 0);
    if (socketFD == -1) {
        fprintf(stderr, "(SERVEUR) Echec d'initialisation du socket\n");
        exit(EXIT_FAILURE);
    }

    struct sockaddr_in socketAddress;
    socketAddress.sin_family = AF_INET;
    socketAddress.sin_port = htons(LISTENING_PORT);
    socketAddress.sin_addr.s_addr = INADDR_ANY;
    int socketAddressLength = sizeof(socketAddress);
    int bindReturnCode = bind(socketFD, (struct sockaddr *) &socketAddress, sizeof(socketAddress));
    if (bindReturnCode == -1) {
        fprintf(stderr, "(SERVEUR) Echec de liaison pour le socket\n");
        exit(EXIT_FAILURE);
    }

    if (listen(socketFD, PENDING_QUEUE_MAXLENGTH) == -1) {
        fprintf(stderr, "(SERVEUR) Echec de demarrage de l'écoute des connexions entrantes\n");
        exit(EXIT_FAILURE);
    }
    puts("---->Patientez pendant que le client se connecte...");

    Account accounts[100];
    int numAccounts = 0;

    FILE *accountsFile = fopen("comptes.txt", "r");
    if (accountsFile == NULL) {
        fprintf(stderr, "(SERVEUR) Echec d'ouverture du fichier des comptes\n");
        exit(EXIT_FAILURE);
    }

    char line[150];
    while (fgets(line, sizeof(line), accountsFile)) {
        sscanf(line, "%s %s %s", accounts[numAccounts].username, accounts[numAccounts].password, accounts[numAccounts].profile);
        numAccounts++;
    }

    fclose(accountsFile);

    // Ouverture du fichier des contacts
    FILE *contactsFile;

    while (1) {
        int connectedSocketFD = accept(socketFD, (struct sockaddr *) &socketAddress, (socklen_t *) &socketAddressLength);
        if (connectedSocketFD == -1) {
            fprintf(stderr, "(SERVEUR) Echec d'etablissement de la connexion\n");
            exit(EXIT_FAILURE);
        }

        int attempts = 0;
        char username[50];
        char password[50];
        while (attempts < 3) {
            

            memset(username, 0, sizeof(username));
            int receivedBytes = recv(connectedSocketFD, username, sizeof(username) - 1, 0);

            if (receivedBytes == -1) {
                fprintf(stderr, "(SERVEUR) Echec de reception du nom d'utilisateur du client\n");
                exit(EXIT_FAILURE);
            }

            memset(password, 0, sizeof(password));
            receivedBytes = recv(connectedSocketFD, password, sizeof(password) - 1, 0);
            if (receivedBytes == -1) {
                fprintf(stderr, "(SERVEUR) Echec de reception du mot de passe du client\n");
                exit(EXIT_FAILURE);
            }

            int accountIndex = verifyCredentials(username, password, accounts, numAccounts);
            const char *response;

            if (accountIndex != -1) {
                response = accounts[accountIndex].profile;

                // Envoi du message de menu approprié en fonction du profil
                if (strcmp(response, "ADMIN") == 0) {
                    
                    const char *adminMenu = "--------------------------VOUS ETES ADMINISTRATEUR------------------------\n"
                                            "************ MENU *************\n"
                                            "1 - Ajouter un contact \n"
                                            "2 - Rechercher un contact \n"
                                            "3 - Supprimer un contact \n"
                                            "4 - Modifier un contact \n"
                                            "5 - Afficher tous les contacts \n"
                                            "6 - Quitter \n";
                    send(connectedSocketFD, adminMenu, strlen(adminMenu), 0);
                } else {
                    const char *userMenu = "----------------------------VOUS ETES UTILISATEUR---------------------------\n"
                                           "************* MENU ************\n"
                                           "1 - Rechercher un contact  \n"
                                           "2 - Afficher tous les contacts\n"
                                           "3 - Quitter \n";
                    
                    send(connectedSocketFD, userMenu, strlen(userMenu), 0);
                }

                break;
            } else {
                response = "Invalide";
                send(connectedSocketFD, response, strlen(response), 0);
                attempts++;
            }
        }

        if (attempts == 3) {
            printf("Nombre maximum de tentatives atteint. Arret du systeme.\n");
            exit(EXIT_FAILURE);

        }

        ThreadData data;
        data.connectedSocketFD = connectedSocketFD;
        printf("Un client se connecte avec le socket :) %d \n", data.connectedSocketFD);
        printf("Authentification \n %s \n %s \n", username, password);
        pthread_t tid;
        if (pthread_create(&tid, NULL, client_handler, &data) != 0) {
            fprintf(stderr, "Failed to create thread\n");
        }
    }

    close(socketFD);

#ifdef _WIN32
    WSACleanup();
#endif

    return 0;
}
