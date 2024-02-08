<?php
include_once("../api/entrainements.php");

/**
 * 
 */
function test_getEntrainements() {
    $json = getEntrainementsArray();
    echo json_encode($json);
    echo "\n";
}




//upgradeFromfiles();
test_getEntrainements();


?>