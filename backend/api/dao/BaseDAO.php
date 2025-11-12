<?php

namespace dao;

use RuntimeException;

class BaseDAO {
    protected $db;

    public function __construct($donnees) {
        // $donnees peut être l'objet Donnees ou l'objet DB direct
        $this->db = $donnees->db;
    }

    protected function prepareAndExecute(string $sql, array $params = []) {
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException("Prepare error: " . $this->db->lastErrorMsg() . " SQL: $sql");
        }
        foreach ($params as $k => $v) {
            // params as [':name' => [value, SQLITE3_TEXT|SQLITE3_INTEGER]]
            if (is_array($v) && count($v) === 2) {
                $stmt->bindValue($k, $v[0], $v[1]);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
        $res = $stmt->execute();
        if ($res === false) {
            throw new RuntimeException("Execute error: " . $this->db->lastErrorMsg());
        }
        return $res;
    }

    protected function fetchAll(\SQLite3Result $res): array {
        $out = [];
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $out[] = (object)$row;
        }
        return $out;
    }
}