#include "prototype.h"
bool estEngendreRecursif(char* mot, int etatCourant, Automate *autom, int *taille) {

    // Verifier si l etat courant est un etat final
    for (int i = 0; i < taille[2]; ++i) {
        if (autom->etatFinaux[i] == etatCourant) {
            // Si on a parcouru tout le mot et qu'on est sur un etat final, le mot alors  accepte
            if (*mot == '\0') {
                return true;
            }
        }
    }

    // Parcourir les transitions depuis l etat courant pour la lettre courante du mot
    for (int i = 0; i < taille[0]; ++i) {
        if (autom->transition[i].E_depart->number == etatCourant && strchr(autom->transition[i].etiquete, *mot) != NULL) {
            // Appel recursif avec l etat d arrivee et la lettre suivante du mot
            if (estEngendreRecursif(mot +1, autom->transition[i].E_arrive->number, autom, taille)) {
                return true;
            }
        }
    }

    // Aucune transition valide pour la lettre courante du mot
    return false;
}

bool estEngendre(char* mot, Automate *autom, int *taille) {
    // Parcourir tous les etats initiaux de l automate
    for (int i = 0; i < taille[1]; ++i) {
        // Appel recursif a partir de chaque etat initial
        if (estEngendreRecursif(mot, autom->etatInitiaux[i], autom, taille)) {
            return true;
        }
    }

    // Aucun etat initial n a conduit  a l acceptation du mot
    return false;
}

void testerMotsDansFichier(Automate *autom,  int *taille)
{
    char **motsEngeres= NULL;
    char buffer[__MAX_NOMBRE_ALPHABET__];
    int nbMotsEngedres = 0;
    bool isGeneratedBy = false;
    FILE *file = fopen("mots.txt", "r");

    if(file == NULL)
    {
        printf("Fichier non trouvé.");
        exit(EXIT_FAILURE);
    }

    while (!feof(file))
    {
        isGeneratedBy = false;
        fscanf(file, "%s", buffer);

        // estIlEngendre(autom, buffer, taille, autom->etatInitiaux, taille[1], &isGeneratedBy);
        isGeneratedBy =  estEngendre(buffer, autom, taille);
        system("cls");

        if(isGeneratedBy)
        {
            motsEngeres = (char**)realloc(motsEngeres, (++nbMotsEngedres)*sizeof(char*));

            motsEngeres[nbMotsEngedres - 1] = (char*)malloc((strlen(buffer))*sizeof(char));

            strcpy(motsEngeres[nbMotsEngedres-1], buffer);
        }

    }

    if(nbMotsEngedres != 0)
    {
        printf("\nLes mots engedres par l'automate sont:\n");
        for (int i = 0; i < nbMotsEngedres; i++)
            printf("\t-%s\n", motsEngeres[i]);
    }
    else
        printf("\nAucun des ces mots est engedre par l'automate.\n");


    fclose(file);
    free(motsEngeres);
}




