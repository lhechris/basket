<?php
include_once("../api/env.php");
include_once("../api/disponibilites.php");
include_once("../api/selections.php");
include_once("../api/donnees.php");
include_once("../api/users.php");
include_once("../api/matchs.php");
include_once("./utilsTest.php");

loadEnv(".env");
$donnees = new Donnees();
$users = new Users($donnees);
$matchs = new Matchs($donnees);
$disponibilites = new Disponibilites($donnees,$users,$matchs);

//creation BD de test
$sql= file_get_contents("../config/createdb.sql");
$donnees->db->exec($sql);

//ajout donnees de test
$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('riri',1)");
$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('fifi',1)");
$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('loulou',2)");
$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('daisy',2)");

$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(1,'2025-09-01','sans titre','0/0')");
$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(2,'2025-09-01','sans titre','0/0')");
$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(2,'2025-09-08','sans titre','0/0')");

$donnees->db->exec("INSERT INTO disponibilites(user,jour,val) VALUES(1,'2025-09-01',1)");
$donnees->db->exec("INSERT INTO disponibilites(user,jour,val) VALUES(2,'2025-09-01',1)");

$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(1,1,1)");


class SSelections extends Selections{
    public function exists($e,$u) {
        return parent::exists($e,$u);
    }
    public function createIfNotExists($e,$u) {
        return parent::createIfNotExists($e,$u);
    }
    public function update($t) {
        return parent::update($t);
    }
}

function SelectionsGet() {
    global $donnees;
    $results = $donnees->db->query('SELECT match,user,val FROM selections ORDER BY match,user');
    $json= array();
    while ($row = $results->fetchArray()) {
        array_push($json,array("match"=>$row['match'],
                               "user"=>$row["user"],
                               "val"=>$row["val"] ));
    }
    return($json);
}


/**
 * 
 */
function test_getSelections() {
    global $donnees,$users,$matchs,$disponibilites;
    $selections = new Selections($donnees,$users,$matchs,$disponibilites);

    $json = $selections->getArray();
    
    //Tableau par equipe liste des match, où dans chaque match liste des joueuses
    $s = '[ { "equipe" : 1,
              "joueurs" : [{"prenom" : "fifi", "nb" : 0},{"prenom" : "riri","nb" : 1}],
              "matchs" : [{
                    "id" : 1,
                    "jour": "2025-09-01",
                    "users": [{"id": 2,"dispo": 1, "selection":0,"prenom": "fifi"},
                            {"id": 1,"dispo": 1, "selection":1,"prenom": "riri"}],
                    "equipe" : 1,
                    "lieu": "sans titre",
                    "nb" : 1
                }]
            },
            { "equipe" : 2,
               "joueurs" : [{"prenom" : "daisy", "nb" : 0},{"prenom" : "loulou","nb" : 0}], 
               "matchs" : [{
                    "id" : 2,
                    "jour": "2025-09-01",
                    "users": [{"id": 4,"dispo": 0, "selection":0,"prenom": "daisy"},
                            {"id": 3,"dispo": 0, "selection":0,"prenom": "loulou"}],
                    "equipe" : 2,
                    "lieu": "sans titre",
                    "nb" : 0
                },
                {
                    "id" : 3,
                    "jour": "2025-09-08",
                    "users": [{"id": 4,"dispo": 0, "selection":0,"prenom": "daisy"},
                            {"id": 3,"dispo": 0, "selection":0,"prenom": "loulou"}],
                    "equipe" : 2,
                    "lieu": "sans titre",
                    "nb" : 0
                }]
          }]';     

    $expected = json_decode($s,true);

    //printf(json_encode($expected,JSON_PRETTY_PRINT));
    //printf(json_encode($json,JSON_PRETTY_PRINT));

    assertArray($json,$expected,__FUNCTION__,"retour");    
}


/**
 * 
 */
function test_updateSelection() {

    global $donnees,$users,$matchs,$disponibilites;
    $selections = new SSelections($donnees,$users,$matchs,$disponibilites);

    $initial=SelectionsGet();
    assertEgal($initial[0]["val"],1,__FUNCTION__,"valeur initiale correcte");

    $input = array("match"=>1, "usr"=>1, "selection"=>2);
    $selections->update($input);
    $json=SelectionsGet();
    $noval = true;
    foreach ($json as $v) {
        if (($v["match"] == 1) && ($v["user"] == 1)) {
            assertEgal($v["val"],2,__FUNCTION__,"valeur correctement modifiée");
            $noval=false;
        }
    }
    if ($noval) {
        assertErr(__FUNCTION__,"Modification enregistrement","nok");
    }

    

    $input = array("match"=>2, "usr"=>4, "selection"=>3);
    $selections->update($input);
    $json=SelectionsGet();
    $noval = true;
    foreach ($json as $v) {
        if (($v["match"] == 2) && ($v["user"] == 4)) {
            assertEgal($v["val"],3,__FUNCTION__,"valeur correctement ajoutée");
            assertEgal($v["user"],4,__FUNCTION__,"user correctement ajoutée");
            assertEgal($v["match"],2,__FUNCTION__,"jour correctement ajoutée");
            $noval=false;
        }
    }
    if ($noval) {
        assertErr(__FUNCTION__,"Ajout enregistrement","nok");
    }



}

/**
 * 
 */
function test_SelectionExists() {

    global $donnees,$users,$matchs,$disponibilites;
    $selections = new SSelections($donnees,$users,$matchs,$disponibilites);

    $ret=$selections->exists(1,1);
    assertEgal($ret,true,__FUNCTION__,"exists");
    $ret=$selections->exists(3,1);
    assertEgal($ret,false,__FUNCTION__,"not exists");

}

/**
 * 
 */
function test_SelectionCreateIfNotExists() {

    global $donnees,$users,$matchs,$disponibilites;
    $selections = new SSelections($donnees,$users,$matchs,$disponibilites);

    
    $selections->createIfNotExists(1,1);
    $json1=SelectionsGet();
    assertEgal(count($json1),1,__FUNCTION__,"exists");

    $selections->createIfNotExists(3,1);
    $json2 = SelectionsGet();
    assertEgal(count($json2),2,__FUNCTION__,"not exists");
}


test_getSelections();
test_SelectionExists();
test_SelectionCreateIfNotExists();
test_updateSelection();

?>