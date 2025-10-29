<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once("api/env.php");

final class IndexTest extends TestCase
{
    private static string $apiDir;
    private $outputBufferLevel;

    public static function setUpBeforeClass(): void
    {
        // load environment for tests
        //loadEnv('api/.env');
        putenv("REPERTOIRE_DATA=../data/");
        putenv("ACTIVELOG=true");
        putenv("DBLOCATION=../data/basketu11.db");
    }

    protected function setUp(): void
    {
        // Store initial output buffer level
        $this->outputBufferLevel = ob_get_level();
        
        // reset superglobals
        $_GET = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SESSION = []; // Reset session for each test
    }

    protected function tearDown(): void
    {
        // Clean up any remaining output buffers created during test
        while (ob_get_level() > $this->outputBufferLevel) {
            ob_end_clean();
        }
    }

    private function launchTestGet($arr) 
    {
        $_GET = $arr;
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        include("api/index.php");
        $output = ob_get_clean();

        return $output;
    }

    public function testUsers(): void
    {
        $output = $this->launchTestGet(['users' => null]);
        
        $this->assertIsString($output);
        $decoded = json_decode($output, true);
        $this->assertNotNull($decoded, 'Output should be valid JSON for users');
        $this->assertIsArray($decoded, 'Output should be an array');
    }

    public function testMatchs(): void
    {
        $output = $this->launchTestGet(['matchs' => null]);
        
        $this->assertIsString($output);
        $decoded = json_decode($output, true);
        $this->assertNotNull($decoded, 'Output should be valid JSON for matchs');
        $this->assertIsArray($decoded, 'Output should be an array');
    }

    public function testMatch(): void
    {
        $_SESSION['islogged'] = "1"; // Simulate logged in user
        $output = $this->launchTestGet(['match' => 1]);
        
        $this->assertIsString($output);
        $decoded = json_decode($output, true);
        $this->assertNotNull($decoded, 'Output should be valid JSON for match');
    }

    public function testMatchSaveCopp(): void
    {
        $_SESSION['islogged'] = true; // Simulate logged in user
        $output = $this->launchTestGet(['matchsavecopp' => null]);
        
        $this->assertIsString($output);
        $decoded = json_decode($output, true);
        $this->assertNotNull($decoded, 'Output should be valid JSON for matchsavecopp');
    }

    public function testMatchSaveCsel(): void
    {
        $output = $this->launchTestGet(['matchsavecsel' => null]);
        
        $this->assertIsString($output);
        $decoded = json_decode($output, true);
        $this->assertNotNull($decoded, 'Output should be valid JSON for matchsavecsel');
    }

    public function testEntrainements(): void
    {
        $_SESSION['islogged'] = true; // Simulate logged in user
        $output = $this->launchTestGet(['entrainements' => null]);
        
        $this->assertIsString($output);
        $decoded = json_decode($output, true);
        $this->assertNotNull($decoded, 'Output should be valid JSON for entrainements');
    }

    public function testPresences(): void
    {
        $_SESSION['islogged'] = true; // Simulate logged in user
        $output = $this->launchTestGet(['presences' => null]);
        
        $this->assertIsString($output);
        $decoded = json_decode($output, true);
        $this->assertNotNull($decoded, 'Output should be valid JSON for presences');
    }

    public function testOppositions(): void
    {
        $_SESSION['islogged'] = true; // Simulate logged in user
        $output = $this->launchTestGet(['oppositions' => 1]);
        
        $this->assertIsString($output);
        $decoded = json_decode($output, true);
        $this->assertNotNull($decoded, 'Output should be valid JSON for oppositions');
    }

    public function testDisponibilites(): void
    {
        $output = $this->launchTestGet(['disponibilites' => null]);
        
        $this->assertIsString($output);
        $decoded = json_decode($output, true);
        $this->assertNotNull($decoded, 'Output should be valid JSON for disponibilites');
    }

    public function testSelections(): void
    {
        $_SESSION['islogged'] = true; // Simulate logged in user
        $output = $this->launchTestGet(['selections' => null]);
        
        $this->assertIsString($output);
        $decoded = json_decode($output, true);
        $this->assertNotNull($decoded, 'Output should be valid JSON for selections');
    }

    public function testIsLogged(): void
    {
        $output = $this->launchTestGet(['islogged' => null]); 
        
        $this->assertIsString($output);
        $this->assertStringContainsString('0', $output);

        $_SESSION['islogged'] = "1";
        $output = $this->launchTestGet(['islogged' => null]); 

        $this->assertIsString($output);
        $this->assertStringContainsString('1', $output);  
    }

    public function testBadRequest(): void
    {
        $output = $this->launchTestGet(['truc' => null]); 

        $this->assertIsString($output);
        $this->assertStringContainsString('Bad Request', $output);
    }
}
?>