<?php

namespace Celtic34fr\ContactCore\Repository;

use Celtic34fr\ContactCore\Entity\Courriels;
use Celtic34fr\ContactCore\Enum\StatusCourrielEnums;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Courriels>
 *
 * @method Courriels|null find($id, $lockMode = null, $lockVersion = null)
 * @method Courriels|null findOneBy(array $criteria, array $orderBy = null)
 * @method Courriels[]    findAll()
 * @method Courriels[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourrielsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Courriels::class);
    }

    public function save(Courriels $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Courriels $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOnError()
    {
        return $this->createQueryBuilder('cc')
            ->andWhere('cc.send_status = :error')
            ->setParameter('error', StatusCourrielEnums::Error->_toString())
            ->orderBy('cc.created_at', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return CRMCourriels[] Returns an array of CRMCourriels objects
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

//    public function findOneBySomeField($value): ?CRMCourriels
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
