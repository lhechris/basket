<?php
class Donnees {

    public $db;

    public function __construct() {
        $this->db = new SQLite3(getenv("DBLOCATION"));
    }
}
