<?php 

require_once("utils.php");
require_once("matchs.php");
require_once("users.php");
require_once("dao/SelectionsDAO.php");
require_once("dao/DisponibilitesDAO.php");
require_once("dao/MatchsDAO.php");

use dao\SelectionsDAO;
use dao\DisponibilitesDAO;
use dao\MatchsDAO;

class Selection extends CommonModel {
	public $match, $user, $val, $jour, $titre,$equipe, $prenom, $dispo;

	public function to_array() : array {
		return [
			"match"  => $this->match,
			"user"   => $this->user,
			"val"    => $this->val,
 			"jour"   => $this->jour,
			"titre"  => $this->titre,
			"equipe" => $this->equipe,
			"prenom" =>$this->prenom,
			"dispo"  => $this->dispo
		];
	}

	public function from_array(array $data) {
		$this->jour = $this->nullifnotexists($data,"jour");		
		$this->match = $this->nullifnotexists($data,"match");
		$this->user = $this->nullifnotexists($data,"user");
		$this->val = $this->nullifnotexists($data,"val");
		$this->titre = $this->nullifnotexists($data,"titre");
		$this->equipe = $this->nullifnotexists($data,"equipe");
		$this->prenom = $this->nullifnotexists($data,"prenom");
		$this->dispo = $this->nullifnotexists($data,"dispo");

	}
}

class Selections extends CommonCtrl {
	private $users;	
	private $matchs, $matchsOld;
	private $disponibilites, $disponibilitesOld;
	private $selections;

	public function __construct($donnees,$users,$matchs) {		
		$this->users = $users;
		$this->matchsOld = $matchs;
		$this->disponibilitesOld = new Disponibilites($donnees,$users,$matchs);
		$this->disponibilites = new DisponibilitesDAO($donnees);
		$this->matchs = new MatchsDAO($donnees);
		$this->selections = new SelectionsDAO($donnees);
		parent::__construct($donnees);
	}	


	/**
	 * Retourne un ensemble de joueurs avec ses selections par match
	 * [ { prenom, nb, matchs : [{jour,equipe,selection,dispo},...]},...] 
	 */
	public function getArray() {
		$users = $this->users->getArray();

		//Initialize le tableau de sortie
		$json = array("jours"=>array(),"joueurs"=>array());
		$jours = array();

		//recupère la liste des matchs par jour
		$results = $this->query('SELECT id,jour,equipe FROM matchs ORDER BY jour',[],"MatchBasket");
		foreach($results as $m) {

			//calcule le nombre de joueur selectionné pour ce match
			$nbmatchs = $this->querycount("matchs A,selections B",
										  "A.id=B.match AND B.val=1 AND A.id=:id",
										  [[':id', $m->id, SQLITE3_INTEGER]]);

			//Ajoute ce match dans la liste de jour
			$notfound=true;
			foreach ($json["jours"] as &$j) { 
				if ($j["jour"] == $m->jour) {					
					array_push($j["matchs"],["id"=>$m->id,"equipe"=>$m->equipe, "nb"=>$nbmatchs]);
					$notfound=false;
					break;
				}
			}
			if ($notfound) {
				array_push($json["jours"],["jour"=>$m->jour ,"matchs"=>array(["id"=>$m->id,"equipe"=>$m->equipe,"nb"=>$nbmatchs])]);
			}	
		}

		//On récupère toutes les selections des tous les matchs de toutes les équipes
		$results = $this->query('SELECT A.match,A.user,A.val, B.jour, B.titre,B.equipe, C.prenom '.
									'FROM selections A, matchs B, users C '.
									'WHERE A.match=B.id AND A.user=C.id '.
									'ORDER BY B.jour,B.equipe,C.prenom',
								[],"Selection");
		
		//Prepare le tableau finale (cree une ligne par joueur)
		foreach ($users as $u) {

			//calcul le nombre de match selectionne pour ce joueur
			$nbmatchs = $this->querycount("users A,selections B, matchs C",
										  "A.id=B.user AND B.val=1 AND C.id=B.match AND A.id=:id",
										  [[':id', $u["id"], SQLITE3_INTEGER]]);

			array_push($json["joueurs"],["id" => $u["id"],"prenom" => $u["prenom"],"nb" => $nbmatchs,"jours" => $json["jours"]]);
		}

		//Ajoute les matchs et selection pour chaque joueurs
		foreach ($results as $row) {
			foreach ($json["joueurs"] as &$u) { 
				if ($u["id"] == $row->user) {
					//ajoute les champs dans le match
					foreach ($u["jours"] as &$jour) {						
						foreach ($jour["matchs"] as &$m) {
							if ($m["id"] == $row->match) {
								$m["selection"] = $row->val;
							}
						}
					}
					break;
				}
			}
		}

		//Ajoute les dispo
		$disponibilites = $this->disponibilites->getAll();

		foreach ($json["joueurs"] as &$u) {
			foreach($u["jours"] as &$jour) {
				$notfound=true;
				foreach ($disponibilites as $dispo) {
					if (($dispo->jour == $jour["jour"]) && ($dispo->user == $u["id"])) {
						$jour["dispo"] = $dispo->val;
						$notfound=false;
						break;
					}
				}
				if ($notfound) {$jour["dispo"] = 0;}
			}
		}
		return $json;

	}


	/**
	 * retourne le fichier json
	 */
	public function getArrayOld() {

		$users = $this->users->getArray();
		$matchs = $this->matchsOld->getArray();	
		$disponibilites = $this->disponibilitesOld->getArray();

		//On récupère toutes les selections des tous les matchs de toutes les équipes
		$results = $this->db->query('SELECT A.match,A.user,A.val, B.jour, B.titre,B.equipe, C.prenom '.
									'FROM selections A, matchs B, users C '.
									'WHERE A.match=B.id AND A.user=C.id '.
									'ORDER BY B.jour,B.equipe,C.prenom');

		//On classe le résultat par match, 
		//On crée une liste de match qui contient la liste des joueurs selectionnés
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
	public function getOld() {

		responseJson($this->getArrayOld());

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
					
			//on supprime les enregistrements sur les autres matchs du même jour
			$match=$this->matchs->getById($json['match']);
			if ($match==null) {return;}
			$this->selections->deleteByDay($match->jour,$json['usr']);
			
			//On ajoute donc un nouvel enregistrement
			$this->selections->create($json['match'],$json['usr'],$json['selection']);
		}
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