<?php
include_once("../api/env.php");
include_once("../api/oppositions.php");
include_once("../api/donnees.php");
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

$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(1,'2025-09-01','sans titre','0/0')");
$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(2,'2025-09-01','sans titre','0/0')");


$donnees->db->exec("INSERT INTO oppositions(user,match,val) VALUES(1,1,'A')");
$donnees->db->exec("INSERT INTO oppositions(user,match,val) VALUES(2,1,'A')");
$donnees->db->exec("INSERT INTO oppositions(user,match,val) VALUES(3,1,'B')");

$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(1,1,1)");
$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(2,1,1)");
$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(3,1,1)");
$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(4,1,1)");


class SOppositions extends Oppositions{
    public function exists($e,$u) {
        return parent::exists($e,$u);
    }
    public function createIfNotExists($e,$u,$v) {
        return parent::createIfNotExists($e,$u,$v);
    }
    public function update($t) {
        return parent::update($t);
    }
}

function OppositionsGet() {
    global $donnees;
    $results = $donnees->db->query('SELECT match,user,val FROM oppositions ORDER BY match,user');
    $json= array();
    while ($row = $results->fetchArray()) {
        array_push($json,array("match"=>$row['match'],
                               "user"=>$row["user"],
                               "val"=>$row["val"] ));
    }
    return($json);
}



function test_getOppositions() {
    global $donnees,$oppositions;
    $oppositions = new Oppositions($donnees);

    $ret = $oppositions->to_array($oppositions->getArray(1));    
    
    //echo(json_encode($ret,JSON_PRETTY_PRINT));

    $s  = '[{ "A" : [
              {"user":2,"prenom":"fifi","val":"A"},              
              {"user":1,"prenom":"riri","val":"A"}
           ],
           "B" : [{"user":3,"prenom":"loulou","val":"B"}],
           "Autres" : [{"user":4,"prenom":"daisy","val":null}]
            }]';
    $expected = json_decode($s,true);
    assertArray($ret,$expected,__FUNCTION__,"retour");    
}

/**
 * 
 */
function test_OppositionExists() {

    global $donnees;
    $oppositions = new SOppositions($donnees);

    $ret=$oppositions->exists(1,1);
    assertEgal($ret,true,__FUNCTION__,"exists");
    $ret=$oppositions->exists(5,1);
    assertEgal($ret,false,__FUNCTION__,"not exists");

}


function test_OppositionsUpdate() {
    global $donnees;
    $oppositions = new SOppositions($donnees);

    $initial=OppositionsGet();
    assertEgal($initial[0]["val"],'A',__FUNCTION__,"valeur initiale correcte");

    $input = array("match"=>1, "usr"=>1, "opposition"=>'B');
    $oppositions->update($input);
    $json=OppositionsGet();
    $noval = true;

    foreach ($json as $v) {
        if (($v["match"] == 1) && ($v["user"] == 1)) {
            assertEgal($v["val"],'B',__FUNCTION__,"valeur correctement modifiée");
            $noval=false;
        }
    }
    if ($noval) {
        assertErr(__FUNCTION__,"Modification enregistrement","nok");
    }

    

    $input = array("match"=>2, "usr"=>4, "opposition"=>'A');
    $oppositions->update($input);
    $json=OppositionsGet();
    $noval = true;
    foreach ($json as $v) {
        if (($v["match"] == 2) && ($v["user"] == 4)) {
            assertEgal($v["val"],'A',__FUNCTION__,"valeur correctement ajoutée");
            assertEgal($v["user"],4,__FUNCTION__,"user correctement ajoutée");
            assertEgal($v["match"],2,__FUNCTION__,"jour correctement ajoutée");
            $noval=false;
        }
    }
    if ($noval) {
        assertErr(__FUNCTION__,"Ajout enregistrement","nok");
    }

}


test_getOppositions();
test_OppositionExists();
test_OppositionsUpdate();