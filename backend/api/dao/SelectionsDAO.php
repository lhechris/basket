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
        return $row[0] > 0;
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

    /**
     * Retourne les joueurs sélectionnés pour un match
     */
    public function getByMatch($matchid): array {
        $sql = 'SELECT A.user, C.prenom,C.licence, C.nom '.
                 'FROM selections A, matchs B, users C '.
                 'WHERE A.match=B.id AND A.user=C.id AND A.match=:match AND A.val = 1 '.
                 'ORDER BY C.prenom';

        $res = $this->prepareAndExecute($sql, [':match'=> [$matchid, SQLITE3_INTEGER]]);
        return $this->fetchAll($res);
    }


    /**
     * Retourne le nombre de joueur sélectionnée pour un match
     * donnée
     */
    public function getNbPlayer(int $matchid) {
        $sql = "SELECT count(*) FROM matchs A,selections B WHERE A.id=B.match AND B.val=1 AND A.id=:id";
        $res = $this->prepareAndExecute($sql, [':id' => [$matchid, SQLITE3_INTEGER]]);
        $out = array();
        while ($row = $res->fetchArray(SQLITE3_NUM)) {
            array_push($out,$row[0]);
        }        
        return $out[0];
    }

    /**
     * Retourne le nombre de match sélectionnée pour un joueur
     * donnée
     */
    public function getNbMatch(int $userid) {
        $sql = "SELECT count(*) FROM users A,selections B, matchs C "
              ."WHERE A.id=B.user AND B.val=1 AND C.id=B.match AND A.id=:id ";

        $res = $this->prepareAndExecute($sql, [':id'=> [$userid, SQLITE3_INTEGER]]);
        
        $row = $res->fetchArray(SQLITE3_NUM);
        return $row[0];
    }

    /**
     * Retourne la liste des joueurs qui ont participé aux matchs de l'equipe 
     * mais qui ne font pas parti de l'équipe
     */
    public function getPlayersSelectedButInOtherTeam(int $equipe) {
        $sql = "SELECT C.id,C.prenom,C.equipe FROM selections A, matchs B, users C ".
					 "WHERE A.match=B.id AND B.equipe=:equipe AND C.id=A.user AND C.equipe!=:equipe AND A.val=1 ".
					 "GROUP BY (C.id) ORDER BY C.prenom";

        $res = $this->prepareAndExecute($sql, [":equipe" => [$equipe, SQLITE3_INTEGER]]);        
        return $this->fetchAll($res);
    }

    /**
     * Retourne le nombre de match joués pour chaque joueur
     */
    public function getNbMatchForEachPlayer() {
        $sql =  "SELECT A.prenom ,count(*) as nb ".
                "FROM users A,selections B, matchs C ".
                "WHERE A.id=B.user AND B.val=1 AND C.id=B.match ".
                "GROUP BY A.prenom ORDER BY A.prenom";

        $res = $this->prepareAndExecute($sql, []);        
        return $this->fetchAll($res);
    }

    /**
     * Retourne les joueurs selectionnées pour ce match
     * Mais uniquement ceux qui sont dans une opposition
     * (pour l'affichage en mode non admin ! comme ça les
     * parents ne voient pas les sélections trop tot)
     */
    public function getPlayersByMatchId(int $id) {
		$sql = 'SELECT A.user,C.prenom '.
               'FROM selections A, matchs B, users C,matchinfos D '.
               'WHERE A.match=B.id AND A.user=C.id AND B.id=:id AND A.val=1 AND D.match=B.id AND D.user=C.id '.
               'ORDER BY C.prenom';

        $res = $this->prepareAndExecute($sql, [':id' => [$id, SQLITE3_INTEGER]]);
        return $this->fetchAll($res);
    }

    /** 
     * Supprime le joueur de la journée
     */
    public function deleteByDay(string $jour, int $user): int {
        $sql = "DELETE FROM selections WHERE match IN (SELECT id FROM matchs B WHERE B.jour=:jour ) AND user=:user";
        $this->prepareAndExecute($sql, [
            ':jour' => [$jour, SQLITE3_TEXT],
            ':user' => [$user, SQLITE3_INTEGER]
        ]);
        return $this->db->changes();
    }    


    public function getAll(): array {
        $sql =  'SELECT A.match,A.user,A.val, B.jour, B.titre,B.equipe, C.prenom '.
			    'FROM selections A, matchs B, users C '.
			    'WHERE A.match=B.id AND A.user=C.id '.
			    'ORDER BY B.jour,B.equipe,C.prenom';
        $res = $this->prepareAndExecute($sql, []);
        return $this->fetchAll($res);
    }
}