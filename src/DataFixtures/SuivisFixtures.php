<?php

namespace App\DataFixtures\ContactCore;

use Celtic34fr\ContactCore\Entity\Courriel;
use Celtic34fr\ContactCore\Entity\Suivi;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SuivisFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var Courriels $courriel */
        foreach ($manager->getRepository(Courriel::class)->findAll() as $courriel) {
            /** création d'un suivi pour signaler la création de chaque courriel */
            $suivi = new Suivi();
            $suivi->setEvtAt($courriel->getCreatedAt());
            $suivi->setEvent("création courriel de test");
            $suivi->setClient($courriel->getDestinataire()->getClient());
            $manager->persist($suivi);
            $manager->flush();
        }
    }

    public static function getGroups(): array
    {
        return ['suivis'];
    }

    public function getDependencies()
    {
    }

}
