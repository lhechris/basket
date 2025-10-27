<?php
function comparerTableauxRecursif($tab1, $tab2) {
    $resultat = [];

    // Parcourir chaque clé du premier tableau
    foreach ($tab1 as $cle => $valeur) {
        if (is_array($valeur) && isset($tab2[$cle]) && is_array($tab2[$cle])) {
            // Si la valeur est un sous-tableau, comparer récursivement
            $resultat[$cle] = comparerTableauxRecursif($valeur, $tab2[$cle]);
        } else {
            // Comparaison simple
            $resultat[$cle] = [
                'valeur1' => $valeur,
                'valeur2' => $tab2[$cle] ?? null,
                'difference' => !array_key_exists($cle, $tab2) || $valeur !== $tab2[$cle],
            ];
        }
    }

    // Ajouter les clés présentes dans $tab2 mais pas dans $tab1
    foreach ($tab2 as $cle => $valeur) {
        if (!array_key_exists($cle, $tab1)) {
            if (is_array($valeur)) {
                $resultat[$cle] = comparerTableauxRecursif([], $valeur);
            } else {
                $resultat[$cle] = [
                    'valeur1' => null,
                    'valeur2' => $valeur,
                    'difference' => true,
                ];
            }
        }
    }

    return $resultat;
}

function comparaisonEstOk($comparaison) {
    $resultat = true;

    foreach ($comparaison as $cle => $valeurs) {
        if (is_array($valeurs) && !isset($valeurs['difference'])) {
            // Cas d'un sous-tableau
            $resultat = $resultat && comparaisonEstOk($valeurs);
        } else {
            // Cas d'une valeur simple
            if ($valeurs['difference']) {
                $resultat = false;
            } 
        }
    }    

    return $resultat;

}

function afficherComparaisonConsole($comparaison, $niveau = 0) {   

    $indent = str_repeat("  ", $niveau);
    foreach ($comparaison as $cle => $valeurs) {
        if (is_array($valeurs) && !isset($valeurs['difference'])) {
            // Cas d'un sous-tableau
            echo $indent . "$cle:\n";
            afficherComparaisonConsole($valeurs, $niveau + 1);
        } else {
            // Cas d'une valeur simple
            echo $indent . "$cle: ";
            if ($valeurs['difference']) {
                // Affichage en jaune pour les différences
                echo "(Attendu)\033[33m" . ($valeurs['valeur1'] ?? 'N/A') . " → \033[0m(Evalué)\033[33m" . ($valeurs['valeur2'] ?? 'N/A') . "\033[0m";
            } else {
                echo $valeurs['valeur1'];
            }
            echo "\n";
        }
    }
}

function assertArray($val,$expected,$funcname,$stepname) {
    $res=comparerTableauxRecursif($expected,$val);
    
    if (comparaisonEstOk($res)) {
        echo("$funcname::$stepname : \033[32m passed \033[0m\n");
    } else {
        echo("$funcname::$stepname : \033[31m nok \033[0m ");
        afficherComparaisonConsole($res);
    }
}


function assertEgal($val,$expected,$funcname,$stepname) {
    if ($val==$expected) { 
        echo("$funcname::$stepname : \033[32m passed \033[0m\n");
    } else {
        echo("$funcname::$stepname : \033[31m nok \033[0m ");
		echo("attendu : $expected evalué: $val \n");
    }
}

function assertErr($funcname,$stepname,$errmsg) {
        echo("$funcname::$stepname : \033[31m $errmsg \033[0m \n");
}

function assertOk($funcname,$stepname) {
        echo("$funcname::$stepname : \033[32m passed \033[0m \n");
}

