#include "prototype.h"

int** change_EDep_WithNextStates(int *nexStatesInA0, int nb_nextStatesInA0, int *nexStatesInA1, int nb_nextStatesInA1);
bool writeTransitionsInProd(Automate *automProduit, int *tailleProduit, int **EDep, int tailleEDep[2], int *nextStates0, int nb_nextStates0, int *nextStates1, int nb_nextStates1, char etiquite, Automate autom0, int *taille0, Automate autom1, int *taille1);

void launchProdOfAutom()
{
    Automate autom0, autom1, automProduit;
    int taille0[4] = {0}, taille1[4] = {0}, tailleProduit[4] = {0};

    autom0 = retourneAutomateImporte(autom0, taille0);
    autom1 = retourneAutomateImporte(autom1, taille1);

    int **EDepInit, tailleEDepInit[2] = {0};
    int i;

    EDepInit = (int**)malloc(2*sizeof(int*));
    for(i=0; i<2; i++)
        EDepInit[i] = (int*)malloc(20*sizeof(int));

    for (i = 0; i < taille0[1]; i++)
        EDepInit[0][i] = autom0.etatInitiaux[i];

    for (i = 0; i < taille1[1]; i++)
        EDepInit[1][i] = autom1.etatInitiaux[i];

    tailleEDepInit[0] = taille0[1];
    tailleEDepInit[1] = taille1[1];

    produit_Automate(autom0, taille0, autom1, taille1, EDepInit, tailleEDepInit, 1, &automProduit, tailleProduit);

    for(i=0; i<2; i++)
        free(EDepInit[i]);

    free(EDepInit);

    printf("***** ON TRAVAILLE MAINTENANT AVEC LE PRODUIT DE 2 AUTOMATES *****\n");
    menu(&automProduit, tailleProduit);

    freeMemory(&automProduit);
}
void produit_Automate(Automate autom0, int *taille0, Automate autom1, int *taille1, int **EDep, int tailleEDep[2], int firstTime, Automate *automProduit, int *tailleProduit)
{
    if(strlen(autom1.alphabet) == strlen(autom0.alphabet))
    {
        int *nextStates0, *nextStates1, **nextEDep;
        int nb_nextStates0 = 0, nb_nextStates1 = 0, taille_nextEDep[2] = {0};
        int i;

        if(firstTime == 1)
            initAutomate(automProduit, tailleProduit);

        for (i = 1; i < strlen(autom1.alphabet); i++)
        {
            //getting the next states from a current state in the 2 autom
            nextStates0 = getNextStateFrom(EDep[0], tailleEDep[0], autom0, taille0, autom1.alphabet[i], &nb_nextStates0);
            nextStates1 = getNextStateFrom(EDep[1], tailleEDep[1], autom1, taille1, autom1.alphabet[i], &nb_nextStates1);

            //fill the automProduit
            if(nb_nextStates0 != 0 && nb_nextStates1 != 0)
            {

                //write transitions in a fileProduit
                bool ifAddInTrans = false;
                ifAddInTrans =  writeTransitionsInProd(automProduit, tailleProduit, EDep, tailleEDep, nextStates0, nb_nextStates0, nextStates1, nb_nextStates1, autom1.alphabet[i], autom0, taille0, autom1, taille1);

                if(ifAddInTrans)
                {
                    nextEDep = change_EDep_WithNextStates(nextStates0, tailleEDep[0], nextStates1, tailleEDep[1]);

                    taille_nextEDep[0] = nb_nextStates0;
                    taille_nextEDep[1] = nb_nextStates1;

                    produit_Automate(autom0, taille0, autom1, taille1, nextEDep, taille_nextEDep, 0, automProduit, tailleProduit); // 0 means not firstTime

                    for(int i=0; i<2; i++)
                        free(nextEDep[i]);

                    free(nextEDep);

                    free(nextStates0); free(nextStates1);
                }
            }
        }
    }
    else
    {
        printf("Ces 2 deux automates n'ont pas le meme alphabet. Veuillez verifier.");
    }

    if(firstTime)
    {
        //ajout etat initiaux
        int etatDepart = 0;
        for (int i = 0; i < tailleEDep[0]; i++)
        {
            for (int j = 0; j < tailleEDep[1]; j++)
            {
                etatDepart = EDep[0][i] * 10 + EDep[1][j];
                ajoutEtatInitaux(automProduit, tailleProduit, etatDepart);

                if(isFinalState(autom0, taille0, EDep[0][i]) && isFinalState(autom1, taille1, EDep[1][j]))
                    ajoutEtatFinaux(automProduit, tailleProduit, etatDepart);

            }
        }

        //synchroniser les Transitions avec les états, càd faire pointer E_depart et E_arrive vers l'état qui lui correspond dans le tableau lesEtats

        for (int  i = 0; i < tailleProduit[0]; i++)
        {
            automProduit->transition[i].E_depart = getEtat(automProduit, tailleProduit, automProduit->transition[i].E_depart->number);
            (*automProduit).transition[i].E_arrive = getEtat(automProduit, tailleProduit, automProduit->transition[i].E_arrive->number);
        }

        //Vérifier les accessibilités des états et les marquer s'il sont accessible (Etats initiaux toujours accessibles)
        trouverEtatInaccessible(automProduit, tailleProduit, automProduit->etatInitiaux, tailleProduit[1]);
    }
}

