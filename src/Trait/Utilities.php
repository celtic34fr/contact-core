<?php

namespace Celtic34fr\ContactCore\Trait;

use Exception;

trait Utilities
{

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

    /**
     * méthode visant à mettre la première lettre d'un prénom, composé ou non, en majuscule
     */
    public function ucfirstPrenom(string $field) : string
    {
        $tab = explode('-', $field);
        $rlst = "";
        foreach ($tab as $elt) {
            $rlst .= ucfirst(trim($elt)). "-";
        }
        $rlst = substr($rlst, 0, strlen($rlst - 1));
        return $rlst;
    }

}
