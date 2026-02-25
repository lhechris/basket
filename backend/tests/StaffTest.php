<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../api/env.php';
require_once __DIR__ . '/../api/utils.php';

use dao\BaseDAO;
use Basket\Staff;

class StaffTest extends TestCase
{
    private static $donnees;
    private $staff;    

    public static function setUpBeforeClass(): void    
    {
        loadEnv(__DIR__ . '/.env');
        //$dbfilename = "tests/data/StaffTest.db";
        //if (file_exists($dbfilename)) { unlink($dbfilename);}
        //putenv("DBLOCATION=".$dbfilename);
        loginfo("START StaffTest");
        self::$donnees = new BaseDAO();
        self::$donnees->close();
        self::$donnees->open();


        // Create test database
        $sql = file_get_contents(__DIR__ . '/../config/createdb.sql');
        self::$donnees->exec($sql);

        // Add test data
        self::$donnees->exec("INSERT INTO staff(prenom,nom,licence,role) VALUES('riri','duck','BC011001','Entraineur')");
        self::$donnees->exec("INSERT INTO staff(prenom,nom,licence,role) VALUES('fifi','duck','BC011002','Otm')");
        self::$donnees->exec("INSERT INTO staff(prenom,nom,licence,role) VALUES('loulou','duck','BC011003','Otm')");

        self::$donnees->exec("INSERT INTO matchs(equipe, jour, titre) VALUES(1,'2025-09-27','match2')");

        self::$donnees->exec("INSERT INTO staffmatchs(match,staff) VALUES(1,1)");
        self::$donnees->exec("INSERT INTO staffmatchs(match,staff) VALUES(1,2)");
        self::$donnees->exec("INSERT INTO staffmatchs(match,staff) VALUES(1,3)");
    }

    public static function tearDownAfterClass(): void
    {
        if (isset(self::$donnees) && self::$donnees) {
            self::$donnees->close();
        }
        loginfo("STOP StaffTest");
    }

    protected function setUp(): void
    {
        $this->staff = new Staff();
    }

    public function testGetArray(): void
    {
        $json = $this->staff->getArray();
        
        $expected = [
            ['id' => 2, 'prenom' => 'fifi', 'nom' => 'duck',  'licence' => 'BC011002', 'role' => 'Otm'],
            ['id' => 3, 'prenom' => 'loulou', 'nom' => 'duck', 'licence' => 'BC011003', 'role' =>'Otm'],
            ['id' => 1, 'prenom' => 'riri', 'nom' => 'duck', 'licence' => 'BC011001', 'role' => 'Entraineur' ]
        ];

        $this->assertEquals($expected, $json);
    }

    public function testUpdate(): void
    {
        // Get initial state
        $initial = $this->staff->getArray();
        $this->assertEquals('fifi', $initial[0]['prenom'], 'Initial prenom should be fifi');
        $this->assertEquals('duck', $initial[0]['nom'], 'Initial nom should be duck');

        // Use ReflectionClass to access protected method
        $reflection = new ReflectionClass(Staff::class);
        $update = $reflection->getMethod('update');
        $update->setAccessible(true);
        
        // Call protected update method
        $update->invoke($this->staff, 1, 'gertrude', 'machin', 'BC11000', 'Entraineur 2');

        // Verify changes
        $json = $this->staff->getArray();
        $updated = array_filter($json, fn($v) => $v['id'] == 1);
        $updated = reset($updated);
        
        $this->assertEquals('gertrude', $updated['prenom'], 'Prenom should be updated to gertrude');
        $this->assertEquals('machin', $updated['nom'], 'Nom should be updated to machin');
        $this->assertEquals('BC11000', $updated['licence'], 'Licence should be updated to BC11000');
        $this->assertEquals('Entraineur 2', $updated['role'], 'Role should be updated to Entraineur 2');
    }


    public function testAjoute(): void
    {

        // Use ReflectionClass to access protected method
        $reflection = new ReflectionClass(Staff::class);
        $ajoute = $reflection->getMethod('ajoute');
        $ajoute->setAccessible(true);
        
        // Call protected update method
        $id = $ajoute->invoke($this->staff, 'falbala', '','LIC','Rien');

        // Verify changes
        $json = $this->staff->getArray();
        $created = array_filter($json, fn($v) => $v['id'] == $id);
        $created = reset($created);
        
        $this->assertEquals('falbala', $created['prenom'], 'Prenom should be updated to falbala');
        $this->assertEquals('LIC', $created['licence'], 'Licence should be updated to LIC');
        $this->assertEquals('Rien', $created['role'], 'Role should be updated to Rine');

        //supprime l'enregistrement
        self::$donnees->exec("DELETE FROM staff WHERE id=$id");

    }

    


}