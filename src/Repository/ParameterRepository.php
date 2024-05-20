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
    public function updateParameter(Parameter $entity, bool $flush = false): void
    {
        $previous = $this->getEntityManager()->getRepository(Parameter::class)
            ->findOneBy(['cle' => $entity->getCle(), 'ord' => $entity->getOrd(), 'created_at' => $entity->getCreatedAt()]);
        $previous->setUpdatedAt(new DateTimeImmutable('now'));
        $this->save($previous, $flush);
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
    public function findItemsByCle(string $cle): mixed
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
            $qb->andWhere("p.$cle LIKE '$partial'");
        }
        if ($orderBy) {
            foreach ($orderBy as $cle => $order) {
                $qb->orderBy('p'.$cle, $order);
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
        if ($values) $socialNetwork = $this->formatSocialNetworksList($values);        
        return $socialNetworks;
    }

      /**
     * retrieve global list of Social Networks with URL of favicon (active and disabled)
     * @return array
     */
    public function findAllSocialNetworks(): array
    {
        $socialNetworks = [];
        $values = $this->findItemsByCle(SocialNetwork::CLE);
        if ($values) $socialNetwork = $this->formatSocialNetworksList($values);        
        return $socialNetworks;
    }

    /**
     * @param string $name
     * @return Parameter|null
     */
    public function findSocialNetworkByName(string $name): ?Parameter
    {
        $socialNetwork = $this->findByPartialFields([
            'cle' => SocialNetwork::CLE,
            'valeur' => $name.'%'
        ], [
            'created_at' => 'DESC',
        ]);
        return $socialNetwork[0] ? $this->formatSocialNetworksList($socialNetwork[0]) : null;
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
        if ($socialNetwork) {
            return $socialNetwork[0]->getLogoID();
        }
        return false;
    }

    /**
     * @return array
     */
    public function findValidRelationCategories(): array
    {
        $relationCategories = [];
        $values = $this->findValidItemsByCle(RelationCategory::CLE);
        if ($values) $relationCategories = $this->formatRelationCategories($values);
        return $relationCategories;
    }

    /**
     * @return array
     */
    public function findAllRelationCategories(): array
    {
        $relationCategories = [];
        $values = $this->findItemsByCle(SocialNetwork::CLE);
        if ($values) $relationCategories = $this->formatSocialNetworksList($values);        
        return $relationCategories;
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
        $valeurs = $this->findItemsByCle(ActivitySector::CLE);
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
     * @return array
     */
    private function formatSocialNetworksList(array $values) : array
    {
        $socialNetworks = [];
        if ($values) {
            foreach ($values as $value) {
                $socialNetwork = new SocialNetwork($value);
                $socialNetworks[$socialNetwork->getId()] = [
                    'id' => $socialNetwork->getId(),
                    'name' => $socialNetwork->getName(),
                    'logoID' => $socialNetwork->getLogoID(),
                    'created' => $socialNetwork->getCreatedAt(),
                    'updated' => $socialNetwork->getUpdatedAt(),
                ];
            }
        }
        return $socialNetworks;
    }

    /**
     * @param array $values
     * @return array
     */
    private function formatActivitiesList(array $values) : array
    {
        $activities = [];
        if ($values) {
            foreach ($$values as $value) {
                $activity = new ActivitySector($value);
                $activities[$activity->getId()] = [
                    'id' => $activity->getId(),
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

    /**
     * @param array $values
     * @return array
     */
    private function formatRelationCategories(array $values): array
    {
        $relationCategories = [];
        if ($values) {
            foreach ($values as $value) {
                $category = new ActivitySector($value);
                $activities[$category->getId()] = [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'description' => $category->getDescription(),
                ];
            }
        }
        return $relationCategories;
    }
}
