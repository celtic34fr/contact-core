<?php

namespace Celtic34fr\ContactCore\Tests\Repository;

use Celtic34fr\ContactCore\Entity\CliInfos;
use Celtic34fr\ContactCore\Repository\CliInfosRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CliInfosRepositotyTest extends KernelTestCase
{
    private $entityManager;

    public function setUp(): void
    {
        $kernel = static::bootKernel();
        $this->entityManager = $kernel->getContainer()
        ->get('doctrine')
        ->getManager();
    }

    public function testIsExitFullname()
    {
        $exist = $this->entityManager->getRepository(CliInfosRepository::class)
                        ->isExistFullname('');
        $this->assertTrue($exist, "fullname '' exist in database");
    }
}