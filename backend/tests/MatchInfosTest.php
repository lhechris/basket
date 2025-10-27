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
    public function testGetArray()
    {
        $match = 1; // Assuming match 1 exists in the database
        $result = $this->matchInfos->getArray($match);

        $this->assertIsArray($result);
        $this->assertInstanceOf(ListMatchInfo::class, $result[0]);

        //On doit avoir un seul enregistrement de ListMatchInfo
        $this->assertEquals(1,count($result));
        $li = $result[0];
        $this->assertInstanceOf(ListMatchInfo::class, $li);

        $a = $li->a;
        $b = $li->b;
        $autres = $li->autres;

        $this->assertEquals(2,count($a));
        $this->assertEquals(1,count($b));
        $this->assertEquals(1,count($autres));
        
        $this->assertInstanceOf(MatchInfo::class,$a[0]);
        $this->assertEquals(2,      $a[0]->joueur->id);
        $this->assertEquals("fifi", $a[0]->joueur->prenom);
        $this->assertEquals("A",    $a[0]->opposition);
        $this->assertEquals(5,      $a[0]->numero);

        $this->assertInstanceOf(MatchInfo::class,$a[1]);
        $this->assertEquals(1,     $a[1]->joueur->id);
        $this->assertEquals("riri",$a[1]->joueur->prenom);
        $this->assertEquals("A",   $a[1]->opposition);
        $this->assertEquals(4,     $a[1]->numero);

        $this->assertInstanceOf(MatchInfo::class,$b[0]);
        $this->assertEquals(3,       $b[0]->joueur->id);
        $this->assertEquals("loulou",$b[0]->joueur->prenom);
        $this->assertEquals("B",     $b[0]->opposition);
        $this->assertEquals(6,       $b[0]->numero);

        $this->assertInstanceOf(MatchInfo::class,$autres[0]);
        $this->assertEquals(4,       $autres[0]->joueur->id);
        $this->assertEquals("daisy", $autres[0]->joueur->prenom);
        $this->assertEquals(null,    $autres[0]->opposition);
        $this->assertEquals(null,    $autres[0]->numero);

    }

    public function testCreateIfNotExists()
    {
        $match = 1;
        $user = 5; // Assuming user 5 does not exist in matchinfos
        $opposition = "Team B";
        $numero = 10;
        $commentaire = "Test comment";


        // Call protected method using Reflection
        $reflection = new ReflectionClass(get_class($this->matchInfos));
        $method = $reflection->getMethod('createIfNotExists');
        $method->setAccessible(true);
        $result = $method->invoke($this->matchInfos, $match, $user, $opposition, $numero, $commentaire);

        $this->assertTrue($result, "Expected to create a new record");

        // Check if the record was created
        $method = $reflection->getMethod('exists');
        $method->setAccessible(true);
        $exists=$method->invoke($this->matchInfos, $match, $user);

        $this->assertTrue($exists, "Record should exist after creation");
    }

    public function testUpdate()
    {
        $json = [
            'match' => 1,
            'usr' => 4, // Assuming user 5 exists
            'opposition' => "B",
            'numero' => 20,
            'commentaire' => "Updated comment"
        ];

        // Call protected method using Reflection
        $reflection = new ReflectionClass(get_class($this->matchInfos));
        $method = $reflection->getMethod('update');
        $method->setAccessible(true);
        $method->invoke($this->matchInfos, $json);

        // Verify the update
        $result = $this->matchInfos->getArray($json['match']);        

        //On doit avoir un seul enregistrement de ListMatchInfo
        $this->assertEquals(1,count($result));
        $li = $result[0];
        $this->assertInstanceOf(ListMatchInfo::class, $li);

        $this->assertEquals(2,count($li->a));
        $this->assertEquals(2,count($li->b));
        $this->assertEquals(0,count($li->autres));

        $this->assertEquals(4,      $li->b[0]->joueur->id);
        $this->assertEquals("daisy",$li->b[0]->joueur->prenom);
        $this->assertEquals("B",    $li->b[0]->opposition);
        $this->assertEquals($json['numero'], $li->b[0]->numero);
        $this->assertEquals($json['commentaire'], $li->b[0]->commentaire);
    }

    public function testUpdateNonExistentRecord()
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
        $method = $reflection->getMethod('exists');
        $method->setAccessible(true);
        $exists = $method->invoke($this->matchInfos, $json['match'], $json['usr']);        

        $this->assertTrue($exists, "Record should exist after trying to update a non-existent record");
    }
}