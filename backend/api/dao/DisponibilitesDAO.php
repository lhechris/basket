<?php
namespace dao;

require_once 'BaseDAO.php';

use dao\BaseDAO;
use SQLite3;

/**
 * DAO pour la table disponibilites
 * Structure de la table:
 * - jour (TEXT)
 * - user (INTEGER)
 * - val (INTEGER)
 */
class DisponibilitesDAO extends BaseDAO {

    public function __construct($donnees) {
        parent::__construct($donnees);
    }

    public function exists(string $jour, int $user): bool {
        
        $sql = "SELECT 1 FROM disponibilites WHERE jour=:jour AND user=:user LIMIT 1";
        $res = $this->prepareAndExecute($sql, [
            ':jour' => $jour,
            ':user' => [$user, SQLITE3_INTEGER]
        ]);
        return $res->fetchArray(SQLITE3_NUM) !== false;
    }

    public function create(string $jour, int $user, int $val = 0): int {

        $sql = "INSERT INTO disponibilites(jour,user,val) VALUES(:jour,:user,:val)";
        $this->prepareAndExecute($sql, [
            ':jour' => [$jour, SQLITE3_TEXT],
            ':user' => [$user, SQLITE3_INTEGER],
            ':val' => [$val, SQLITE3_INTEGER]
        ]);
        return $this->db->lastInsertRowID();
    }

    public function update(string $jour,int $user, int $val): int {
        $sql = "UPDATE disponibilites SET val=:val WHERE jour=:jour AND user=:user";
        $this->prepareAndExecute($sql, [
            ':val' => [$val, SQLITE3_INTEGER],
            ':jour' => [$jour, SQLITE3_TEXT],
            ':user' => [$user, SQLITE3_INTEGER]
        ]);
        return $this->db->changes();
    }

    /**
     * Retourne toutes les disponibilites classées par jour et prenom
     */
    public function getAll(): array {
        $sql = "SELECT A.jour,A.user,A.val FROM disponibilites A, users B WHERE A.user=B.id ORDER BY A.jour,B.prenom";
                
        $res = $this->prepareAndExecute($sql, []);
        return $this->fetchAll($res);
    }
}