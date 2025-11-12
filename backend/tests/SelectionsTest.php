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


        self::$donnees->db->exec("INSERT INTO disponibilites(user,jour,val) VALUES(1,'2025-09-01',2)");
        self::$donnees->db->exec("INSERT INTO disponibilites(user,jour,val) VALUES(2,'2025-09-01',1)");
        self::$donnees->db->exec("INSERT INTO disponibilites(user,jour,val) VALUES(3,'2025-09-01',1)");
        self::$donnees->db->exec("INSERT INTO disponibilites(user,jour,val) VALUES(4,'2025-09-01',1)");

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
        $this->assertIsArray($json);

        //print_r($json);

        $expected = json_decode(file_get_contents('tests/data/selections.json'),true);
        $this->assertEquals($expected, $json);
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
       
        //Verifie qu'on ne peut pas ajouter un match qui n'existe pas
        $input = array("match"=>99, "usr"=>98, "selection"=>97);
        $update->invoke($this->selections,$input);
        $json=$this->SelectionsGet();
        $noval = true;
        foreach ($json as $v) {
            if (($v["match"] == 99) && ($v["user"] == 98)) {
                $noval=false;
            }
        }
        $this->assertTrue($noval,"Ajout enregistrement nok");
        

        //Test l'update sur un autre match le meme jour (il ne doit y avoir qu'un seul enregistrement a la fin)
        $input = array("match"=>2, "usr"=>1, "selection"=>33);
        $update->invoke($this->selections,$input);
        $json=$this->SelectionsGet();
        $nb = 0;
        foreach ($json as $v) {
            if ($v["user"] == 1) {
                $nb++;
                if ($v["match"] == 2) {
                    $this->assertEquals(33,$v["val"]);
                }
            }
        }

        $this->assertEquals(1,$nb,"update sur un autre match");


    }

    public static function tearDownAfterClass(): void
    {
        self::$donnees->db->close();
    }
}