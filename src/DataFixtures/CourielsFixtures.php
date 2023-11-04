<?php

namespace Celtic34fr\ContactCore\DataFixtures\ContactCore;

use Celtic34fr\ContactCore\DataFixtures\ContactCore\ClientelesFixtures;
use Celtic34fr\ContactCore\Entity\Clientele;
use Celtic34fr\ContactCore\Entity\CliInfos;
use Celtic34fr\ContactCore\Entity\Courriel;
use Celtic34fr\ContactCore\Enum\CourrielEnums;
use Celtic34fr\ContactCore\Enum\StatusCourrielEnums;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CourielsFixtures  extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $courrielStatus = [
            0 => StatusCourrielEnums::Created->_toString(),
            1 => StatusCourrielEnums::Error->_toString(),
            2 => StatusCourrielEnums::Send->_toString(),
        ];

        for ($idx = 0; $idx < 30; $idx++) {
            $clientIDX = mt_rand(0, 9);
            
            $client = $manager->getRepository(Clientele::class)->findOneBy(['courriel' => 'client-test'.$clientIDX.'@dot.net']);
            if ($client->getCliInfos()) {
                $cliInfosIDX = mt_rand(0, $client->getCliInfos());
                $cliInfos = $manager->getRepository(CliInfos::class)->findOneBy(['telephone' => '04673788'.$clientIDX.$cliInfosIDX]);

                if ($cliInfos) {
                    $courriel = new Courriel();
                    $courriel->setDestinataire($cliInfos);

                    $courriel->setSujet("");
                    $days = mt_rand(0, 20);
                    $courriel->setCreatedAt((new DateTimeImmutable('now'))->modify("-$days days"));
                    $status = mt_rand(0, 2);
                    $courriel->setSendStatus($courrielStatus[$status]);
                    if ($status === 2) {
                        $courriel->setSendAt((new DateTimeImmutable('now'))->modify('-'.strval((int)($days/2)).' days'));
                    }
                    $courriel->setNature(CourrielEnums::Contact->_toString());
                    $courriel->setTemplateCourriel("@contact-core/courriels/test_courriel.html.twig");
                    $courriel->setContextCourriel(['client' => $cliInfos, 
                        'courriel' =>  [
                            'created_at' => $courriel->getCreatedAt(),
                            'status' => $courriel->getSendStatus(),
                            'sujet' => $courriel->getSujet(),
                            'send_at' => $courriel->getSendAt() ?? "",
                            'nature' => $courriel->getNature(),
                        ]]);

                    $manager->persist($courriel);
                    $manager->flush();
                }
            }
        }
    }

    public static function getGroups(): array
    {
        return ['courriels', 'suivis'];
    }

    public function getDependencies()
    {
        return [
            ClientelesFixtures::class,
        ];
    }

}
