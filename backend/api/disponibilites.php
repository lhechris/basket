<?php 

require_once("utils.php");

require_once("dao/DisponibilitesDAO.php");
require_once("dao/MatchsDAO.php");
require_once("dao/UsersDAO.php");

use dao\DisponibilitesDAO;
use dao\MatchsDAO;
use dao\UsersDAO;

class Disponibilites {

	private $users;
	private $disponibilites;
	private $matchs;
	
	public function __construct() {
		$this->users = new UsersDAO();
		$this->matchs = new MatchsDAO();
		$this->disponibilites = new DisponibilitesDAO();
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
		
		$matchs=$this->matchs->getAll();
		$users=$this->users->getAll();
		if ($users == null) {
			loginfo("Il n'y a pas d'utilisateur!");
			return array();
		}	
		if ($matchs == null) {
			loginfo("Il n'y a pas de match!");
			return array();
		}			

		$results = $this->disponibilites->getAll();
		$dispo= array();
		foreach ($results as $row) {
			if (!array_key_exists($row->jour,$dispo)) {
				$dispo[$row->jour]=array();
			}
			array_push($dispo[$row->jour],array( "user"=>$row->user,"val"=>$row->val));
		}

		$json = array();
		$prevmatch=null;

		foreach ($matchs as $e) {
			
			if (($prevmatch!=null) && ($e->jour==$prevmatch["jour"])) {
				//On ne retourne qu'un seul match par jour
				continue;				
			}

			$currentmatch=array( "jour"  => $e->jour,
								 "users" => array(),
								 "titre" => $e->titre);

			$prevmatch=$currentmatch;

			foreach($users as $u) {
				$val=0;
				if (array_key_exists($e->jour,$dispo)) {
					foreach($dispo[$e->jour] as $p) {
						if ($p["user"] == $u->id) {
							$val=$p["val"];
							break;
						}
					}
				}

				array_push($currentmatch["users"],array(
					"id" => $u->id,
					"dispo" => $val,
					"prenom" => $u->prenom
				));
			}
			
			//setlocale(LC_COLLATE, 'fr_FR.UTF-8');
			//usort($currentmatch["users"], function($a, $b) {
			//	return strcoll($a["prenom"], $b["prenom"]);
			//});
			
			array_push($json,$currentmatch);
		}
		
		return $json;
	}



	/**
	 * Met à jour la BDD
	 */
	protected function update($json) {
			
		if ( is_int($json['usr']) && is_string($json['jour']) && is_int($json['value'])) {

			if ($this->disponibilites->exists($json['jour'],$json['usr'])) {
				$this->disponibilites->update($json['jour'],$json['usr'],$json['value']);
			} else {
				$this->disponibilites->create($json['jour'],$json['usr'],$json['value']);
			}

		} else {
			loginfo("bad input values");
		}
	}


}
?>