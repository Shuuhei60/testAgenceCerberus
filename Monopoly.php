<?php

// Classe Joueur qui représente un joueur du jeu
class Joueur {
    public $nom;
    public $position; // Position actuelle sur le plateau
    public $enPrison = false; // Indique si le joueur est en prison
    public $lancersEnPrison = 0; // Nombre de lancers effectués en prison

    public function __construct($nom, $position) {
        $this->nom = $nom;
        $this->position = $position;
    }
}

// Fonction pour déplacer un joueur sur le plateau
function deplacerJoueur($joueur, $pas, $taillePlateau) {
    $joueur->position = ($joueur->position + $pas) % $taillePlateau;
}

// Fonction pour gérer le tour d'un joueur
function gererTour($joueur, $lancersDes, $taillePlateau) {
    $doublesConsecutifs = 0;

    foreach ($lancersDes as $lancer) {
        [$de1, $de2] = $lancer;
        $estDouble = $de1 === $de2;

        if ($joueur->enPrison) {
            if ($estDouble) {
                $joueur->enPrison = false;
                $joueur->lancersEnPrison = 0;
                deplacerJoueur($joueur, $de1 + $de2, $taillePlateau);
                return; // Fin du tour après sortie de prison
            } else {
                $joueur->lancersEnPrison++;
                if ($joueur->lancersEnPrison >= 3) {
                    $joueur->enPrison = false;
                    $joueur->lancersEnPrison = 0;
                    deplacerJoueur($joueur, $de1 + $de2, $taillePlateau);
                    return; // Fin du tour après sortie obligatoire de prison
                }
            }
            return; // Le joueur reste en prison si les conditions ne sont pas remplies
        }

        deplacerJoueur($joueur, $de1 + $de2, $taillePlateau);

        // Vérifie si le joueur tombe sur une case spéciale
        if ($joueur->position == 30) { // Aller en Prison
            $joueur->position = 10; // Prison
            $joueur->enPrison = true;
            return;
        }

        if ($estDouble) {
            $doublesConsecutifs++;
            if ($doublesConsecutifs == 3) {
                $joueur->position = 10;
                $joueur->enPrison = true;
                return;
            }
        } else {
            return; // Fin du tour si ce n'est pas un double
        }
    }
}

// Fonction principale pour jouer la partie
function jouerPartie() {
    fscanf(STDIN, "%d", $nombreJoueurs); // Nombre de joueurs
    $joueurs = [];
    for ($i = 0; $i < $nombreJoueurs; $i++) {
        $ligneJoueur = stream_get_line(STDIN, 256 + 1, "\n");
        [$nom, $position] = explode(" ", $ligneJoueur);
        $joueurs[] = new Joueur($nom, (int)$position);
    }

    fscanf(STDIN, "%d", $nombreLancers); // Nombre de lancers de dés
    $lancersDes = [];
    for ($i = 0; $i < $nombreLancers; $i++) {
        $ligneDes = stream_get_line(STDIN, 256 + 1, "\n");
        $lancersDes[] = array_map('intval', explode(" ", $ligneDes));
    }

    for ($i = 0; $i < 40; $i++) {
        $lignePlateau = stream_get_line(STDIN, 256 + 1, "\n"); // On ignore les noms des cases
    }

    $taillePlateau = 40;
    $indexLancer = 0;

    // Simulation des tours
    while ($indexLancer < count($lancersDes)) {
        foreach ($joueurs as $joueur) {
            if ($indexLancer >= count($lancersDes)) {
                break;
            }

            $lancersJoueur = [$lancersDes[$indexLancer]];

            if (isset($lancersDes[$indexLancer + 1]) && $lancersDes[$indexLancer][0] === $lancersDes[$indexLancer][1]) {
                $lancersJoueur[] = $lancersDes[++$indexLancer];
                if (isset($lancersDes[$indexLancer + 1]) && $lancersDes[$indexLancer][0] === $lancersDes[$indexLancer][1]) {
                    $lancersJoueur[] = $lancersDes[++$indexLancer];
                }
            }

            gererTour($joueur, $lancersJoueur, $taillePlateau);
            $indexLancer++;
        }
    }

    // Préparation des résultats
    $resultats = [];
    foreach ($joueurs as $joueur) {
        $resultats[] = $joueur->nom . ' ' . $joueur->position;
    }

    return implode("\n", $resultats);
}

// Joue la partie et affiche les résultats
echo jouerPartie();

// Fin du programme
// Je n'ai pas compris ce que je devais faire pour les tests "Early Release / Friends Forever / Big Run", le reste fonctionne. Résultat final d'après Coding Game : 62%.
?>