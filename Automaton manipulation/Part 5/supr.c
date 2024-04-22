#include "prototype.h"
Automate getAutomateAllAccessible(Automate autom, int taille[]);

void newTransition(Automate autom, int taille[], int etatMemeNiv[], int tailleTemp, int etatSource)
{
    FILE *fileTemp = fopen("automTemp.txt", "a");
    if(fileTemp != NULL)
    {
        for (int i = 0; i < tailleTemp; i++)
        {
            for (int j = 0; j < taille[0]; j++)
            {
                if(autom.transition[j].E_depart->number == etatMemeNiv[i] && autom.transition[j].etiquete[0] != '`' && autom.transition[j].E_arrive->accessible == false)
                    fprintf(fileTemp,"%d %d %s\n", etatSource, autom.transition[j].E_arrive->number, autom.transition[j].etiquete);
            }

        }
        fclose(fileTemp);
    }
    else
        perror("Ouverture du fichier a echoue.");

}

void supprEpsiloneTrans(Automate *autom, int *taille, int *EDep, int tailleT, int etat) //EDep : tableau contenant tous les états possibles de même niveau. Initialement les états initiaux; isGeneretedBy est initialisé à false
{
    int tailleTemp = 0;
    int *temp = NULL;
    int index = 0;

    for(int i=0; i<tailleT; i++)
    {
        tailleTemp = 0;

        for(int j=0; j<taille[0] ; j++)
        {
            /*
            il faut que E_depart soit true(parcouru) pour mettre les E_arrive dans le tableau temp(ou EDep),càd parcourir l'état fils: tableau des états de même niveau à partir d'un état de départ EDep[i]
            */
            if(autom->transition[j].E_depart->number == EDep[i])
            {
                if(autom->transition[j].etiquete[0] == '`' && autom->transition[j].E_arrive->accessible == false)
                {
                    temp = (int*)realloc(temp, (++tailleTemp)*sizeof(int));
                    temp[tailleTemp-1] = autom->transition[j].E_arrive->number;
                    autom->transition[j].E_arrive->accessible = true;

                    for (int index = 0; index < taille[2]; index++)
                    {
                        if(autom->transition[j].E_arrive->number == autom->etatFinaux[index] && etat != autom->etatFinaux[index])
                        {
                            (*autom).etatFinaux  = (int*)realloc((*autom).etatFinaux, (++(taille[2])) * sizeof(int));
                            autom->etatFinaux[taille[2]-1] = etat;
                        }
                    }

                }
                else if(etat == EDep[i])
                {
                    FILE *f = fopen("automTemp.txt", "a");

                    if(f != NULL)
                        fprintf(f, "%d %d %s\n", etat, autom->transition[j].E_arrive->number, autom->transition[j].etiquete);

                    fclose(f);
                }

            }
        }

        if(tailleTemp != 0)
        {
            newTransition(*autom, taille, temp, tailleTemp, etat);
            supprEpsiloneTrans(autom, taille, temp, tailleTemp, etat); //temp: nouveau EDep
        }
    }
    free(temp);
}

void automateSansEpsilone(Automate *autom, int *taille)
{
    FILE *file = NULL, *fileRead = NULL ;
    Automate nowAutom;
    int nouvTaille[4]= {0};

    int i;
    for (i = 0; i < taille[3]; i++)
    {
        for (int j = 0; j < taille[3]; j++)
        {
            autom->lesEtats[j].accessible = false;
        }

        supprEpsiloneTrans(autom, taille, &(autom->lesEtats[i].number), 1, autom->lesEtats[i].number);
    }

    file = fopen("automTemp.txt", "a");
    fileRead = fopen("automTemp.txt", "r");

    if(file!=NULL && fileRead != NULL)
    {
        fprintf(file, ">\n");
        for (i = 0; i < taille[1]; i++) //etats initiaux
            fprintf(file,"%d ", autom->etatInitiaux[i]);

        fprintf(file, "\n");

        for (i = 0; i < taille[2]; i++)
            fprintf(file,"%d ", autom->etatFinaux[i]);

        fclose(file);

        initAutomate(&nowAutom, nouvTaille);
        importerAutomate(&nowAutom, fileRead, nouvTaille);

        fclose(fileRead);

        for(i = 0; i< 4; i++)
            taille[i] = nouvTaille[i];

        *autom = nowAutom;

        // rename("automTemp.txt", "automSansEpsilone.txt");
        remove("automTemp.txt");

        *autom = getAutomateAllAccessible(nowAutom, taille);

    }
    else
    {
        perror("Suppression des epsilons a echoue.\n");
        remove("automTemp.txt");
    }

}

Automate getAutomateAllAccessible(Automate autom, int taille[])
{
    FILE *fileTemp = fopen("automTemp.txt", "a");
    FILE *fileTempR = fopen("automTemp.txt", "r");
    int i;
    int tailleTemp[4] = {0};
    Automate automateNew;

    if (fileTemp != NULL && fileTempR != NULL)
    {
        for (i = 0; i < taille[0]; i++)
        {
            if(autom.transition[i].E_depart->accessible == true)
                fprintf(fileTemp, "%d %d %s\n", autom.transition[i].E_depart->number, autom.transition[i].E_arrive->number, autom.transition[i].etiquete);
        }

        fprintf(fileTemp,">\n");

        for (i = 0; i < taille[1]; i++)
            fprintf(fileTemp, "%d ", autom.etatInitiaux[i]);

        fprintf(fileTemp,"\n");

        for (i = 0; i < taille[2]; i++)
        {
            for (int j = 0;  j< taille[3]; j++)
            {
                if(autom.etatFinaux[i] == autom.lesEtats[j].number && autom.lesEtats[j].accessible == true)
                    fprintf(fileTemp, "%d ", autom.etatFinaux[i]);
            }
        }
        fclose(fileTemp);

        initAutomate(&automateNew, tailleTemp);
        importerAutomate(&automateNew, fileTempR, tailleTemp);

        fclose(fileTempR);

        for(i = 0; i< 4; i++)
            taille[i] = tailleTemp[i];

        // rename("automTemp.txt", "automAllAccessible.txt");
        remove("automTemp.txt");

        return automateNew;
    }
    else
        perror("Erreur de suppression des états inatteignables.\n");

}
