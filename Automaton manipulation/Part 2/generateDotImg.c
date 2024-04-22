#include "prototype.h"

void genererDotImg(Automate *autom, int *taille)
{
    //ouverture du fichier
    FILE *file = fopen("automate.dot", "wt");
    int sys = 0;

    if(file == NULL)
    {
        printf("Erreur de l'ouverture du fichier. Reessayez.");
        return;
    }

    //entete
    fprintf(file, "digraph{\n\tnodesep=\"1.5\"\n\trankdir=LR\n\tnode[shape=circle, fontsize=18 style=filled, fontcolor=white, color=black]\n");

    //écriture des transitions avec ses étiquettes
    for(int i = 0; i < taille[0]; i++)
    {
        fprintf(file, "\t%d->%d[label=\"%s\"]\n", (*autom).transition[i].E_depart->number, autom->transition[i].E_arrive->number, autom->transition[i].etiquete);
    }

    //écriture de la couleur des états initiaux( green )
    for(int i=0; i< taille[1]; i++)
    {
        fprintf(file, "\t%d[fillcolor=\"webgreen\", color=\"red\"]\n", autom->etatInitiaux[i]);
    }

    //écriture de la couleur des états finaux( blue )
    for(int i=0; i< taille[2]; i++)
    {
        fprintf(file, "\t%d[fillcolor=\"dodgerblue\"]\n", autom->etatFinaux[i]);
    }

    //écriture de la couleur des états inaccessible( gray )
    for(int i=0; i< taille[3]; i++)
    {
        if(autom->lesEtats[i].accessible == false)
            fprintf(file, "\t%d[fillcolor=\"dimgray\"]\n", autom->lesEtats[i].number);
    }


    fprintf(file, "}");

    fclose(file);

    sys = system("dot -Tpng automate.dot > automate.png");

    if(sys != 0)
    {
        printf("\nLa commande \"dot -Tpng automate.dot > automate.png\" n'est pas pu etre executer. Verifier le fichier .dot ou si dot est installe.");

        system("del ./automate.dot");
    }

    system("automate.png");

}
