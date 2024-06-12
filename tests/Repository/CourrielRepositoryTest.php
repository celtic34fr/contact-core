<?php

namespace Celtic34fr\ContactCore\Tests\Repository;

use Celtic34fr\ContactCore\Enum\StatusCourrielEnums;
use Celtic34fr\ContactCore\Repository\CourrielRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CourrielRepositoryTest extends KernelTestCase
{
    private $entityManager;

    public function setUp(): void
    {
        $kernel = static::bootKernel();
        $this->entityManager = $kernel->getContainer()
        ->get('doctrine')
        ->getManager();
    }

    public function testFindAllOnError()
    {
        $inErrors = $this->entityManager->getRepository(CourrielRepository::class)
                            ->findAllOnError();
        /** voir quel assert faire au regard du remplissage de la base */
    }

    public function testFindCourrielsAll()
    {
        $courriels = $this->entityManager->getRepository(CourrielRepository::class)
                            ->findCourrielsAll("all", 1, 10);
        /** voir quel assert faire au regard du remplissage de la base */
    }

    public function testFindCourrielsSend()
    {
        $courriels = $this->entityManager->getRepository(CourrielRepository::class)
                            ->findCourrielsAll(StatusCourrielEnums::Send->_toString(), 1, 10);
        /** voir quel assert faire au regard du remplissage de la base */
    }

    public function testFindCourrielsError()
    {
        $courriels = $this->entityManager->getRepository(CourrielRepository::class)
                            ->findCourrielsAll(StatusCourrielEnums::Error->_toString(), 1, 10);
        /** voir quel assert faire au regard du remplissage de la base */
    }

    public function testFindCourrielsCreated()
    {
        $courriels = $this->entityManager->getRepository(CourrielRepository::class)
                            ->findCourrielsAll(StatusCourrielEnums::Created->_toString(), 1, 10);
        /** voir quel assert faire au regard du remplissage de la base */
    }
}