<?php
include_once("../api/env.php");
include_once("../api/presences.php");
include_once("../api/donnees.php");
include_once("../api/users.php");

include_once("./utilsTest.php");

loadEnv(".env");
$donnees = new Donnees();

//creation BD de test
$sql= file_get_contents("../config/createdb.sql");
$donnees->db->exec($sql);

//ajout donnees de test
$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('riri',1)");
$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('fifi',1)");
$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('loulou',2)");
$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('daisy',2)");

$donnees->db->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-01','sans titre')");
$donnees->db->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-04','sans titre')");
$donnees->db->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-08','sans titre')");

$donnees->db->exec("INSERT INTO presences(user,entrainement,val) VALUES(1,1,1)");
$donnees->db->exec("INSERT INTO presences(user,entrainement,val) VALUES(2,1,1)");


class SPresences extends Presences{
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



function presencesGet() {
    global $donnees;
    $results = $donnees->db->query('SELECT entrainement,user,val FROM presences ORDER BY entrainement,user');
    $json= array();
    while ($row = $results->fetchArray()) {
        array_push($json,array("entrainement"=>$row['entrainement'],
                               "user"=>$row["user"],
                               "val"=>$row["val"] ));
    }
    return($json);
}



/**
 * 
 */
function test_getPresences() {
    global $donnees;
    $users=new Users($donnees);
    $entrainements=new Entrainements($donnees);
    $presences = new Presences($donnees,$users,$entrainements);

    $json = $presences->getArray();
    echo json_encode($json,JSON_PRETTY_PRINT);
    echo "\n";
}


/**
 * 
 */
function test_updatePresence() {

    global $donnees;
    $users=new Users($donnees);
    $entrainements=new Entrainements($donnees);
    $presences = new SPresences($donnees,$users,$entrainements);

    $initial=presencesGet();
    assertEgal($initial[0]["val"],1,__FUNCTION__,"valeur initiale correcte");

    $input = array("entrainement"=>1, "usr"=>1, "pres"=>2);
    $presences->update($input);
    $json=presencesGet();
    assertEgal($json[0]["val"],2,__FUNCTION__,"valeur correctement modifiée");

    $input = array("entrainement"=>3, "usr"=>4, "pres"=>3);
    $presences->update($input);
    $json=presencesGet();
    assertEgal($json[count($json)-1]["val"],3,__FUNCTION__,"valeur correctement ajoutée");
    assertEgal($json[count($json)-1]["user"],4,__FUNCTION__,"user correctement ajoutée");
    assertEgal($json[count($json)-1]["entrainement"],3,__FUNCTION__,"entrainement correctement ajoutée");

}

/**
 * 
 */
function test_presenceExists() {

    global $donnees;
    $users=new Users($donnees);
    $entrainements=new Entrainements($donnees);
    $presences = new SPresences($donnees,$users,$entrainements);
    
    $ret=$presences->exists(1,1);
    assertEgal($ret,true,__FUNCTION__,"exists");
    $ret=$presences->exists(3,1);
    assertEgal($ret,false,__FUNCTION__,"not exists");

}

/**
 * 
 */
function test_presenceCreateIfNotExists() {

    global $donnees;
    $users=new Users($donnees);
    $entrainements=new Entrainements($donnees);
    $presences = new SPresences($donnees,$users,$entrainements);

    
    $presences->createIfNotExists(1,1);
    $json1=presencesGet();
    assertEgal(count($json1),2,__FUNCTION__,"exists");

    $presences->createIfNotExists(3,1);
    $json2 = presencesGet();
    assertEgal(count($json2),3,__FUNCTION__,"not exists");
}


//test_getPresences();
test_presenceExists();
test_presenceCreateIfNotExists();
test_updatePresence();

?>