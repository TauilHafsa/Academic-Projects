#include "prototype.h"
// D�claration de la fonction unionAutomates dans le fichier prototype.h
Automate unionAutomates(Automate *automate1, Automate *automate2, int* taille);

// Impl�mentation de la fonction unionAutomates
Automate unionAutomates(Automate *automate1, Automate *automate2, int* taille) {
    Automate unionAutomate;

    // Copier les �tats initiaux
    unionAutomate.etatInitiaux = malloc(taille[1] * sizeof(int));
    memcpy(unionAutomate.etatInitiaux, automate1->etatInitiaux, taille[1] * sizeof(int));

    // Copier les �tats finaux
    unionAutomate.etatFinaux = malloc(taille[2] * sizeof(int));
    memcpy(unionAutomate.etatFinaux, automate1->etatFinaux, taille[2] * sizeof(int));

    // Concat�ner les transitions
    unionAutomate.transition = malloc(taille[4] * sizeof(Transitions));
    memcpy(unionAutomate.transition, automate1->transition, taille[0] * sizeof(Transitions));

    // Concat�ner l'alphabet
    int totalAlphabetLength = strlen(automate1->alphabet) + strlen(automate2->alphabet);
    unionAutomate.alphabet = malloc((totalAlphabetLength + 1) * sizeof(char));
    strcpy(unionAutomate.alphabet, automate1->alphabet);
    strcat(unionAutomate.alphabet, automate2->alphabet);

    // Copier les �tats
    unionAutomate.lesEtats = malloc(taille[3] * sizeof(Etat));
    memcpy(unionAutomate.lesEtats, automate1->lesEtats, taille[3] * sizeof(Etat));

    // Mettre � jour les tailles
    taille[1] += taille[3];
    taille[2] = taille[3];

    return unionAutomate;
}



