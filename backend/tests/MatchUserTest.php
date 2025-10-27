<?php

use PHPUnit\Framework\TestCase;

include_once("api/env.php");
include_once("api/donnees.php");
include_once("api/matchuser.php");

class MatchUserTest extends TestCase
{
    private $matchUser;
    private $matchUsers;

    private static $donnees;

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
        self::$donnees->db->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(2,'2025-09-08','sans titre','0/0')");

        self::$donnees->db->exec("INSERT INTO matchuser(match,user,numero,commentaire) VALUES(1,1,5,'no comments')");
        self::$donnees->db->exec("INSERT INTO matchuser(match,user,numero,commentaire) VALUES(1,2,6,'no comments')");
        self::$donnees->db->exec("INSERT INTO matchuser(match,user,numero,commentaire) VALUES(1,3,7,'no comments')");
        self::$donnees->db->exec("INSERT INTO matchuser(match,user,numero,commentaire) VALUES(1,4,8,'no comments')");

    }

    protected function setUp(): void {
        $this->matchUser = new MatchUser();
        $this->matchUsers = new MatchUsers(self::$donnees);
    }

    public function testMatchUserToArray()
    {
        $this->matchUser->user = 1;
        $this->matchUser->match = 100;
        $this->matchUser->numero = 23;
        $this->matchUser->commentaire = "Test comment";

        $array = $this->matchUser->to_array();

        $this->assertEquals([
            'user' => 1,
            'match' => 100,
            'numero' => 23,
            'commentaire' => "Test comment"
        ], $array);
    }

    public function testMatchUserFromArray()
    {
        $data = [
            'user' => 1,
            'match' => 100,
            'numero' => 23,
            'commentaire' => "Test comment"
        ];

        $this->matchUser->from_array($data);

        $this->assertEquals(1, $this->matchUser->user);
        $this->assertEquals(100, $this->matchUser->match);
        $this->assertEquals(23, $this->matchUser->numero);
        $this->assertEquals("Test comment", $this->matchUser->commentaire);
    }

    public function testExists()
    {
        // Test when match user exists
        $result = $this->matchUsers->exists(1, 1);
        $this->assertTrue($result);

        // Test when match user doesn't exist
        $result = $this->matchUsers->exists(999, 999);
        $this->assertFalse($result);
    }

    public function testGetArray()
    {
        $results = $this->matchUsers->getArray(1);

        $this->assertIsArray($results);
        $this->assertEquals(4,count($results));

        if (count($results) > 0) {
            $this->assertInstanceOf(MatchUser::class, $results[0]);
        }
    }


    public function testUpdate()
    {
        // Test updating an existing match user
        $match = 1;
        $user = 1;
        $newNumero = 10;
        $newCommentaire = "Updated comment";

        // Call protected method using Reflection
        $reflection = new ReflectionClass(get_class($this->matchUsers));
        $method = $reflection->getMethod('update');
        $method->setAccessible(true);
        $method->invoke($this->matchUsers, $match, $user, $newNumero, $newCommentaire);

        // Verify the update
        $results = $this->matchUsers->getArray($match);
        $updated = false;
        foreach ($results as $result) {
            if ($result->user == $user) {
                $this->assertEquals($newNumero, $result->numero);
                $this->assertEquals($newCommentaire, $result->commentaire);
                $updated = true;
            }
        }
        $this->assertTrue($updated, "Match user was not found after update");
    }

    public function testUpdateNonExistentRecord()
    {
        $match = 999;
        $user = 999;
        $numero = 99;
        $commentaire = "Should not update";
        $reflection = new ReflectionClass(get_class($this->matchUsers));
        $method = $reflection->getMethod('update');
        $method->setAccessible(true);
        $result = $method->invoke($this->matchUsers, $match, $user, $numero, $commentaire);

        $this->assertTrue($result);
    }


    public function testAjoute()
    {
        // Test data
        $match = 2;  // Using match 2 since match 1 already has data
        $user = 5;   // New user not in test data
        $numero = 42;
        $commentaire = "New player";

        // Call protected method using Reflection
        $reflection = new ReflectionClass(get_class($this->matchUsers));
        $method = $reflection->getMethod('ajoute');
        $method->setAccessible(true);
        $method->invoke($this->matchUsers, $match, $user, $numero, $commentaire);

        // Get the inserted record and verify its contents
        $results = $this->matchUsers->getArray($match);
        $found = false;
        foreach ($results as $result) {
            if ($result->user == $user && $result->match == $match) {
                $this->assertEquals($numero, $result->numero);
                $this->assertEquals($commentaire, $result->commentaire);
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, "Inserted match user data does not match expected values");
    }

    public function testSupprime()
    {

        self::$donnees->db->exec("INSERT INTO matchuser(match,user,numero,commentaire) VALUES(55,66,5,'no comments')");

        // Call protected method using Reflection
        $reflection = new ReflectionClass(get_class($this->matchUsers));
        $method = $reflection->getMethod('supprime');
        $method->setAccessible(true);
        $method->invoke($this->matchUsers, 55,66);

        // Get the inserted record and verify its contents
        $results = $this->matchUsers->getArray(55);
        $found = false;
        foreach ($results as $result) {
            if ($result->user == 66 ) {
                $found = true;
                break;
            }
        }
        $this->assertFalse($found, "Deleted match user data does not working");
    }



}