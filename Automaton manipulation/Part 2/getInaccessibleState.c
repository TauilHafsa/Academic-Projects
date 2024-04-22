#include "prototype.h"

void modifierEtatCommeAccessible(Automate *autom, int *taille, int currentState)
{
    for(int i=0; i<taille[3]; i++)
    {
        if(autom->lesEtats[i].number == currentState)
        {
            autom->lesEtats[i].accessible = true;
        }

    }
}

void trouverEtatInaccessible(Automate *autom, int *taille, int *EDep, int tailleT) //EDep : tableau contenant tous les états possibles de même niveau. Initialement les états initiaux
{
    int tailleTabTemp = 0;
    int *temp = NULL;

    for(int i=0; i<tailleT; i++)
    {
        //s'assurer que l'état de départ est parcouru
        modifierEtatCommeAccessible(autom, taille, EDep[i]);

        tailleTabTemp = 0;
        for(int j=0; j<taille[0]; j++)
        {
            /*
            il faut que E_depart soit true(parcouru) et E_arrive soit false(non parcouru) pour mettre les E_arrive dans le tableau temp(ou EDep): tableau des états de même niveau à partir d'un état de départ EDep[i]
            */
            if(autom->transition[j].E_depart->number == EDep[i] && autom->transition[j].E_depart->accessible == true && autom->transition[j].E_arrive->accessible == false)
            {
                temp = (int*)realloc(temp, (++tailleTabTemp)*sizeof(int));
                temp[tailleTabTemp-1] = autom->transition[j].E_arrive->number;
                autom->transition[j].E_arrive->accessible = true;
            }
        }

        if(tailleTabTemp != 0)
            trouverEtatInaccessible(autom, taille, temp, tailleTabTemp);
    }
    free(temp);
}
