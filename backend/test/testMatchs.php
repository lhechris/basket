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
$donnees->db->exec("INSERT INTO matchs(equipe, jour, titre, score,collation,otm,maillots) ".
                   "VALUES(1,'2025-09-27','match2','24/8','gontran','geo trouvetou','machine à laver')");
$donnees->db->exec("INSERT INTO matchs(equipe, jour, titre, score,collation,otm,maillots) ".
                   "VALUES(1,'2025-09-20','match1','0/0','donald','picsou','nobody')");
$donnees->db->exec("INSERT INTO matchs(equipe, jour, titre, score,collation,otm,maillots) ".
                   "VALUES(2,'2025-10-05','match3','24/8','flagada','flairsou','rapetou')");


class SMatchs extends Matchs{
    public function supprime($id) {
        return parent::supprime($id);
    }
    public function ajoute($equipe,$titre,$score,$jour,$collation,$otm,$maillots) {
        return parent::ajoute($equipe,$titre,$score,$jour,$collation,$otm,$maillots);
    }
    public function update($id,$equipe,$titre,$score,$jour,$collation,$otm,$maillots) {        
        return parent::update($id,$equipe,$titre,$score,$jour,$collation,$otm,$maillots);
    }
}


/**
 * 
 */
function test_getArrayMatchs() {

    global $donnees;
    $matchs = new Matchs($donnees);

    $json = $matchs->getArray();
    
    $s = '[{ "id": 2, "equipe": 1, "date" : "2025-09-20", "lieu":"match1" , "resultat":"0/0" , "collation":"donald", "otm":"picsou", "maillots": "nobody" },
           { "id": 1, "equipe": 1, "date" : "2025-09-27", "lieu":"match2" , "resultat":"24/8" , "collation":"gontran", "otm":"geo trouvetou", "maillots":"machine à laver" },
           { "id": 3, "equipe": 2, "date" : "2025-10-05", "lieu":"match3" , "resultat":"24/8" , "collation":"flagada", "otm":"flairsou", "maillots":"rapetou" }
    ]';

    $expected = json_decode($s,true);

    assertArray($json,$expected,__FUNCTION__,"retour");
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

    $match->update(1,2,'match numero 1','8/9','2025-10-06',"germaine","personne","gigi");
    $json=$match->getArray();
    foreach($json as $v) {
        if ($v["id"] == 1) {
            assertEgal($v["lieu"],'match numero 1',__FUNCTION__,"lieu correctement modifié");
            assertEgal($v["equipe"],2,__FUNCTION__,"equipe correctement modifiée");
            assertEgal($v["date"],"2025-10-06",__FUNCTION__,"date correctement modifiée");
            assertEgal($v["resultat"],'8/9',__FUNCTION__,"resultat correctement modifié");
            assertEgal($v["collation"],'germaine',__FUNCTION__,"collation correctement modifiée");
            assertEgal($v["otm"],'personne',__FUNCTION__,"otm correctement modifié");
            assertEgal($v["maillots"],'gigi',__FUNCTION__,"maillots correctement modifié");
        }
    }    
}


function test_ajoutMatchs() {

    global $donnees;
    $match=new SMatchs($donnees);

    $match->ajoute(2,'match numero 4','5/9','2025-11-08',"bubulle","jojo","coco");
    $json=$match->getArray();
    $notfound=true;
    foreach($json as $v) {
        if ($v["id"] == 4) {
            assertEgal($v["lieu"],'match numero 4',__FUNCTION__,"lieu correctement ajouté");
            assertEgal($v["equipe"],2,__FUNCTION__,"equipe correctement ajoutée");
            assertEgal($v["date"],"2025-11-08",__FUNCTION__,"date correctement ajoutée");
            assertEgal($v["resultat"],'5/9',__FUNCTION__,"resultat correctement ajouté");
            assertEgal($v["collation"],'bubulle',__FUNCTION__,"collation correctement ajouté");
            assertEgal($v["otm"],'jojo',__FUNCTION__,"otm correctement ajouté");
            assertEgal($v["maillots"],'coco',__FUNCTION__,"maillots correctement ajouté");
            $notfound=false;
        }
    }
    if ($notfound) { assertErr(__FUNCTION__,"equipe correctement ajoutée","nok"); }
}

function test_supprimeMatchs() {
    global $donnees;
    $match=new SMatchs($donnees); 

    $match->supprime(4);
    $json=$match->getArray();
    $notfound=true;
    foreach($json as $v) {
        if ($v["id"] == 4) {
            $notfound=false;
        }
    }
    if ($notfound) { 
        assertOk(__FUNCTION__,"equipe correctement supprimée"); 
    } else {
        assertErr(__FUNCTION__,"equipe correctement supprimée","nok"); 
    }       

    //TODO verifier que la table selection est purgée

}


test_getArrayMatchs();
test_updateMatchs();
test_ajoutMatchs();
test_supprimeMatchs();