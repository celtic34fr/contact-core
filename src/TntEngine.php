<?php

declare(strict_types=1);

namespace Celtic34fr\ContactCore;

use Celtic34fr\ContactCore\Doctrine\ConnectionConfig;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use TeamTNT\TNTSearch\TNTSearch;

class TntEngine
{
    /** @var TNTSearch|null */
    private $tnt;

    public function __construct(private ConnectionConfig $connectionConfig, private ExtensionConfig $extConfig)
    {
        $this->connectionConfig = $connectionConfig;
        $this->extConfig = $extConfig;
    }

    public function get(): TNTSearch
    {
        if (! $this->tnt) {
            $this->tnt = new TNTSearch();
            $this->tnt->loadConfig($this->connectionConfig->getConfig());

            $config = $$this->extConfig->get('celtic34fr-contactcore');

            $this->tnt->fuzziness = $config['fuzzy']['enabled'] ?? false;
            $this->tnt->fuzzy_distance = $config['fuzzy']['distance'] ?? 2;
            $this->tnt->fuzzy_prefix_length = $config['fuzzy']['prefix_length'] ?? 2;
            $this->tnt->fuzzy_max_expansions = $config['fuzzy']['max_expansions'] ?? 50;
        }

        return $this->tnt;
    }
}
