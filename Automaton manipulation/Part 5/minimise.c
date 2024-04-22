#include "prototype.h"
Automate inverserAutom(Automate autom, int *taille, int *tailleAutomInv);

Automate minimiserAutomate(Automate autom, int *taille, int *tailleAutomMinim)
{
    Automate automInvers; int tailleAutomInv[4] = {0};
    Automate automMinim;

    automInvers = inverserAutom(autom, taille, tailleAutomInv);
    determinerAutom(automInvers, tailleAutomInv, autom.etatInitiaux, tailleAutomInv[1], 1, &automMinim, tailleAutomMinim);
    automInvers = inverserAutom(automMinim, tailleAutomMinim, tailleAutomInv);
    determinerAutom(automInvers, tailleAutomInv, autom.etatInitiaux, tailleAutomInv[1], 1, &automMinim, tailleAutomMinim);

    return automMinim;
}

Automate inverserAutom(Automate autom, int *taille, int *tailleAutomInv)
{
    Automate automInverse;
    int i;

    Transitions *newTransitions = (Transitions*)malloc(sizeof(Transitions));
    newTransitions[0].E_arrive = (Etat*)malloc(sizeof(Etat));
    newTransitions[0].E_depart = (Etat*)malloc(sizeof(Etat));

    //ajout transition dans l'automate inversé
    for (i = 0; i < taille[0]; i++)
    {
        newTransitions->E_depart->number = autom.transition[i].E_arrive->number;
        newTransitions->E_arrive->number = autom.transition[i].E_depart->number;
        strcpy(newTransitions->etiquete, autom.transition[i].etiquete);

        ajoutTransition(&automInverse, *newTransitions, tailleAutomInv);
        ajouterAlphabet(&automInverse, newTransitions->etiquete);
        ajoutEtat(&automInverse, *newTransitions, tailleAutomInv);
    printf("TafaVoka\n");
    }

    //ajout etat initial dans l'automate inversé
    for (i = 0; i < taille[2]; i++)
        ajoutEtatInitaux(&automInverse, tailleAutomInv, autom.etatFinaux[i]);


    for (i = 0; i < taille[1]; i++)
        ajoutEtatFinaux(&automInverse, tailleAutomInv, autom.etatInitiaux[i]);

    free(newTransitions);

    return automInverse;
}
