<?php

namespace Celtic34fr\ContactCore\Tests\Service;

use Celtic34fr\ContactCore\Repository\CourrielRepository;
use Celtic34fr\ContactCore\Service\CourrielsDbInfos;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CourrielsDbInfosTest extends TestCase
{
    private $entityManagerMock;
    private $courrielRepositoryMock;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->courrielRepositoryMock = $this->createMock(CourrielRepository::class);
    }

    public function testCountCourrielsToSend()
    {
        $courrielsDbInfos = new CourrielsDbInfos($this->entityManagerMock, $this->courrielRepositoryMock);

        $this->courrielRepositoryMock->expects($this->once())
            ->method('findAllOnError')
            ->willReturn([]);

        $this->assertEquals(0, $courrielsDbInfos->countCourrielsToSend());
    }
}