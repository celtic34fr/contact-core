<?php

namespace Celtic34fr\ContactCore\Tests\FormEntity;

use Celtic34fr\ContactCore\FormEntity\SysActivitySector;
use PHPUnit\Framework\TestCase;

class SysActivitySectorTest extends TestCase
{
    public function testGetName()
    {
        $sector = new SysActivitySector();
        $sector->setName('Sector Name');
        
        $this->assertEquals('Sector Name', $sector->getName());
    }
    
    public function testGetDescription()
    {
        $sector = new SysActivitySector();
        $sector->setDescription('Sector Description');
        
        $this->assertEquals('Sector Description', $sector->getDescription());
    }
    
    public function testGetParentId()
    {
        $sector = new SysActivitySector();
        $sector->setParentId(1);
        
        $this->assertEquals(1, $sector->getParentId());
    }
    
    public function testGetValeur()
    {
        $sector = new SysActivitySector();
        $sector->setName('Sector Name');
        $sector->setDescription('Sector Description');
        
        $this->assertEquals('Sector Name|Sector Description|', $sector->getValeur());
        
        $sector->setParentId(1);
        
        $this->assertEquals('Sector Name|Sector Description|1', $sector->getValeur());
    }
}
