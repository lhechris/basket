<?php
namespace dao;

require_once __DIR__ . '/BaseDAO.php';

use dao\BaseDAO;

class StaffMatchsDAO extends BaseDAO {
    /**
     * Retourne tous les entraineurs
     */
    public function getEntraineurs($match): array {
        $sql = "SELECT A.id, A.prenom, A.nom, A.licence,A.role ".
               "FROM staff A, staffmatchs B ".
               "WHERE A.id=B.staff AND B.match=:match AND A.role='entraineur' ".
               "ORDER BY prenom";
        $res = $this->prepareAndExecute($sql, [
			':match' => [ $match, SQLITE3_INTEGER]
        ]);
        return $this->fetchAll($res);
    }

    public function getOtm($match): array {
        $sql = "SELECT A.id, A.prenom, A.nom, A.licence,A.role ".
               "FROM staff A, staffmatchs B ".
               "WHERE A.id=B.staff AND B.match=:match AND A.role='otm' ".
               "ORDER BY prenom";
        $res = $this->prepareAndExecute($sql, [
			':match' => [ $match, SQLITE3_INTEGER]
        ]);
        return $this->fetchAll($res);
    }

    public function create(int $match,int $staff): int {        
        $sql = "INSERT INTO staffmatchs(match,staff) VALUES(:match,:staff)";
        $this->prepareAndExecute($sql, [
			':staff' => [ $staff, SQLITE3_INTEGER],
			':match' => [ $match, SQLITE3_INTEGER]
        ]);
        return $this->lastInsertRowID();
    }

    public function delete(int $match,int $staff): int {
       
        $sql = "DELETE FROM staffmatchs WHERE staff=:staff AND match=:match";
        $this->prepareAndExecute($sql, [
			':staff' => [ $staff, SQLITE3_INTEGER],
			':match' => [ $match, SQLITE3_INTEGER]
        ]);

        return $this->changes();
    }

    public function deleteMatchs(int $match): int {
       
        $sql = "DELETE FROM staffmatchs WHERE match=:match";
        $this->prepareAndExecute($sql, [
			':match' => [ $match, SQLITE3_INTEGER]
        ]);

        return $this->changes();
    }

    public function deleteStaff(int $staff): int {
       
        $sql = "DELETE FROM staffmatchs WHERE staff=:staff ";
        $this->prepareAndExecute($sql, [
			':staff' => [ $staff, SQLITE3_INTEGER]
        ]);

        return $this->changes();
    }

}