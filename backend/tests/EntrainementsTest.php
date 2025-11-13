<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/env.php';
require_once __DIR__ . '/../api/donnees.php';
require_once __DIR__ . '/../api/utils.php';
require_once __DIR__ . '/../api/entrainements.php';

class EntrainementsTest extends TestCase
{
    private static $donnees;
    private $entrainements;

    public static function setUpBeforeClass(): void
    {
        // load environment for tests
        loadEnv(__DIR__ . '/.env');

        self::$donnees = new Donnees();

        // create schema
        $sql = file_get_contents(__DIR__ . '/../config/createdb.sql');
        self::$donnees->db->exec($sql);

        // seed entrainements
        self::$donnees->db->exec("INSERT INTO entrainements(jour) VALUES('2025-10-08')");
        self::$donnees->db->exec("INSERT INTO entrainements(jour) VALUES('2025-10-01')");
    }

    protected function setUp(): void
    {
        $this->entrainements = new Entrainements(self::$donnees);
    }

    public function testGetArrayReturnsOrderedEntries(): void
    {
        $arr = $this->entrainements->getArray();

        $this->assertIsArray($arr);
        $this->assertCount(2, $arr);

        // Ordered by jour ascending -> first should be 2025-10-01
        $this->assertEquals('2025-10-01', $arr[0]->jour);
        $this->assertEquals('2025-10-08', $arr[1]->jour);
    }

    public function testGetOutputsJson(): void
    {
        // capture echo from get()
        ob_start();
        $this->entrainements->get();
        $output = ob_get_clean();

        // must be valid JSON and match getArray()
        $decoded = json_decode($output, true);
        $this->assertIsArray($decoded);
    
        print($output."\n");

        $expected = json_decode(file_get_contents('tests/data/entrainements.json'),true);
        $this->assertEquals($expected, $decoded);

    }

    public static function tearDownAfterClass(): void
    {
        if (isset(self::$donnees) && self::$donnees->db) {
            self::$donnees->db->close();
        }
    }
}
?>