<?php

use PHPUnit\Framework\TestCase;
use dao\StaffDAO;

class StaffDAOTest extends TestCase
{
    private $staffDAOMock;
    private $mockDb;

    protected function setUp(): void
    {
        // Crée un mock de SQLite3
        $this->mockDb = $this->createMock(SQLite3::class);

        // Crée un mock partiel de StaffDAO
        $this->staffDAOMock = $this->getMockBuilder(StaffDAO::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['prepareAndExecute', 'fetchAll'])
            ->getMock();

        // Utilise la réflexion pour injecter le mock de SQLite3 dans la propriété db
        $reflection = new ReflectionClass($this->staffDAOMock);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($this->staffDAOMock, $this->mockDb);
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
        $mockStaff = [
            ['id' => 1, 'prenom' => 'Alice', 'nom' => 'Martin', 'licence' => 'LIC456', 'role' => 'entraineur'],
            ['id' => 2, 'prenom' => 'Bob', 'nom' => 'Smith',  'licence' => 'LIC789', 'role' => 'otm'],
        ];

        $mockResult = $this->createMockSQLite3Result($mockStaff);
        $this->staffDAOMock->method('prepareAndExecute')
            ->willReturn($mockResult);

        $this->staffDAOMock->method('fetchAll')
            ->willReturn($mockStaff);

        $result = $this->staffDAOMock->getAll();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Alice', $result[0]['prenom']);
    }

    // Test de getById()
    public function testGetById()
    {
        $mockStaff = [
            ['id' => 1, 'prenom' => 'Alice', 'nom' => 'Martin', 'licence' => 'LIC456', 'role' => 'entraineur'],
        ];

        $mockResult = $this->createMockSQLite3Result($mockStaff);
        $this->staffDAOMock->method('prepareAndExecute')
            ->willReturn($mockResult);

        $this->staffDAOMock->method('fetchAll')
            ->willReturn($mockStaff);

        $result = $this->staffDAOMock->getById(1);

        $this->assertIsArray($result);
        $this->assertEquals('Alice', $result['prenom']);
    }

    // Test de create()
    public function testCreate()
    {
        $this->staffDAOMock->method('prepareAndExecute')
            ->willReturn(true);

        $this->mockDb->method('lastInsertRowID')
            ->willReturn(42);

        $newId = $this->staffDAOMock->create('Alice', 'Martin', 'LIC456', 'Entraineur');

        $this->assertEquals(42, $newId);
    }

    // Test de update()
    public function testUpdate()
    {
        $this->staffDAOMock->method('prepareAndExecute')
            ->willReturn(true);

        $this->mockDb->method('changes')
            ->willReturn(1);

        $changes = $this->staffDAOMock->update(1, 'Alice', 'Martin', 'LIC456', 'OTM');

        $this->assertEquals(1, $changes);
    }

    // Test de delete()
    public function testDelete()
    {
        $this->staffDAOMock->method('prepareAndExecute')
            ->willReturn(true);

        $this->mockDb->method('changes')
            ->willReturn(4);

        $changes = $this->staffDAOMock->delete(1);

        $this->assertEquals(4, $changes);
    }
}
?>
