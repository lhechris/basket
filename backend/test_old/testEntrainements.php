<?php
include_once("../api/env.php");
include_once("../api/donnees.php");
include_once("../api/entrainements.php");

include_once("./utilsTest.php");

loadEnv(".env");
$donnees = new Donnees();

//creation BD de test
$sql= file_get_contents("../config/createdb.sql");
$donnees->db->exec($sql);

//ajout donnees de test
$donnees->db->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-15','sans titre')");
$donnees->db->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-08','sans titre')");
$donnees->db->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-01','sans titre')");


/**
 * 
 */
function test_getEntrainements() {
    global $donnees;
    $ent = new Entrainements($donnees);

    $json = $ent->getArray();

    if (($json[0]["jour"] == "2025-09-01") && ($json[1]["jour"] == "2025-09-08") && ($json[2]["jour"] == "2025-09-15")) {
        assertEgal(1,1,__FUNCTION__,"entrainements correctement trié");
    }
}


test_getEntrainements();

?>