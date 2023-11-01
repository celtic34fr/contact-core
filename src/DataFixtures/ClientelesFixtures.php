<?php

namespace Bolt\Extension\Celtic34fr\ContactCore\DataFixtures\ContactCore;

use Bolt\Extension\Celtic34fr\ContactCore\Entity\Clientele;
use Bolt\Extension\Celtic34fr\ContactCore\Entity\CliInfos;
use Bolt\Extension\Celtic34fr\ContactCore\Enum\CustomerEnums;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/** Création d'utilisateur : internautes (prospects) et/ou client */
/** tables inpactées Clientele & CliInfos */
class ClientelesFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $types = [
            0 => CustomerEnums::Prospect->_toString(),
            1 => CustomerEnums::Client->_toString(),
        ];

        for ($idx = 0; $idx < 10; $idx++) {
            $client = new Clientele();
            $client->setCourriel('client-test'.$idx.'@dot.net');
            $client->setType($types[mt_rand(0, 1)]);
            $manager->persist($client);
            $manager->flush();

            /** ajout des informations variables */
            $nb_names = mt_rand(0, 5);
            for ($jdx = 0; $jdx < $nb_names; $idx++) {
                $this->createCliinfo($idx.$jdx, $client, $manager);
            }
        }
    }

    public static function getGroups(): array
    {
        return ['clientele', 'courriels', 'contacts'];
    }

    public function getDependencies()
    {
    }


    private function createCliinfo(int $noCliinfo, Clientele $client, ObjectManager $manager): void
    {
        $cliinfos = new CliInfos();
        $cliinfos->setNom('TEST ' . $noCliinfo);
        $cliinfos->setPrenom('Ptest' . $noCliinfo);
        $cliinfos->setTelephone('04673788'.$noCliinfo);
        $cliinfos->setClient($client);
        $client->addCliInfos($cliinfos);
        $manager->persist($cliinfos);
        $manager->flush();
    }
}
