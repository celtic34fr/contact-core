<?php

namespace Celtic34fr\ContactCore\Repository;

use Celtic34fr\ContactCore\Entity\Parameter;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameter::class);
    }

    public function save(Parameter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Parameter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNameParametersList(): mixed
    {
        $nameList = [];
        $rslt = $this->createQueryBuilder('p')
            ->where('p.ord = 0')
            ->orderBy('p.cle', 'ASC')
            ->getQuery()
            ->getResult();
        if (!$rslt) return [];
        foreach ($rslt as $item) {
            /** prise de toutes les lises de para mètres sauf celle préfixée par 'SYS' pour système */
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
        return $nameList;
    }

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

    public function reorgValues(string $cle): void
    {
        $values = $this->createQueryBuilder('p')
            ->where("p.cle = :cle")
            ->orderBy("ord", "ASC")
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

    public function findItemsByCle(string $cle)
    {
        return $this->createQueryBuilder('p')
            ->where('p.cle = :cle')
            ->andWhere('p.ord > 0')
            ->setParameter('cle', $cle)
            ->getQuery()
            ->getResult();
    }

    public function findByPartialFields(array $criteria, array $orderBy = null)
    {
        $qb = $this->createQueryBuilder('p');
        foreach ($criteria as $cle => $partial) {
            $qb->andWhere("p.$cle LIKE '%$partial%'");
        }
        if ($orderBy) {
            foreach ($orderBy as $cle => $order) {
                $qb->orderBy($cle, $order);
            }
        }
        return $qb->getQuery()->getResult();
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
}
