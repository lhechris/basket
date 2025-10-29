<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/env.php';
require_once __DIR__ . '/../api/donnees.php';
require_once __DIR__ . '/../api/selections.php';
require_once __DIR__ . '/../api/matchinfos.php';
require_once __DIR__ . '/../api/disponibilites.php';


class SelectionsTest extends TestCase
{
    private static $donnees;
    private $selections;
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

        self::$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(1,'2025-09-01','sans titre','0/0')");
        self::$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(2,'2025-09-01','sans titre','0/0')");
        self::$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(2,'2025-09-08','sans titre','0/0')");
        self::$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(1,'2025-09-09','sans titre','0/0')");


        self::$donnees->db->exec("INSERT INTO disponibilites(user,jour,val) VALUES(1,'2025-09-01',1)");
        self::$donnees->db->exec("INSERT INTO disponibilites(user,jour,val) VALUES(2,'2025-09-01',1)");

        self::$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(1,1,1)");
        self::$donnees->db->exec("INSERT INTO selections(user,match,val) VALUES(3,1,1)");
    }

    protected function setUp(): void
    {
        $users = new Users(self::$donnees);
        $matchInfos = new MatchInfos(self::$donnees);
        $matchs = new Matchs(self::$donnees,$matchInfos);
        $disponibilites = new Disponibilites(self::$donnees,$users,$matchs);        
        $this->selections = new Selections(self::$donnees,$users,$matchs,$disponibilites);
        $this->reflection = new ReflectionClass(Selections::class);
    }

    private function SelectionsGet() {
        $results = self::$donnees->db->query('SELECT match,user,val FROM selections ORDER BY match,user');
        $json= array();
        while ($row = $results->fetchArray()) {
            array_push($json,array("match"=>$row['match'],
                                "user"=>$row["user"],
                                "val"=>$row["val"] ));
        }
        return($json);
    }


    public function testGetArray(): void
    {
        $results = $this->selections->getArray(1);
        $json = $results;

        //Tableau par equipe liste des match, où dans chaque match liste des joueuses
        $s = '[ { "equipe" : 1,
                  "joueurs" : [
                      {"prenom" : "fifi", "nb" : 0},
                      {"prenom" : "riri" ,"nb" : 1}],
                  "autrejoueurs" : [  
                      {"prenom" : "loulou", "nb":1}],
                  "matchs" : [{
                        "id" : 1,
                        "jour": "2025-09-01",
                        "users": [{"id": 2,"dispo": 1, "selection":0,"prenom": "fifi"},
                                {"id": 1,"dispo": 1, "selection":1,"prenom": "riri"}
                                ],
                        "autres" : [{"id": 3,"dispo": 0, "selection":1,"prenom": "loulou"}],
                        "equipe" : 1,
                        "titre": "sans titre",
                        "nb" : 2
                    },
                    {
                        "id" : 4,
                        "jour": "2025-09-09",
                        "users": [{"id": 2,"dispo": 0, "selection":0,"prenom": "fifi"},
                                {"id": 1,"dispo": 0, "selection":0,"prenom": "riri"}
                                ],
                        "autres": [],
                        "equipe" : 1,
                        "titre": "sans titre",
                        "nb" : 0
                    }]
                },
                { "equipe" : 2,
                "joueurs" : [
                    {"prenom" : "daisy",  "nb" : 0},
                    {"prenom" : "loulou", "nb" : 1}], 
                "autrejoueurs" : [],
                "matchs" : [{
                        "id" : 2,
                        "jour": "2025-09-01",
                        "users": [{"id": 4,"dispo": 0, "selection":0,"prenom": "daisy"},
                                {"id": 3,"dispo": 0, "selection":0,"prenom": "loulou"}],
                        "autres": [],
                        "equipe" : 2,
                        "titre": "sans titre",
                        "nb" : 0
                    },
                    {
                        "id" : 3,
                        "jour": "2025-09-08",
                        "users": [{"id": 4,"dispo": 0, "selection":0,"prenom": "daisy"},
                                {"id": 3,"dispo": 0, "selection":0,"prenom": "loulou"}],
                        "autres": [],
                        "equipe" : 2,
                        "titre": "sans titre",
                        "nb" : 0
                    }]
            }]';     

    $expected = json_decode($s,true);
    $this->assertIsArray($json);
    $this->assertCount(2, $json);
    $this->assertEquals($expected, $json);        

    }

    public function testExists(): void
    {
        $exists = $this->reflection->getMethod('exists');
        $exists->setAccessible(true);
        
        $this->assertTrue($exists->invoke($this->selections, 1, 1));
        $this->assertFalse($exists->invoke($this->selections, 999, 999));
    }

    public function testUpdate(): void
    {
        $update = $this->reflection->getMethod('update');
        $update->setAccessible(true);
       
        $input = array("match"=>1, "usr"=>1, "selection"=>2);
        $update->invoke($this->selections,$input);
        $json=$this->SelectionsGet();
        $noval = true;
        foreach ($json as $v) {
            if (($v["match"] == 1) && ($v["user"] == 1)) {
                $this->assertEquals(2,$v["val"]);
                $noval=false;
            }
        }
        $this->assertFalse($noval,"Modification enregistrement nok");
       

        $input = array("match"=>99, "usr"=>98, "selection"=>97);
        $update->invoke($this->selections,$input);
        $json=$this->SelectionsGet();
        $noval = true;
        foreach ($json as $v) {
            if (($v["match"] == 99) && ($v["user"] == 98)) {
                $this->assertEquals(97,$v["val"]);
                $this->assertEquals(98,$v["user"]);
                $this->assertEquals(99,$v["match"]);
                $noval=false;
            }
        }

        $this->assertFalse($noval,"Ajout enregistrement nok");
        
        //Supprime l'enregistrement nouvellement cree
        self::$donnees->db->exec("DELETE FROM selections WHERE match=99");

    }

    public function test_SelectionExists() {

        $method = $this->reflection->getMethod('exists');
        $method->setAccessible(true);

        $ret=$method->invoke($this->selections,1,1);
        $this->assertTrue($ret);
        
        $ret=$method->invoke($this->selections,3,1);
        $this->assertFalse($ret);

    }

    /**
     * 
     */
    public function testCreateIfNotExists(): void
    {
        $create = $this->reflection->getMethod('createIfNotExists');
        $create->setAccessible(true);

        // Test creating new selection
        $create->invoke($this->selections, 1, 1);
        $json1=$this->SelectionsGet();
        $this->assertEquals(2,count($json1));
       
        $this->assertTrue($create->invoke($this->selections, 3, 1));
        $json2 = $this->SelectionsGet();
        $this->assertEquals(3,count($json2));
    }

    public static function tearDownAfterClass(): void
    {
        self::$donnees->db->close();
    }
}