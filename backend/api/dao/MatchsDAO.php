<?php

namespace dao;

require_once 'BaseDAO.php';

use dao\BaseDAO;

class MatchsDAO extends BaseDAO {
    public function getAll(): array {
        $sql = "SELECT * FROM matchs ORDER BY jour, equipe";
        $res = $this->prepareAndExecute($sql);
        
        return $this->fetchAll($res);
    }

    public function getById(int $id) {
        $sql = "SELECT * FROM matchs WHERE id=:id";
        $res = $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);
        $rows = $this->fetchAll($res);
        return $rows[0] ?? null;
    }

    public function update($id,$numero,$equipe,$titre,$score,$jour,$collation,$otm,$maillots,$adresse,$horaire,$rendezvous) {
        $sql='UPDATE matchs '.
			  'SET numero=:numero, equipe=:equipe, titre=:titre, score=:score, jour=:jour, collation=:collation, otm=:otm, maillots=:maillots,adresse=:adresse, horaire=:horaire,rendezvous=:rendezvous  '.
			  'WHERE id=:id';
		
		$this->prepareAndExecute ($sql,
			[':id'=> [$id, SQLITE3_INTEGER],
			':numero'=> [$numero, SQLITE3_TEXT],
			':equipe'=> [$equipe, SQLITE3_INTEGER],
			':titre'=> [$titre, SQLITE3_TEXT],
			':score'=> [$score, SQLITE3_TEXT],
			':jour'=> [$jour, SQLITE3_TEXT],
			':collation'=> [$collation, SQLITE3_TEXT],
			':otm'=> [$otm, SQLITE3_TEXT],
			':maillots'=> [$maillots, SQLITE3_TEXT],
			':adresse'=> [$adresse, SQLITE3_TEXT],
			':horaire'=> [$horaire, SQLITE3_TEXT],
			':rendezvous'=> [$rendezvous, SQLITE3_TEXT]
		]);

        return $this->changes();
    }
	
    public function create($numero,$equipe,$titre,$score,$jour,$collation,$otm,$maillots,$adresse,$horaire,$rendezvous) {
		
		$sql = 'INSERT INTO matchs(titre,score,jour,numero,equipe,collation,otm,maillots,adresse,horaire,rendezvous) '.
								   'VALUES(:titre,:score,:jour,:numero,:equipe,:collation,:otm,:maillots,:adresse,:horaire,:rendezvous)';
		$this->prepareAndExecute ($sql,
			[':numero'=> [$numero, SQLITE3_TEXT],
			':equipe'=> [$equipe, SQLITE3_INTEGER],
			':titre'=> [$titre, SQLITE3_TEXT],
			':score'=> [$score, SQLITE3_TEXT],
			':jour'=> [$jour, SQLITE3_TEXT],
			':collation'=> [$collation, SQLITE3_TEXT],
			':otm'=> [$otm, SQLITE3_TEXT],
			':maillots'=> [$maillots, SQLITE3_TEXT],
			':adresse'=> [$adresse, SQLITE3_TEXT],
			':horaire'=> [$horaire, SQLITE3_TEXT],
			':rendezvous'=> [$rendezvous, SQLITE3_TEXT]
		]);		
        
        return $this->lastInsertRowID();
	}


    public function delete($id) {
		
		$sql='DELETE FROM matchs WHERE id=:id';
		$this->prepareAndExecute ($sql,[':id' => [$id, SQLITE3_INTEGER]]); 

		//TODO supprime dans la table disponibilite uniquement s'il n'y a plus de match ce jour ci

		$sql='DELETE FROM selections WHERE match=:id';
		$this->prepareAndExecute ($sql,[':id' => [$id, SQLITE3_INTEGER]]);

		$sql='DELETE FROM matchinfos WHERE match=:id';
		$this->prepareAndExecute ($sql,[':id' => [$id, SQLITE3_INTEGER]]);

        $sql = "DELETE FROM staffmatchs WHERE match=:id";
        $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);

	}


}