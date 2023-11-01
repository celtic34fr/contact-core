<?php

namespace Bolt\Extension\Celtic34fr\ContactCore\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

/** classe réunissant des méthodes utilitraires pour le projet */
class UtilitiesOld
{
    private ?\Doctrine\DBAL\Schema\AbstractSchemaManager $schemaManager;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->schemaManager = $entityManager->getConnection()->getSchemaManager();
    }

    /**
     * m"thode de test d'existance d'une table dans l'ORM Doctrine
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
     * méthode de test d'existance de tables dans l'ORM Doctrine
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