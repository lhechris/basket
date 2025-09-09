<?php
include_once("../api/presences.php");

/**
 * 
 */
function test_getPresences() {
    $json = getPresencesArray();
    echo json_encode($json);
    echo "\n";
}


/**
 * 
 */
function test_updatePresence() {
    
    $json = getPresencesArray();
    echo json_encode($json[0]["users"][0],JSON_PRETTY_PRINT);
    
    $d = $json[0]["users"][0]["pres"];
    $d = $d==1?2:1;
    
    $pres = array("entrainement"=>1, "usr"=>1, "pres"=>$d);
    _updatePresence($pres);

    $json = getPresencesArray();
    echo json_encode($json[0]["users"][0],JSON_PRETTY_PRINT);
}


//initPresences();
test_getPresences();
//test_updatePresence();

?>