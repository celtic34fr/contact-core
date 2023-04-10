<?php

declare(strict_types=1);

namespace Celtic34fr\ContactCore\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TeamTNT\TNTSearch\Stemmer\PorterStemmer;

class ConnectionConfig
{
    /** @var string */
    private $projectDir;

    public function __construct(private  EntityManagerInterface $em, private ContainerInterface $container)
    {
        $this->projectDir = $container->get('kernel')->getProjectDir();
    }

    public function getConfig(): array
    {
        $connection = $this->em->getConnection();

        return [
            'driver' => $connection->getDatabasePlatform()->getName(),
            'host' => $connection->getHost(),
            'database'  => $connection->getDatabase(),
            'username'  => $connection->getUsername(),
            'password'  => $connection->getPassword(),
            'storage'   => sprintf('%s%s', $this->projectDir, '/var/data/'),
            'stemmer'   => PorterStemmer::class,
        ];
    }
}
