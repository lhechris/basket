<?php 

require_once("utils.php");
require_once("entrainements.php");
require_once("auth.php");

class Presences {
	private $db;
	private $users;
	private $entrainements;

	public function __construct($donnees,$users,$entrainements) {
		$this->db = $donnees->db;
		$this->users = $users;
		$this->entrainements = $entrainements;
	}

	/**
	 * 
	 */
	public function getArray() {
		
		$entrainements=$this->entrainements->getArray();
		$myusers=$this->users->getArray();
		if ($myusers == null) {
			loginfo("Il n'y a pas d'utilisateurs!");
			return array();
		}		

		$results = $this->db->query('SELECT A.entrainement,A.user,A.val FROM presences A, users B WHERE A.user=B.id ORDER BY A.entrainement,B.prenom');
		$presences= array();
		while ($row = $results->fetchArray()) {
			if (!array_key_exists($row['entrainement'],$presences)) {
				$presences[$row['entrainement']]=array();
			}
			array_push($presences[$row['entrainement']],array( "user"=>$row['user'],"val"=>$row['val']));
		}

		$json = array();

		foreach ($entrainements as $e) {

			$currententrainement=array( "id"    => $e["id"],
										"date"  => $e["jour"],
										"users" => array());

			foreach($myusers as $u) {
				$val=0;
				if (array_key_exists($e["id"],$presences)) {
					foreach($presences[$e["id"]] as $p) {
						if ($p["user"] == $u["id"]) {
							$val=$p["val"];
							break;
						}
					}
				}

				array_push($currententrainement["users"],array(
					"id" => $u["id"],
					"pres" => $val,
					"prenom" => $u["prenom"]
				));
			}
			
			array_push($json,$currententrainement);
		}

		return $json;
	}

	/**
	 * Retourne toutes les présence sur la console au format json
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

	/**
	 * Comme son nom l'indique retourne true si l'enregistrement existe
	 * db doit etre instancié
	 */
	protected function exists($entrainement,$usr) {
		$query = 'SELECT count(*) FROM presences WHERE entrainement=:entrainement AND user=:user';
		$stmt = $this->db->prepare($query);

		if (($stmt->bindValue(':entrainement', $entrainement, SQLITE3_INTEGER)) &&
			($stmt->bindValue(':user', $usr, SQLITE3_INTEGER))) {	
		
			$result = $stmt->execute();
			if ($result===false) {
				loginfo($stmt->getSQL(true));
				loginfo("Erreur");	
				return false;
			}
			while ($row = $result->fetchArray()) {
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
			$query = 'INSERT INTO presences(entrainement,user,val) VALUES (:entrainement,:user,0)';
			$stmt = $this->db->prepare($query);

			if (($stmt->bindValue(':entrainement', $entrainement, SQLITE3_INTEGER)) &&
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

	/**
	 * Verifie s'il y a déjà un enregistrement sinon le cree
	 * Ensuite met à jour la valeur de la presence
	 * Attend en entree un json : { entrainement : id, "usr" : id, "pres": int}
	 */
	protected function update($json) {
			

		if ( is_int($json['usr']) && is_int($json['entrainement']) && is_int($json['pres'])) {

			$this->createIfNotExists($json['entrainement'],$json['usr']);
			
			$query='UPDATE presences SET val=:val WHERE entrainement=:entrainement AND user=:user';
		
			$stmt = $this->db->prepare($query);

			if (($stmt->bindValue(':entrainement', $json['entrainement'], SQLITE3_INTEGER)) &&
				($stmt->bindValue(':user', $json['usr'], SQLITE3_INTEGER)) &&
				($stmt->bindValue(':val', $json['pres'], SQLITE3_INTEGER)) ) {

				if ($stmt->execute()===false) {
					loginfo($stmt->getSQL(true));
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
}

?>