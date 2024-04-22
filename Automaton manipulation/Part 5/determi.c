#include "prototype.h"

bool writeTransitionsInDeterm(Automate *automDeterm, int *taille_automDeterm, int *EDep, int tailleEDep, int *nextStates, int nb_nextStates, char etiquite, Automate autom, int *taille);

void determinerAutom(Automate autom, int *taille, int *EDep, int tailleEDep, int firstTime, Automate *automDetermine, int *taille_AutomDetermine)
{
    int *nextStates;
    int nb_nextStates = 0;
    int i;

    if(firstTime == 1)
        initAutomate(automDetermine, taille_AutomDetermine);

    for (i = 1; i < strlen(autom.alphabet); i++)
    {

        //getting the next states from a current state in the 2 autom
        nextStates =  getNextStateFrom(EDep, tailleEDep, autom, taille, autom.alphabet[i], &nb_nextStates);

        //fill the automProduit
        if(nb_nextStates != 0 && nextStates != NULL)
        {
            //write transitions in a fileProduit
            bool ifAddInTrans = false;
            ifAddInTrans =  writeTransitionsInDeterm(automDetermine, taille_AutomDetermine, EDep, tailleEDep, nextStates, nb_nextStates, autom.alphabet[i], autom, taille);

            if(ifAddInTrans)
                determinerAutom(autom, taille, nextStates, nb_nextStates, 0, automDetermine, taille_AutomDetermine);
        }
    }

    free(nextStates);

    if(firstTime)
    {
        //ajout etat initiaux
        int etatDepart = 0;
        for (int j = 0; j < tailleEDep; j++)
        {
            etatDepart += EDep[j];
            ajoutEtatInitaux(automDetermine, taille_AutomDetermine, etatDepart);

            if(isFinalState(*automDetermine, taille_AutomDetermine, EDep[j]))
                ajoutEtatFinaux(automDetermine, taille_AutomDetermine, etatDepart);

        }

        //synchroniser les Transitions avec les états, càd faire pointer E_depart et E_arrive vers l'état qui lui correspond dans le tableau lesEtats

        for (int  i = 0; i < taille_AutomDetermine[0]; i++)
        {
            automDetermine->transition[i].E_depart = getEtat(automDetermine, taille_AutomDetermine, automDetermine->transition[i].E_depart->number);
            (*automDetermine).transition[i].E_arrive = getEtat(automDetermine, taille_AutomDetermine, automDetermine->transition[i].E_arrive->number);
        }

        //Vérifier les accessibilités des états et les marquer s'il sont accessible (Etats initiaux toujours accessibles)
        trouverEtatInaccessible(automDetermine, taille_AutomDetermine, automDetermine->etatInitiaux, taille_AutomDetermine[1]);
    }
}

int* getNextStateFrom(int *curState, int nbCurState, Automate autom, int *taille, char etiquete, int *nb_nextStates)
{
    int *nextStates = (int*) calloc(20, sizeof(int));
    *nb_nextStates = 0;
    for (int j = 0; j < nbCurState; j++)
    {
        for (int i = 0; i < taille[0]; i++)
        {
            if(autom.transition[i].E_depart->number == curState[j] && strchr(autom.transition[i].etiquete, etiquete) != NULL) //
                nextStates[(*nb_nextStates)++] = autom.transition[i].E_arrive->number;
        }
    }

    return (*nb_nextStates==0)? NULL : nextStates;
}

bool writeTransitionsInDeterm(Automate *automDeterm, int *taille_automDeterm, int *EDep, int tailleEDep, int *nextStates, int nb_nextStates, char etiquite, Automate autom, int *taille)
{
    int sourceStates = 0, destStates = 0;
    int i,j; char etiqueteString[2] = "";
    etiqueteString[0] = etiquite;
    etiqueteString[1] = '\0';

        //create source states
        for (j = 0; j < tailleEDep; j++)
            sourceStates += EDep[j];

        //create destination states
        for ( j = 0; j < nb_nextStates; j++)
        {
            destStates += nextStates[j];
            //add final states not from EDep but from nextStates
            if(isFinalState(autom, taille, nextStates[j]))
                ajoutEtatFinaux(automDeterm, taille_automDeterm, destStates);
        }

        //write transitions in file
        bool ifAddInTrans = false;

        Transitions *newTransitions = (Transitions*)malloc(sizeof(Transitions));
        newTransitions[0].E_arrive = (Etat*)malloc(sizeof(Etat));
        newTransitions[0].E_depart = (Etat*)malloc(sizeof(Etat));

        newTransitions->E_depart->number = sourceStates;
        newTransitions->E_arrive->number = destStates;
        strcpy(newTransitions->etiquete, etiqueteString);

        if(!isTransitionExits(*automDeterm, taille_automDeterm, *newTransitions))
        {
            ifAddInTrans = true;
            ajoutTransition(automDeterm, *newTransitions, taille_automDeterm);

            ajoutEtat(automDeterm, *newTransitions, taille_automDeterm);
            ajouterAlphabet(automDeterm, etiqueteString);
        }

        free(newTransitions);

    return ifAddInTrans;
}
