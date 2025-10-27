<?php
include_once("../api/env.php");
include_once("../api/donnees.php");
include_once("../api/users.php");

include_once("./utilsTest.php");

loadEnv(".env");
$donnees = new Donnees();

//creation BD de test
$sql= file_get_contents("../config/createdb.sql");
$donnees->db->exec($sql);

//ajout donnees de test
$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('riri',1,  'duck','BC011001',1,1)");
$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('fifi',1,  'duck','BC011002',0,1)");
$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('loulou',2,'duck','BC011003',1,0)");

$donnees->db->exec("INSERT INTO presences(user,entrainement,val) VALUES(1,1,1)");
$donnees->db->exec("INSERT INTO presences(user,entrainement,val) VALUES(2,1,1)");


class SUsers extends Users{
    public function supprime($id) {
        return parent::supprime($id);
    }
    public function ajoute($prenom,$equipe) {
        return parent::ajoute($prenom,$equipe);
    }
    public function update($id, $prenom, $nom, $equipe, $licence, $otm, $charte) {
        return parent::update($id, $prenom, $nom, $equipe, $licence, $otm, $charte);
    }
}

/**
 * 
 */
function test_getArrayUsers() {

    global $donnees;
    $users = new Users($donnees);

    $json = $users->getArray();
    
    $s = '[{ "id": 2, "prenom": "fifi", "nom" : "duck", "equipe": 1, "licence": "BC011002", "otm": false, "charte": true },
           { "id": 3, "prenom": "loulou", "nom" : "duck", "equipe":2, "licence": "BC011003", "otm": true, "charte": false },
           { "id": 1, "prenom": "riri", "nom" : "duck", "equipe": 1, "licence": "BC011001", "otm": true, "charte": true }
    ]';

    $expected = json_decode($s,true);

    assertArray($json,$expected,__FUNCTION__,"retour");
}



/**
 * 
 */
function test_updateUsers() {

    global $donnees;
    $users=new SUsers($donnees);

    $initial=$users->getArray();
    assertEgal($initial[0]["prenom"],'fifi',__FUNCTION__,"prenom initial correcte");
    assertEgal($initial[0]["equipe"],1,__FUNCTION__,"equipe initial correcte");

    $users->update(1,"gertrude","machin",2,"BC11000",1,1);
    $json=$users->getArray();
    foreach($json as $v) {
        if ($v["id"] == 1) {
            assertEgal($v["prenom"],"gertrude",__FUNCTION__,"prenom correctement modifiée");
            assertEgal($v["equipe"],2,__FUNCTION__,"equipe correctement modifiée");
        }
    }
    
}

test_getArrayUsers();
test_updateUsers();
