#include "prototype.h"

void afficherAutomate(Automate *autom, int *taille)
{
    affEtatInitiaux(autom, taille);

    affEtatFinaux(autom, taille);

    affTransitions(autom, taille);

    afficherAlphabet(autom);
}

void affEtatInitiaux(Automate *autom, int *taille)
{
    printf("Les etats initiaux :");
    for (int i = 0; i < taille[1]; i++)
    {
        printf("-%d", (*autom).etatInitiaux[i]);
    }
}

void affEtatFinaux(Automate *autom, int *taille)
{
    printf("\n\nLes etats finaux: ");
    for (int k = 0; k < taille[2]; k++)
    {
        printf("-%d", (*autom).etatFinaux[k]);
    }
}

void affTransitions(Automate *autom, int *taille)
{
    printf("\n\nLes transtions: \n\n");
    printf("%-20s %-20s %-20s", "E_depart", "E_arrive", "etiquette");

    for (int j = 0; j < taille[0]; j++)
    {
        printf("\n%-20d %-20d %-20s", (*autom).transition[j].E_depart->number, (*autom).transition[j].E_arrive->number, (*autom).transition[j].etiquete);
    }
}

void afficherAlphabet(Automate *autom)
{
    printf("\nLes aphabets utilises sont: A = {");
    for (int i = 1; i < strlen((*autom).alphabet); i++)
    {
        if(i!=1)
            printf(",%c", (*autom).alphabet[i]);
        else
            printf("%c", (*autom).alphabet[i]);
    }

    printf("}\n");

}

void menu(Automate *autom, int *taille)
{
    char choix;

    while(choix != 'r' && choix != 'R')
    {
        printf("\nx--------------------------------------------------------------x\n");
        printf("\n\tMENU:");
        printf("\n\t-afficher automate detaillee(1)");
        printf("\n\t-afficher alphabet(a)");
        printf("\n\t-generer le graphe en.png(g)");
        printf("\n\t-afficher l'accessibilite des etats(x)");
        printf("\n\t-Tester si un mot est genere par l'automate(t)");
        printf("\n\t-Tester les mots d'un fichier(f)");
        printf("\n\t-Supprimer les epsilons d'un automate(s))");
        printf("\n\t-Determiniser un automate: to DFA (d)");
        printf("\n\t-Minimiser un automate (m)");
        printf("\n\t-Retourner vers le menu principal(r)\n");

        printf("\nEntrez votre choix:");
        choix = getchar();

        // Nettoyer le tampon d'entrée
        int c;
        while ((c = getchar()) != '\n' && c != EOF);

        switch(choix)
        {
            case '1':
                afficherAutomate(autom, taille);
                break;

            case 'a':
            case 'A':
                afficherAlphabet(autom);
                break;

            case 'r':
            case 'R':
                printf("\nRetour vers le menu principal...");
                break;
            case 'g':
            case 'G':
                genererDotImg(autom, taille);
                break;

            case 'x':
            case 'X':
                printf("\n");
                for (int i = 0; i < taille[3]; i++)
                {
                    printf("Etat: %d -- accessible: %d\n", autom->lesEtats[i].number, autom->lesEtats[i].accessible);
                }
                printf("\nNB: 1 = accessible, 0 = inaccessible\n");
                break;

            case 't':
            case 'T':
                char mot[] = "";
                bool isGeneratedBy = false;
                printf("\n Entrer le mot a tester : ");
                scanf("%s", mot);
                while ((getchar())!='\n');

                // estIlEngendre(autom, mot, taille, autom->etatInitiaux, taille[1], &isGeneratedBy);
                isGeneratedBy =  estEngendre(mot, autom, taille);

                if(isGeneratedBy)
                    printf("\t\n\n%s est genere par l'automate.\n", mot);
                else
                {
                    system("cls");
                    printf("\n\n\t%s n'est pas engendre par l'automate\n", mot);
                }

                break;

            case 'f':
            case 'F':
                testerMotsDansFichier(autom, taille);
                break;

            case 's':
            case 'S':
                automateSansEpsilone(autom, taille);
                break;

            case 'd':
            case 'D':
                Automate automDeterm;
                int taille_det[4] = {0};

                determinerAutom(*autom, taille, autom->etatInitiaux, taille[1], 1, &automDeterm, taille_det);

                genererDotImg(&automDeterm, taille_det);
                break;

            case 'm':
            case 'M':
                Automate automMinim; int tailleMinim[4] = {0};

                automMinim = minimiserAutomate(*autom, taille, tailleMinim);

                genererDotImg(&automMinim, tailleMinim);
                break;

            default:
                printf("\nChoix invalide...");
                break;
        }
    }

}

void menuPrincipal()
{
    char choix;

    while(choix != 'q' && choix != 'Q')
    {
        printf("\n\n");
        for (int i = 0; i < 24; i++)
            printf("=");
        printf("\n%-22s", "||"); printf("||\n");
        printf("||"); printf("%17s", "MENU PRINCIPAL"); printf("%5s", "||");
        printf("\n%-22s", "||"); printf("||\n");
        for (int i = 0; i < 24; i++)
            printf("=");
        printf("\n\n");

        //Les options possibles
        printf("\t-Automate simple (s)\n");
        printf("\t-Union de 2 automates (u)\n");
        printf("\t-Etoile d'un automate (e)\n");
        printf("\t-Produit de 2 automates (p)\n");
        printf("\t-Quitter (q)\n");

        printf("\nEntrez votre choix:");
        choix = getchar();

        // Nettoyer le tampon d'entrée
        int c;
        while ((c = getchar()) != '\n' && c != EOF);

        switch(choix)
        {
            case 'S':
            case 's':
                    Automate nouvAutomate;
                    int taille[4] = {0};

                    nouvAutomate = retourneAutomateImporte(nouvAutomate, taille);

                    printf("***** ON TRAVAILLE MAINTENANT AVEC UN AUTOMATE SIMPLE *****\n");
                    menu(&nouvAutomate, taille);

                    freeMemory(&nouvAutomate);
                break;

            case 'U':
            case 'u':
                launchUnionAutomate();
                break;

            case 'e':
            case 'E':
                launchEtoileAutomate();
                break;

            case 'p':
            case 'P':
                launchProdOfAutom();
                break;

            case 'q':
            case 'Q':
                printf("\nQuitter...");
                break;

            default:
                printf("\nChoix invalide...");
                break;
        }
    }
}
