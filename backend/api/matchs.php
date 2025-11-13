<?php 

require_once("utils.php");

require_once("dao/MatchsDAO.php");
require_once("dao/SelectionsDAO.php");
require_once("dao/MatchInfosDAO.php");

use dao\MatchsDAO;
use dao\SelectionsDAO;
use dao\MatchInfosDAO;

class Matchs {

	private $matchinfos;
	private $matchs;
	private $selections;

	public function __construct($donnees) {
		$this->matchinfos =  new MatchInfosDAO($donnees);
		$this->matchs = new MatchsDAO($donnees);
		$this->selections = new SelectionsDAO($donnees);
	}

	public function getArray($id=null) {
		if ($id === null) {
			$results = $this->matchs->getAll();
		
		} else {
			$results = $this->matchs->getById($id);
			$results->oppositions = $this->getOppositions($id);		
		}
		return $results;
	}

	/**
	 * Retourne une liste de jour avec les matchs et les oppositions
	 * pour chaque match
	 */
	public function getAvecOppositionsArray() {
		$allmatchs = $this->matchs->getAll();

		$results=array();

		foreach($allmatchs as &$m) {			
		
			$m->oppositions = $this->getOppositions($m->id);

			$notfound=true;
			foreach($results as &$res) {
				if ($res->jour == $m->jour) {
					array_push($res->matchs,$m);
					$notfound=false;
					break;
				}
			}
			if ($notfound) {
				$nm = new stdClass();
				$nm->jour = $m->jour;
				$nm->matchs = [$m];				
				array_push($results,$nm);
			}		
		}
		//loginfo(print_r($results,true));

		return $results;
	}


	/**
	 * Retourne une liste de jour avec les matchs et les joueurs
	 * sélectionnés pour chaque match
	 */
	public function getAvecSelectionsArray() {
		$allmatchs = $this->matchs->getAll();

		$results=array();

		//On regroupe les matchs par journée
		foreach($allmatchs as &$m) {			
			//On ajoute les joueurs sélectionné pour ce match
			$m->selections = $this->selections->getPlayersByMatchId($m->id);						
			
			//Si la journée existe déjà, on rajoute ce match
			$notfound=true;
			foreach($results as &$res) {
				if ($res->jour == $m->jour) {
					array_push($res->matchs,$m);
					$notfound=false;
					break;
				}
			}
			//Sinon on crée une nouvelle journée avec ce match
			if ($notfound) {
				$nm = new stdClass();
				$nm->jour = $m->jour;
				$nm->matchs = [$m];				
				array_push($results,$nm);				
			}
			
		}
		//loginfo(print_r($results,true));

		return $results;
	}
	
	public function get($id=null) {		
		$resp = $this->getArray($id);

		if ($id==null) {
			return responseJson($resp);
		} else {
			return responseJson($resp);
		}
	}

	public function getAvecOppositions() {		
		$resp = $this->getAvecOppositionsArray();
		return responseJson($resp);
	}




	public function getAvecSelections() {				
		$resp = $this->getAvecSelectionsArray();
		return responseJson($resp);
	}


    private function getOppositions($match) {     

        //On recupère les oppositions dans la table matchinfos
        $opps = $this->matchinfos->getByMatch($match);


        //on ajoute tous les joueurs sélectionnées pour le match et qui ne sont pas dans 
        //la table oppositions
        $selectionnes = $this->selections->getByMatch($match);

        $ret = new stdClass;
        $ret->A = [];
        $ret->B = [];
        $ret->Autres = [];

        foreach ($selectionnes as &$sel ) {
            $sel->opposition = null;
            $sel->numero = null;
            $sel->commentaire = null;

            foreach ($opps as $opp) {
                if ($opp->user == $sel->user) {
                    $sel->opposition = $opp->opposition;
                    $sel->numero = $opp->numero;
                    $sel->commentaire = $opp->commentaire;
                }
            }
            if ($sel->opposition == 'A') { array_push($ret->A, $sel); }
            else if ($sel->opposition == 'B') { array_push($ret->B, $sel); }
            else  { array_push($ret->Autres, $sel); }
        }
        usort($ret->A, "cb_tri");
        usort($ret->B, "cb_tri");
        return $ret;

    }


	/**
	 * Execute la requete UPDATE sur la table match
	 */
	protected function update($id,$equipe,$titre,$score,$jour,$collation,$otm,$maillots,$adresse,$horaire,$rendezvous) {

		$this->matchs->update($id,$equipe,$titre,$score,$jour,$collation,$otm,$maillots,$adresse,$horaire,$rendezvous);
	}

	/**
	 * Execute la requete INSERT INTO dans la table match
	 */
	protected function ajoute($equipe,$titre,$score,$jour,$collation,$otm,$maillots,$adresse,$horaire,$rendezvous) {

		$this->matchs->create($equipe,$titre,$score,$jour,$collation,$otm,$maillots,$adresse,$horaire,$rendezvous);				
	}

	/**
	 * Execute la requete DELETE dans la table match
	 * Supprime aussi les entrees dans disponibilites et selections
	 */
	protected function supprime($id) {
		
		$this->matchs->delete($id);
	}


	private function setOne($tab) {
		if (is_array($tab) && 
			array_key_exists("titre",$tab) && 
			array_key_exists("jour",$tab) && 
			array_key_exists("score",$tab) && 
			array_key_exists("equipe",$tab)  && 
			array_key_exists("collation",$tab) && 
			array_key_exists("otm",$tab) &&
			array_key_exists("maillots",$tab)
			) {
		

			if (array_key_exists("id",$tab)) {
				if (array_key_exists("todelete",$tab)) {
					$this->supprime($tab["id"]);

				} else {
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