<?php
namespace dao;

require_once __DIR__ . '/BaseDAO.php';

use dao\BaseDAO;

class StaffDAO extends BaseDAO {
    /**
     * Retourne tous les entraineurs
     */
    public function getAll(): array {
        $sql = "SELECT id, prenom, nom, licence,role FROM staff ORDER BY prenom";
        $res = $this->prepareAndExecute($sql);
        return $this->fetchAll($res);
    }

    public function getById(int $id): ?array {
        $sql = "SELECT * FROM staff WHERE id=:id";
        $res = $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);
        $rows = $this->fetchAll($res);
        return $rows[0] ?? null;
    }

    public function create(string $prenom,string $nom,string $licence,string $role): int {
        $sql = "INSERT INTO staff(prenom,nom,licence,role) VALUES(:prenom,:nom,:licence,:role)";
        $this->prepareAndExecute($sql, [
			':prenom' => [ $prenom, SQLITE3_TEXT],
			':nom' => [ $nom, SQLITE3_TEXT],
			':licence' => [ $licence, SQLITE3_TEXT],
			':role' => [ $role, SQLITE3_TEXT]
        ]);
        return $this->lastInsertRowID();
    }


    public function update(int $id, string $prenom,string $nom,string $licence,string $role): int {
        $sql = "UPDATE staff SET prenom=:prenom, nom=:nom, licence=:licence, role=:role WHERE id=:id";
        $this->prepareAndExecute($sql, [
			':id' => [$id, SQLITE3_INTEGER],
			':prenom' => [ $prenom, SQLITE3_TEXT],
			':nom' => [ $nom, SQLITE3_TEXT],
			':licence' => [ $licence, SQLITE3_TEXT],
			':role' => [ $role, SQLITE3_TEXT]
        ]);

        return $this->changes();
    }

    public function delete(int $id): int {
        $sql = "DELETE FROM staff WHERE id=:id";
        $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);
        
        $sql = "DELETE FROM staffmatchs WHERE staff=:id";
        $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);

        return $this->changes();
    }
}