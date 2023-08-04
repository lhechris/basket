<?php 

require_once("constantes.php");

function getMatchsArray() {
	$fullpath = REPERTOIRE_DATA."matchs.json";
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

function getMatchs() {
	
	$json=getMatchsArray();
	$ret=[];

	//decoupe en paquet de 5 colonnes
	for ($n=0;$n<count($json);$n+=5) {
		$t=array_slice($json,$n,5);
		array_push($ret,$t);
	}
	
	responseJson($ret);

}



?>