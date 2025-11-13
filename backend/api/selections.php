<?php 

//require_once("utils.php");
require_once("dao/SelectionsDAO.php");
require_once("dao/DisponibilitesDAO.php");
require_once("dao/MatchsDAO.php");
require_once("dao/UsersDAO.php");

use dao\SelectionsDAO;
use dao\DisponibilitesDAO;
use dao\MatchsDAO;
use dao\UsersDAO;

class Selections  {
	private $users;	
	private $matchs;
	private $disponibilites;
	private $selections;

	public function __construct($donnees) {		
		$this->users = $users = new UsersDAO($donnees);
		$this->disponibilites = new DisponibilitesDAO($donnees);
		$this->matchs = new MatchsDAO($donnees);
		$this->selections = new SelectionsDAO($donnees);
		//parent::__construct($donnees);
	}	


	/**
	 * Retourne un ensemble de joueurs avec ses selections par match
	 * [ { prenom, nb, matchs : [{jour,equipe,selection,dispo},...]},...] 
	 */
	public function getArray() {

		//Initialize le tableau de sortie		
		$jours = array();
		$joueurs = array();

		//recupère la liste des matchs par jour
		$results = $this->matchs->getAll();
		foreach($results as $m) {

			//calcule le nombre de joueur selectionné pour ce match
			$nbjoueurs = $this->selections->getNbPlayer($m->id);
				
			//Ajoute ce match dans la liste de jour
			$notfound=true;
			foreach ($jours as &$j) { 
				if ($j["jour"] == $m->jour) {					
					array_push($j["matchs"],["id"=>$m->id,"equipe"=>$m->equipe, "nb"=>$nbjoueurs]);
					$notfound=false;
					break;
				}
			}
			if ($notfound) {
				array_push($jours,["jour"=>$m->jour ,"matchs"=>array(["id"=>$m->id,"equipe"=>$m->equipe,"nb"=>$nbjoueurs])]);
			}	
		}

		//On récupère toutes les selections des tous les matchs de toutes les équipes
		$results = $this->selections->getAll();
		
		//Prepare le tableau finale (cree une ligne par joueur)
		$users = $this->users->getAll();
		foreach ($users as $u) {

			//calcul le nombre de match selectionne pour ce joueur
			$nbmatchs = $this->selections->getNbMatch($u->id);
			$jours2=unserialize(serialize($jours));
			array_push($joueurs,["id" => $u->id,"prenom" => $u->prenom,"nb" => $nbmatchs, "jours" => $jours2]);
		}

		//Ajoute les matchs et selection pour chaque joueurs
		foreach ($results as $row) {
			foreach ($joueurs as &$u) { 
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

		foreach ($joueurs as &$u) {
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

		return ["jours" => $jours,"joueurs" => $joueurs];

	}


	/**
	 * retourne le fichier json
	 */
	public function getArrayOld() {
		
		$disponibilites = $this->disponibilites->getAll();
		$matchs = $this->matchs->getAll();

		//On récupère toutes les selections des tous les matchs de toutes les équipes
		$results = $this->selections->getAll();

		//On classe le résultat par match, 
		//On crée une liste de match qui contient la liste des joueurs selectionnés
		$selections = array();
		foreach ($results as $row) {
			if (!array_key_exists($row->match,$selections)) {				
				$selections[$row->match]=array();
			}
			array_push($selections[$row->match],array( "user"=>$row->user,"val"=>$row->val));

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
			$users = $this->users->getAll();
			foreach($users as $u) {
				$selected=0;
				//si ce joueur à une selection pour ce match on garde sa valeur				
				if (array_key_exists($m->id,$selections)) {					
					foreach($selections[$m->id] as $p) {
						if ($p["user"] == $u->id) {
							$selected=$p["val"];
							if ($selected == 1) {$nbselected++;}
							break;
						}
					}
				}

				if (($selected == 0) && ($u->equipe!=$m->equipe)) {
					//on ne met pas le joueur parce qu'il ne fait pas partie de l'equipe
					//et qu'il n'a pas de selection (ça peut arriver si on l'a changé d'équipe)
				} else {
					//on recherche sa disponibilité
					$dispo=0;
					foreach ($disponibilites as $disponibilite) {
						if ($disponibilite->jour == $m->jour && $disponibilite->user == $u->id) {
							$dispo=$disponibilite->val;
							break;
						}
					}
					
					$joueur=array(
						"id" => $u->id,
						"selection" => $selected,
						"dispo" => $dispo,
						"prenom" => $u->prenom
					);
					//en fonction de l'equipe on ne le met pas dans la meme liste
					if ($u->equipe==$m->equipe) {
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
			//L'equipe n'est pas dans le json on cree donc l'entete
			$joueurs = array();
			//on cree la liste des joueurs de l'equipe
			$result = $this->users->getPlayersByTeam($match["equipe"]);

			foreach ($result as $row) {
				array_push($joueurs,array("prenom"=>$row->prenom,"nb"=>0));
			}		

			//on ajoute les joueurs qui ne font pas partie de l'equipe
			//mais qui ont participes aux matchs
			$result = $this->selections->getPlayersSelectedButInOtherTeam($match["equipe"]);
			
			$autrejoueurs=array();
			foreach ($result as $row) {
					array_push($autrejoueurs,array("prenom"=>$row->prenom,"nb"=>0,"id"=>$row->id));					
			}
			$match["autres"] = $this->reclassejoueurs($autrejoueurs,$match["autres"]);
			array_push($json,array(
						"equipe" => $match["equipe"],
						"joueurs" => $joueurs,
						"autrejoueurs" =>$autrejoueurs,
						"matchs" => array($match)
			));
		} else {
			//l'equipe existe dejà on ajoute juste le match
			
			//On reclasse autrejoueurs pour etre le meme que dans l'entete
			$match["autres"] = $this->reclassejoueurs($json[$key]["autrejoueurs"],$match["autres"]);
			array_push($json[$key]["matchs"],$match);
		}
	}

	private function reclassejoueurs($listejoueurs, $toreclasse) {
		$out = $listejoueurs;
		foreach($out as &$nj) {
			$nj["dispo"] = 0;
			$nj["selection"] = 0;
			foreach ($toreclasse as $j) {
				if ($nj["id"] == $j["id"]) {
					$nj["dispo"] = $j["dispo"];
					$nj["selection"] = $j["selection"];
				}
			}
		}
		return $out;
	}


	/**
	 * Met à jour les compteurs du nombre de match selectionne par joueur
	 */
	private function majcompteurs(&$json) {
		
		$result = $this->selections->getNbMatchForEachPlayer();
		foreach ($json as &$equipe) {		
			foreach ($result as $row) {
				foreach ($equipe["joueurs"] as &$joueur){ 
					if ($joueur["prenom"] == $row->prenom) {
						$joueur["nb"] = $row->nb;
						break;
					}
				}
				foreach ($equipe["autrejoueurs"] as &$joueur){ 
					if ($joueur["prenom"] == $row->prenom) {
						$joueur["nb"] = $row->nb;
						break;
					}
				}
			}
		}
	}
}
?>