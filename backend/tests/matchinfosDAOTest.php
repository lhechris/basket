<?php

include_once("api/dao/MatchInfosDAO.php");

use PHPUnit\Framework\TestCase;
use dao\MatchInfosDAO;

class MatchInfosDAOTest extends TestCase
{
    private $matchinfosDAOMock;
    private $mockDb;

    protected function setUp(): void
    {
        // Crée un mock de SQLite3
        $this->mockDb = $this->createMock(SQLite3::class);

        // Crée un mock partiel de MatchInfosDAO
        $this->matchinfosDAOMock = $this->getMockBuilder(MatchInfosDAO::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['prepareAndExecute', 'fetchAll'])
            ->getMock();

        // Utilise la réflexion pour injecter le mock de SQLite3 dans la propriété db
        $reflection = new ReflectionClass($this->matchinfosDAOMock);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($this->matchinfosDAOMock, $this->mockDb);
    }

    // Crée un mock de SQLite3Result
    private function createMockSQLite3Result(array $data)
    {
        return new class($data) extends SQLite3Result {
            private $data;
            private $index = 0;

            public function __construct(array $data)
            {
                $this->data = $data;
            }
            
            #[\ReturnTypeWillChange]
            public function fetchArray(int $mode = SQLITE3_BOTH)
            {
                return $this->data[$this->index++] ?? null;
            }
        };
    }

    // Test de getAll()
    public function getByMatch()
    {
        $mockUsers = [
            ["user" => 1, "opposition" =>"AAA", "numero" => 99, "commentaire" => "non", "prenom" => "Luigi", "licence" => "LIC333", "nom" => "Bros"],
            ["user" => 2, "opposition" =>"BBB", "numero" => 55, "commentaire" => "bof", "prenom" => "Mario", "licence" => "LIC888", "nom" => "Bros"],
        ];

        $mockResult = $this->createMockSQLite3Result($mockUsers);
        $this->matchinfosDAOMock->method('prepareAndExecute')
            ->willReturn($mockResult);

        $this->matchinfosDAOMock->method('fetchAll')
            ->willReturn($mockUsers);

        $result = $this->matchinfosDAOMock->getAll();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Mario', $result[0]['prenom']);
    }

    public function testExistsTrue()
    {
        $mockmi = [[1]];

        $mockResult = $this->createMockSQLite3Result($mockmi);
        $this->matchinfosDAOMock->method('prepareAndExecute')
            ->willReturn($mockResult);

        $this->matchinfosDAOMock->method('fetchAll')
            ->willReturn($mockmi);

        $result = $this->matchinfosDAOMock->exists(1,1);

        $this->assertTrue($result);
    }

    public function testExistsFalse()
    {
        $mockmi = [[0]];

        $mockResult = $this->createMockSQLite3Result($mockmi);
        $this->matchinfosDAOMock->method('prepareAndExecute')
            ->willReturn($mockResult);

        $this->matchinfosDAOMock->method('fetchAll')
            ->willReturn($mockmi);

        $result = $this->matchinfosDAOMock->exists(1,1);

        $this->assertFalse($result);
    }


    // Test de create()
    public function testCreate()
    {
        $this->matchinfosDAOMock->method('prepareAndExecute')
            ->willReturn(true);

        $this->mockDb->method('lastInsertRowID')
            ->willReturn(42);

        $newId = $this->matchinfosDAOMock->create(1, 1, "", 99, "blabla");

        $this->assertEquals(42, $newId);
    }

    // Test de update()
    public function testUpdate()
    {
        $this->matchinfosDAOMock->method('prepareAndExecute')
            ->willReturn(true);

        $this->mockDb->method('changes')
            ->willReturn(1);

        $changes = $this->matchinfosDAOMock->update(1, 1, "", 99, "blabla");

        $this->assertEquals(1, $changes);
    }

    // Test de delete()
    public function testDelete()
    {
        $this->matchinfosDAOMock->method('prepareAndExecute')
            ->willReturn(true);

        $this->mockDb->method('changes')
            ->willReturn(4);

        $changes = $this->matchinfosDAOMock->delete(1,1);

        $this->assertEquals(4, $changes);
    }
}
?>
