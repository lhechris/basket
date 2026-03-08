<?php 

namespace Basket;

require_once("utils.php");

use dao\MatchsDAO;
use dao\SelectionsDAO;
use dao\MatchInfosDAO;
use dao\UsersDAO;
use dao\StaffMatchsDAO;
use dao\StaffDAO;
use dao\AnimationsMatchsDAO;

class Matchs {

	private MatchInfosDAO $matchinfos;
	private MatchsDAO $matchs;
	private SelectionsDAO $selections;
	private StaffMatchsDAO $staffmatchs;
	private StaffDAO $staff;
	private AnimationsMatchsDAO $animationsmatchs;

	public function __construct() {
		$this->matchinfos =  new MatchInfosDAO();
		$this->matchs = new MatchsDAO();
		$this->selections = new SelectionsDAO();	
		$this->staffmatchs = new StaffMatchsDAO();
		$this->staff = new StaffDAO();
		$this->animationsmatchs = new AnimationsMatchsDAO();
	}

	public function getArray($id=null) {
		if ($id === null) {
			$results = $this->matchs->getAll();
		
		} else {
			$results = $this->matchs->getById($id);
			$selectionnes = $this->selections->getByMatch($id);
			$results->oppositions = $this->getOppositions($id,$selectionnes);	
			$results->entraineurs = $this->getEntraineursArray($id);
			$results->otm = $this->getOtmArray($id);
			$results->maillots = $this->getMaillotsArray($id,$selectionnes);
			$results->collation = $this->getCollationArray($id,$selectionnes);	
		}
		return $results;
	}

	private function getEntraineursArray(int $match) {	
		$results = $this->staff->getEntraineurs();
		$active = $this->staffmatchs->getEntraineurs($match);
		
		foreach ($results as &$row) {
			$row->selected = false;
			foreach($active as $a) {
				if ($row->id == $a->id) {
					$row->selected = true;
				}
			}
		}
		return $results;
	}

	private function getEntraineurOnlySelectedArray(int $match) {	
		$results = $this->staffmatchs->getEntraineurs($match);
		return $results;
	}

	private function getOtmArray(int $match) {	
		$results = $this->staff->getOtm();
		$active = $this->staffmatchs->getOtm($match);
		
		foreach ($results as &$row) {
			$row->selected = false;
			foreach($active as $a) {
				if ($row->id == $a->id) {
					$row->selected = true;
				}
			}
		}
		return $results;
	}

	private function getOtmOnlySelectedArray(int $match) {	
		$results = $this->staffmatchs->getOtm($match);
		return $results;
	}

	private function getCollationArray(int $match,$selectionnes) {	
		$selected = $selectionnes;
		$active = $this->animationsmatchs->getCollations($match);
		$results=array();
		foreach ($selected as $row) {
			$r=new \StdClass();
			$r->id = $row->user;
			$r->prenom = $row->prenom;
			$r->selected = false;
			
			foreach($active as $a) {
				if ($row->user == $a->id) {
					$r->selected = true;
				}
			}
			array_push($results,$r);
		}
		return $results;
	}

	private function getCollationsOnlySelectedArray(int $match) {	
		$results = $this->animationsmatchs->getCollations($match);
		return $results;
	}


	private function getMaillotsArray(int $match,$selectionnes) {	
		$selected = $selectionnes;
		$active = $this->animationsmatchs->getMaillots($match);
		$results=array();
		foreach ($selected as $row) {
			$r=new \StdClass();
			$r->id = $row->user;
			$r->prenom = $row->prenom;
			$r->selected = false;
			foreach($active as $a) {
				if ($row->user == $a->id) {
					$r->selected = true;
				}
			}
			array_push($results,$r);
		}
		return $results;
	}

	private function getMaillotsOnlySelectedArray(int $match) {	
		$results = $this->animationsmatchs->getMaillots($match);
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
		
			$selectionnes = $this->selections->getByMatch($m->id);
			$m->oppositions = $this->getOppositions($m->id,$selectionnes);			
			$m->entraineurs = $this->getEntraineursArray($m->id);
			$m->otm = $this->getOtmArray($m->id);
			$m->maillots = $this->getMaillotsArray($m->id,$selectionnes);
			$m->collation = $this->getCollationArray($m->id,$selectionnes);

			$notfound=true;
			foreach($results as &$res) {
				if ($res->jour == $m->jour) {
					array_push($res->matchs,$m);
					$notfound=false;
					break;
				}
			}
			if ($notfound) {
				$nm = new \stdClass();
				$nm->jour = $m->jour;
				$nm->matchs = [$m];				
				array_push($results,$nm);
			}		
		}
		//loginfo(print_r($results,true));

		//pour chaque jour prepare la convocation
		foreach ($results as &$j) {
			
			$j->convocation = $this->makeConvocation($j);
		}

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
			$m->otm = $this->getOtmOnlySelectedArray($m->id);			
			
