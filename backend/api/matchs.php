<?php 

require_once("utils.php");

class Matchbasket extends CommonModel {

	public $id,$equipe,$jour,$titre,$score,$otm,$collation,$maillots,$oppositions;

	public function to_array() : array {
		
		$opps = null;
		if (is_array($this->oppositions)) {
			$opps=array();
			foreach ($this->oppositions as $opp) {
				array_push($opps,$opp->to_array());
			}
			if (count($opps)==1) {$opps=$opps[0];}
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
			"oppositions" => $opps
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
		$this->oppositions = null;
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

	
	public function get($id=null) {		
		$resp = $this->to_array($this->getArray($id));
		if ($id==null) {
			return responseJson($resp);
		} else {
			return responseJson($resp[0]);
		}

	}

	/**
	 * Execute la requete UPDATE sur la table match
	 */
	protected function update($id,$equipe,$titre,$score,$jour,$collation,$otm,$maillots) {
		
		$sql='UPDATE matchs '.
			  'SET equipe=:equipe, titre=:titre, score=:score, jour=:jour, collation=:collation, otm=:otm, maillots=:maillots '.
			  'WHERE id=:id';
		
		$this->query ($sql,
			[[':id', $id, SQLITE3_INTEGER],
			[':equipe', $equipe, SQLITE3_INTEGER],
			[':titre', $titre, SQLITE3_TEXT],
			[':score', $score, SQLITE3_TEXT],
			[':jour', $jour, SQLITE3_TEXT],
			[':collation', $collation, SQLITE3_TEXT],
			[':otm', $otm, SQLITE3_TEXT],
			[':maillots', $maillots, SQLITE3_TEXT]]);

	}

	/**
	 * Execute la requete INSERT INTO dans la table match
	 */
	protected function ajoute($equipe,$titre,$score,$jour,$collation,$otm,$maillots) {
		
		$sql = 'INSERT INTO matchs(titre,score,jour,equipe,collation,otm,maillots) '.
								   'VALUES(:titre,:score,:jour,:equipe,:collation,:otm,:maillots)';
		$this->query ($sql,
			[[':titre', $titre, SQLITE3_TEXT],
			[':score', $score, SQLITE3_TEXT], 
			[':jour', $jour, SQLITE3_TEXT],
			[':equipe', $equipe, SQLITE3_INTEGER],
			[':collation', $collation, SQLITE3_TEXT],
			[':otm', $otm, SQLITE3_TEXT],
			[':maillots', $maillots, SQLITE3_TEXT]]);		
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



	/**
	 * Modifie/Ajoute/Supprime des matchs 
	 * En entree un fichier JSON contenant la liste des matchs
	 * Si pas d'ID on ajoute et si param todelete on supprime sinon on modifie
	 */
	public function set($json) {

		foreach($json as $nm) {

			if (is_array($nm) && 
			    array_key_exists("titre",$nm) && 
				array_key_exists("jour",$nm) && 
				array_key_exists("score",$nm) && 
				array_key_exists("equipe",$nm)  && 
				array_key_exists("collation",$nm) && 
				array_key_exists("otm",$nm) &&
				array_key_exists("maillots",$nm)
				) {

				if (array_key_exists("id",$nm)) {
					if (array_key_exists("todelete",$nm)) {
						$this->supprime($nm["id"]);

					} else {
						$this->update($nm["id"],$nm["equipe"],$nm["titre"],$nm["score"],$nm["jour"],$nm['collation'],$nm['otm'],$nm['maillots']);
					}

				} else {
					/**
					 * Il n'y a pas d'id pour ce match c'est donc un ajout 
					 */
					$this->ajoute($nm["equipe"],$nm["titre"],$nm["score"],$nm["jour"],$nm['collation'],$nm['otm'],$nm['maillots']);
				}
			}
		}
		$this->get();	
	}

}

?>