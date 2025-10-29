<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/env.php';
require_once __DIR__ . '/../api/presences.php';
require_once __DIR__ . '/../api/donnees.php';
require_once __DIR__ . '/../api/users.php';
require_once __DIR__ . '/../api/entrainements.php';

class PresencesTest extends TestCase
{
    private static $donnees;
    private $presences;
    private $reflection;

    public static function setUpBeforeClass(): void
    {
        loadEnv(__DIR__ . '/.env');
        self::$donnees = new Donnees();

        // Create test database
        $sql = file_get_contents(__DIR__ . '/../config/createdb.sql');
        self::$donnees->db->exec($sql);

        // Add test data
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('riri',1)");
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('fifi',1)");
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('loulou',2)");
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe) VALUES('daisy',2)");

        self::$donnees->db->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-01','sans titre')");
        self::$donnees->db->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-04','sans titre')");
        self::$donnees->db->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-08','sans titre')");

        self::$donnees->db->exec("INSERT INTO presences(user,entrainement,val) VALUES(1,1,1)");
        self::$donnees->db->exec("INSERT INTO presences(user,entrainement,val) VALUES(2,1,1)");
    }

    protected function setUp(): void
    {
        $users = new Users(self::$donnees);
        $entrainements = new Entrainements(self::$donnees);
        $this->presences = new Presences(self::$donnees, $users, $entrainements);
        $this->reflection = new ReflectionClass(Presences::class);        
    }

    private function presencesGet() {

        $results = self::$donnees->db->query('SELECT entrainement,user,val FROM presences ORDER BY entrainement,user');
        $json= array();
        while ($row = $results->fetchArray()) {
            array_push($json,array("entrainement"=>$row['entrainement'],
                                "user"=>$row["user"],
                                "val"=>$row["val"] ));
        }
        return($json);
    }


    public function testGetPresences(): void
    {
        $json = $this->presences->getArray();

        $s = '[{
             "id": 1, "date" :"2025-09-01",
             "users": [ {"id" : 4,"pres" : 0,"prenom" : "daisy"},
                        {"id" : 2,"pres" : 1,"prenom" : "fifi" },
                        {"id" : 3,"pres" : 0,"prenom" : "loulou"},
                        {"id" : 1,"pres" : 1,"prenom" : "riri"} ]
             },
             {
             "id": 2, "date" :"2025-09-04",
             "users": [ {"id" : 4,"pres" : 0,"prenom" : "daisy"},
                        {"id" : 2,"pres" : 0,"prenom" : "fifi" },
                        {"id" : 3,"pres" : 0,"prenom" : "loulou"},
                        {"id" : 1,"pres" : 0,"prenom" : "riri"} ]
            },
            {
             "id": 3, "date" :"2025-09-08",
             "users": [ {"id" : 4,"pres" : 0,"prenom" : "daisy"},
                        {"id" : 2,"pres" : 0,"prenom" : "fifi" },
                        {"id" : 3,"pres" : 0,"prenom" : "loulou"},
                        {"id" : 1,"pres" : 0,"prenom" : "riri"} ]
        }]';
        $expected = json_decode($s,true);

        $this->assertIsArray($json);
        $this->assertCount(3, $json);
        $this->assertEquals($expected, $json); 
    }

    public function testUpdatePresence(): void
    {
        $method = $this->reflection->getMethod('update');
        $method->setAccessible(true);

        $initial=$this->presencesGet();
        $this->assertEquals(1, $initial[0]["val"], "Initial value should be correct");

        $input = ["entrainement" => 1, "usr" => 1, "pres" => 2];
        $method->invoke($this->presences,$input);
        $json = $this->presencesGet();
        $this->assertEquals(2, $json[0]["val"], "Value should be correctly modified");

        
        $input = ["entrainement" => 99, "usr" => 98, "pres" => 97];
        $method->invoke($this->presences,$input);
        $json = $this->presencesGet();
        $this->assertEquals(97, $json[count($json) - 1]["val"], "Value should be correctly added");
        $this->assertEquals(98, $json[count($json) - 1]["user"], "User should be correctly added");
        $this->assertEquals(99, $json[count($json) - 1]["entrainement"], "Entrainement should be correctly added");

        //Supprime l'enregistrement nouvellement cree
        self::$donnees->db->exec("DELETE FROM presences WHERE entrainement=99");

    }

    public function testPresenceExists(): void
    {
        $method = $this->reflection->getMethod('exists');
        $method->setAccessible(true);

        $ret=$method->invoke($this->presences,1,1);
        $this->assertTrue($ret,"Presence should exist");
        
        $ret=$method->invoke($this->presences,99,99);
        $this->assertFalse($ret, "Presence should not exist");

    }

    public function testPresenceCreateIfNotExists(): void
    {
        $create = $this->reflection->getMethod('createIfNotExists');
        $create->setAccessible(true);

        // Test creating new selection
        $create->invoke($this->presences, 1, 1);
        $json1=$this->presencesGet();
        $this->assertEquals(2,count($json1));
       
        $this->assertTrue($create->invoke($this->presences, 3, 1));
        $json2 = $this->presencesGet();
        $this->assertEquals(3,count($json2));

    }

    public static function tearDownAfterClass(): void
    {
        self::$donnees->db->close();
    }
}