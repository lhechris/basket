<?php
require_once("utils.php");
require_once("users.php");
require_once("matchs.php");

require_once("dao/MatchInfosDAO.php");
require_once("dao/SelectionsDAO.php");

use dao\MatchInfosDAO;
use dao\SelectionsDAO;

// Callback de usort pour trier les opposition par numero
function cb_tri($a,$b) {
    if ($a->numero === null) return 1;
    if ($b->numero === null) return -1;
    $ret=$a->numero - $b->numero;
    return $a->numero - $b->numero;
}

class MatchInfos  {

    private $matchinfos;
    private $selections;

    public function __construct() {
        $this->matchinfos = new MatchInfosDAO();
        $this->selections = new SelectionsDAO();
    }

    public function set($json) {
        $this->update($json);
    
    }

	/**
	 * Verifie s'il y a déjà un enregistrement sinon le cree
	 * Ensuite met à jour la valeur de la presence
	 * Attend en entree un json : { match : id, "user" : id, "opposition": string, "numero" : int, "commentaire" : string}
	 */
	protected function update($json) {
	
        if ( is_int($json['usr']) && is_int($json['match']) && is_string($json['opposition'])) {
            $numero=null;
            if (array_key_exists("numero",$json)) { $numero=$json['numero'];}
            $commentaire=null;
            if (array_key_exists("commentaire",$json)) { $commentaire=$json['commentaire'];}

			if ($this->matchinfos->exists($json['match'],$json['usr'])) {
                $this->matchinfos->update($json['match'],$json['usr'],$json['opposition'],$numero,$commentaire);
            } else {
                $this->matchinfos->create($json['match'],$json['usr'],$json['opposition'],$numero,$commentaire);
            }

		} else {
			responseError("Bad input");
		}
	}

}