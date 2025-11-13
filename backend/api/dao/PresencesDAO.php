<?php

namespace dao;

require_once 'BaseDAO.php';

use dao\BaseDAO;

class PresencesDAO extends BaseDAO {

    /**
     * Retourne tous les enregistrements de la table 
     */
    public function getAll(): array {

        $sql = "SELECT A.entrainement,A.user,A.val FROM presences A, users B WHERE A.user=B.id ORDER BY A.entrainement,B.prenom";

        $res = $this->prepareAndExecute($sql,[]);
        
        return $this->fetchAll($res);
    }

    /**
     * Retourne la présence de l'enregistrement 
     */
	public function exists($entrainementid,$userid) {
		$sql = 'SELECT count(*) FROM presences WHERE entrainement=:entrainement AND user=:user';
		$result = $this->prepareAndExecute(
                    $sql,
                    [':entrainement'=> [$entrainementid, SQLITE3_INTEGER],
					 ':user' => [$userid, SQLITE3_INTEGER]] );
        
        $row = $result->fetchArray(SQLITE3_NUM);
        return $row[0] > 0 ;
	}


    public function update($entrainementid,$userid,$val) {

        $sql='UPDATE presences SET val=:val WHERE entrainement=:entrainement AND user=:user';            

		$this->prepareAndExecute ($sql,
                    [':entrainement'=> [$entrainementid, SQLITE3_INTEGER],
					 ':user' => [$userid, SQLITE3_INTEGER],
                     ':val' => [$val, SQLITE3_INTEGER]] );

        return $this->db->changes();
    }
	
    public function create($entrainementid,$userid,$val) {

		$sql = 'INSERT INTO presences(entrainement,user,val) VALUES (:entrainement,:user,:val)';
		$this->prepareAndExecute($sql,
                    [':entrainement'=> [$entrainementid, SQLITE3_INTEGER],
					 ':user' => [$userid, SQLITE3_INTEGER],
                     ':val' => [$val, SQLITE3_INTEGER]] );

        return $this->db->lastInsertRowID();
	}


    public function delete($entrainementid,$userid) {
		
		$sql='DELETE FROM presences WHERE entrainement=:entrainement AND user=:user';
		$this->prepareAndExecute ($sql,
                    [':entrainement'=> [$entrainementid, SQLITE3_INTEGER],
					 ':user' => [$userid, SQLITE3_INTEGER]] );

		return $this->db->changes();
	}


}