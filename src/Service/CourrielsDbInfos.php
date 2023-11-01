<?php

namespace Bolt\Extension\Celtic34fr\ContactCore\Service;

use Bolt\Extension\Celtic34fr\ContactCore\Repository\CourrielRepository;
use Bolt\Extension\Celtic34fr\ContactCore\Traits\DbPaginateTrait;
use Doctrine\ORM\EntityManagerInterface;

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