<?php

namespace Celtic34fr\ContactCore\Service;

use Celtic34fr\ContactCore\Entity\Courriels;
use Celtic34fr\ContactCore\Trait\DbPaginateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class CourrielsDbInfos
{
    use DbPaginateTrait;

    private EntityManagerInterface $entityManager;
    private ObjectRepository|\Doctrine\ORM\EntityRepository $courrielsRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->courrielsRepository = $entityManager->getRepository(Courriels::class);
    }

    public function countCourrielsToSend(): int
    {
        return sizeof($this->courrielsRepository->findAllOnError()) ?? 0;
    }
}