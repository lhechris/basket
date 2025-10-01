<?php 

require_once("utils.php");

class Matchbasket extends CommonModel {

	public $id,$equipe,$jour,$titre,$score,$otm,$collation,$maillots,$oppositions;
	public $adresse,$horaire,$rendezvous;

	public function to_array() : array {
		
		$opps = $this->toarrayrecursif($this->oppositions);
		if (($opps!=null) && (count($opps)==1)) {
			$opps=$opps[0];
		}

		return [
			"id" => $this->id,
			"equipe" => $this->equipe,
			"jour" => $this->jour,
			"titre" => $this->titre,
			"score" => $this->score,
			"otm" => $this->otm,
			"collation" => $this->collation,
			"maillots" => $this->maillots,
			"adresse" => $this->adresse,
			"horaire" => $this->horaire,
			"rendezvous" => $this->rendezvous,
			"oppositions" => $opps,
			"selections" => $this->toarrayrecursif($this->selections)
		];
	}

	public function from_array(array $data) {
		$this->id = $this->nullifnotexists($data,"id");
		if ($this->id == null) { $this->id = $this->nullifnotexists($data,"match");}
		$this->equipe = $this->nullifnotexists($data,"equipe");
		$this->jour = $this->nullifnotexists($data,"jour");
		$this->titre = $this->nullifnotexists($data,"titre");
		$this->score = $this->nullifnotexists($data,"score");
		$this->collation = $this->nullifnotexists($data,"collation");
		$this->otm = $this->nullifnotexists($data,"otm");
		$this->maillots = $this->nullifnotexists($data,"maillots");
		$this->adresse = $this->nullifnotexists($data,"adresse");
		$this->horaire = $this->nullifnotexists($data,"horaire");
		$this->rendezvous = $this->nullifnotexists($data,"rendezvous");
		$this->oppositions = null;
		$this->selections = null;
	}

}

class SelectionMatch extends CommonModel {
	public $user,$prenom;

	public function to_array() : array {
		return [
			"user" => $this->user,
			"prenom" => $this->prenom
		];
	}

	public function from_array(array $data) {
		$this->user = $this->nullifnotexists($data,"user");
		$this->prenom = $this->nullifnotexists($data,"prenom");
	}
}

class MatchsParJour extends CommonModel {
	public $jour,$matchs;

	public function to_array() : array {
		return [
			"jour" => $this->jour,
			"matchs" => $this->toarrayrecursif($this->matchs)
		];
	}

	public function from_array(array $data) {
		$this->jour = $this->nullifnotexists($data,"jour");		
		$this->matchs = array($this->nullifnotexists($data,"matchs"));
	}

}


class Matchs extends CommonCtrl{

	private $oppositions;

	public function __construct($donnees,$oppositions) {
		$this->oppositions = $oppositions;
		parent::__construct($donnees);
	}

	public function getArray($id=null) {
		if ($id === null) {
			$results = $this->query('SELECT * FROM matchs ORDER BY jour',[],'MatchBasket');
		
		} else {
			$results = $this->query('SELECT * FROM matchs WHERE id=:id',[[':id',intval($id),SQLITE3_INTEGER]],'MatchBasket');
			if (count($results)>0) {
				$o = $this->oppositions->getArray($id);
				$results[0]->oppositions = $o;
			}
		}

		return $results;
	}


	public function getAvecOppositionsArray() {
		$allmatchs = $this->query('SELECT * FROM matchs ORDER BY jour,equipe',[],'MatchBasket');

		$results=array();

		foreach($allmatchs as &$m) {			
		
			$o = $this->oppositions->getArray($m->id);
			$m->oppositions = $o;

			$notfound=true;
			foreach($results as &$res) {
				if ($res->jour == $m->jour) {
					array_push($res->matchs,$m);
					$notfound=false;
					break;
				}
			}
			if ($notfound) {
				$nm = new MatchsParJour();
				$nm->from_array(["jour"=>$m->jour,"matchs"=>$m]);
				array_push($results,$nm);
			}		
		}
		//loginfo(print_r($results,true));

		return $results;
	}



	public function getAvecSelectionsArray() {
		$allmatchs = $this->query('SELECT * FROM matchs ORDER BY jour,equipe',[],'MatchBasket');

		$results=array();

		foreach($allmatchs as &$m) {			
			$s = $this->query('SELECT A.user,C.prenom '.
					 'FROM selections A, matchs B, users C '.
					 'WHERE A.match=B.id AND A.user=C.id AND B.id=:id '.
					 'ORDER BY C.prenom',[[':id',intval($m->id),SQLITE3_INTEGER]],'SelectionMatch');			
			$m->selections = $s;
			
			$notfound=true;
			foreach($results as &$res) {
				if ($res->jour == $m->jour) {
					array_push($res->matchs,$m);
					$notfound=false;
					break;
				}
			}
			if ($notfound) {
				$nm = new MatchsParJour();
				$nm->from_array(["jour"=>$m->jour,"matchs"=>$m]);
				array_push($results,$nm);				
			}
			
		}
		//loginfo(print_r($results,true));

		return $results;
	}
	
	public function get($id=null) {		
		$resp = $this->to_array($this->getArray($id));
		if ($id==null) {
			return responseJson($resp);
		} else {
			return responseJson($resp[0]);
		}
	}

