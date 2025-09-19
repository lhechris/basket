<?php 

require_once("utils.php");

class Matchs {
	private $db;

	public function __construct($donnees) {
		$this->db = $donnees->db;
	}


	public function getArray() {
		$results = $this->db->query('SELECT * FROM matchs ORDER BY jour');
		$json = array();

		while ($row = $results->fetchArray()) {
			array_push($json,array( "id" => $row["id"],
									"equipe" => $row['equipe'],
									"date"=>$row["jour"],
									"lieu" =>$row["titre"],
									"resultat" => $row["score"],
									"collation" => $row["collation"],
									"otm"=> $row["otm"],
									"maillots" => $row["maillots"]));
		}

		return $json;
	}

	public function get() {
		
		$json=$this->getArray();
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
	protected function update($id,$equipe,$titre,$score,$jour,$collation,$otm,$maillots) {
		
		$stmt = $this->db->prepare('UPDATE matchs '.
								   'SET equipe=:equipe, titre=:titre, score=:score, jour=:jour, collation=:collation, otm=:otm, maillots=:maillots '.
								   'WHERE id=:id');
		
		if (
			($stmt->bindValue(':id', $id, SQLITE3_INTEGER)) &&
			($stmt->bindValue(':equipe', $equipe, SQLITE3_INTEGER)) &&
			($stmt->bindValue(':titre', $titre, SQLITE3_TEXT)) &&
			($stmt->bindValue(':score', $score, SQLITE3_TEXT)) &&
			($stmt->bindValue(':jour', $jour, SQLITE3_TEXT)) &&
			($stmt->bindValue(':collation', $collation, SQLITE3_TEXT)) &&
			($stmt->bindValue(':otm', $otm, SQLITE3_TEXT)) &&
			($stmt->bindValue(':maillots', $maillots, SQLITE3_TEXT)) 
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
	 */
	protected function ajoute($equipe,$titre,$score,$jour,$collation,$otm,$maillots) {
		$stmt = $this->db->prepare('INSERT INTO matchs(titre,score,jour,equipe,collation,otm,maillots) '.
								   'VALUES(:titre,:score,:jour,:equipe,:collation,:otm,:maillots)');
		if (
			($stmt->bindValue(':titre', $titre, SQLITE3_TEXT)) &&
			($stmt->bindValue(':score', $score, SQLITE3_TEXT)) &&
			($stmt->bindValue(':jour', $jour, SQLITE3_TEXT)) &&
			($stmt->bindValue(':equipe', $equipe, SQLITE3_INTEGER)) &&
			($stmt->bindValue(':collation', $collation, SQLITE3_TEXT)) &&
			($stmt->bindValue(':otm', $otm, SQLITE3_TEXT)) &&
			($stmt->bindValue(':maillots', $maillots, SQLITE3_TEXT)) 
 
		) {
			loginfo($stmt->getSQL(true));
			if ($stmt->execute()===false) {loginfo("Erreur");}

			//$lastid=$db->lastInsertRowID();				

		} else {
			loginfo("Erreur query values");
		}
	}

	/**
	 * Execute la requete DELETE dans la table match
	 * Supprime aussi les entrees dans disponibilites et selections
	 */
	protected function supprime($id) {
		
		$stmt = $this->db->prepare('DELETE FROM matchs WHERE id=:id');

		if 	($stmt->bindValue(':id', $id, SQLITE3_INTEGER)) 
		{
			loginfo($stmt->getSQL(true));
			if ($stmt->execute()===false) {loginfo("Erreur");}

			//TODO supprime dans la table disponibilite uniquement s'il n'y a plus de match ce jour ci

			/*$stmt = $this->db->prepare('DELETE FROM disponibilites WHERE match=:id');
			$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
			loginfo($stmt->getSQL(true));
			if ($stmt->execute()===false) {loginfo("Erreur");}*/

			$stmt = $this->db->prepare('DELETE FROM selections WHERE match=:id');
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
	public function set($json) {

		foreach($json as $nm) {

			if (is_array($nm) && 
			    array_key_exists("lieu",$nm) && 
				array_key_exists("date",$nm) && 
				array_key_exists("resultat",$nm) && 
				array_key_exists("equipe",$nm)  && 
				array_key_exists("collation",$nm) && 
				array_key_exists("otm",$nm) &&
				array_key_exists("maillots",$nm)
				) {

				if (array_key_exists("id",$nm)) {
					if (array_key_exists("todelete",$nm)) {
						$this->supprime($nm["id"]);

					} else {
						$this->update($nm["id"],$nm["equipe"],$nm["lieu"],$nm["resultat"],$nm["date"],$nm['collation'],$nm['otm'],$nm['maillots']);
					}

				} else {
					/**
					 * Il n'y a pas d'id pour ce match c'est donc un ajout 
					 */
					$this->ajoute($nm["equipe"],$nm["lieu"],$nm["resultat"],$nm["date"],$nm['collation'],$nm['otm'],$nm['maillots']);
				}
			}
		}
		$this->get();	
	}

}

?>