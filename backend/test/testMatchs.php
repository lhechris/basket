<?php
include_once("../api/env.php");
include_once("../api/donnees.php");
include_once("../api/matchs.php");
include_once("../api/oppositions.php");
include_once("./utilsTest.php");

loadEnv(".env");
$donnees = new Donnees();
$oppositions = new Oppositions($donnees);

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

$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('riri',1,  'duck','BC011001',1,1)");
$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('fifi',1,  'duck','BC011002',0,1)");
$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('loulou',2,'duck','BC011003',1,0)");

$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(1,1,1)");
$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(2,1,1)");
$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(3,1,1)");




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

    global $donnees,$oppositions;
    $matchs = new Matchs($donnees,$oppositions);

    $json = $matchs->to_array($matchs->getArray());
    
    $s = '[{ "id": 2, "equipe": 1, "jour" : "2025-09-20", "titre":"match1" , "score":"0/0" , "collation":"donald", "otm":"picsou", "maillots": "nobody", "oppositions" : null },
           { "id": 1, "equipe": 1, "jour" : "2025-09-27", "titre":"match2" , "score":"24/8" , "collation":"gontran", "otm":"geo trouvetou", "maillots":"machine à laver","oppositions" : null },
           { "id": 3, "equipe": 2, "jour" : "2025-10-05", "titre":"match3" , "score":"24/8" , "collation":"flagada", "otm":"flairsou", "maillots":"rapetou","oppositions" : null }
    ]';

    $expected = json_decode($s,true);

    assertArray($json,$expected,__FUNCTION__,"retour");
}


/**
 * 
 */
function test_getArrayMatch() {

    global $donnees,$oppositions;
    $matchs = new Matchs($donnees,$oppositions);

    $res = $matchs->getArray(1);
    $json = $matchs->to_array($res);
    echo(json_encode($json,JSON_PRETTY_PRINT));
    
    $s = '[{ "id": 1, 
             "equipe": 1, 
             "jour" : "2025-09-27", 
             "titre":"match2" , 
             "score":"24/8" , 
             "collation":"gontran", 
             "otm":"geo trouvetou", 
             "maillots":"machine à laver",
             "oppositions": { "A" : [], 
                              "B" : [], 
                              "Autres": [
                        {"user" : 2, "prenom" : "fifi", "val" : null },
                        {"user" : 3, "prenom" : "loulou", "val" : null },
                        {"user" : 1, "prenom" : "riri", "val" : null  } ]
                            }
           
            }]';

    $expected = json_decode($s,true);
    if ($expected == null) { 
        echo("\033[31m Erreur decode expected: \033[0m ");
        echo(json_last_error_msg()."\n");
        return;
    }

    assertArray($json,$expected,__FUNCTION__,"retour");
}



/**
 * 
 */
function test_updateMatchs() {

    global $donnees,$oppositions;
    $match = new SMatchs($donnees,$oppositions);

    $initial=$match->to_array($match->getArray());;
    assertEgal($initial[0]["titre"],'match1',__FUNCTION__,"titre initial correcte");
    assertEgal($initial[0]["equipe"],1,__FUNCTION__,"equipe initial correcte");
    assertEgal($initial[0]["jour"],"2025-09-20",__FUNCTION__,"jour initial correcte");
    assertEgal($initial[0]["score"],'0/0',__FUNCTION__,"score initial correcte");

    $match->update(1,2,'match numero 1','8/9','2025-10-06',"germaine","personne","gigi");
    $json=$match->to_array($match->getArray());;
    foreach($json as $v) {
        if ($v["id"] == 1) {
            assertEgal($v["titre"],'match numero 1',__FUNCTION__,"titre correctement modifié");
            assertEgal($v["equipe"],2,__FUNCTION__,"equipe correctement modifiée");
            assertEgal($v["jour"],"2025-10-06",__FUNCTION__,"jour correctement modifiée");
            assertEgal($v["score"],'8/9',__FUNCTION__,"score correctement modifié");
            assertEgal($v["collation"],'germaine',__FUNCTION__,"collation correctement modifiée");
            assertEgal($v["otm"],'personne',__FUNCTION__,"otm correctement modifié");
            assertEgal($v["maillots"],'gigi',__FUNCTION__,"maillots correctement modifié");
        }
    }    
}


function test_ajoutMatchs() {

    global $donnees,$oppositions;
    $match = new SMatchs($donnees,$oppositions);

    $match->ajoute(2,'match numero 4','5/9','2025-11-08',"bubulle","jojo","coco");
    $json=$match->to_array($match->getArray());;
    $notfound=true;
    foreach($json as $v) {
        if ($v["id"] == 4) {
            assertEgal($v["titre"],'match numero 4',__FUNCTION__,"titre correctement ajouté");
            assertEgal($v["equipe"],2,__FUNCTION__,"equipe correctement ajoutée");
            assertEgal($v["jour"],"2025-11-08",__FUNCTION__,"jour correctement ajoutée");
            assertEgal($v["score"],'5/9',__FUNCTION__,"score correctement ajouté");
            assertEgal($v["collation"],'bubulle',__FUNCTION__,"collation correctement ajouté");
            assertEgal($v["otm"],'jojo',__FUNCTION__,"otm correctement ajouté");
            assertEgal($v["maillots"],'coco',__FUNCTION__,"maillots correctement ajouté");
            $notfound=false;
        }
    }
    if ($notfound) { assertErr(__FUNCTION__,"equipe correctement ajoutée","nok"); }
}

function test_supprimeMatchs() {

    global $donnees,$oppositions;
    $match = new SMatchs($donnees,$oppositions);

    $match->supprime(4);
    $json=$match->to_array($match->getArray());;
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
test_getArrayMatch();
test_updateMatchs();
test_ajoutMatchs();
test_supprimeMatchs();