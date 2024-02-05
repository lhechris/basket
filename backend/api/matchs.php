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
	/*$ret=[];

	//decoupe en paquet de 5 colonnes
	for ($n=0;$n<count($json);$n+=5) {
		$t=array_slice($json,$n,5);
		array_push($ret,$t);
	}
	
	responseJson($ret);*/
	responseJson($json);

}


function setMatchs($json) {
	$oldJson = getMatchsArray();

	foreach($json as $nm) {

		if (is_array($nm) && array_key_exists("lieu",$nm) && array_key_exists("date",$nm) && array_key_exists("resultat",$nm)) {

			if (array_key_exists("id",$nm)) {
				//Met à jour tous les matchs avec qui on un id
				foreach($oldJson as $key => &$om) {
					if ($om["id"] === $nm["id"]) {
						$om["lieu"] = $nm["lieu"];
						$om["date"] = $nm["date"];
						$om["resultat"] = $nm["resultat"];
						if (array_key_exists("todelete",$nm) && ($nm["todelete"]==true)) {
							//Supprime le match
							unset($oldJson[$key]);
						}
					}
				}
				unset($om);

			} else {
				//Il n'y a pas d'ID c'est donc un ajout.
				//recherche l'ID max pour en creer un nouveau
				$maxid=-1;
				foreach($oldJson as $om) {
					if ($om["id"]>$maxid) {$maxid=$om["id"];}
				}
				$maxid+=1;

				array_push($oldJson, [
					"id" => $maxid,
					"lieu" => $nm["lieu"],
					"date" => $nm["date"],
					"resultat" => $nm["resultat"]
				]
				);
				
			}
		}
	}

	//Sauve le fichier
	if (writeMatchsFile($oldJson)) {
		getMatchs();
	}
	
}


function writeMatchsFile($json) {
	$fullpath=REPERTOIRE_DATA."matchs.json";
	
	if (!$fp = fopen($fullpath, 'w')) {
		responseError("Impossible d'ouvrir le fichier");
		return false;
   }

   if (fwrite($fp, json_encode($json,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)) === FALSE) {
	   responseError("Impossible d'écrire dans le fichier");
	   fclose($fp);  
	   return false;
   }

	fclose($fp);	
	return true;
}




?>