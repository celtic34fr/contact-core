<?php

namespace Celtic34fr\ContactCore\Service;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Celtic34fr\ContactCore\Service\TntEngine;

/** Classe permettant la mise en oeuvre de l'outil TNTSearch à partir de la class TntEngine */
class IndexGenerator
{
    private Connection $connexion;

    public function __construct(private TntEngine $engine, private EntityManagerInterface $em)
    {
        $this->engine = $engine;
        $this->em = $em;
        $this->connexion = $this->em->getConnection();
    }

    /** 
     * méthode de génération d'un index de recherche
     * @param string $query Sql pour insertion dans l'index
     * @param string $index Nom de l'index
     */
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
     * méthode vidant à mettre à jour un index en fonction d'informations founies (tableau ids)
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

    /**
     * méthode visant à mettre à jour un index en fonction d'informations fournies pour un type d'opération
     * @param string $index     Nom de l'index
     * @param array $entity     tableau des valeur utile pour agir sur TntSearch indexes
     * @param array $ope        Type d'opération :
     *                                  i       pour insertion
     *                                  u       pour mise à jour
     *                                  d       pour suppression
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function updateByArray(string $index, array $entity, string $operation): void
    {
        $tnt = $this->engine->get();
        $tnt->selectIndex("$index.index");
        $index = $tnt->getIndex();
        $operation = substr($operation, 0, 1);

        switch($operation) {
            case 'i':
                $index->insert($entity);
                break;
            case 'u':
                $index->insert($entity['id'], $entity);
                break;
            case 'd':
                $index->insert($entity['id']);
                break;
            }
    }
}