	public function getAvecOppositions() {		
		$resp = $this->to_array($this->getAvecOppositionsArray());
		return responseJson($resp);
	}




	public function getAvecSelections() {				
		$resp = $this->to_array($this->getAvecSelectionsArray());
		return responseJson($resp);
	}



	/**
	 * Execute la requete UPDATE sur la table match
	 */
	protected function update($id,$equipe,$titre,$score,$jour,$collation,$otm,$maillots,$adresse,$horaire,$rendezvous) {
		
		$sql='UPDATE matchs '.
			  'SET equipe=:equipe, titre=:titre, score=:score, jour=:jour, collation=:collation, otm=:otm, maillots=:maillots,adresse=:adresse, horaire=:horaire,rendezvous=:rendezvous  '.
			  'WHERE id=:id';
		
		$this->query ($sql,
			[[':id', $id, SQLITE3_INTEGER],
			[':equipe', $equipe, SQLITE3_INTEGER],
			[':titre', $titre, SQLITE3_TEXT],
			[':score', $score, SQLITE3_TEXT],
			[':jour', $jour, SQLITE3_TEXT],
			[':collation', $collation, SQLITE3_TEXT],
			[':otm', $otm, SQLITE3_TEXT],
			[':maillots', $maillots, SQLITE3_TEXT],
			[':adresse', $adresse, SQLITE3_TEXT],
			[':horaire', $horaire, SQLITE3_TEXT],
			[':rendezvous', $rendezvous, SQLITE3_TEXT]
		]);

	}

	/**
	 * Execute la requete INSERT INTO dans la table match
	 */
	protected function ajoute($equipe,$titre,$score,$jour,$collation,$otm,$maillots,$adresse,$horaire,$rendezvous) {
		
		$sql = 'INSERT INTO matchs(titre,score,jour,equipe,collation,otm,maillots,adresse,horaire,rendezvous) '.
								   'VALUES(:titre,:score,:jour,:equipe,:collation,:otm,:maillots,:adresse,:horaire,:rendezvous)';
		$this->query ($sql,
			[[':titre', $titre, SQLITE3_TEXT],
			[':score', $score, SQLITE3_TEXT], 
			[':jour', $jour, SQLITE3_TEXT],
			[':equipe', $equipe, SQLITE3_INTEGER],
			[':collation', $collation, SQLITE3_TEXT],
			[':otm', $otm, SQLITE3_TEXT],
			[':maillots', $maillots, SQLITE3_TEXT],
			[':adresse', $adresse, SQLITE3_TEXT],
			[':horaire', $horaire, SQLITE3_TEXT],
			[':rendezvous', $rendezvous, SQLITE3_TEXT]
		]);		
	}

	/**
	 * Execute la requete DELETE dans la table match
	 * Supprime aussi les entrees dans disponibilites et selections
	 */
	protected function supprime($id) {
		
		$sql='DELETE FROM matchs WHERE id=:id';
		$this->query ($sql,[[':id', $id, SQLITE3_INTEGER]]); 

		//TODO supprime dans la table disponibilite uniquement s'il n'y a plus de match ce jour ci

		$sql='DELETE FROM selections WHERE match=:id';
		$this->query ($sql,[[':id', $id, SQLITE3_INTEGER]]);

		$sql='DELETE FROM oppositions WHERE match=:id';
		$this->query ($sql,[[':id', $id, SQLITE3_INTEGER]]);

	}


	private function setOne($tab) {
		loginfo("setOne");
		loginfo(print_r($tab,true));
		if (is_array($tab) && 
			array_key_exists("titre",$tab) && 
			array_key_exists("jour",$tab) && 
			array_key_exists("score",$tab) && 
			array_key_exists("equipe",$tab)  && 
			array_key_exists("collation",$tab) && 
			array_key_exists("otm",$tab) &&
			array_key_exists("maillots",$tab)
			) {

			loginfo("ici");

			if (array_key_exists("id",$tab)) {
				if (array_key_exists("todelete",$tab)) {
					loginfo("todelete");
					$this->supprime($tab["id"]);

				} else {
					loginfo("update");
					$this->update($tab["id"],
								  $tab["equipe"],
								  $tab["titre"],
								  $tab["score"],
								  $tab["jour"],
								  $tab['collation'],
								  $tab['otm'],
								  $tab['maillots'],
								  $tab['adresse'],
								  $tab['horaire'],
								  $tab['rendezvous']
								);
				}

			} else {
				/**
				 * Il n'y a pas d'id pour ce match c'est donc un ajout 
				 */
				loginfo("la");
				$this->ajoute($tab["equipe"],
							  $tab["titre"],
							  $tab["score"],
							  $tab["jour"],
							  $tab['collation'],
							  $tab['otm'],
							  $tab['maillots'],
							  $tab['adresse'],
							  $tab['horaire'],
							  $tab['rendezvous'],
							);
			}			
		}
		loginfo("end");
	}


	/**
	 * Modifie/Ajoute/Supprime des matchs 
	 * En entree un fichier JSON contenant la liste des matchs
	 * Si pas d'ID on ajoute et si param todelete on supprime sinon on modifie
	 */
	public function set($json) {

		if (is_array($json) && (array_key_exists("titre",$json))) {
			$this->setOne($json);
			$this->get();
		
		} else {		
			foreach($json as $nm) {
				$this->setOne($nm);
			}
			$this->getAvecOppositions();
		}		
	}
}

?>