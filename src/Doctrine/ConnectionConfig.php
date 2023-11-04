<?php

declare(strict_types=1);

namespace Celtic34fr\ContactCore\Doctrine;

use Doctrine\DBAL\Connection;
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
        /** @var Connection $connection */
        $connection = $this->em->getConnection();
        $params = $connection->getParams();

        return [
            'driver'        => $connection->getDatabasePlatform()->getName(),
            'host'          => $params['host'],
            'database'      => $params['dbname'],
            'username'      => $params['user'],
            'password'      => $params['password'],
            'port'          => $params['port'],
            'storage'       => sprintf('%s%s', $this->projectDir, '/var/data/'),
            'stemmer'       => PorterStemmer::class,
        ];
    }
}
