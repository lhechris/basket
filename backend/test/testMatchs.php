<?php
include_once("../api/matchs.php");

function testGetMatchs() {
    $json = getMatchsArray();

    foreach($json as $m) {
        print($m['id']." : Le ".$m['date']." à ".$m['lieu']."(".$m['resultat'].")\n");
    }
}

function test_ajouteMatch() {
    $db = new SQLite3(DBLOCATION);

    $titre = "ASLB/Villeurbane";
    $jour = "04/04/2024";
    $score = "24/8";

    _ajouteMatch($db,$titre,$score,$jour) ;
}

function test_supprimeMatch($id) {
    $db = new SQLite3(DBLOCATION);
    _supprimeMatch($db,$id) ;
}


testGetMatchs();
print("Ajoute un match\n");
test_ajouteMatch();
//testGetMatchs();
//print("Supprime un match\n");
//test_supprimeMatch(21);
testGetMatchs();

?>