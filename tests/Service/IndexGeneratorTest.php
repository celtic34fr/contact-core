<?php

namespace Celtic34fr\ContactCore\Tests\Service;

use Celtic34fr\ContactCore\Doctrine\ConnectionConfig;
use Celtic34fr\ContactCore\Service\IndexGenerator;
use Celtic34fr\ContactCore\Service\TntEngine;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class IndexGeneratorTest extends TestCase
{
    private IndexGenerator $indexGenerator;

    protected function setUp(): void
    {
        list($engine, $em, $connectionConfig) = $this->initMock();
        $this->indexGenerator = new IndexGenerator($engine, $em, $connectionConfig);
    }

    public function testGenerate()
    {
        $query = "INSERT INTO table VALUES ('value')";
        $index = "test_index";

        //$this->expectExceptionMessage('failed to execute query');
        $this->expectExceptionMessage('Attempt to assign property "disableOutput" on null');
        $this->indexGenerator->generate($query, $index);
    }

    public function testUpdate()
    {
        $query = "UPDATE table SET column = 'value' WHERE id = :id";
        $index = "test_index";
        $ids = [
            ['id' => 1, 'ope' => 'i'],
            ['id' => 2, 'ope' => 'u'],
            ['id' => 3, 'ope' => 'd'],
        ];

        //$this->expectExceptionMessage('failed to execute query');
        $this->expectExceptionMessage('Attempt to assign property "disableOutput" on null');
        $this->indexGenerator->update($query, $index, $ids);
    }

    public function testUpdateByArray()
    {
        $index = "test_index";
        $entity = ['id' => 1, 'value' => 'example'];
        $operation = 'insert';
        $query = "INSERT INTO table VALUES (:value)";

        //$this->expectExceptionMessage('failed to execute query');
        $this->expectExceptionMessage('Attempt to assign property "disableOutput" on null');
        $this->indexGenerator->updateByArray($index, $entity, $operation, $query);
    }

    public function testIsExistOrCreateIndex()
    {
        $indexName = "test_index";
        $query = "SELECT * FROM table";

        //$this->expectExceptionMessage('failed to execute query');
        $this->expectExceptionMessage('Attempt to assign property "disableOutput" on null');
        $this->indexGenerator->isExistOrCreateIndex($indexName, $query);
    }

    private function initMock() : array
    {
        $params = [];
        $driver = $this->createMock(Driver::class);

        $engine = $this->createMock(TntEngine::class);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $connection = $this->getMockBuilder(Connection::class)
            ->setConstructorArgs([$params, $driver])
            ->getMock();
        $entityManager->method('getConnection')->willReturn($connection);

        $connectionConfig = $this->createMock(ConnectionConfig::class);

        return [$engine, $entityManager, $connectionConfig];        
    }
}