<?php 

require_once("utils.php");

require_once("dao/UsersDAO.php");

use dao\UsersDAO;


class Users {
	private $users;

	public function __construct($donnees) {
		$this->users = new UsersDAO($donnees);
	}


	public function getArray() {	
		$results = $this->users->getAll();
		$json = array();

		foreach ($results as $row) {
			array_push($json,array( "id" => $row->id,
									"prenom"=>$row->prenom,
									"nom"=>$row->nom,
									"equipe"=>$row->equipe,
									"licence" =>$row->licence,
									"otm" => $row->otm == 1 ? true : false,
									"charte" =>$row->charte == 1 ? true : false));
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

		foreach($json as $nm) {

			if (is_array($nm) && 
				array_key_exists("prenom",$nm) && 
				array_key_exists("nom",$nm) &&
				array_key_exists("equipe",$nm) && 
				array_key_exists("licence",$nm) &&
				array_key_exists("charte",$nm) &&
				array_key_exists("otm",$nm) ) {

				if (array_key_exists("id",$nm)) {
					if (array_key_exists("todelete",$nm)) {
						$this->supprime($nm["id"]);

					} else {
						$this->update($nm["id"],$nm["prenom"],$nm["nom"],$nm["equipe"],$nm["licence"],$nm["otm"],$nm["charte"]);
					}

				} else {
					/**
					 * Il n'y a pas d'id pour ce match c'est donc un ajout 
					 */
					$this->ajoute($nm["prenom"],$nm["equipe"]);
				}
			}
		}
		$this->get();	
	}

	/**
	 * Execute la requete UPDATE sur la table match
	 */
	protected function update($id,$prenom,$nom,$equipe,$licence,$otm,$charte) {
		return $this->users->update($id,$prenom,$nom,$equipe,$licence,$otm,$charte);
	}

	/**
	 * Execute la requete INSERT INTO dans la table match
	 * 
	 */
	protected function ajoute($prenom,$equipe) {
		
		return $this->users->create($prenom,"",$equipe,"",0,0);		

	}

	/**
	 * Execute la requete DELETE dans la table users
	 * Supprime aussi les entrees dans disponibilites, selections et presences
	 */
	protected function supprime($id) {
		
		return $this->users->delete($id);

	}

}

?>