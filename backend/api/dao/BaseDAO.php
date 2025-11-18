<?php

namespace dao;

//include_once("../utils.php");

use RuntimeException;
use SQLite3;

class BaseDAO {
    static public $db=null;

    public function __construct() {
        $this->open();
    }

    public function exec($sql) {
        return self::$db->exec($sql);
    }
    public function query($sql) {
        return self::$db->query($sql);
    }
    public function close() {
        //loginfo("Close DB");
        $ret=self::$db->close();
        self::$db = null;
        return $ret;
    }   
    public function open() {
        if (self::$db == null) {
            self::$db = new SQLite3(getenv("DBLOCATION"));
            //loginfo("DB Initialisee (".getenv("DBLOCATION").")");
        }
    }


    protected function prepareAndExecute(string $sql, array $params = []) {
        $this->open();

        $stmt = self::$db->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException("Prepare error: " . self::$db->lastErrorMsg() . " SQL: $sql");
        }
        foreach ($params as $k => $v) {
            // params as [':name' => [value, SQLITE3_TEXT|SQLITE3_INTEGER]]
            if (is_array($v) && count($v) === 2) {
                $stmt->bindValue($k, $v[0], $v[1]);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
        //loginfo($stmt->getSql(true));
        $res = $stmt->execute();

        if ($res === false) {
            throw new RuntimeException("Execute error: " . self::$db->lastErrorMsg());
        }
        return $res;
    }

    protected function fetchAll(\SQLite3Result $res): array {
        $out = [];
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $out[] = (object)$row;
        }
        //loginfo(print_r($out,true));
        return $out;
    }

    protected function lastInsertRowID() {
        return self::$db->lastInsertRowID();
    }

    protected function changes() {
        return self::$db->changes();
    }
}