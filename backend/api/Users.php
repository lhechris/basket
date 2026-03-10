<?php 

namespace Basket;

require_once("utils.php");

//require_once("dao/UsersDAO.php");

use dao\UsersDAO;
use dao\SelectionsDAO;
use dao\AnimationsMatchsDAO;

class Users {
	private UsersDAO $users;

	public function __construct() {
		$this->users = new UsersDAO();
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


    public function getStats() {
        $usrs=$this->users->getAll();

		$sel = new SelectionsDAO();
		$an = new AnimationsMatchsDAO();
		$tm = array();

		//Liste du nombre de match joués
		$nmatchs=$sel->getNbMatchForEachPlayer();
		//ranger le resultat
		foreach($nmatchs as $r) {
			$tm[$r->prenom]["matchs"] = $r->nb;
		}

		//Liste des collations et maillots
		$amatchs=$an->getStats();
		//ranger le resultat
		foreach($amatchs as $r) {
			$tm[$r->prenom][$r->role] = $r->nb;
		}
		//loginfo(print_r($tm,true));

		$results = array();
		foreach ($usrs as $row) {
			array_push($results,array( "id" => $row->id,
									"prenom"=>$row->prenom,
									"matchCount"=> $tm[$row->prenom]["matchs"],
									"maillotsCount" => array_key_exists("maillots",$tm[$row->prenom])?$tm[$row->prenom]["maillots"]:0,
									"collationCount" => array_key_exists("collation",$tm[$row->prenom])?$tm[$row->prenom]["collation"]:0));
		}

        return responseJson($results);
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
					$this->ajoute($nm["prenom"],$nm["nom"],$nm["equipe"],$nm["licence"],$nm["otm"],$nm["charte"]);
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
	protected function ajoute($prenom,$nom,$equipe,$licence,$otm,$charte) {
		
		return $this->users->create($prenom,$nom,$equipe,$licence,$otm,$charte);		

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