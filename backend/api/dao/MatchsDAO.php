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

    // Ajoutez méthodes spécifiques: getAvecSelections, getParJour, create, update, etc.
}