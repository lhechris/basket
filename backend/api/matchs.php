<?php 

require_once("constantes.php");

/** Initialise  */
function initMatchs() {
	$db = new SQLite3(DBLOCATION);
	$db->query('DELETE FROM matchs');
}

/** Rempli la BD depuis un fichier json */
function upgradeMatchsFromfiles() {
	$fullpath = REPERTOIRE_DATA."matchs.json";
	if (!file_exists($fullpath)) { return;}

    //Recupere le fichier json
	$json = json_decode(file_get_contents($fullpath),true);	
	if (!is_array($json)) {	return; }

	$db = new SQLite3(DBLOCATION);

	foreach($json as $m) {
		$stmt = $db->prepare("INSERT INTO matchs(equipe,jour,titre,score) VALUES(:equipe,:jour,:titre,:score)");
		if ($stmt === false) {return;}
		$stmt->bindValue(':equipe', $m["equipe"], SQLITE3_INTEGER);
		$stmt->bindValue(':jour', $m["date"], SQLITE3_TEXT);
		$stmt->bindValue(':titre', $m["lieu"], SQLITE3_TEXT);
		$stmt->bindValue(':score', $m["resultat"], SQLITE3_TEXT);
		$stmt->execute();
	}
}

function getMatchsArray() {
	$db = new SQLite3(DBLOCATION);
	$results = $db->query('select * from matchs');
	$json = array();

	while ($row = $results->fetchArray()) {
		array_push($json,array( "id" => $row["id"],
								"equipe" => $row['equipe'],
								"date"=>$row["jour"],
								"lieu" =>$row["titre"],
								"resultat" => $row["score"]));
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

/**
 * Execute la requete UPDATE sur la table match
 */
function _updateMatch($db,$id,$equipe,$titre,$score,$jour) {
	
	$stmt = $db->prepare('UPDATE matchs SET equipe=:equipe, titre=:titre, score=:score, jour=:jour WHERE id=:id');
	
	if (
		($stmt->bindValue(':id', $id, SQLITE3_INTEGER)) &&
		($stmt->bindValue(':equipe', $id, SQLITE3_INTEGER)) &&
		($stmt->bindValue(':titre', $titre, SQLITE3_TEXT)) &&
		($stmt->bindValue(':score', $score, SQLITE3_TEXT)) &&
		($stmt->bindValue(':jour', $jour, SQLITE3_TEXT)) 
	) {
		loginfo($stmt->getSQL(true));
		if ($stmt->execute()===false) {
			loginfo("Erreur");
		}
		$stmt->reset();					

	} else {
		loginfo("Erreur query values");
	}
}

/**
 * Execute la requete INSERT INTO dans la table match
 * Et ajoute un entree dans les tables disponibilites et selections
 */
function _ajouteMatch($db,$equipe,$titre,$score,$jour) {
	$stmt = $db->prepare('INSERT INTO matchs(titre,score,jour,equipe) VALUES(:titre,:score,:jour,:equipe)');
	if (
		($stmt->bindValue(':titre', $titre, SQLITE3_TEXT)) &&
		($stmt->bindValue(':score', $score, SQLITE3_TEXT)) &&
		($stmt->bindValue(':jour', $jour, SQLITE3_TEXT)) &&
		($stmt->bindValue(':equipe', $jour, SQLITE3_INTEGER)) 
	) {
		loginfo($stmt->getSQL(true));
		if ($stmt->execute()===false) {loginfo("Erreur");}

		$lastid=$db->lastInsertRowID();				

		if ($lastid>0) {
			$stmt = $db->prepare('SELECT id FROM users WHERE equipe=:equipe');
			$stmt->bindValue(':equipe', $equipe, SQLITE3_INTEGER);
			$users = $stmt->execute();

			while ($u = $users->fetchArray()) {
				$stmt = $db->prepare('INSERT INTO disponibilites(match,user) VALUES (:mid,:uid)');			
				$stmt->bindValue(':uid', $u['id'], SQLITE3_INTEGER);
				$stmt->bindValue(':mid', $lastid, SQLITE3_INTEGER);
				loginfo($stmt->getSQL(true));
				if ($stmt->execute()===false) {loginfo("Erreur");}
				$stmt->reset();

				$stmt = $db->prepare('INSERT INTO selections(match,user) VALUES (:mid,:uid)');			
				$stmt->bindValue(':uid', $u['id'], SQLITE3_INTEGER);
				$stmt->bindValue(':mid', $lastid, SQLITE3_INTEGER);
				loginfo($stmt->getSQL(true));
				if ($stmt->execute()===false) {loginfo("Erreur");}
				$stmt->reset();
			}
		}

	} else {
		loginfo("Erreur query values");
	}
}

/**
 * Execute la requete DELETE dans la table match
 * Supprime aussi les entrees dans disponibilites et selections
 */
function _supprimeMatch($db,$id) {
	
	$stmt = $db->prepare('DELETE FROM matchs WHERE id=:id');

	if 	($stmt->bindValue(':id', $id, SQLITE3_INTEGER)) 
	{
		loginfo($stmt->getSQL(true));
		if ($stmt->execute()===false) {loginfo("Erreur");}

		$stmt = $db->prepare('DELETE FROM disponibilites WHERE match=:id');
		$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
		loginfo($stmt->getSQL(true));
		if ($stmt->execute()===false) {loginfo("Erreur");}

		$stmt = $db->prepare('DELETE FROM selections WHERE match=:id');
		$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
		loginfo($stmt->getSQL(true));
		if ($stmt->execute()===false) {loginfo("Erreur");}

	} else {
		loginfo("Erreur query values");
	}

}



/**
 * Modifie/Ajoute/Supprime des matchs 
 * En entree un fichier JSON contenant la liste des matchs
 * Si pas d'ID on ajoute et si param todelete on supprime sinon on modifie
 */
function setMatchs($json) {
	$db = new SQLite3(DBLOCATION);

	foreach($json as $nm) {

		if (is_array($nm) && array_key_exists("lieu",$nm) && array_key_exists("date",$nm) && array_key_exists("resultat",$nm) && array_key_exists("equipe",$nm) ) {

			if (array_key_exists("id",$nm)) {
				if (array_key_exists("todelete",$nm)) {
					_supprimeMatch($db,$nm["id"]);

				} else {
					_updateMatch($db,$nm["id"],$nm["equipe"],$nm["lieu"],$nm["resultat"],$nm["date"]);
				}

			} else {
				/**
				 * Il n'y a pas d'id pour ce match c'est donc un ajout 
				 */
				_ajouteMatch($db,$nm["equipe"],$nm["lieu"],$nm["resultat"],$nm["date"]);
			}
		}
	}
	getMatchs();	
}

?>