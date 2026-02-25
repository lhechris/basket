<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/env.php';
require_once __DIR__ . '/../api/utils.php';

use dao\BaseDAO;
use Basket\Presences;

class PresencesTest extends TestCase
{
    private static $donnees;
    private $presences;
    private $reflection;

    public static function setUpBeforeClass(): void
    {
        loadEnv(__DIR__ . '/.env');
        loginfo("START PresencesTest");
        self::$donnees = new BaseDAO();

        // Create test database
        $sql = file_get_contents(__DIR__ . '/../config/createdb.sql');
        self::$donnees->exec($sql);

        // Add test data
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('riri',1)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('fifi',1)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('loulou',2)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('daisy',2)");

        self::$donnees->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-01','sans titre')");
        self::$donnees->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-04','sans titre')");
        self::$donnees->exec("INSERT INTO entrainements(jour,titre) VALUES('2025-09-08','sans titre')");

        self::$donnees->exec("INSERT INTO presences(user,entrainement,val) VALUES(1,1,1)");
        self::$donnees->exec("INSERT INTO presences(user,entrainement,val) VALUES(2,1,1)");
    }

    protected function setUp(): void
    {
        $this->presences = new Presences();
        $this->reflection = new ReflectionClass(Presences::class);        
    }

    private function presencesGet() {

        $results = self::$donnees->query('SELECT entrainement,user,val FROM presences ORDER BY entrainement,user');
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
             "id": 1, "jour" :"2025-09-01",
             "users": [ {"id" : 4,"pres" : 0,"prenom" : "daisy"},
                        {"id" : 2,"pres" : 1,"prenom" : "fifi" },
                        {"id" : 3,"pres" : 0,"prenom" : "loulou"},
                        {"id" : 1,"pres" : 1,"prenom" : "riri"} ]
             },
             {
             "id": 2, "jour" :"2025-09-04",
             "users": [ {"id" : 4,"pres" : 0,"prenom" : "daisy"},
                        {"id" : 2,"pres" : 0,"prenom" : "fifi" },
                        {"id" : 3,"pres" : 0,"prenom" : "loulou"},
                        {"id" : 1,"pres" : 0,"prenom" : "riri"} ]
            },
            {
             "id": 3, "jour" :"2025-09-08",
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
        self::$donnees->exec("DELETE FROM presences WHERE entrainement=99");

    }


    public static function tearDownAfterClass(): void
    {
        self::$donnees->close();
        loginfo("STOP PresencesTest");
    }
}