<?php 

require_once("constantes.php");
require_once("matchs.php");
require_once("users.php");
/**
 * retourne le fichier json
 */
function getPresencesArray() {
	$fullpath = REPERTOIRE_DATA."presences.json";
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

/**
 * 
 */
function getPresences() {

	responseJson(getPresencesArray());

}

/**
 * Met à jour le fichier json
 */
function setPresence($json) {

	if ( is_int($json['usr']) && is_int($json['match']) && is_int($json['value'])) {
		
		$users=getUsersArray();
		$matchs=getMatchsArray();
		$pres=getPresencesArray();

		$idu=-1;
		$idm=-1;

		foreach($users as $u) {
			if ($u["id"]==$json['usr']) {
				$idu=$json['usr'];
			}
		}

		foreach($matchs as $m) {
			if ($m["id"]==$json['match']) {
				$idm=$json['match'];
			}
		}
		$msg="idu=".$idu." idm=".$idm;
		$pres[$idu][$idm] = $json['value'];

		if (($idu==-1) || ($idm==-1)) {
			responseError("Bad input");
			return;
		}

		$fullpath=REPERTOIRE_DATA."presences.json";
	
		if (!$fp = fopen($fullpath, 'w')) {
			responseError("Impossible d'ouvrir le fichier");
			return;
	   }
	
	   if (fwrite($fp, json_encode($pres,JSON_UNESCAPED_SLASHES)) === FALSE) {
		   responseError("Impossible d'écrire dans le fichier");
		   fclose($fp);  
		   return;
	   }

		fclose($fp);

		//responseText("success");
		responseText($msg);

	} else {
		reponseError("Bad input");
	}

}


?>