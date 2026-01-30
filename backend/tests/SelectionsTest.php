<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/utils.php';
require_once __DIR__ . '/../api/env.php';
require_once __DIR__ . '/../api/dao/BaseDAO.php';
require_once __DIR__ . '/../api/dao/MatchInfosDAO.php';
require_once __DIR__ . '/../api/selections.php';

use dao\BaseDAO;
use dao\MatchInfosDAO;

class SelectionsTest extends TestCase
{
    private static $donnees;
    private $selections;
    private $reflection;

    public static function setUpBeforeClass(): void
    {
        loadEnv(__DIR__ . '/.env');
        loginfo("START SelectionsTest");
        self::$donnees = new BaseDAO();

        // Create test database
        $sql = file_get_contents(__DIR__ . '/../config/createdb.sql');
        self::$donnees->exec($sql);

        // Add test data
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('riri',1)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('fifi',1)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('loulou',2)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('daisy',2)");

        self::$donnees->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(1,'2025-09-01','sans titre','0/0')");
        self::$donnees->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(2,'2025-09-01','sans titre','0/0')");
        self::$donnees->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(2,'2025-09-08','sans titre','0/0')");
        self::$donnees->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(1,'2025-09-09','sans titre','0/0')");


        self::$donnees->exec("INSERT INTO disponibilites(user,jour,val) VALUES(1,'2025-09-01',2)");
        self::$donnees->exec("INSERT INTO disponibilites(user,jour,val) VALUES(2,'2025-09-01',1)");
        self::$donnees->exec("INSERT INTO disponibilites(user,jour,val) VALUES(3,'2025-09-01',1)");
        self::$donnees->exec("INSERT INTO disponibilites(user,jour,val) VALUES(4,'2025-09-01',1)");

        self::$donnees->exec("INSERT INTO selections(user,match,val) VALUES(1,1,1)");
        self::$donnees->exec("INSERT INTO selections(user,match,val) VALUES(3,1,1)");
    }

    protected function setUp(): void
    {
        $this->selections = new Selections();
        $this->reflection = new ReflectionClass(Selections::class);
    }

    private function SelectionsGet() {
        $results = self::$donnees->query('SELECT match,user,val FROM selections ORDER BY match,user');
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
        $results = $this->selections->getArray();
        $json = $results;
        $this->assertIsArray($json);

        //print_r($json);

        $expected = json_decode(file_get_contents('tests/data/selections.json'),true);
        $this->assertEquals($expected, $json);
    }

    public function testGetArrayOld(): void
    {
        $results = $this->selections->getArrayOld();
        $json = $results;
        $this->assertIsArray($json);

        //print(json_encode($json));

        $expected = json_decode(file_get_contents('tests/data/selectionsOld.json'),true);
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
        self::$donnees->exec("INSERT INTO matchinfos(user,match,opposition) VALUES(1,1,'A')");
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

        $matchinfos = new MatchInfosDAO();        
        $this->assertFalse($matchinfos->exists(1,1),"Suppression de matchinfos");       


    }

    public static function tearDownAfterClass(): void
    {
        self::$donnees->close();
        loginfo("STOP SelectionsTest");

    }
}