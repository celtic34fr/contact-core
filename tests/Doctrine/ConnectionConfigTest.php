<?php

namespace Celtic34fr\ContactCore\Tests\Doctrine;

use Celtic34fr\ContactCore\Doctrine\ConnectionConfig;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TeamTNT\TNTSearch\Stemmer\PorterStemmer;

class ConnectionConfigTest extends TestCase
{
    public function testGetConfig()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $connection = $this->createMock(Connection::class);
        $em->method('getConnection')->willReturn($connection);
        $params = [
            'host' => 'localhost',
            'dbname' => 'test_db',
            'user' => 'root',
            'password' => 'password',
            'port' => '3306',
        ];
        $connection->method('getParams')->willReturn($params);
        $connection->method('getDatabasePlatform')->willReturn(new MySqlPlatform());
        
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with('kernel')->willReturn(new class {
            public function getProjectDir() {
                return __DIR__.'/../datas';
            }
        });
        
        $connectionConfig = new ConnectionConfig($em, $container);
        $config = $connectionConfig->getConfig();
        
        $this->assertEquals('mysql', $config['driver']);
        $this->assertEquals('localhost', $config['host']);
        $this->assertEquals('test_db', $config['database']);
        $this->assertEquals('root', $config['username']);
        $this->assertEquals('password', $config['password']);
        $this->assertEquals('3306', $config['port']);
        $this->assertEquals(__DIR__.'/../datas/var/data/', $config['storage']);
        $this->assertEquals(PorterStemmer::class, $config['stemmer']);
    }
}