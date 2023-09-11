<?php 

require_once("constantes.php");
require_once("matchs.php");
require_once("users.php");

/**
 * retourne le fichier json
 */
function getSelectionsArray() {
	$fullpath = REPERTOIRE_DATA."selections.json";
	if (!file_exists($fullpath)) { 
		$users=getUsersArray();
		$matchs=getMatchsArray();
		
		//cherche l'identifiant max pour user
		$mu=0;
		foreach($users as $u) {
			if ($mu<$u["id"]) { $mu=$u["id"];}
		}	
		
		//cherche l'identifiant max pour match
		$mm=0;
		foreach($matchs as $m) {
			if ($mm<$m["id"]) { $mm=$m["id"];}
		}
		$mat=array_pad(array(),$mm+1,0);
		$sel=array_pad(array(),$mu+1,$mat);
		writeSelectionFile($sel);
		return $sel;
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
function getSelections() {

	responseJson(getSelectionsArray());

}

/**
 * Met à jour le fichier json
 */
function setSelection($json) {

	if ( is_int($json['usr']) && is_int($json['match']) && is_int($json['selection'])) {
		
		$users=getUsersArray();
		$matchs=getMatchsArray();
		$select=getSelectionsArray();
		
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
		$select[$idu][$idm] = $json['selection'];

		if (($idu==-1) || ($idm==-1)) {
			responseError("Bad input");
			return;
		}

		if (writeSelectionFile($select)) {
			responseText("success");
		}

	} else {
		responseError("Bad input");
	}

}


function writeSelectionFile($json) {
	$fullpath=REPERTOIRE_DATA."selections.json";
	
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