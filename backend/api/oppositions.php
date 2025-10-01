<?php
require_once("utils.php");
require_once("Users.php");
require_once("Matchs.php");

class Opposition extends CommonModel{
    public Joueur $joueur;
    public MatchBasket $match;
    public $val;

    public function to_array() : array {
        $ret=[
            "user"   => $this->joueur->id,
            "prenom" => $this->joueur->prenom,
            "licence" => $this->joueur->licence,
            "val"    => $this->val
        ];
        return $ret;
    }

    public function from_array(array $data) :void {
        $this->joueur = new Joueur();
        $this->joueur->from_array($data);
        $this->val = $this->nullifnotexists($data,"val"); 
    }
}

class ListOpposition extends CommonModel {
    public array $a=[];
    public array $b=[];
    public array $autres=[];

    public function to_array() : array {
        $a = [];
        $b=[];
        $autres=[];
        foreach ($this->a as $i) {array_push($a,$i->to_array());}
        foreach ($this->b as $i) {array_push($b,$i->to_array());}
        foreach ($this->autres as $i) {array_push($autres,$i->to_array());}
        return [
            "A" => $a,
            "B" => $b,
            "Autres" => $autres
        ];
    }

}

class Oppositions extends CommonCtrl {

    public function getArray($match) {     

        //On recupère les oppositions dans la table oppositions
        $query = 'SELECT A.user,A.val, C.prenom,C.licence '.
                 'FROM oppositions A, matchs B, users C '.
                 'WHERE A.match=B.id AND A.user=C.id AND A.match=:match '.
                 'ORDER BY C.prenom';

		$opps = $this->query($query, [[':match',$match, SQLITE3_INTEGER]],'Opposition');


        //on ajoute tous les joueurs sélectionnées pour le match et qui ne sont pas dans 
        //la table oppositions
        $query = 'SELECT A.user, C.prenom,C.licence '.
                 'FROM selections A, matchs B, users C '.
                 'WHERE A.match=B.id AND A.user=C.id AND A.match=:match AND A.val = 1 '.
                 'ORDER BY C.prenom';

		$selectionnes = $this->query($query, [[':match',$match, SQLITE3_INTEGER]],'Opposition');

        $ret = new ListOpposition();

        foreach ($selectionnes as &$sel ) {
            foreach ($opps as $opp) {
                if ($opp->joueur->id == $sel->joueur->id) {
                    $sel->val = $opp->val;
                }
            }
            if ($sel->val == 'A') { array_push($ret->a, $sel); }
            else if ($sel->val == 'B') { array_push($ret->b, $sel); }
            else  { array_push($ret->autres, $sel); }
        }
        return array($ret);

    }

    public function get($match) {
        responseJson($this->to_array($this->getArray($match)));
    }

	/**
	 * Comme son nom l'indique retourne true si l'enregistrement existe
	 * db doit etre instancié
	 */
	protected function exists($match,$usr) {
		$query = 'SELECT count(*) FROM oppositions WHERE match=:match AND user=:user';
		$result = $this->query(
                    $query,
                    [[':match', $match, SQLITE3_INTEGER],[':user', $usr, SQLITE3_INTEGER]],
                    "CommonModelCount"
                );
        return ($result[0]->count >=1);
	}


	/** Cree l'enregistrement s'il n'existe pas.
	 * db doit etre instancié
	*/
	protected function createIfNotExists($match,$usr,$val) {

		if ($this->exists($match,$usr)==false) {
			$query = 'INSERT INTO oppositions(match,user,val) VALUES (:match,:user,:val)';
			$this->query($query, 
                         [[':match', $match, SQLITE3_INTEGER],
                          [':user', $usr, SQLITE3_INTEGER],
                          [':val', $val, SQLITE3_TEXT]]);
		    return true;
		} else {
		    return false;
        }
	}


    public function set($json) {
        $this->update($json);
    
    }

	/**
	 * Verifie s'il y a déjà un enregistrement sinon le cree
	 * Ensuite met à jour la valeur de la presence
	 * Attend en entree un json : { match : id, "user" : id, "val": int}
	 */
	protected function update($json) {
	
        if ( is_int($json['usr']) && is_int($json['match']) && is_string($json['opposition'])) {
            loginfo("jsuistoujourslà");
			if ($this->createIfNotExists($json['match'],$json['usr'],$json['opposition']) == false ) {
                
                loginfo("jveuxupdate");
                $query='UPDATE oppositions SET val=:val WHERE match=:match AND user=:user';
            
                $this->query($query,
                            [[':match', $json['match'], SQLITE3_INTEGER],
                            [':user', $json['usr'], SQLITE3_INTEGER],
                            [':val', $json['opposition'], SQLITE3_TEXT]]);
            }
		} else {
			responseError("Bad input");
		}
        loginfo("je sors de lupdate");
	}

}