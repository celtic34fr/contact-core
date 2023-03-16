<?php

namespace Celtic34fr\ContactCore\Repository;

use Celtic34fr\ContactCore\Entity\PiecesJointes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PiecesJointes>
 *
 * @method PiecesJointes|null find($id, $lockMode = null, $lockVersion = null)
 * @method PiecesJointes|null findOneBy(array $criteria, array $orderBy = null)
 * @method PiecesJointes[]    findAll()
 * @method PiecesJointes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PiecesJointesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PiecesJointes::class);
    }

    public function save(PiecesJointes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PiecesJointes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PiecesJointes[] Returns an array of PiecesJointes objects
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

//    public function findOneBySomeField($value): ?PiecesJointes
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
