<?php 

require_once("constantes.php");
require_once("matchs.php");
require_once("users.php");
/**
 * retourne le fichier json
 */
function getDisponibilitesArray() {
	$fullpath = REPERTOIRE_DATA."disponibilites.json";
	if (!file_exists($fullpath)) { 
		//on cree le fichier

		$users=getUsersArray();
		$matchs=getMatchsArray();
		
		//cherche l'identifiant max pour user
		$mu=0;
		foreach($users as $u) {
			if ($mu<$u["id"]) { $mu=$u["id"];}
		}	
		
		//cherche l'identifiant max pour entrainement
		$mm=0;
		foreach($matchs as $m) {
			if ($mm<$m["id"]) { $mm=$m["id"];}
		}
		$mat=array_pad(array(),$mm+1,0);
		$dispo=array_pad(array(),$mu+1,$mat);
		writePresenceFile($dispo);
		return $dispo;
		
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
function getDisponibilites() {

	responseJson(getDisponibilitesArray());

}

/**
 * Met à jour le fichier json
 */
function setDisponibilite($json) {

	if ( is_int($json['usr']) && is_int($json['match']) && is_int($json['value'])) {
		
		$users=getUsersArray();
		$matchs=getMatchsArray();
		$dispo=getDisponibilitesArray();

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
		$dispo[$idu][$idm] = $json['value'];

		if (($idu==-1) || ($idm==-1)) {
			responseError("Bad input");
			return;
		}

		if (writeDispoFile($dispo)) {
			responseText("success");
		}

	} else {
		responseError("Bad input");
	}

}


function writeDispoFile($json) {
	$fullpath=REPERTOIRE_DATA."disponibilites.json";
	
	if (!$fp = fopen($fullpath, 'w')) {
		responseError("Impossible d'ouvrir le fichier");
		return false;
   }

   if (fwrite($fp, json_encode($json,JSON_UNESCAPED_SLASHES)) === FALSE) {
	   responseError("Impossible d'écrire dans le fichier");
	   fclose($fp);  
	   return false;
   }

	fclose($fp);
	return true;
}

?>