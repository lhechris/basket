<?php

use PHPUnit\Framework\TestCase;

include_once("api/env.php");
include_once("api/donnees.php");
include_once("api/matchinfos.php");


class MatchInfosTest extends TestCase
{
    private static $donnees;
    public MatchInfos $matchInfos;

    public static function setUpBeforeClass(): void    
    {
        loadEnv("tests/.env");
        self::$donnees = new Donnees();
        $sql= file_get_contents("config/createdb.sql");
        self::$donnees->db->exec($sql);

        //ajout donnees de test
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('riri',1)");
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('fifi',1)");
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('loulou',2)");
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('daisy',2)");

        self::$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(1,'2025-09-01','sans titre','0/0')");
        self::$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(2,'2025-09-01','sans titre','0/0')");


        self::$donnees->db->exec("INSERT INTO matchinfos(user,match,opposition,numero,commentaire) VALUES(1,1,'A',4,'no comment')");
        self::$donnees->db->exec("INSERT INTO matchinfos(user,match,opposition,numero,commentaire) VALUES(2,1,'A',5,'no comment')");
        self::$donnees->db->exec("INSERT INTO matchinfos(user,match,opposition,numero,commentaire) VALUES(3,1,'B',6,'no comment')");

        self::$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(1,1,1)");
        self::$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(2,1,1)");
        self::$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(3,1,1)");
        self::$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(4,1,1)");

    }

    protected function setUp(): void {
        $this->matchInfos = new MatchInfos(self::$donnees);
    }

    public function testUpdateRecordExists()
    {
        $json = [
            'match' => 1,
            'usr' => 3, 
            'opposition' => "C",
            'numero' => 20,
            'commentaire' => "Updated comment"
        ];

        // Call protected method using Reflection
        $reflection = new ReflectionClass(get_class($this->matchInfos));
        $method = $reflection->getMethod('update');
        $method->setAccessible(true);
        $method->invoke($this->matchInfos, $json);


        //Verifie qu'il y a un nouvelle enregistrement
        $nb=0;
        $results = self::$donnees->db->query('SELECT user,match,opposition,numero,commentaire FROM matchinfos');
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            if ($row["user"] == $json["usr"] && $row["match"] == $json["match"]) {
                $this->assertEquals($json["opposition"],    $row["opposition"]);
                $this->assertEquals($json['numero'], $row["numero"]);
                $this->assertEquals($json['commentaire'], $row["commentaire"]);                
            }
            $nb++;
        }
        $this->assertEquals(3,$nb);
        
    }


    public function testUpdateRecordNotExists()
    {
        $json = [
            'match' => 1,
            'usr' => 4, // Assuming user 4 exists
            'opposition' => "B",
            'numero' => 20,
            'commentaire' => "Updated comment"
        ];

        // Call protected method using Reflection
        $reflection = new ReflectionClass(get_class($this->matchInfos));
        $method = $reflection->getMethod('update');
        $method->setAccessible(true);
        $method->invoke($this->matchInfos, $json);


        //Verifie qu'il y a un nouvelle enregistrement
        $nb=0;
        $results = self::$donnees->db->query('SELECT user,match,opposition,numero,commentaire FROM matchinfos');
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            if ($row["user"] == $json["usr"] && $row["match"] == $json["match"]) {
                $this->assertEquals($json["opposition"],    $row["opposition"]);
                $this->assertEquals($json['numero'], $row["numero"]);
                $this->assertEquals($json['commentaire'], $row["commentaire"]);                
            }
            $nb++;
        }
        $this->assertEquals(4,$nb);
        
        //supprime l'enregistrement
        self::$donnees->db->exec("DELETE FROM matchinfos WHERE user=".$json['usr']." and match=".$json['match']);
    }




    public function testUpdateNonExistentPlayer()
    {
        $json = [
            'match' => 999, // Assuming this match does not exist
            'usr' => 999,   // Assuming this user does not exist
            'opposition' => "Team truc",
            'numero' => 99,
            'commentaire' => "Should be created"
        ];

        $reflection = new ReflectionClass(get_class($this->matchInfos));
        $method = $reflection->getMethod('update');
        $method->setAccessible(true);
        $method->invoke($this->matchInfos, $json);

        // Verify that record was created
        $nb=0;
        $results = self::$donnees->db->query('SELECT user,match,opposition,numero,commentaire FROM matchinfos');
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            if ($row["user"] == $json["usr"] && $row["match"] == $json["match"]) {
                $this->assertEquals($json["opposition"],    $row["opposition"]);
                $this->assertEquals($json['numero'], $row["numero"]);
                $this->assertEquals($json['commentaire'], $row["commentaire"]);                
            }
            $nb++;
        }
        $this->assertEquals(4,$nb);
    }
}