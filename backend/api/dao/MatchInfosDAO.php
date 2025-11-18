<?php

namespace dao;

require_once 'BaseDAO.php';

use dao\BaseDAO;

class MatchInfosDAO extends BaseDAO {

    public function getByMatch($matchid): array {

        $sql = 'SELECT A.user,A.opposition,A.numero, A.commentaire, C.prenom,C.licence, C.nom '.
                 'FROM matchinfos A, matchs B, users C '.
                 'WHERE A.match=B.id AND A.user=C.id AND A.match=:match '.
                 'ORDER BY C.prenom';

        $res = $this->prepareAndExecute($sql,[':match'=> [$matchid, SQLITE3_INTEGER]]);
        
        return $this->fetchAll($res);
    }

	public function exists($matchid,$userid) {
		$sql = 'SELECT count(*) FROM matchinfos WHERE match=:match AND user=:user';
		$result = $this->prepareAndExecute(
                    $sql,
                    [':match'=> [$matchid, SQLITE3_INTEGER],
					 ':user' => [$userid, SQLITE3_INTEGER]] );
        
        $row = $result->fetchArray(SQLITE3_NUM);
        return $row[0] > 0 ;
	}


    public function update($matchid,$userid,$opposition,$numero,$commentaire) {

        $sql='UPDATE matchinfos '.
			 'SET opposition=:opposition, numero=:numero, commentaire=:commentaire '.
			 'WHERE match=:match AND user=:user';
            

		$this->prepareAndExecute ($sql,
					[':match' => [$matchid, SQLITE3_INTEGER],
					':user' => [$userid, SQLITE3_INTEGER],
					':opposition' => [$opposition, SQLITE3_TEXT],
					':numero' => [$numero, SQLITE3_INTEGER],
					':commentaire' => [$commentaire, SQLITE3_TEXT]
				]);

        return $this->changes();
    }
	
    public function create($matchid,$userid,$opposition,$numero,$commentaire) {
		
		$sql = 'INSERT INTO matchinfos(match,user,opposition,numero,commentaire) '
				.'VALUES (:match,:user,:opposition,:numero,:commentaire)';

		$this->prepareAndExecute($sql,[ 
						':match' => [$matchid, SQLITE3_INTEGER],
						':user' => [$userid, SQLITE3_INTEGER],
						':opposition' => [$opposition, SQLITE3_TEXT],
						':numero' => [$numero, SQLITE3_INTEGER],
						':commentaire' => [$commentaire, SQLITE3_TEXT]
					]);

        
        return $this->lastInsertRowID();
	}


    public function delete($matchid,$userid) {
		
		$sql='DELETE FROM matchinfos match WHERE match=:match AND user=:user';
		$this->prepareAndExecute ($sql,[':match' => [$matchid, SQLITE3_INTEGER],
									    ':user' => [$userid, SQLITE3_INTEGER]]); 

		return $this->changes();
	}


}