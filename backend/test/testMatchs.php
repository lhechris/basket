<?php
include_once("../api/env.php");
include_once("../api/donnees.php");
include_once("../api/matchs.php");
include_once("./utilsTest.php");

loadEnv(".env");
$donnees = new Donnees();

//creation BD de test
$sql= file_get_contents("../config/createdb.sql");
$donnees->db->exec($sql);

//ajout donnees de test
$donnees->db->exec("INSERT INTO matchs(equipe, jour, titre, score) VALUES(1,'2025-09-20','match1','0/0')");
$donnees->db->exec("INSERT INTO matchs(equipe, jour, titre, score) VALUES(1,'2025-09-27','match2','24/8')");
$donnees->db->exec("INSERT INTO matchs(equipe, jour, titre, score) VALUES(2,'2025-10-05','match3','24/8')");


class SMatchs extends Matchs{
    public function supprime($id) {
        return parent::supprime($id);
    }
    public function ajoute($equipe,$titre,$score,$jour) {
        return parent::ajoute($equipe,$titre,$score,$jour);
    }
    public function update($id,$equipe,$titre,$score,$jour) {
        return parent::update($id,$equipe,$titre,$score,$jour);
    }
}

/**
 * 
 */
function test_updateMatchs() {

    global $donnees;
    $match=new SMatchs($donnees);

    $initial=$match->getArray();
    assertEgal($initial[0]["lieu"],'match1',__FUNCTION__,"lieu initial correcte");
    assertEgal($initial[0]["equipe"],1,__FUNCTION__,"equipe initial correcte");
    assertEgal($initial[0]["date"],"2025-09-20",__FUNCTION__,"date initial correcte");
    assertEgal($initial[0]["resultat"],'0/0',__FUNCTION__,"resultat initial correcte");

    $match->update(1,2,'match numero 1','8/9','2025-10-06');
    $json=$match->getArray();
    foreach($json as $v) {
        if ($v["id"] == 1) {
    assertEgal($v["lieu"],'match numero 1',__FUNCTION__,"lieu correctement modifié");
    assertEgal($v["equipe"],2,__FUNCTION__,"equipe correctement modifié");
    assertEgal($v["date"],"2025-10-06",__FUNCTION__,"date correctement modifié");
    assertEgal($v["resultat"],'8/9',__FUNCTION__,"resultat correctement modifié");
        }
    }
    
}

//TODO : tester la supression et l'ajout


test_updateMatchs();