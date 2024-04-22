#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "prototype.h"
void afficherMotsEngendres(Automate *autom, int* taille) {
    FILE *fichier = fopen("Mots.txt", "r");
    if (fichier == NULL) {
        printf("Erreur lors de l'ouverture du fichier.\n");
        return;
    }

    char mot[100];
    int motsEngendres = 0; // Compteur pour le nombre de mots engendrés

    printf("Les mots engendres par l'automate sont :\n");

    while (fgets(mot, sizeof(mot), fichier)) {
        // Supprimer le saut de ligne à la fin du mot
        mot[strcspn(mot, "\n")] = '\0';

        if (estEngendre(mot, autom, taille)) {
            printf("%s\n", mot);
            motsEngendres++;
        }
    }

    printf("Le nombre total de mots engendres est : %d\n", motsEngendres);

    if (motsEngendres == 0) {
        printf("Aucun mot n'est engendre par l'automate.\n");
    }

    fclose(fichier);
}
