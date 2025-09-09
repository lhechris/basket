<?php 

require_once("constantes.php");
require_once("entrainements.php");
require_once("users.php");
require_once("auth.php");

/** Initialise dispo */
function initPresences() {
	$db = new SQLite3(DBLOCATION);
	$db->query('DELETE FROM presences');

	$users=getUsersArray();
	$entrainements=getEntrainementsArray();
	$n=0;
	foreach($entrainements as $e) {
		echo(round(($n*100)/count($entrainements))."%\r");
		$n=$n+1;
		foreach($users as $u) {
			$stmt = $db->prepare("INSERT INTO presences(entrainement,user,val) VALUES(:entrainement,:user,0)");
			$stmt->bindValue(':entrainement', $e["id"], SQLITE3_INTEGER);
			$stmt->bindValue(':user', $u["id"], SQLITE3_INTEGER);
			$stmt->execute();
		}
	}
}

function initUserInPresence($username) {
	$db = new SQLite3(DBLOCATION);
	$db->query('DELETE FROM presences');

	$users=getUsersArray();
	$entrainements=getEntrainementsArray();
	$n=0;
	foreach($entrainements as $e) {
		echo(round(($n*100)/count($entrainements))."%\r");
		$n=$n+1;
		foreach($users as $u) {
			$stmt = $db->prepare("INSERT INTO presences(entrainement,user,val) VALUES(:entrainement,:user,0)");
			$stmt->bindValue(':entrainement', $e["id"], SQLITE3_INTEGER);
			$stmt->bindValue(':user', $u["id"], SQLITE3_INTEGER);
			$stmt->execute();
		}
	}


}



function upgradePresencesFromFile() {
	$fullpath = REPERTOIRE_DATA."presences.json";
	if (!file_exists($fullpath)) { return;}

    //Recupere le fichier json
	$json = json_decode(file_get_contents($fullpath),true);	
	if (!is_array($json)) {	return; }

	$db = new SQLite3(DBLOCATION);

	foreach($json as $uid=>$u) {
		foreach($u as $mid=>$v) {
			$stmt = $db->prepare("UPDATE presences ".
			                     "SET val=:val ".
								 "WHERE entrainement=:mid AND user=:uid");
			if ($stmt === false) {return;}
			$stmt->bindValue(':mid', $mid+1, SQLITE3_INTEGER);
			$stmt->bindValue(':uid', $uid+1, SQLITE3_INTEGER);
			$stmt->bindValue(':val', $v, SQLITE3_TEXT);
			$stmt->execute();
		}
	}
}

/**
 * retourne le fichier json
 */
function getPresencesArray() {
	
	$db = new SQLite3(DBLOCATION);
/**select A.titre,B.nom,C.val from matchs A, users B, disponibilites C WHERE C.match=A.id AND C.user=B.id; */

	$results = $db->query('SELECT A.jour as jour,B.nom as user,C.val as val,A.id as eid,B.id as uid '. 
	                      'FROM entrainements A, users B, presences C '.
						  'LEFT JOIN ON C.user=B.id '.
						  'WHERE C.entrainement=A.id ORDER BY C.entrainement,B.nom');
	$json = array();

	while ($row = $results->fetchArray()) {

		$id=-1;
		foreach($json as $k => $r ) {
			if ($r["id"]==$row["eid"]) {
				$id = $k;
			}
		}
		
		if ($id==-1) {
			array_push($json, array( "id"    => $row["eid"],
									 "date"  => $row["jour"],
									 "users" => array()));
			$id = count($json)-1;
		}
		
		array_push($json[$id]["users"],array("nom"   => $row["user"], 
													 "pres" => $row["val"],
													 "id"    => $row["uid"]));
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
 * Met à jour et retourne les nouvelles valeurs 
 */
function setPresence($json) {
	_updatePresence($json);
	getPresences();
}


/**
 * Met à jour la BDD
 */
function _updatePresence($json) {
		

	if ( is_int($json['usr']) && is_int($json['entrainement']) && is_int($json['pres'])) {
		$db = new SQLite3(DBLOCATION);
		$query='UPDATE presences SET val=:val WHERE entrainement=:entrainement AND user=:user';
	
		$stmt = $db->prepare($query);

		if (($stmt->bindValue(':entrainement', $json['entrainement'], SQLITE3_INTEGER)) &&
			($stmt->bindValue(':user', $json['usr'], SQLITE3_INTEGER)) &&
			($stmt->bindValue(':val', $json['pres'], SQLITE3_INTEGER)) ) {

			loginfo($stmt->getSQL(true));
			if ($stmt->execute()===false) {
				loginfo("Erreur");
			}
			$stmt->reset();					

		} else {
			loginfo("Erreur query values");
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