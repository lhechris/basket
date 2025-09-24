<?php

include_once("../api/env.php");
include_once("../api/disponibilites.php");
include_once("../api/donnees.php");
include_once("../api/users.php");
include_once("../api/matchs.php");
include_once("./utilsTest.php");


loadEnv(".env");
$donnees = new Donnees();
$users = new Users($donnees);
$matchs = new Matchs($donnees);

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

class SDisponibilites extends Disponibilites{
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

function DisponibilitesGet() {
    global $donnees;
    $results = $donnees->db->query('SELECT jour,user,val FROM disponibilites ORDER BY jour,user');
    $json= array();
    while ($row = $results->fetchArray()) {
        array_push($json,array("jour"=>$row['jour'],
                               "user"=>$row["user"],
                               "val"=>$row["val"] ));
    }
    return($json);
}


/**
 * 
 */
function test_getDisponibilites() {
    global $donnees,$users,$matchs;
    $Disponibilites = new Disponibilites($donnees,$users,$matchs);

    $json = $Disponibilites->getArray();

    $s = '[
    {
        "jour": "2025-09-01",
        "users": [{"id": 4,"dispo": 0,"prenom": "daisy"},
                  {"id": 2,"dispo": 1,"prenom": "fifi"},
                  {"id": 3,"dispo": 0,"prenom": "loulou"},
                  {"id": 1,"dispo": 1,"prenom": "riri"}],
        "titre": "sans titre"
    },
    {
        "jour": "2025-09-08",
        "users": [{"id": 4,"dispo": 0,"prenom": "daisy"},
                  {"id": 2,"dispo": 0,"prenom": "fifi"},
                  {"id": 3,"dispo": 0,"prenom": "loulou"},
                  {"id": 1,"dispo": 0,"prenom": "riri"}],
        "titre": "sans titre"
    }]';
    $expected = json_decode($s,true);
    assertArray($json,$expected,__FUNCTION__,"retour");    


}


/**
 * 
 */
function test_updateDisponibilite() {

    global $donnees,$users,$matchs;
    $Disponibilites = new SDisponibilites($donnees,$users,$matchs);

    $initial=DisponibilitesGet();
    assertEgal($initial[0]["val"],1,__FUNCTION__,"valeur initiale correcte");

    $input = array("jour"=>'2025-09-01', "usr"=>1, "value"=>2);
    $Disponibilites->update($input);
    $json=DisponibilitesGet();
    $noval = true;
    foreach ($json as $v) {
        if (($v["jour"] == "2025-09-01") && ($v["user"] == 1)) {
            assertEgal($v["val"],2,__FUNCTION__,"valeur correctement modifiée");
            $noval=false;
        }
    }
    if ($noval) {
        assertErr(__FUNCTION__,"Modification enregistrement","nok");
    }

    

    $input = array("jour"=>'2025-09-01', "usr"=>4, "value"=>3);
    $Disponibilites->update($input);
    $json=DisponibilitesGet();
    $noval = true;
    foreach ($json as $v) {
        if (($v["jour"] == "2025-09-01") && ($v["user"] == 4)) {
            assertEgal($v["val"],3,__FUNCTION__,"valeur correctement ajoutée");
            assertEgal($v["user"],4,__FUNCTION__,"user correctement ajoutée");
            assertEgal($v["jour"],'2025-09-01',__FUNCTION__,"jour correctement ajoutée");
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
function test_DisponibiliteExists() {

    global $donnees,$users,$matchs;;
    $Disponibilites = new SDisponibilites($donnees,$users,$matchs);

    $ret=$Disponibilites->exists('2025-09-01',1);
    assertEgal($ret,true,__FUNCTION__,"exists");
    $ret=$Disponibilites->exists('2025-10-10',1);
    assertEgal($ret,false,__FUNCTION__,"not exists");

}

/**
 * 
 */
function test_DisponibiliteCreateIfNotExists() {

    global $donnees,$users,$matchs;
    $Disponibilites = new SDisponibilites($donnees,$users,$matchs);

    
    $Disponibilites->createIfNotExists('2025-09-01',1);
    $json1=DisponibilitesGet();
    assertEgal(count($json1),2,__FUNCTION__,"exists");

    $Disponibilites->createIfNotExists('2025-09-20',1);
    $json2 = DisponibilitesGet();
    assertEgal(count($json2),3,__FUNCTION__,"not exists");
}


test_getDisponibilites();
test_DisponibiliteExists();
test_DisponibiliteCreateIfNotExists();
test_updateDisponibilite();


?>