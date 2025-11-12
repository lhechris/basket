<?php
namespace dao;

require_once 'BaseDAO.php';

use dao\BaseDAO;


class SelectionsDAO extends BaseDAO {
    public function exists(int $match, int $user): bool {
        $sql = "SELECT 1 FROM selections WHERE match=:match AND user=:user LIMIT 1";
        $res = $this->prepareAndExecute($sql, [
            ':match' => [$match, SQLITE3_INTEGER],
            ':user' => [$user, SQLITE3_INTEGER]
        ]);
        $row = $res->fetchArray(SQLITE3_NUM);
        return $row !== false;
    }

    public function create(int $match, int $user, int $val = 0): int {
        $sql = "INSERT INTO selections(match,user,val) VALUES(:match,:user,:val)";
        $this->prepareAndExecute($sql, [
            ':match' => [$match, SQLITE3_INTEGER],
            ':user' => [$user, SQLITE3_INTEGER],
            ':val' => [$val, SQLITE3_INTEGER]
        ]);
        return $this->db->lastInsertRowID();
    }

    /*public function update(int $match, int $user, int $val): int {
        $sql = "UPDATE selections SET val=:val WHERE match=:match AND user=:user";
        $this->prepareAndExecute($sql, [
            ':val' => [$val, SQLITE3_INTEGER],
            ':match' => [$match, SQLITE3_INTEGER],
            ':user' => [$user, SQLITE3_INTEGER]
        ]);
        return $this->db->changes();
    }*/

    //Supprime le joueur de la journée
    public function deleteByDay(string $jour, int $user): int {
        $sql = "DELETE FROM selections WHERE match IN (SELECT id FROM matchs B WHERE B.jour=:jour ) AND user=:user";
        $this->prepareAndExecute($sql, [
            ':jour' => [$jour, SQLITE3_TEXT],
            ':user' => [$user, SQLITE3_INTEGER]
        ]);
        return $this->db->changes();
    }    

    public function getByMatch(int $match): array {
        $sql = "SELECT s.*, u.prenom FROM selections s LEFT JOIN users u ON u.id=s.user WHERE s.match=:match ORDER BY u.prenom";
        $res = $this->prepareAndExecute($sql, [':match' => [$match, SQLITE3_INTEGER]]);
        return $this->fetchAll($res);
    }
}