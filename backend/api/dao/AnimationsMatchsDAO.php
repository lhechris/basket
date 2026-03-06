<?php
namespace dao;

require_once __DIR__ . '/BaseDAO.php';

use dao\BaseDAO;

class AnimationsMatchsDAO extends BaseDAO {
    /**
     * Retourne tous les entraineurs
     */
    public function getCollations($match): array {
        $sql = "SELECT A.id, A.prenom, A.nom, B.role ".
               "FROM users A, animationsmatchs B ".
               "WHERE A.id=B.user AND B.match=:match AND B.role='collation' ".
               "ORDER BY A.prenom";
        $res = $this->prepareAndExecute($sql, [
			':match' => [ $match, SQLITE3_INTEGER]
        ]);
        return $this->fetchAll($res);
    }

    public function getMaillots($match): array {
        $sql = "SELECT A.id, A.prenom, A.nom, B.role ".
               "FROM users A, animationsmatchs B ".
               "WHERE A.id=B.user AND B.match=:match AND B.role='maillots' ".
               "ORDER BY prenom";
        $res = $this->prepareAndExecute($sql, [
			':match' => [ $match, SQLITE3_INTEGER]
        ]);
        return $this->fetchAll($res);
    }

    public function create(int $match,int $user,string $role): int {        
        $sql = "INSERT INTO animationsmatchs(match,user,role) VALUES(:match,:user,:role)";
        $this->prepareAndExecute($sql, [
			':user' => [ $user, SQLITE3_INTEGER],
			':match' => [ $match, SQLITE3_INTEGER],
			':role' => [ $role, SQLITE3_TEXT]
        ]);
        return $this->lastInsertRowID();
    }

    public function delete(int $match,int $user): int {
       
        $sql = "DELETE FROM animationsmatchs WHERE user=:user AND match=:match";
        $this->prepareAndExecute($sql, [
			':user' => [ $user, SQLITE3_INTEGER],
			':match' => [ $match, SQLITE3_INTEGER]
        ]);

        return $this->changes();
    }

    public function deleteMatchs(int $match): int {
       
        $sql = "DELETE FROM animationsmatchs WHERE match=:match";
        $this->prepareAndExecute($sql, [
			':match' => [ $match, SQLITE3_INTEGER]
        ]);

        return $this->changes();
    }

    public function deleteUser(int $user): int {
       
        $sql = "DELETE FROM animationsmatchs WHERE user=:user ";
        $this->prepareAndExecute($sql, [
			':user' => [ $user, SQLITE3_INTEGER]
        ]);

        return $this->changes();
    }

}