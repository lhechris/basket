<?php 

namespace Basket;

require_once("utils.php");

use dao\StaffDAO;


class Staff {
	private StaffDAO $staff;

	public function __construct() {
		$this->staff = new StaffDAO();
	}


	public function getArray() {	
		$results = $this->staff->getAll();		
		$json = array();

		foreach ($results as $row) {
			array_push($json,array( "id" => $row->id,
									"prenom"=>$row->prenom,
									"nom"=>$row->nom,
									"licence" =>$row->licence,
									"role" => $row->role
			));
		}
		return $json;
	}

	public function get() {
		responseJson($this->getArray());
	}

	/**
	 * Modifie/Ajoute/Supprime des users 
	 * En entree un fichier JSON contenant la liste des matchs
	 * Si pas d'ID on ajoute et si param todelete on supprime sinon on modifie
	 */
	public function set($json) {
		loginfo("Staff::set");
		if (is_array($json) && 
			array_key_exists("prenom",$json) && 
			array_key_exists("nom",$json) &&
			array_key_exists("licence",$json) &&
			array_key_exists("role",$json) ) {

			if (array_key_exists("id",$json)) {
				if (array_key_exists("todelete",$json)) {
					$this->supprime($json["id"]);

				} else {
					$this->update($json["id"],$json["prenom"],$json["nom"],$json["licence"],$json["role"]);
				}

			} else {
				/**
				 * Il n'y a pas d'id pour ce match c'est donc un ajout 
				 */
				$this->ajoute($json["prenom"],$json["nom"],$json["licence"],$json["role"]);
			}
		}
		$this->get();	
	}

	/**
	 * Execute la requete UPDATE sur la table staff
	 */
	protected function update($id,$prenom,$nom,$licence,$role) {
		return $this->staff->update($id,$prenom,$nom,$licence,$role);
	}

	/**
	 * Execute la requete INSERT INTO dans la table staff
	 * 
	 */
	protected function ajoute($prenom,$nom,$licence,$role) {
		
		return $this->staff->create($prenom,$nom,$licence,$role);		

	}

	/**
	 * Execute la requete DELETE dans la table staff
	 * Supprime aussi les entrees dans disponibilites, selections et presences
	 */
	protected function supprime($id) {
		
		return $this->staff->delete($id);

	}

}

?>