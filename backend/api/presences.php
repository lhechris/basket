<?php 

require_once("constantes.php");
require_once("entrainements.php");
require_once("users.php");
require_once("auth.php");

/**
 * retourne le fichier json
 */
function getPresencesArray() {
	
	$fullpath = REPERTOIRE_DATA."presences.json";
	if (!file_exists($fullpath)) { 
		//on cree le fichier
		
		$pres=array();
		$users=getUsersArray();
		$entrainements=getEntrainementsArray();
		
		//cherche l'identifiant max pour user
		$mu=0;
		foreach($users as $u) {
			if ($mu<$u["id"]) { $mu=$u["id"];}
		}	
		
		//cherche l'identifiant max pour entrainement
		$me=0;
		foreach($entrainements as $e) {
			if ($me<$e["id"]) { $me=$e["id"];}
		}
		$ent=array_pad(array(),$me+1,0);
		$pres=array_pad(array(),$mu+1,$ent);
		writePresenceFile($pres);
		return $pres;
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

	if ( is_int($json['usr']) && is_int($json['entrainement']) && is_int($json['pres'])) {
		
		$users=getUsersArray();
		$entrainements=getEntrainementsArray();
		$pres=getPresencesArray();

		$idu=-1;
		$ide=-1;

		foreach($users as $u) {
			if ($u["id"]==$json['usr']) {
				$idu=$json['usr'];
			}
		}

		foreach($entrainements as $e) {
			if ($e["id"]==$json['entrainement']) {
				$ide=$json['entrainement'];
			}
		}
		$msg="idu=".$idu." ide=".$ide;
		$pres[$idu][$ide] = $json['pres'];

		if (($idu==-1) || ($ide==-1)) {
			responseError("Bad input");
			return;
		}

		if (writePresenceFile($pres)) {
			responseText("success");
		} 

	} else {
		responseError("Bad input");
	}

}


function writePresenceFile($json) {
	$fullpath=REPERTOIRE_DATA."presences.json";
	
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