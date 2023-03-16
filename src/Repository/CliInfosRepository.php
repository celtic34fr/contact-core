<?php

namespace Celtic34fr\ContactCore\Repository;

use Celtic34fr\ContactCore\Entity\CliInfos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CliInfos>
 *
 * @method CliInfos|null find($id, $lockMode = null, $lockVersion = null)
 * @method CliInfos|null findOneBy(array $criteria, array $orderBy = null)
 * @method CliInfos[]    findAll()
 * @method CliInfos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CliInfosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CliInfos::class);
    }

    public function save(CliInfos $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CliInfos $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CliInfosCRM[] Returns an array of CliInfosCRM objects
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

//    public function findOneBySomeField($value): ?CliInfosCRM
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
