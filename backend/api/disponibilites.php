<?php 

require_once("utils.php");
require_once("matchs.php");


class Disponibilites {

	private $db;
	private $users;
	private $matchs;
	
	public function __construct($donnees,$users,$matchs) {
		$this->db = $donnees->db;
		$this->users = $users;
		$this->matchs = $matchs;
	}

	/**
	 * 
	 */
	public function get() {

		responseJson($this->getArray());

	}

	/**
	 * Met à jour et retourne les nouvelles valeurs 
	 */
	public function set($json) {
		$this->update($json);
		$this->get();
	}


	public function getArray() {
		
		$matchs=$this->matchs->getArray();
		$users=$this->users->getArray();
		if ($users == null) {
			loginfo("Il n'y a pas d'utilisateur!");
			return array();
		}	
		if ($matchs == null) {
			loginfo("Il n'y a pas de match!");
			return array();
		}			

		$results = $this->db->query('SELECT A.jour,A.user,A.val FROM disponibilites A, users B WHERE A.user=B.id ORDER BY A.jour,B.prenom');
		$dispo= array();
		while ($row = $results->fetchArray()) {
			if (!array_key_exists($row['jour'],$dispo)) {
				$dispo[$row['jour']]=array();
			}
			array_push($dispo[$row['jour']],array( "user"=>$row['user'],"val"=>$row['val']));
		}

		$json = array();
		$prevmatch=null;

		foreach ($matchs as $e) {
			
			if (($prevmatch!=null) && ($e["date"]==$prevmatch["jour"])) {
				//On ne retourne qu'un seul match par jour
				continue;				
			}

			$currentmatch=array( "jour"  => $e["date"],
								 "users" => array(),
								 "lieu" => $e['lieu']);

			$prevmatch=$currentmatch;

			foreach($users as $u) {
				$val=0;
				if (array_key_exists($e["date"],$dispo)) {
					foreach($dispo[$e["date"]] as $p) {
						if ($p["user"] == $u["id"]) {
							$val=$p["val"];
							break;
						}
					}
				}

				array_push($currentmatch["users"],array(
					"id" => $u["id"],
					"dispo" => $val,
					"prenom" => $u["prenom"]
				));
			}
			
			array_push($json,$currentmatch);
		}

		return $json;
	}



	/**
	 * Met à jour la BDD
	 */
	protected function update($json) {
			
		if ( is_int($json['usr']) && is_string($json['jour']) && is_int($json['value'])) {

			$this->createIfNotExists($json['jour'],$json['usr']);

			$query='UPDATE disponibilites SET val=:val WHERE jour=:jour AND user=:user';
			$stmt = $this->db->prepare($query);

			if (($stmt->bindValue(':jour', $json['jour'], SQLITE3_TEXT)) &&
				($stmt->bindValue(':user', $json['usr'], SQLITE3_INTEGER)) &&
				($stmt->bindValue(':val', $json['value'], SQLITE3_INTEGER)) ) {

				if ($stmt->execute()===false) {
					loginfo($stmt->getSQL(true));
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

	/**
	 * Comme son nom l'indique retourne true si l'enregistrement existe
	 * db doit etre instancié
	 */
	protected function exists($jour,$usr) {
		$query = 'SELECT count(*) FROM disponibilites WHERE jour=:jour AND user=:user';
		$stmt = $this->db->prepare($query);

		if (($stmt->bindValue(':jour', $jour, SQLITE3_TEXT)) &&
			($stmt->bindValue(':user', $usr, SQLITE3_INTEGER))) {	
		
			$result = $stmt->execute();
			if ($result===false) {
				loginfo($stmt->getSQL(true));
				loginfo("Erreur");	
				return false;
			}
			while ($row = $result->fetchArray()) {
				loginfo($row[0]);
				return ($row[0] >= 1);
			}
			
		} else {
			loginfo("Erreur bindValue");	
			return false;
		}
	}


	/** Cree l'enregistrement s'il n'existe pas.
	 * db doit etre instancié
	*/
	protected function createIfNotExists($entrainement,$usr) {

		if ($this->exists($entrainement,$usr)==false) {
			$query = 'INSERT INTO disponibilites(jour,user,val) VALUES (:jour,:user,0)';
			$stmt = $this->db->prepare($query);

			if (($stmt->bindValue(':jour', $entrainement, SQLITE3_TEXT)) &&
				($stmt->bindValue(':user', $usr, SQLITE3_INTEGER))) {
			
				$result = $stmt->execute();
				if ($result===false) {
					loginfo($stmt->getSQL(true));
					loginfo("Erreur");	
					return false;
				}
			}	
		}
		return true;
	}


}
?>