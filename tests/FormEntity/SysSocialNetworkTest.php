<?php

namespace Celtic34fr\ContactCore\Tests\FormEntity;

use Celtic34fr\ContactCore\FormEntity\SysSocialNetwork;
use PHPUnit\Framework\TestCase;

class SysSocialNetworkTest extends TestCase
{
    public function testGetName()
    {
        $sysSocialNetwork = new SysSocialNetwork();
        $sysSocialNetwork->setName('Facebook');
        
        $this->assertEquals('Facebook', $sysSocialNetwork->getName());
    }

    public function testGetUrlPage()
    {
        $sysSocialNetwork = new SysSocialNetwork();
        $sysSocialNetwork->setUrlPage('https://www.facebook.com');
        
        $this->assertEquals('https://www.facebook.com', $sysSocialNetwork->getUrlPage());
    }

    public function testGetLogoID()
    {
        $sysSocialNetwork = new SysSocialNetwork();
        $sysSocialNetwork->setLogoID('1234');
        
        $this->assertEquals('1234', $sysSocialNetwork->getLogoID());
    }

    public function testGetLogoSN()
    {
        $sysSocialNetwork = new SysSocialNetwork();
        $sysSocialNetwork->setLogoSN(1);
        
        $this->assertEquals(1, $sysSocialNetwork->getLogoSN());
    }

    public function testGetValeur()
    {
        $sysSocialNetwork = new SysSocialNetwork();
        $sysSocialNetwork->setName('Facebook');
        $sysSocialNetwork->setLogoID('1234');

        $this->assertEquals('Facebook|1234', $sysSocialNetwork->getValeur());
    }

    public function testGetValeurReturnsNullIfNameOrLogoIDIsEmpty()
    {
        $sysSocialNetwork = new SysSocialNetwork();
        $sysSocialNetwork->setName('');
        $sysSocialNetwork->setLogoID('1234');

        $this->assertNull($sysSocialNetwork->getValeur());

        $sysSocialNetwork->setName('Facebook');
        $sysSocialNetwork->setLogoID('');

        $this->assertNull($sysSocialNetwork->getValeur());
    }
}
