<?php

include_once("api/dao/UsersDAO.php");

use PHPUnit\Framework\TestCase;
use dao\UsersDAO;

class UsersDAOTest extends TestCase
{
    private $usersDAOMock;
    private $mockDb;

    protected function setUp(): void
    {
        // Crée un mock de SQLite3
        $this->mockDb = $this->createMock(SQLite3::class);

        // Crée un mock partiel de UsersDAO
        $this->usersDAOMock = $this->getMockBuilder(UsersDAO::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['prepareAndExecute', 'fetchAll'])
            ->getMock();

        // Utilise la réflexion pour injecter le mock de SQLite3 dans la propriété db
        $reflection = new ReflectionClass($this->usersDAOMock);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($this->usersDAOMock, $this->mockDb);
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
    public function testGetAll()
    {
        $mockUsers = [
            ['id' => 1, 'prenom' => 'Alice', 'nom' => 'Martin', 'equipe' => 2, 'licence' => 'LIC456', 'otm' => 0, 'charte' => 1],
            ['id' => 2, 'prenom' => 'Bob', 'nom' => 'Smith', 'equipe' => 1, 'licence' => 'LIC789', 'otm' => 1, 'charte' => 0],
        ];

        $mockResult = $this->createMockSQLite3Result($mockUsers);
        $this->usersDAOMock->method('prepareAndExecute')
            ->willReturn($mockResult);

        $this->usersDAOMock->method('fetchAll')
            ->willReturn($mockUsers);

        $result = $this->usersDAOMock->getAll();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Alice', $result[0]['prenom']);
    }

    // Test de getPlayersByTeam()
    public function testGetPlayersByTeam()
    {
        $mockPlayers = [
            ['prenom' => 'Alice', 'equipe' => 2],
            ['prenom' => 'Bob', 'equipe' => 2],
        ];

        $mockResult = $this->createMockSQLite3Result($mockPlayers);
        $this->usersDAOMock->method('prepareAndExecute')
            ->willReturn($mockResult);

        $this->usersDAOMock->method('fetchAll')
            ->willReturn($mockPlayers);

        $result = $this->usersDAOMock->getPlayersByTeam(2);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Alice', $result[0]['prenom']);
    }

    // Test de getById()
    public function testGetById()
    {
        $mockUser = [
            ['id' => 1, 'prenom' => 'Alice', 'nom' => 'Martin', 'equipe' => 2, 'licence' => 'LIC456', 'otm' => 0, 'charte' => 1],
        ];

        $mockResult = $this->createMockSQLite3Result($mockUser);
        $this->usersDAOMock->method('prepareAndExecute')
            ->willReturn($mockResult);

        $this->usersDAOMock->method('fetchAll')
            ->willReturn($mockUser);

        $result = $this->usersDAOMock->getById(1);

        $this->assertIsArray($result);
        $this->assertEquals('Alice', $result['prenom']);
    }

    // Test de create()
    public function testCreate()
    {
        $this->usersDAOMock->method('prepareAndExecute')
            ->willReturn(true);

        $this->mockDb->method('lastInsertRowID')
            ->willReturn(42);

        $newId = $this->usersDAOMock->create('Alice', 'Martin', 2, 'LIC456', 0, 1);

        $this->assertEquals(42, $newId);
    }

    // Test de update()
    public function testUpdate()
    {
        $this->usersDAOMock->method('prepareAndExecute')
            ->willReturn(true);

        $this->mockDb->method('changes')
            ->willReturn(1);

        $changes = $this->usersDAOMock->update(1, 'Alice', 'Martin', 2, 'LIC456', 0, 1);

        $this->assertEquals(1, $changes);
    }

    // Test de delete()
    public function testDelete()
    {
        $this->usersDAOMock->method('prepareAndExecute')
            ->willReturn(true);

        $this->mockDb->method('changes')
            ->willReturn(4);

        $changes = $this->usersDAOMock->delete(1);

        $this->assertEquals(4, $changes);
    }
}
?>
