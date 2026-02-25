<?php

use PHPUnit\Framework\TestCase;
use dao\DisponibilitesDAO;

class DisponibilitesDAOTest extends TestCase
{
    private $dispoDAOMock;
    private $mockDb;

    protected function setUp(): void
    {
        // Crée un mock de SQLite3
        $this->mockDb = $this->createMock(SQLite3::class);

        // Crée un mock partiel de UsersDAO
        $this->dispoDAOMock = $this->getMockBuilder(DisponibilitesDAO::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['prepareAndExecute', 'fetchAll'])
            ->getMock();

        // Utilise la réflexion pour injecter le mock de SQLite3 dans la propriété db
        $reflection = new ReflectionClass($this->dispoDAOMock);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($this->dispoDAOMock, $this->mockDb);
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
 $sql = "SELECT A.jour,A.user,A.val FROM disponibilites A, users B WHERE A.user=B.id ORDER BY A.jour,B.prenom";

        $mockUsers = [
            ['jour' => '2025-12-13', 'user' => 'Zoe', 'val' => 1],
            ['jour' => '2025-12-01', 'user' => 'Lilou', 'val' => 1],
            ['jour' => '2025-12-01', 'user' => 'Léna', 'val' => 1],
            ['jour' => '2025-12-13', 'user' => 'Gwendoline', 'val' => 1],
            ['jour' => '2025-12-01', 'user' => 'abby', 'val' => 1],
            ['jour' => '2025-12-13', 'user' => 'géraldine', 'val' => 1],
        ];

        $mockResult = $this->createMockSQLite3Result($mockUsers);
        $this->dispoDAOMock->method('prepareAndExecute')
            ->willReturn($mockResult);

        $this->dispoDAOMock->method('fetchAll')
            ->willReturn($mockUsers);

        $result = $this->dispoDAOMock->getAll();

        $this->assertIsArray($result);
        $this->assertCount(6, $result);
        $this->assertEquals('Zoe', $result[0]["user"]);
    }


}
?>
