<?php

namespace dao;

require_once 'BaseDAO.php';

use dao\BaseDAO;

class EntrainementsDAO extends BaseDAO {

    public function getAll(): array {

        $sql = "SELECT id,jour FROM entrainements ORDER BY jour";

        $res = $this->prepareAndExecute($sql,[]);
        
        return $this->fetchAll($res);
    }
	
    public function create($jour) {

		$sql = 'INSERT INTO entrainements(jour) VALUES(:jour)';
		$this->prepareAndExecute($sql,[ ':jour' => [$jour, SQLITE3_TEXT]]);        
        return $this->lastInsertRowID();
	}


    public function delete($jour) {
		
		$sql='DELETE FROM entrainements WHERE jour=:jour';
		$this->prepareAndExecute ($sql,[':jour' => [$jour, SQLITE3_TEXT]]); 
		return $this->changes();
	}

    public function deleteAll() {
		
		$sql='DELETE FROM entrainements';
		$this->prepareAndExecute ($sql,[]); 
		return $this->changes();
	}

}