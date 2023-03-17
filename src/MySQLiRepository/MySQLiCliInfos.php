<?php

namespace Celtic34fr\ContactCore\MySQLiRepository;

use Celtic34fr\ContactCore\MySQLiEntity\MSICliInfos;
use Celtic34fr\ContactCore\Trait\MySQLiTrait;

/**
 * Class MySQLiCrmCliInfos : accès via mysqli à l'entité CrmCliInfos
 *
 * find(int $table_id)                                          : recherche directe d'un enregistrement par champ ID
 */
class MySQLiCliInfos
{
    use MySQLiTrait {
        MySQLiTrait::__construct as mysqli_construct;
    }
    /**
     * @var mixed|string
     */
    private mixed $dbPrefix;
    private string $dbName;
    protected array $structure;

    /**
     */
    public function __construct()
    {
        $this->mysqli_construct();
        $this->dbPrefix = $_ENV['DB_PREFIX'] ?? "";
        $this->dbName = $this->dbPrefix . "cli_infos";
        $this->className = MSICliInfos::class;
        $this->structure = $this->getTableColumns();
    }

}
