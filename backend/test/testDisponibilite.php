<?php
include_once("../api/disponibilites.php");

/**
 * 
 */
function test_getDisponibilites() {
    $json = getDisponibilitesArray();
    echo json_encode($json,JSON_PRETTY_PRINT);
}

/**
 * 
 */
function test_updateDisponibilites() {
    
    $json = getDisponibilitesArray();
    echo json_encode($json[0]["users"][0],JSON_PRETTY_PRINT);
    
    $d = $json[0]["users"][0]["dispo"];
    $d = $d==1?2:1;
    
    $dispo = array("match"=>1, "usr"=>1, "value"=>$d);
    _updateDisponibilite($dispo);

    $json = getDisponibilitesArray();
    echo json_encode($json[0]["users"][0],JSON_PRETTY_PRINT);
}


//test_getDisponibilites();
test_updateDisponibilites();






?>