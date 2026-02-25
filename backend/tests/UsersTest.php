<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../api/env.php';
require_once __DIR__ . '/../api/utils.php';

use dao\BaseDAO;
use Basket\Users;

class UsersTest extends TestCase
{
    private static $donnees;
    private $users;    

    public static function setUpBeforeClass(): void    
    {
        loadEnv(__DIR__ . '/.env');
        //$dbfilename = "tests/data/UsersTest.db";
        //if (file_exists($dbfilename)) { unlink($dbfilename);}
        //putenv("DBLOCATION=".$dbfilename);
        loginfo("START UsersTest");
        self::$donnees = new BaseDAO();
        self::$donnees->close();
        self::$donnees->open();


        // Create test database
        $sql = file_get_contents(__DIR__ . '/../config/createdb.sql');
        self::$donnees->exec($sql);

        // Add test data
        self::$donnees->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('riri',1,'duck','BC011001',1,1)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('fifi',1,'duck','BC011002',0,1)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('loulou',2,'duck','BC011003',1,0)");

        self::$donnees->exec("INSERT INTO presences(user,entrainement,val) VALUES(1,1,1)");
        self::$donnees->exec("INSERT INTO presences(user,entrainement,val) VALUES(2,1,1)");
    }

    public static function tearDownAfterClass(): void
    {
        if (isset(self::$donnees) && self::$donnees) {
            self::$donnees->close();
        }
        loginfo("STOP UsersTest");
    }

    protected function setUp(): void
    {
        $this->users = new Users();
    }

    public function testGetArray(): void
    {
        $json = $this->users->getArray();
        
        $expected = [
            ['id' => 2, 'prenom' => 'fifi', 'nom' => 'duck', 'equipe' => 1, 'licence' => 'BC011002', 'otm' => false, 'charte' => true],
            ['id' => 3, 'prenom' => 'loulou', 'nom' => 'duck', 'equipe' => 2, 'licence' => 'BC011003', 'otm' => true, 'charte' => false],
            ['id' => 1, 'prenom' => 'riri', 'nom' => 'duck', 'equipe' => 1, 'licence' => 'BC011001', 'otm' => true, 'charte' => true]
        ];

        $this->assertEquals($expected, $json);
    }

    public function testUpdate(): void
    {
        // Get initial state
        $initial = $this->users->getArray();
        $this->assertEquals('fifi', $initial[0]['prenom'], 'Initial prenom should be fifi');
        $this->assertEquals(1, $initial[0]['equipe'], 'Initial equipe should be 1');

        // Use ReflectionClass to access protected method
        $reflection = new ReflectionClass(Users::class);
        $update = $reflection->getMethod('update');
        $update->setAccessible(true);
        
        // Call protected update method
        $update->invoke($this->users, 1, 'gertrude', 'machin', 2, 'BC11000', 1, 1);

        // Verify changes
        $json = $this->users->getArray();
        $updated = array_filter($json, fn($v) => $v['id'] == 1);
        $updated = reset($updated);
        
        $this->assertEquals('gertrude', $updated['prenom'], 'Prenom should be updated to gertrude');
        $this->assertEquals(2, $updated['equipe'], 'Equipe should be updated to 2');
    }


    public function testAjoute(): void
    {

        // Use ReflectionClass to access protected method
        $reflection = new ReflectionClass(Users::class);
        $ajoute = $reflection->getMethod('ajoute');
        $ajoute->setAccessible(true);
        
        // Call protected update method
        $id = $ajoute->invoke($this->users, 'falbala', 3);

        // Verify changes
        $json = $this->users->getArray();
        $created = array_filter($json, fn($v) => $v['id'] == $id);
        $created = reset($created);
        
        $this->assertEquals('falbala', $created['prenom'], 'Prenom should be updated to gertrude');
        $this->assertEquals(3, $created['equipe'], 'Equipe should be updated to 2');

        //supprime l'enregistrement
        self::$donnees->exec("DELETE FROM users WHERE id=$id");

    }

    


}