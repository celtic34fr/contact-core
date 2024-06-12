<?php

namespace Celtic34fr\ContactCore\Tests\Repository;

use Celtic34fr\ContactCore\Repository\ParameterRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ParameterRepositoryTest extends KernelTestCase
{
    private $entityManager;

    public function setUp(): void
    {
        $kernel = static::bootKernel();
        $this->entityManager = $kernel->getContainer()
        ->get('doctrine')
        ->getManager();
    }

    public function testGetNameParametersList()
    {
        $list = $this->entityManager->getrepository(ParameterRepository::class)
                        ->getNameParametersList();
        /** voir quel assert faire au regard du remplissage de la base */                        
    }

    public function testGetValuesParamterList()
    {
        $listName = "liste";
        $listDescription = "Description de la liste";
        $listRecords = [
            0 => "item 0", 1 => "item 1", 2 => "item 2", 3 => "item 3",
        ];
        $this->entityManager->getRepository(ParameterRepository::class)
                        ->recordParamtersList($listName, $listRecords, $listDescription);

        $list = $this->entityManager->getRepository(ParameterRepository::class)
                        ->getValuesParameterList("liste");
        /** voir quel assert faire au regard du remplissage de la base */                        
    }
}