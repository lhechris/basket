<?php 

require_once("utils.php");
require_once("matchs.php");
require_once("users.php");

class Selections {
	private $db;
	private $users;	
	private $matchs;
	private $disponibilites;

	public function __construct($donnees,$users,$matchs,$disponibilites) {		
		$this->db = $donnees->db;
		$this->users = $users;
		$this->matchs = $matchs;
		$this->disponibilites = $disponibilites;
	}	

	/**
	 * retourne le fichier json
	 */
	public function getArray() {

		$users = $this->users->getArray();
		$matchs = $this->matchs->getArray();
		$disponibilites = $this->disponibilites->getArray();

		$results = $this->db->query('SELECT A.match,A.user,A.val, B.jour, B.titre,B.equipe, C.prenom '.
									'FROM selections A, matchs B, users C '.
									'WHERE A.match=B.id AND A.user=C.id '.
									'ORDER BY B.jour,B.equipe,C.prenom');

		$selections = array();
		while ($row = $results->fetchArray()) {
			if (!array_key_exists($row['match'],$selections)) {				
				$selections[$row['match']]=array();
			}
			array_push($selections[$row['match']],array( "user"=>$row['user'],"val"=>$row['val']));

		}
		
		$json = array();

		foreach ($matchs as $m) {

			$currentmatch=array( "id" => $m->id,
								 "jour"  => $m->jour,
								 "users" => array(),
								 "autres" => array(),
								 "equipe" => $m->equipe,
								 "titre" => $m->titre,
								 "nb" => 0);
			
			$nbselected=0;					 
			//on ajoute chaque joueurs
			foreach($users as $u) {
				$selected=0;
				//si ce joueur à une selection pour ce match on garde sa valeur				
				if (array_key_exists($m->id,$selections)) {					
					foreach($selections[$m->id] as $p) {
						if ($p["user"] == $u["id"]) {
							$selected=$p["val"];
							if ($selected == 1) {$nbselected++;}
							break;
						}
					}
				}

				if (($selected == 0) && ($u["equipe"]!=$m->equipe)) {
					//on ne met pas le joueur parce qu'il ne fait pas partie de l'equipe
					//et qu'il n'a pas de selection (ça peut arriver si on l'a changé d'équipe)
				} else {
					//on recherche sa disponibilité
					$dispo=0;
					foreach ($disponibilites as $disponibilite) {
						if ($disponibilite["jour"]==$m->jour) {
							foreach($disponibilite["users"] as $du) {
								if ($du["id"] == $u["id"]) {
									$dispo=$du["dispo"];
									break;
								}
							}
							break;
						}
					}
					$joueur=array(
						"id" => $u["id"],
						"selection" => $selected,
						"dispo" => $dispo,
						"prenom" => $u["prenom"]
					);
					//en fonction de l'equipe on ne le met pas dans la meme liste
					if ($u["equipe"]==$m->equipe) {
						array_push($currentmatch["users"],$joueur);
					} else {
						array_push($currentmatch["autres"],$joueur);
					}
				}
			}
			$currentmatch["nb"] = $nbselected;
			
			$this->addmatch($json,$currentmatch);
		}
		$this->majcompteurs($json);
		return $json;

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


	/**
	 * Met à jour la BDD
	 */
	protected function update($json) {
		if ( is_int($json['usr']) && is_int($json['match']) && is_int($json['selection'])) {

			$this->createIfNotExists($json['match'],$json['usr']);

			$query='UPDATE selections SET val=:val WHERE match=:match AND user=:user';
			$stmt = $this->db->prepare($query);

			if (($stmt->bindValue(':match', $json['match'], SQLITE3_INTEGER)) &&
				($stmt->bindValue(':user', $json['usr'], SQLITE3_INTEGER)) &&
				($stmt->bindValue(':val', $json['selection'], SQLITE3_INTEGER)) ) {

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
	protected function exists($match,$usr) {
		$query = 'SELECT count(*) FROM selections WHERE match=:match AND user=:user';
		$stmt = $this->db->prepare($query);

		if (($stmt->bindValue(':match', $match, SQLITE3_INTEGER)) &&
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
	protected function createIfNotExists($match,$usr) {

		if ($this->exists($match,$usr)==false) {
			$query = 'INSERT INTO selections(match,user,val) VALUES (:match,:user,0)';
			$stmt = $this->db->prepare($query);

			if (($stmt->bindValue(':match', $match, SQLITE3_INTEGER)) &&
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
	 * Si l'equipe du match n'est pas dans le tableau on cree un
	 * nouveau enregistrement sinon on ajoute le match.
	 */
	private function addmatch(&$json,$match) {
		
		//recherche si cette equipe est déjà dans le json
		$key=-1;
		foreach ($json as $k=>$j) {
			if ($j["equipe"] == $match["equipe"]) { $key=$k;}
		}
		if ($key < 0) {
			$joueurs = array();
			$autrejoueurs=array();
			//on cree la liste des joueurs de l'equipe
			$query = "SELECT prenom,equipe FROM users WHERE equipe=:equipe ORDER BY prenom";
			$stmt = $this->db->prepare($query);
			
			if ($stmt->bindValue(':equipe', $match["equipe"], SQLITE3_INTEGER)) {
				$result = $stmt->execute();
				if ($result===false) {
					loginfo($stmt->getSQL(true));
					loginfo("Erreur");	
					return false;
				}

				while ($row = $result->fetchArray()) {
					array_push($joueurs,array("prenom"=>$row['prenom'],"nb"=>0));
				}		
			}			

			//on ajoute les joueurs qui ne font pas partie de l'equipe
			//mais qui ont participes aux matchs
			$query = "SELECT C.prenom,C.equipe FROM selections A, matchs B, users C ".
					 "WHERE A.match=B.id AND B.equipe=:equipe AND C.id=A.user AND C.equipe!=:equipe AND A.val=1 ".
					 "GROUP BY (C.id) ORDER BY C.prenom";
			$stmt = $this->db->prepare($query);

			if ($stmt->bindValue(':equipe', $match["equipe"], SQLITE3_INTEGER)) {
				$result = $stmt->execute();
				if ($result===false) {
					loginfo($stmt->getSQL(true));
					loginfo("Erreur");	
					return false;
				}

				while ($row = $result->fetchArray()) {
					array_push($autrejoueurs,array("prenom"=>$row['prenom'],"nb"=>0));
				}		
			}

			array_push($json,array(
						"equipe" => $match["equipe"],
						"joueurs" => $joueurs,
						"autrejoueurs" =>$autrejoueurs,
						"matchs" => array($match)
			));
		} else {
			//l'equipe existe dejà on ajoute juste le match
			array_push($json[$key]["matchs"],$match);
		}
	}

	/**
	 * Met à jour les compteurs du nombre de match selectionne par joueur
	 */
	private function majcompteurs(&$json) {
		foreach ($json as &$equipe) {
			$query = 'SELECT A.prenom ,count(*) '.
					  'FROM users A,selections B, matchs C '.
					  'WHERE A.id=B.user AND B.val=1 AND C.id=B.match '.
					  'GROUP BY A.prenom ORDER BY A.prenom';
			$stmt = $this->db->prepare($query);

			$result = $stmt->execute();
			if ($result===false) {
				loginfo($stmt->getSQL(true));
				loginfo("Erreur");	
				return false;
			}

			while ($row = $result->fetchArray()) {
				foreach ($equipe["joueurs"] as &$joueur){ 
					if ($joueur["prenom"] == $row["prenom"]) {
						$joueur["nb"] = $row["count(*)"];
						break;
					}
				}
				foreach ($equipe["autrejoueurs"] as &$joueur){ 
					if ($joueur["prenom"] == $row["prenom"]) {
						$joueur["nb"] = $row["count(*)"];
						break;
					}
				}
			}
		}
	}
}
?>