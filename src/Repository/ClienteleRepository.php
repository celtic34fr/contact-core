<?php

namespace Celtic34fr\ContactCore\Repository;

use Celtic34fr\ContactCore\Entity\Clientele;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Clientele>
 *
 * @method Clientele|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clientele|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clientele[]    findAll()
 * @method Clientele[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class ClienteleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clientele::class);
    }

    public function save(Clientele $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Clientele $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ClienteleCRM[] Returns an array of ClienteleCRM objects
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

//    public function findOneBySomeField($value): ?ClienteleCRM
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
