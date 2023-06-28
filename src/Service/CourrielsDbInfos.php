<?php

namespace Celtic34fr\ContactCore\Service;

use Celtic34fr\ContactCore\Repository\CourrielRepository;
use Celtic34fr\ContactCore\Traits\DbPaginateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class CourrielsDbInfos
{
    use DbPaginateTrait;

    public function __construct(private EntityManagerInterface $entityManager,
        private CourrielRepository $courrielRepository)
    {
    }

    public function countCourrielsToSend(): int
    {
        return sizeof($this->courrielRepository->findAllOnError()) ?? 0;
    }
}