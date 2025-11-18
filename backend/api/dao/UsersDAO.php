<?php
namespace dao;

require_once __DIR__ . '/BaseDAO.php';

use dao\BaseDAO;

class UsersDAO extends BaseDAO {
    /**
     * Retourne tous les joueurs
     */
    public function getAll(): array {
        $sql = "SELECT id, prenom, nom, equipe, licence, otm, charte FROM users ORDER BY prenom";
        $res = $this->prepareAndExecute($sql);
        return $this->fetchAll($res);
    }

    /**
     * Retourne la liste des joueurs dans une équipe
     */
    public function getPlayersByTeam($equipe): array {
        $sql = "SELECT id,prenom,equipe FROM users WHERE equipe=:equipe ORDER BY prenom";
        $res = $this->prepareAndExecute($sql, [':equipe' => [$equipe, SQLITE3_INTEGER]]);
        return $this->fetchAll($res);
    }


    public function getById(int $id): ?array {
        $sql = "SELECT * FROM users WHERE id=:id";
        $res = $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);
        $rows = $this->fetchAll($res);
        return $rows[0] ?? null;
    }

    public function create(string $prenom,string $nom,int $equipe,string $licence,int $otm,int $charte): int {
        $sql = "INSERT INTO users(prenom,nom,equipe,licence,otm,charte) VALUES(:prenom,:nom,:equipe,:licence,:otm,:charte)";
        $this->prepareAndExecute($sql, [
			':equipe' => [ $equipe, SQLITE3_INTEGER],
			':prenom' => [ $prenom, SQLITE3_TEXT],
			':nom' => [ $nom, SQLITE3_TEXT],
			':licence' => [ $licence, SQLITE3_TEXT],
			':otm' => [ $otm, SQLITE3_INTEGER],
			':charte' => [ $charte, SQLITE3_INTEGER]
        ]);
        return $this->lastInsertRowID();
    }


    public function update(int $id, string $prenom,string $nom,int $equipe,string $licence,int $otm,int $charte): int {
        $sql = "UPDATE users SET prenom=:prenom, nom=:nom, equipe=:equipe, licence=:licence, otm=:otm, charte=:charte WHERE id=:id";
        $this->prepareAndExecute($sql, [
			':id' => [$id, SQLITE3_INTEGER],
			':equipe' => [ $equipe, SQLITE3_INTEGER],
			':prenom' => [ $prenom, SQLITE3_TEXT],
			':nom' => [ $nom, SQLITE3_TEXT],
			':licence' => [ $licence, SQLITE3_TEXT],
			':otm' => [ $otm, SQLITE3_INTEGER],
			':charte' => [ $charte, SQLITE3_INTEGER]
        ]);

        return $this->changes();
    }

    public function delete(int $id): int {
        $sql = "DELETE FROM users WHERE id=:id";
        $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);
        
        $sql = "DELETE FROM selections WHERE user=:id";
        $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);

        $sql = "DELETE FROM disponibilites WHERE user=:id";
        $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);

        $sql = "DELETE FROM presences WHERE user=:id";
        $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);
        
        return $this->changes();
    }
}