			$m->entraineurs = $this->getEntraineurOnlySelectedArray($m->id);
			$m->maillots = $this->getMaillotsOnlySelectedArray($m->id);
			$m->collation = $this->getCollationsOnlySelectedArray($m->id);			

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
				$nm = new \stdClass();
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


    private function getOppositions($match,$selectionnes) {     

        //On recupère les oppositions dans la table matchinfos
        $opps = $this->matchinfos->getByMatch($match);


        //on ajoute tous les joueurs sélectionnées pour le match et qui ne sont pas dans 
        //la table oppositions
        

        $ret = new \stdClass;
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

	private function makeconvocation($jour) {

		$txt="";
		$usersObj = new UsersDAO();
		$users = $usersObj->getAll();
		
		$selected = array();

		foreach ($jour->matchs as $match) {

			if ($txt!="") { $txt.="\n";}
			
			$txt .= "Equipe ".$match->equipe.": ";
			$l=false;
			foreach ($match->oppositions->A as $j) {
				if ($l) {$txt.=", ";}
				$l=true;
				array_push($selected,$j->prenom);
				$txt .= $j->prenom;
			}
			foreach ($match->oppositions->B as $j) {
				if ($l) {$txt.=", ";}
				$l=true;
				array_push($selected,$j->prenom);
				$txt .= $j->prenom;
			}
			foreach ($match->oppositions->Autres as $j) {
				if ($l) {$txt.=", ";}
				$l=true;
				array_push($selected,$j->prenom);
				$txt .= $j->prenom;
			}	

		}
		//recherche les non selectionnes
		$txt .= "\nAu repos: ";	
		$l=false;
		foreach ($users as $u) {
			if (! in_array($u->prenom,$selected) ) {
				if ($l) {$txt.=", ";}
				$l=true;				
				$txt.= $u->prenom;
			}
		}



		return $txt;			
	}

	/**
	 * Supprime tous les otm du match 
	 * et cree les otm pour ce match
	 */
	private function updateOtm($id,$entraineurs,$otm,$maillots,$collations) {
		$this->staffmatchs->deleteMatchs($id);
		foreach($otm as $o) {			
			if ($o["selected"]==true) {
				$this->staffmatchs->create($id,$o["id"]);
			}
		}
		foreach($entraineurs as $e) {			
			if ($e["selected"]==true) {
				$this->staffmatchs->create($id,$e["id"]);
			}
		}		$this->animationsmatchs->deleteMatchs($id);		
		foreach($maillots as $o) {	
			if ($o["selected"]==true) {
				$this->animationsmatchs->create($id,$o["id"],'maillots');
			}
		}
		foreach($collations as $o) {			
			if ($o["selected"]==true) {
				$this->animationsmatchs->create($id,$o["id"],'collation');
			}
		}
	}

	/**
	 * Execute la requete UPDATE sur la table match
	 */
	protected function update($id,$numero,$equipe,$titre,$score,$jour,$entraineurs,$collation,$otm,$maillots,$adresse,$horaire,$rendezvous) {
		$this->matchs->update($id,$numero,$equipe,$titre,$score,$jour,$adresse,$horaire,$rendezvous);
		$this->updateOtm($id,$entraineurs,$otm,$maillots,$collation);
	}

	/**
	 * Execute la requete INSERT INTO dans la table match
	 */
	protected function ajoute($numero,$equipe,$titre,$score,$jour,$entraineurs,$collation,$otm,$maillots,$adresse,$horaire,$rendezvous) {
		$id = $this->matchs->create($numero,$equipe,$titre,$score,$jour,$adresse,$horaire,$rendezvous);				
		$this->updateOtm($id,$entraineurs,$otm,$maillots,$collation);
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
			array_key_exists("numero",$tab) && 
			array_key_exists("titre",$tab) && 
			array_key_exists("jour",$tab) && 
			array_key_exists("score",$tab) && 
			array_key_exists("equipe",$tab)  && 
			array_key_exists("entraineurs",$tab)  &&
			array_key_exists("collation",$tab) && 
			array_key_exists("otm",$tab) &&
			array_key_exists("maillots",$tab)
			) {		

			if (array_key_exists("id",$tab)) {
				if (array_key_exists("todelete",$tab)) {
					$this->supprime($tab["id"]);

				} else {
					$this->update($tab["id"],
								  $tab["numero"],
								  $tab["equipe"],
								  $tab["titre"],
								  $tab["score"],
								  $tab["jour"],
								  $tab['entraineurs'],
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
				$this->ajoute($tab["numero"],
							  $tab["equipe"],
							  $tab["titre"],
							  $tab["score"],
							  $tab["jour"],
							  $tab["entraineurs"],
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