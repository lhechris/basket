<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/env.php';
require_once __DIR__ . '/../api/dao/BaseDAO.php';
require_once __DIR__ . '/../api/users.php';
require_once __DIR__ . '/../api/matchs.php';
require_once __DIR__ . '/../api/matchinfos.php';
require_once __DIR__ . '/../api/disponibilites.php';

use dao\BaseDAO;

final class DisponibilitesTest extends TestCase
{
    private Users $users;
    private Disponibilites $disponibilites;

    private static $donnees;

    public static function setUpBeforeClass(): void    
    {
        loadEnv("tests/.env");
        loginfo("START IndDisponibilitesTestexTest");
        self::$donnees = new BaseDAO();
        $sql= file_get_contents("config/createdb.sql");
        self::$donnees->exec($sql);

        // seed users
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('riri',1)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('fifi',1)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('loulou',2)");
        self::$donnees->exec("INSERT INTO users(prenom,equipe) VALUES('daisy',2)");

        // seed matchs
        self::$donnees->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(1,'2025-09-01','sans titre','0/0')");
        self::$donnees->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(2,'2025-09-01','sans titre','0/0')");
        self::$donnees->exec("INSERT INTO matchs(equipe,jour,titre,score) VALUES(2,'2025-09-08','sans titre','0/0')");

        // seed disponibilites
        self::$donnees->exec("INSERT INTO disponibilites(user,jour,val) VALUES(1,'2025-09-01',1)");
        self::$donnees->exec("INSERT INTO disponibilites(user,jour,val) VALUES(2,'2025-09-01',1)");
    }
    
    public static function tearDownAfterClass(): void
    {
        if (isset(self::$donnees) && self::$donnees) {
            self::$donnees->close();
        }
        loginfo("STOP DisponibilitesTest");
    }
    protected function setUp(): void
    {
        $this->users = new Users();
        $this->disponibilites = new Disponibilites();

    }

    private function fetchDisponibilitesRows(): array
    {
        $results = self::$donnees->query('SELECT jour,user,val FROM disponibilites ORDER BY jour,user');
        $out = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $out[] = ['jour' => $row['jour'], 'user' => (int)$row['user'], 'val' => (int)$row['val']];
        }
        return $out;
    }

    public function test_getDisponibilites(): void
    {
        
        $json = $this->disponibilites->getArray();

        $expected = json_decode(file_get_contents('tests/data/dispo.json'),true);

        // Basic structural assertions before full comparison
        $this->assertIsArray($json);
        $this->assertCount(2, $json);
        $this->assertEquals($expected, $json);
    }

    public function test_updateDisponibilite(): void
    {
        // Call protected method using Reflection
        $reflection = new ReflectionClass(get_class($this->disponibilites));
        $method = $reflection->getMethod('update');
        $method->setAccessible(true);

        // initial check
        $rows = $this->fetchDisponibilitesRows();
        $this->assertEquals(1, $rows[0]['val']);

        // update existing record (user 1 on 2025-09-01 -> value 2)
        $input = ['jour' => '2025-09-01', 'usr' => 1, 'value' => 2];
        $method->invoke($this->disponibilites,$input);

        $rows = $this->fetchDisponibilitesRows();
        $found = false;
        foreach ($rows as $r) {
            if ($r['jour'] === '2025-09-01' && $r['user'] === 1) {
                $this->assertEquals(2, $r['val']);
                $found = true;
            }
        }
        $this->assertTrue($found, 'Updated record should be present');

        // update non-existing record -> should insert (user 4 on 2025-09-01)
        $input = ['jour' => '2025-09-01', 'usr' => 4, 'value' => 3];
        $method->invoke($this->disponibilites,$input);

        $rows = $this->fetchDisponibilitesRows();
        $found = false;
        foreach ($rows as $r) {
            if ($r['jour'] === '2025-09-01' && $r['user'] === 4) {
                $this->assertEquals(3, $r['val']);
                $found = true;
            }
        }
        $this->assertTrue($found, 'Inserted record should be present after update on non-existent');
    }

}
?>