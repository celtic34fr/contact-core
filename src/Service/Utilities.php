<?php

namespace Celtic34fr\ContactCore\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

class Utilities
{
    private EntityManagerInterface $entityManager;
    private ?\Doctrine\DBAL\Schema\AbstractSchemaManager $schemaManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->schemaManager = $entityManager->getConnection()->getSchemaManager();
    }

    /**
     * @throws Exception
     */
    public function existsTable(string $table_name): bool
    {
        if ($this->schemaManager) {
            return $this->schemaManager->tablesExist([$table_name]);
        }
        throw new Exception("Can't obtain SchemaManager from EntityManager, verify the database parameters");
    }

    /**
     * @throws Exception
     */
    public function existsTables(array $tables_name): bool
    {
        if ($this->schemaManager) {
        return $this->schemaManager->tablesExist($tables_name);
        }
        throw new Exception("Can't obtain SchemaManager from EntityManager, verify the database parameters");
    }
}