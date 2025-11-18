<?php 

require_once("utils.php");

require_once("dao/EntrainementsDAO.php");
require_once("dao/UsersDAO.php");
require_once("dao/PresencesDAO.php");

use dao\EntrainementsDAO;
use dao\UsersDAO;
use dao\PresencesDAO;

class Presences {
	private $users;
	private $entrainements;
	private $presences;

	public function __construct() {
		$this->users = new UsersDAO();
		$this->entrainements = new EntrainementsDAO();
		$this->presences = new PresencesDAO();
	}

	/**
	 * 
	 */
	public function getArray() {
		
		$entrainements=$this->entrainements->getAll();
		$myusers=$this->users->getAll();
		if ($myusers == null) {
			loginfo("Il n'y a pas d'utilisateurs!");
			return array();
		}		

		$results = $this->presences->getAll();
		$presences= array();
		foreach ($results as $row) {
			if (!array_key_exists($row->entrainement,$presences)) {
				$presences[$row->entrainement]=array();
			}
			array_push($presences[$row->entrainement],array( "user"=>$row->user,"val"=>$row->val));
		}


		$json = array();

		foreach ($entrainements as $e) {

			$currententrainement=array( "id"    => $e->id,
										"date"  => $e->jour,
										"users" => array());

			foreach($myusers as $u) {
				$val=0;
				if (array_key_exists($e->id,$presences)) {
					foreach($presences[$e->id] as $p) {
						if ($p["user"] == $u->id) {
							$val=$p["val"];
							break;
						}
					}
				}

				array_push($currententrainement["users"],array(
					"id" => $u->id,
					"pres" => $val,
					"prenom" => $u->prenom
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
	 * Verifie s'il y a déjà un enregistrement sinon le cree
	 * Ensuite met à jour la valeur de la presence
	 * Attend en entree un json : { entrainement : id, "usr" : id, "pres": int}
	 */
	protected function update($json) {
			

		if ( is_int($json['usr']) && is_int($json['entrainement']) && is_int($json['pres'])) {

			if ($this->presences->exists($json['entrainement'],$json['usr'])) {				
				$this->presences->update($json['entrainement'],$json['usr'],$json['pres']);
			} else {
				$this->presences->create($json['entrainement'],$json['usr'],$json['pres']);
			}

		} else {
			responseError("Bad input");
		}

	}
}

?>