int** change_EDep_WithNextStates(int *nexStatesInA0, int nb_nextStatesInA0, int *nexStatesInA1, int nb_nextStatesInA1)
{
    int **temp = NULL, i;

    temp = (int**)malloc(2*sizeof(int*));
    for(i=0; i<2; i++)
        temp[i] = (int*)malloc(20*sizeof(int));

    //nextStates in the first autom
    for (i = 0; i < nb_nextStatesInA0; i++)
        temp[0][i] = nexStatesInA0[i];

    //nextStates in the second autom
    for (i = 0; i < nb_nextStatesInA1; i++)
        temp[1][i] = nexStatesInA1[i];

    return temp;
}

bool writeTransitionsInProd(Automate *automProduit, int *tailleProduit, int **EDep, int tailleEDep[2], int *nextStates0, int nb_nextStates0, int *nextStates1, int nb_nextStates1, char etiquite, Automate autom0, int *taille0, Automate autom1, int *taille1)
{
    int sourceStates[40] = {0}, indexSource = 0;
    int destStates[40] = {0}, indexDest = 0;
    int i,j; char etiqueteString[2] = "";
    etiqueteString[0] = etiquite;
    etiqueteString[1] = '\0';

        //create source states
        for (i = 0; i < tailleEDep[0]; i++)
        {
            for (j = 0; j < tailleEDep[1]; j++)
                sourceStates[indexSource++] = EDep[0][i] * 10 + EDep[1][j];
        }

        //create destination states
        for (i = 0; i < nb_nextStates0; i++)
        {
            for ( j = 0; j < nb_nextStates1; j++)
            {
                destStates[indexDest++] = nextStates0[i] * 10 + nextStates1[j];
                //add final states not from EDep but from nextStates
                if(isFinalState(autom0, taille0, nextStates0[i]) && isFinalState(autom1, taille1, nextStates1[j]))
                    ajoutEtatFinaux(automProduit, tailleProduit, destStates[indexDest-1]);
            }
        }

        //write transitions in file
        bool ifAddInTrans = false;

        for (i = 0; i < indexSource; i++)
        {
            for ( j = 0; j < indexDest; j++)
            {
                Transitions *newTransitions = (Transitions*)malloc(sizeof(Transitions));
                newTransitions[0].E_arrive = (Etat*)malloc(sizeof(Etat));
                newTransitions[0].E_depart = (Etat*)malloc(sizeof(Etat));

                newTransitions->E_depart->number = sourceStates[i];
                newTransitions->E_arrive->number = destStates[j];
                strcpy(newTransitions->etiquete, etiqueteString);

                if(!isTransitionExits(*automProduit, tailleProduit, *newTransitions))
                {
                    ifAddInTrans = true;
                    ajoutTransition(automProduit, *newTransitions, tailleProduit);

                    ajoutEtat(automProduit, *newTransitions, tailleProduit);
                    ajouterAlphabet(automProduit, etiqueteString);
                }

                free(newTransitions);
            }

        }

    return ifAddInTrans;
}
