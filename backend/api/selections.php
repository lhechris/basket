<?php 

require_once("constantes.php");
require_once("matchs.php");
require_once("users.php");

/** Initialise selection */
function initSelections() {
	$db = new SQLite3(DBLOCATION);
	$db->query('DELETE FROM selections');

	$users=getUsersArray();
	$matchs=getMatchsArray();
	foreach($matchs as $m) {
		foreach($users as $u) {
			$stmt = $db->prepare("INSERT INTO selections(match,user,val) VALUES(:match,:user,0)");
			$stmt->bindValue(':match', $m["id"], SQLITE3_INTEGER);
			$stmt->bindValue(':user', $u["id"], SQLITE3_INTEGER);
			$stmt->execute();
		}
	}
}

function upgradeSelectionsFromFile() {
	$fullpath = REPERTOIRE_DATA."selections.json";
	if (!file_exists($fullpath)) { return;}

    //Recupere le fichier json
	$json = json_decode(file_get_contents($fullpath),true);	
	if (!is_array($json)) {	return; }

	$db = new SQLite3(DBLOCATION);

	foreach($json as $uid=>$u) {
		foreach($u as $mid=>$v) {
			$stmt = $db->prepare("UPDATE selections ".
			                     "SET val=:val ".
								 "WHERE match=:mid AND user=:uid");
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
function getSelectionsArray() {
	$db = new SQLite3(DBLOCATION);

	$results = $db->query('SELECT A.titre as titre,A.jour as jour,A.score as score,B.nom as user,C.val as dispo,A.id as mid,B.id as uid, D.val as selection '.
						'FROM matchs A, users B, disponibilites C, selections D '.
						'WHERE C.match=A.id AND C.user=B.id AND D.match=A.id AND D.user=B.id '.
						'ORDER BY C.match,C.user');
	$json = array();

	while ($row = $results->fetchArray()) {
		$id=-1;
		foreach($json as $k => $r ) {
			if ($r["id"]==$row["mid"]) {
				$id = $k;
			}
		}
		
		if ($id==-1) {
			array_push($json, array( "id"    => $row["mid"],
									 "lieu"  => $row["titre"], 
									 "date"  => $row["jour"],
									 "resultat"  => $row["score"],
									 "users" => array()));
			$id = count($json)-1;
		}
		
		array_push($json[$id]["users"],array("nom"   => $row["user"], 
													 "dispo" => $row["dispo"],
													 "selection" => $row["selection"],
													 "id"    => $row["uid"]));
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
 * Met à jour et retourne les nouvelles valeurs 
 */
function setSelection($json) {
	_updateSelection($json);
	getSelections();
}


/**
 * Met à jour la BDD
 */
function _updateSelection($json) {
		
	if ( is_int($json['usr']) && is_int($json['match']) && is_int($json['selection'])) {

		$db = new SQLite3(DBLOCATION);
		$query='UPDATE selections SET val=:val WHERE match=:match AND user=:user';
	
		$stmt = $db->prepare($query);

		if (($stmt->bindValue(':match', $json['match'], SQLITE3_INTEGER)) &&
			($stmt->bindValue(':user', $json['usr'], SQLITE3_INTEGER)) &&
			($stmt->bindValue(':val', $json['selection'], SQLITE3_INTEGER)) ) {

			loginfo($stmt->getSQL(true));
			if ($stmt->execute()===false) {
				loginfo("Erreur");
			}
			$stmt->reset();					

		} else {
			loginfo("Erreur query values");
		}
	} else {
		loginfo("bad input values");
	}
}

?>