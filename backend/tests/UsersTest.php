<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/env.php';
require_once __DIR__ . '/../api/donnees.php';
require_once __DIR__ . '/../api/users.php';

class UsersTest extends TestCase
{
    private static $donnees;
    private $users;    

    public static function setUpBeforeClass(): void    
    {
        loadEnv(__DIR__ . '/.env');
        self::$donnees = new Donnees();

        // Create test database
        $sql = file_get_contents(__DIR__ . '/../config/createdb.sql');
        self::$donnees->db->exec($sql);

        // Add test data
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('riri',1,'duck','BC011001',1,1)");
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('fifi',1,'duck','BC011002',0,1)");
        self::$donnees->db->exec("INSERT INTO users(prenom,equipe,nom,licence,otm,charte) VALUES('loulou',2,'duck','BC011003',1,0)");

        self::$donnees->db->exec("INSERT INTO presences(user,entrainement,val) VALUES(1,1,1)");
        self::$donnees->db->exec("INSERT INTO presences(user,entrainement,val) VALUES(2,1,1)");
    }

    protected function setUp(): void
    {
        $this->users = new Users(self::$donnees);
    }

    public function testGetArrayUsers(): void
    {
        $json = $this->users->getArray();
        
        $expected = [
            ['id' => 2, 'prenom' => 'fifi', 'nom' => 'duck', 'equipe' => 1, 'licence' => 'BC011002', 'otm' => false, 'charte' => true],
            ['id' => 3, 'prenom' => 'loulou', 'nom' => 'duck', 'equipe' => 2, 'licence' => 'BC011003', 'otm' => true, 'charte' => false],
            ['id' => 1, 'prenom' => 'riri', 'nom' => 'duck', 'equipe' => 1, 'licence' => 'BC011001', 'otm' => true, 'charte' => true]
        ];

        $this->assertEquals($expected, $json);
    }

    public function testUpdateUsers(): void
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

}