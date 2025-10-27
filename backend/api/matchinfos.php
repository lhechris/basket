<?php
require_once("utils.php");
require_once("users.php");
require_once("matchs.php");

class MatchInfo extends CommonModel{
    public Joueur $joueur;
    public $opposition;
    public $numero;
    public $commentaire;

    public function to_array() : array {
        $ret=[
            "user"   => $this->joueur->id,
            "prenom" => $this->joueur->prenom,
            "nom" => $this->joueur->nom,
            "licence" => $this->joueur->licence,
            "numero" => $this->numero,
            "opposition" => $this->opposition,
            "commentaire" => $this->commentaire          
        ];
        return $ret;
    }

    public function from_array(array $data) :void {
        $this->joueur = new Joueur();
        $this->joueur->from_array($data);
        $this->opposition = $this->nullifnotexists($data,"opposition"); 
        $this->numero = $this->nullifnotexists($data,"numero"); 
        $this->commentaire = $this->nullifnotexists($data,"commentaire"); 
    }
}

class ListMatchInfo extends CommonModel {
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

class MatchInfos extends CommonCtrl {

    public function getArray($match) {     

        //On recupère les oppositions dans la table matchinfos
        $query = 'SELECT A.user,A.opposition,A.numero, A.commentaire, C.prenom,C.licence, C.nom '.
                 'FROM matchinfos A, matchs B, users C '.
                 'WHERE A.match=B.id AND A.user=C.id AND A.match=:match '.
                 'ORDER BY C.prenom';

		$opps = $this->query($query, [[':match',$match, SQLITE3_INTEGER]],'MatchInfo');
        //loginfo(print_r($opps,true));

        //on ajoute tous les joueurs sélectionnées pour le match et qui ne sont pas dans 
        //la table oppositions
        $query = 'SELECT A.user, C.prenom,C.licence, C.nom '.
                 'FROM selections A, matchs B, users C '.
                 'WHERE A.match=B.id AND A.user=C.id AND A.match=:match AND A.val = 1 '.
                 'ORDER BY C.prenom';

		$selectionnes = $this->query($query, [[':match',$match, SQLITE3_INTEGER]],'MatchInfo');
        //loginfo(print_r($selectionnes,true));

        $ret = new ListMatchInfo();

        foreach ($selectionnes as &$sel ) {
            foreach ($opps as $opp) {
                if ($opp->joueur->id == $sel->joueur->id) {
                    $sel->opposition = $opp->opposition;
                    $sel->numero = $opp->numero;
                    $sel->commentaire = $opp->commentaire;
                }
            }
            if ($sel->opposition == 'A') { array_push($ret->a, $sel); }
            else if ($sel->opposition == 'B') { array_push($ret->b, $sel); }
            else  { array_push($ret->autres, $sel); }
        }
        usort($ret->a, function($a, $b) {
            if ($a->numero === null) return 1;
            if ($b->numero === null) return -1;
            return $a->numero - $b->numero;
        });

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
		$query = 'SELECT count(*) FROM matchinfos WHERE match=:match AND user=:user';
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
	protected function createIfNotExists($match,$usr,$opposition,$numero,$commentaire) {

		if ($this->exists($match,$usr)==false) {
			$query = 'INSERT INTO matchinfos(match,user,opposition,numero,commentaire) '
                    .'VALUES (:match,:user,:opposition,:numero,:commentaire)';
			$this->query($query, 
                         [[':match', $match, SQLITE3_INTEGER],
                          [':user', $usr, SQLITE3_INTEGER],
                          [':opposition', $opposition, SQLITE3_TEXT],
                          [':numero', $numero, SQLITE3_INTEGER],
                          [':commentaire', $commentaire, SQLITE3_TEXT],
                        ]);
		    
           
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
	 * Attend en entree un json : { match : id, "user" : id, "opposition": int}
	 */
	protected function update($json) {
	
        if ( is_int($json['usr']) && is_int($json['match']) && is_string($json['opposition'])) {
            $numero=null;
            if (array_key_exists("numero",$json)) { $numero=$json['numero'];}
            $commentaire=null;
            if (array_key_exists("commentaire",$json)) { $commentaire=$json['commentaire'];}

			if ($this->createIfNotExists($json['match'],$json['usr'],$json['opposition'],$numero,$commentaire) == false ) {
                
                $query='UPDATE matchinfos '.
                       'SET opposition=:opposition, numero=:numero, commentaire=:commentaire '.
                       'WHERE match=:match AND user=:user';
            
                $this->query($query,
                            [[':match', $json['match'], SQLITE3_INTEGER],
                            [':user', $json['usr'], SQLITE3_INTEGER],
                            [':opposition', $json['opposition'], SQLITE3_TEXT],
                            [':numero', $numero, SQLITE3_INTEGER],
                            [':commentaire', $commentaire, SQLITE3_TEXT]
                        ]);
            }
		} else {
			responseError("Bad input");
		}
	}

}