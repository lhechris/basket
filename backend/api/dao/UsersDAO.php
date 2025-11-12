<?php
require_once __DIR__ . '/BaseDAO.php';

class UsersDAO extends BaseDAO {
    public function getAll(): array {
        $sql = "SELECT id, prenom, nom, equipe, licence, otm, charte FROM users ORDER BY equipe, prenom";
        $res = $this->prepareAndExecute($sql);
        return $this->fetchAll($res);
    }

    public function getById(int $id): ?array {
        $sql = "SELECT * FROM users WHERE id=:id";
        $res = $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);
        $rows = $this->fetchAll($res);
        return $rows[0] ?? null;
    }

    public function create(array $data): int {
        $sql = "INSERT INTO users(prenom,nom,equipe,licence,otm,charte) VALUES(:prenom,:nom,:equipe,:licence,:otm,:charte)";
        $this->prepareAndExecute($sql, [
            ':prenom' => $data['prenom'] ?? null,
            ':nom' => $data['nom'] ?? null,
            ':equipe' => [$data['equipe'] ?? 0, SQLITE3_INTEGER],
            ':licence' => $data['licence'] ?? null,
            ':otm' => [$data['otm'] ?? 0, SQLITE3_INTEGER],
            ':charte' => [$data['charte'] ?? 0, SQLITE3_INTEGER],
        ]);
        return $this->db->lastInsertRowID();
    }

    public function update(int $id, array $data): int {
        $sql = "UPDATE users SET prenom=:prenom, nom=:nom, equipe=:equipe, licence=:licence, otm=:otm, charte=:charte WHERE id=:id";
        $this->prepareAndExecute($sql, [
            ':prenom' => $data['prenom'] ?? null,
            ':nom' => $data['nom'] ?? null,
            ':equipe' => [$data['equipe'] ?? 0, SQLITE3_INTEGER],
            ':licence' => $data['licence'] ?? null,
            ':otm' => [$data['otm'] ?? 0, SQLITE3_INTEGER],
            ':charte' => [$data['charte'] ?? 0, SQLITE3_INTEGER],
            ':id' => [$id, SQLITE3_INTEGER],
        ]);
        return $this->db->changes();
    }

    public function delete(int $id): int {
        $sql = "DELETE FROM users WHERE id=:id";
        $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);
        return $this->db->changes();
    }
}