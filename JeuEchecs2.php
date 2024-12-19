<?php
// Fonction pour trouver toutes les cases attaquées par une pièce blanche
function casesAttaqueesParBlancs($plateau, $taille, $ligne, $colonne) {
    $directions = [
        [0, 1], [1, 0], [0, -1], [-1, 0], // Directions tour/reine
        [1, 1], [1, -1], [-1, 1], [-1, -1], // Directions fou/reine
    ];
    $cases = [];

    $piece = $plateau[$ligne][$colonne];

    // Pions blancs
    if ($piece === 'P') {
        if ($ligne > 0 && $colonne > 0) $cases[] = [$ligne - 1, $colonne - 1];
        if ($ligne > 0 && $colonne < $taille - 1) $cases[] = [$ligne - 1, $colonne + 1];
        return $cases;
    }

    // Cavaliers blancs
    if ($piece === 'N') {
        $mouvements = [
            [-2, -1], [-1, -2], [1, -2], [2, -1],
            [2, 1], [1, 2], [-1, 2], [-2, 1]
        ];
        foreach ($mouvements as [$dx, $dy]) {
            $x = $ligne + $dx; $y = $colonne + $dy;
            if ($x >= 0 && $x < $taille && $y >= 0 && $y < $taille) {
                $cases[] = [$x, $y];
            }
        }
        return $cases;
    }

    // Tours, fous, et reines blancs
    foreach ($directions as [$dx, $dy]) {
        $x = $ligne; $y = $colonne;
        while (true) {
            $x += $dx; $y += $dy;
            if ($x < 0 || $x >= $taille || $y < 0 || $y >= $taille) break;
            $cases[] = [$x, $y];
            if ($plateau[$x][$y] !== '.') break;
        }
    }
    return $cases;
}

// Fonction pour vérifier si le roi est en échec
function estEnEchec($plateau, $taille, $roiLigne, $roiColonne) {
    for ($i = 0; $i < $taille; $i++) {
        for ($j = 0; $j < $taille; $j++) {
            if (ctype_upper($plateau[$i][$j])) { // Pièce blanche
                $casesAttaquees = casesAttaqueesParBlancs($plateau, $taille, $i, $j);
                foreach ($casesAttaquees as [$x, $y]) {
                    if ($roiLigne === $x && $roiColonne === $y) {
                        return true;
                    }
                }
            }
        }
    }
    return false;
}

// Fonction pour vérifier si le roi peut échapper à l'échec
function peutEchapper($plateau, $taille, $roiLigne, $roiColonne) {
    $casesEchappement = [
        [-1, -1], [-1, 0], [-1, 1], [0, -1], [0, 1], [1, -1], [1, 0], [1, 1]
    ];
    foreach ($casesEchappement as [$dx, $dy]) {
        $x = $roiLigne + $dx;
        $y = $roiColonne + $dy;
        if ($x >= 0 && $x < $taille && $y >= 0 && $y < $taille && $plateau[$x][$y] === '.') {
            if (!estEnEchec($plateau, $taille, $x, $y)) {
                return true; // Le roi peut s'échapper
            }
        }
    }
    return false; // Le roi ne peut pas s'échapper
}

// Fonction principale pour déterminer le gagnant
function trouverGagnant($plateau) {
    $taille = 8; // Taille du plateau

    // Localiser les rois
    $positionRoiNoir = null;
    $positionRoiBlanc = null;
    for ($i = 0; $i < $taille; $i++) {
        for ($j = 0; $j < $taille; $j++) {
            if ($plateau[$i][$j] === 'k') $positionRoiNoir = [$i, $j];
            if ($plateau[$i][$j] === 'K') $positionRoiBlanc = [$i, $j];
        }
    }

    // Vérifier si le roi noir est en échec
    $roiNoirEnEchec = estEnEchec($plateau, $taille, $positionRoiNoir[0], $positionRoiNoir[1]);

    // Vérifier si le roi noir a un mouvement de fuite
    if ($roiNoirEnEchec) {
        if (!peutEchapper($plateau, $taille, $positionRoiNoir[0], $positionRoiNoir[1])) {
            return 'W'; // Le roi noir est en échec et mat
        }
        return 'N'; // Le roi noir est en échec mais peut échapper
    }

    // Vérifier si le roi blanc est en échec
    $roiBlancEnEchec = estEnEchec($plateau, $taille, $positionRoiBlanc[0], $positionRoiBlanc[1]);

    // Vérifier si le roi blanc a un mouvement de fuite
    if ($roiBlancEnEchec) {
        if (!peutEchapper($plateau, $taille, $positionRoiBlanc[0], $positionRoiBlanc[1])) {
            return 'B'; // Le roi blanc est en échec et mat
        }
        return 'N'; // Le roi blanc est en échec mais peut échapper
    }

    return 'N'; // Aucun roi en échec ou échec mat
}

// Lecture de l'entrée
$plateau = [];
for ($i = 0; $i < 8; $i++) {
    $ligne = trim(fgets(STDIN));
    $plateau[] = str_split($ligne);
}

// Déterminer le gagnant
echo trouverGagnant($plateau);

//tentative d'une nouvelle approche 46% d'après codingame
?>


