#include "prototype.h"
void unionAutomate(Automate* autom1, int* taille1, Automate* autom2, int* taille2, Automate* resultat, int* tailleResultat) {
    // Vérifier que les automates ont le même alphabet
    if (strcmp(autom1->alphabet, autom2->alphabet) != 0) {
        printf("Les automates n'ont pas le même alphabet. L'union n'est pas possible.\n");
        return;
    }

    // Initialiser l'automate résultat
    resultat->etatInitiaux = malloc((taille1[1] + taille2[1]) * sizeof(int));
    memcpy(resultat->etatInitiaux, autom1->etatInitiaux, taille1[1] * sizeof(int));
    memcpy(resultat->etatInitiaux + taille1[1], autom2->etatInitiaux, taille2[1] * sizeof(int));

    resultat->etatFinaux = malloc((taille1[2] + taille2[2]) * sizeof(int));
    memcpy(resultat->etatFinaux, autom1->etatFinaux, taille1[2] * sizeof(int));
    memcpy(resultat->etatFinaux + taille1[2], autom2->etatFinaux, taille2[2] * sizeof(int));

    resultat->transition = malloc((taille1[0] + taille2[0]) * sizeof(Transitions));
    memcpy(resultat->transition, autom1->transition, taille1[0] * sizeof(Transitions));
    memcpy(resultat->transition + taille1[0], autom2->transition, taille2[0] * sizeof(Transitions));

    // Mettre à jour les pointeurs d'états dans les transitions de l'automate2
    for (int i = 0; i < taille2[0]; i++) {
        resultat->transition[taille1[0] + i].E_depart = &(resultat->lesEtats[autom2->transition[i].E_depart->number]);
        resultat->transition[taille1[0] + i].E_arrive = &(resultat->lesEtats[autom2->transition[i].E_arrive->number]);
    }

    resultat->lesEtats = malloc(tailleResultat[3] * sizeof(Etat));
    memcpy(resultat->lesEtats, autom1->lesEtats, taille1[3] * sizeof(Etat));
    memcpy(resultat->lesEtats + taille1[3], autom2->lesEtats, taille2[3] * sizeof(Etat));

    // Mettre à jour les numéros d'états dans les transitions de l'automate2
    for (int i = 0; i < taille2[3]; i++) {
        resultat->lesEtats[taille1[3] + i].number += taille1[3];
    }

    // Mettre à jour les tailles dans le tableau de taille du résultat
    tailleResultat[0] = taille1[0] + taille2[0];
    tailleResultat[1] = taille1[1] + taille2[1];
    tailleResultat[2] = taille1[2] + taille2[2];
    tailleResultat[3] = taille1[3] + taille2[3];

    printf("L'union des automates a été calculée avec succès.\n");
}
