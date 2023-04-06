<?php

namespace Celtic34fr\ContactCore;

use Doctrine\DBAL\FetchMode;
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
        $this->connexion = $this->em->getConnection();
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
     * @param string $query Sql pour insertion dans l'index
     * @param string $index Nom de l'index
     * @param array $ids Tableau des identifiants d'enregistrement couple de valeur :
     *                              id      identifiant de l'enregistrement
     *                              ope     type d'opération :
     *                                  i       pour insertion
     *                                  u       pour mise à jour
     *                                  d       pour suppression
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function update(string $query, string $index, array $ids): void
    {
        if ($ids && is_array($ids)) {
            $tnt = $this->engine->get();
            $tnt->selectIndex("$index.index");
            $index = $tnt->getIndex();

            foreach ($ids as $item) {
                $result = [];
                if ($item['ope'] !== 'd') {
                    $query .= " AND idx.id = :idx";
                    $stmt = $this->connexion->prepare($query);
                    $stmt->bindValue('idx', $item['id']);
                    $result = $stmt->executeQuery()->fetchAllAssociative();
                }
                if ($result) {
                    switch (strtolower($item['ope'])) {
                        case 'i':
                            /** Recherche enregistrement à traiter */
                            $index->insert($result);
                            break;
                        case 'u':
                            /** Recherche enregistrement à traiter */
                            $index->update($item['id'], $result);
                            break;
                        case 'd':
                            $index->delete($item['id']);
                            break;
                    }
                }
            }
        }
    }
}