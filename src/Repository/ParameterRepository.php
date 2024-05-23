<?php

namespace Celtic34fr\ContactCore\Repository;

use Celtic34fr\ContactCore\Entity\Parameter;
use Celtic34fr\ContactCore\EntityRedefine\ActivitySector;
use Celtic34fr\ContactCore\EntityRedefine\RelationCategory;
use Celtic34fr\ContactCore\EntityRedefine\SocialNetwork;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parameter>
 *
 * @method Parameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parameter[]    findAll()
 * @method Parameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParameterRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameter::class);
    }

    /**
     * @param Parameter $entity
     * @param boolean $flush
     * @return void
     */
    public function save(Parameter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Parameter $entity
     * @param boolean $flush
     * @return void
     */
    public function remove(Parameter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Parameter $entity
     * @param boolean $flush
     * @return void
     */
    public function update(Parameter $previous, $value, bool $flush = false): void
    {
        $previous->setUpdatedAt(new DateTimeImmutable('now'));
        $this->save($previous, $flush);
        $entity = new Parameter();
        $entity->setCle($previous->getCle());
        $entity->setOrd($previous->getOrd());
        $entity->setValeur($value);
        $entity->setCreatedAt(new DateTimeImmutable('now'));
        $entity->setUpdatedAt(null);
        $this->save($entity, $flush);
    }

    /**
     * @return array
     */
    public function getNameParametersList(): array
    {
        $nameList = [];
        $rslt = $this->createQueryBuilder('p')
            ->where('p.ord = 0')
            ->orderBy('p.cle', 'ASC')
            ->getQuery()
            ->getResult();
        if ($rslt) {
            foreach ($rslt as $item) {
                /** prise de toutes les listes de paramètres sauf celle préfixée par 'SYS' pour système */
                if (strpos(strtoupper($item->getCle()), 'SYS') === false) {
                    $occur = [
                        'id' => $item->getId(),
                        'name' => $item->getCle(),
                        'description' => $item->getValeur(),
                        'created_at' => $item->getCreatedAt(),
                        'updated_at' => !$item->isEmptyUpdatedAt() ? $item->getUpdatedAt() : null,
                    ];
                    $qb = $this->createQueryBuilder('p')
                        ->where('p.cle = :name')
                        ->setParameter('name', $item->getCle())
                        ->getQuery()
                        ->getResult();
                    $occur['valeurs'] = $qb ? sizeof($qb) - 1 : 0;
                    $nameList[] = $occur;
                }
            }
        }
        return $nameList;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getValuesParamterList(string $name): array
    {
        $paramList = [];
        $rslt = $this->findBy(['cle' => $name], ['ord' => 'ASC']);
        if (!$rslt) return [];
        foreach ($rslt as $item) {
            if ((int) $item->getOrd() > 0)
                $paramList[(int) $item->getOrd() - 1] = $item;
        }
        return $paramList;
    }

    /**
     * @param string $name
     * @param array $valeurs
     * @param string|null $description
     * @return void
     */
    public function recordParamtersList(string $name, array $valeurs, string $description = null): void
    {
        $entity = $this->findOneBy(['cle' => $name, 'ord' => 0]);
        if (!$entity) $entity = new Parameter();
        $entity->setCle($name);
        $entity->setOrd(0);
        $entity->setValeur($description ?? $name);
        $this->save($entity, false);

        foreach ($valeurs as $idx => $valeur) {
            $entity = $this->findOneBy(['cle' => $name, 'ord' => ((int) $idx + 1)]);
            if (!$entity) $entity = new Parameter();

            $entity->setCle($name);
            $entity->setOrd((int) $idx + 1);
            $entity->setValeur($valeur);

            if (!$entity->getId()) $this->save($entity, false);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $cle
     * @return void
     */
    public function reorgValues(string $cle): void
    {
        $values = $this->createQueryBuilder('p')
            ->where("p.cle = :cle")
            ->orderBy("p.ord", "ASC")
            ->setParameter('cle', $cle)
            ->getQuery()
            ->getResult();
        if ($values) {
            $idxOrd = 0;
            /** @var Parameter $value */
            foreach ($values as $value) {
                $value->setOrd($idxOrd);
                $this->getEntityManager()->flush();
                $idxOrd++;
            }
        }
    }

    /**
     * @param string $cle
     * @return null|array
     */
    public function findValidItemsByCle(string $cle): mixed
    {
        $db = $this->createQueryBuilder('p');
        return $db
            ->where('p.cle = :cle')
            ->andWhere('p.ord > 0')
            ->andWhere($db->expr()->isNull('p.updated_at'))
            ->orderBy('p.ord', 'ASC')
            ->setParameter('cle', $cle)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $cle
     * @return null|array
     */
    public function findAllItemsByCle(string $cle): mixed
    {
        $db = $this->createQueryBuilder('p');
        return $db
            ->where('p.cle = :cle')
            ->andWhere('p.ord > 0')
            ->orderBy('p.ord', 'ASC')
            ->addOrderBy('p.created_at', 'ASC')
            ->setParameter('cle', $cle)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @return void
     */
    public function findByPartialFields(array $criteria, array $orderBy = null)
    {
        $qb = $this->createQueryBuilder('p');
        foreach ($criteria as $cle => $partial) {
            if ($cle == "valeur") $partial = '"'.$partial;
            $qb->andWhere("p.$cle LIKE '$partial'");
        }
        if ($orderBy) {
            foreach ($orderBy as $cle => $order) {
                $qb->orderBy("p.$cle", $order);
            }
        }
        return $qb->getQuery()->getResult();
    }

    /** ---------------------------------------------------- 
     * specificans methods according system list managment *
     * -----------------------------------------------------
     */

     /**
      * Social Networks
      */

      /**
     * retrieve current declared list of Social Networks with URL of favicon (onluy active)
     * @return array
     */
    public function findValidSocialNetworks():  array
    {
        $socialNetworks = [];
        $values = $this->findValidItemsByCle(SocialNetwork::CLE);
        if ($values) {
            foreach ($values as $value) {
                /** @var SocialNetwork $socialNetwork */
                $socialNetwork = $this->ParameterToSocialNetworks($value);
                $socialNetworks[] = $this->formatSocialNetworks($socialNetwork);
            }
        }        
        return $socialNetworks;
    }

      /**
     * retrieve global list of Social Networks with URL of favicon (active and disabled)
     * @return array
     */
    public function findAllSocialNetworks(): array
    {
        $socialNetworks = [];
        $values = $this->findAllItemsByCle(SocialNetwork::CLE);
        if ($values) {
            foreach ($values as $value) {
                /** @var SocialNetwork $socialNetwork */
                $socialNetwork = $this->ParameterToSocialNetworks($value);
                $socialNetworks[] = $this->formatSocialNetworks($socialNetwork);
            }
        }
        return $socialNetworks;
    }

    /**
     * @param string $name
     * @return array|null
     */
    public function findSocialNetworkByName(string $name): ?array
    {
        $socialNetwork = $this->findByPartialFields([
            'cle' => SocialNetwork::CLE,
            'valeur' => $name.'%'
        ], [
            'created_at' => 'DESC',
        ]);
        $socialNetworkItem = $socialNetwork[0] ?? null;
        if ($socialNetworkItem) {
            /** @var SocialNetwork $socialNetwork */
            $socialNetwork = $this->ParameterToSocialNetworks($socialNetworkItem);
            return $this->formatSocialNetworks($socialNetwork);
        }
        return null;
    }

    /**
     * retrieve icon for 1 social network by name
     * @param string $name
     * @return string|bool
     */
    public function findSocialNetworkIcon(string $name): mixed
    {
        $socialNetwork = $this->findByPartialFields([
            'cle' => SocialNetwork::CLE,
            'valeur' => $name.'%'
        ], [
            'created_at' => 'DESC',
        ]);
        $socialNetworkItem = $this->findSocialNetworkByName($name);
        $socialNetworkLogo = $socialNetworkItem[0] ? $socialNetworkItem[0]['logoID'] : null;
        return $socialNetworkLogo ?? false;
    }

    /**
     * @return int
     */
    public function findNextOrdSocialNetworks(): int
    {
        $values = $this->findAllItemsByCle(SocialNetwork::CLE);
        $ord = null;
        /** @var Parameter $value */
        foreach ($values as $value) {
            if ($ord != $value->getOrd()) $ord = $value->getOrd();
        }
        return ((int)$ord +1);
    }

    /**
     * @return array
     */
    public function findValidRelationCategories(): array
    {
        $relationCategories = [];
        $values = $this->findValidItemsByCle(RelationCategory::CLE);
        if ($values) {
            foreach ($values as $value) {
                /** @var RelationCategory $relationCategory */
                $relationCategory = $this->ParameterToRelationCategories($value);
                $relationCategories[] = $this->formatRelationCategories($relationCategory);
            }
        }
        return $relationCategories;
    }

    /**
     * @return array
     */
    public function findAllRelationCategories(): array
    {
        $relationCategories = [];
        $values = $this->findAllItemsByCle(RelationCategory::CLE);
        if ($values) {
            foreach ($values as $value) {
                /** @var RelationCategory $relationCategory */
                $relationCategory = $this->ParameterToRelationCategories($value);
                $relationCategories[] = $this->formatRelationCategories($relationCategory);
            }
        }
        return $relationCategories;
    }

    /**
     * @return int
     */
    public function findNextOrdRelationCategories(): int
    {
        $values = $this->findAllItemsByCle(RelationCategory::CLE);
        $ord = null;
        /** @var Parameter $value */
        foreach ($values as $value) {
            if ($ord != $value->getOrd()) $ord = $value->getOrd();
        }
        return ((int)$ord +1);
    }

    /**
     * @param string $name
     * @return array|null
     */
    public function findRelationCategoryByName(string $name): ?array
    {
        $relationCategories = $this->findByPartialFields([
            'cle' => RelationCategory::CLE,
            'valeur' => $name.'%'
        ], [
            'created_at' => 'DESC',
        ]);
        $relationCategoryItem = $relationCategories[0] ?? null;
        if ($relationCategoryItem) {
            /** @var RelationCategory $relationCategory */
            $relationCategory = $this->ParameterToRelationCategories($relationCategoryItem);
            return $this->formatRelationCategories($relationCategory);
        }
        return null;
    }

    public function findValidActivitiesSectors(): array
    {
        $activitiesSectors = [];
        $values = $this->findValidItemsByCle(ActivitySector::CLE);
        if ($values) {
            $activitiesSectors = $this->formatActivitiesList($values);
            $activitiesSectors = $this->buildActivitiesTree($activitiesSectors);
        }
        return $activitiesSectors;
    }

    public function findAllActivitiesSectors(): array
    {
        $activitiesSectors = [];
        $valeurs = $this->findAllItemsByCle(ActivitySector::CLE);
        if ($valeurs) {
            $activitiesSectors = $this->formatActivitiesList($valeurs);
            $activitiesSectors = $this->buildActivitiesTree($activitiesSectors);
        }
        return $activitiesSectors;
    }
    
    //    /**
    //     * @return Parameter[] Returns an array of Parameter objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Parameter
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * @param array $values
     * @return SocialNetwork
     */
    private function ParameterToSocialNetworks(Parameter $parameter) : SocialNetwork
    {
        return new SocialNetwork($parameter);
    }

    /**
     * @param array $values
     * @return SocialNetwork
     */
    private function ParameterToRelationCategories(Parameter $parameter) : RelationCategory
    {
        return new RelationCategory($parameter);
    }

    /**
     * @param $values
     * @return array
     */
    private function formatSocialNetworks($values) : array
    {
        $socialNetworks = [];
        if ($values) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    $socialNetwork = new SocialNetwork($value);
                    $socialNetworks[$socialNetwork->getId()] = [
                        'id' => $socialNetwork->getId(),
                        'cle' => $socialNetwork->getCle(),
                        'ord' => $socialNetwork->getOrd(),
                        'name' => $socialNetwork->getName(),
                        'logoID' => $socialNetwork->getLogoID(),
                        'created' => $socialNetwork->getCreatedAt(),
                        'updated' => $socialNetwork->getUpdatedAt(),
                    ];
                }
            } elseif ($values instanceof SocialNetwork) {
                $socialNetworks = $values->getAsArray();
            }
        }
        return $socialNetworks;
    }

    /**
     * @param $values
     * @return array
     */
    private function formatRelationCategories($values): array
    {
        $relationCategories = [];
        if ($values) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    $category = new RelationCategory($value);
                    $activities[$category->getId()] = [
                        'id' => $category->getId(),
                        'cle' => $category->getCle(),
                        'ord' =>$category->getOrd(),
                        'created' => $category->getCreatedAt(),
                        'updated' => $category->getUpdatedAt(),
                        'category' => $category->getCategory(),
                        'description' => $category->getDescription(),
                    ];
                }
            }elseif ($values instanceof RelationCategory){
                return $values->getAsArray();
            }
        }
        return $relationCategories;
    }

    /**
     * @param array $values
     * @return array
     */
    private function formatActivitiesList(array $values) : array
    {
        $activities = [];
        if ($values) {
            foreach ($values as $value) {
                $activity = new ActivitySector($value);
                $activities[$activity->getId()] = [
                    'id' => $activity->getId(),
                    'cle' => $activity->getCle(),
                    'ord' =>$activity->getOrd(),
                    'created' => $activity->getCreatedAt(),
                    'updated' => $activity->getUpdatedAt(),
                    'name' => $activity->getName(),
                    'description' => $activity->getDescription(),
                    'parentId' => $activity->getParentId(),
                ];
            }
        }
        return $activities;
    }

    /**
     * @param array $activities
     * @return array
     */
    private function buildActivitiesTree(array $activities) : array
    {
        $activitiesTree = [];
        if ($activities) {
            /**
             * @var ActivitySector $activity
             */
            foreach ($activities as $activity) {
                if ($activity['parentId']) {
                    if (!array_key_exists($activity['parentId'], $activitiesTree)) {
                        $activitiesTree[$activity['parentId']] = [];
                    }
                    if (!array_key_exists('children', $activitiesTree[$activity['parentId']])) {
                        $activitiesTree[$activity['parentId']]['children'] = [];
                    }
                    $activitiesTree[$activity['parentId']]['children'][] = $activity; 
                } else {
                    $activitiesTree[$activity['id']] = $activity;
                }
            }
        }
        return $activitiesTree;
    }
}
