<?php 

require_once("constantes.php");

function getUsersArray() {
	$fullpath = REPERTOIRE_DATA."users.json";
	if (!file_exists($fullpath)) { 
		return array();
	}

	//Recupere le fichier json
	$json = json_decode(file_get_contents($fullpath),true);	
	if (!is_array($json)) {			
		return array();
	}

	return $json;
}

function getUsers() {

	responseJson(getUsersArray());

}

?>