<?php
include_once("../api/selections.php");

/**
 * 
 */
function test_getSelections() {
    $json = getSelectionsArray();
    echo json_encode($json,JSON_PRETTY_PRINT);
}


/**
 * 
 */
function test_updateSelections() {
    
    $json = getSelectionsArray();
    echo json_encode($json["1"]["users"][0],JSON_PRETTY_PRINT);
    echo "\n";
    
    $d = $json["1"]["users"][0]["selection"];
    $d = $d==1?2:1;
    
    $dispo = array("match"=>1, "usr"=>1, "selection"=>$d);
    _updateSelection($dispo);

    $json = getSelectionsArray();
    echo json_encode($json["1"]["users"][0],JSON_PRETTY_PRINT);
    echo "\n";
}

//initSelection();
//test_getSelections();
test_updateSelections();


?>