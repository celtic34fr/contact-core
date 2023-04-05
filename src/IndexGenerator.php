<?php

namespace Celtic34fr\ContactCore;

use Doctrine\ORM\EntityManagerInterface;
use TeamTNT\TNTSearch\TNTSearch;

class IndexGenerator
{

    private TntEngine $engine;
    private EntityManagerInterface $em;

    public function __construct(TntEngine $engine, EntityManagerInterface $em)
    {
        $this->engine = $engine;
        $this->em = $em;
    }

    public function generate(string $query, string $index): void
    {
        $tnt = $this->engine->get();
        $index = $tnt->createIndex("$index.index");
        $index->disableOutput = true;
        $index->query($query);
        $index->disableOutput = true;
        $index->steps = 1;
        $index->run();
    }

    /**
     * @param string $query     Sql pour insertion dans l'index
     * @param string $index     Nom de l'index
     * @param array $ids        Tableau des identifiants d'enregistrement couple de valeur :
     *                              id      identifiant de l'enregistrement
     *                              ope     type d'opération :
     *                                  i       pour insertion
     *                                  u       pour mise à jour
     *                                  d       pour suppression
     */
    public function update(string $query, string $index, array $ids): void
    {
        if ($ids && is_array($ids)) {
            $tnt = new TNTSearch;
            $tnt->loadConfig([]);
            $tnt->selectIndex("$index.index");
            $index = $tnt->getIndex();

            foreach ($ids as $item) {
                if ($item['ope'] !== 'd') {
                    $record = $this->em->getConnection()->executeQuery($query. "AND idx.id = ".$item['id']);
                    dd($record);
                    $arrayRecordIndex = [];
                }
                switch (strtolower($item['ope'])) {
                    case 'i':
                        /** Recherche enregistrement à traiter */
                        $index->insert($arrayRecordIndex);
                        break;
                    case 'u':
                        /** Recherche enregistrement à traiter */
                        $index->update($item['id'], $arrayRecordIndex);
                        break;
                    case 'd':
                        $index->delete($item['id']);
                        break;
                }
            }
        }
    }
}