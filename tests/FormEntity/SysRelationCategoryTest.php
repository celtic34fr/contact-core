<?php

namespace Celtic34fr\ContactCore\Tests\FormEntity;

use Celtic34fr\ContactCore\FormEntity\SysRelationCategory;
use PHPUnit\Framework\TestCase;

class SysRelationCategoryTest extends TestCase
{
    public function testGetCategory()
    {
        $sysRelationCategory = new SysRelationCategory();
        $sysRelationCategory->setCategory('Test category');
        $this->assertEquals('Test category', $sysRelationCategory->getCategory());
    }

    public function testGetDescription()
    {
        $sysRelationCategory = new SysRelationCategory();
        $sysRelationCategory->setDescription('Test description');
        $this->assertEquals('Test description', $sysRelationCategory->getDescription());
    }

    public function testGetValeur()
    {
        $sysRelationCategory = new SysRelationCategory();
        $sysRelationCategory->setCategory('Test category');
        $sysRelationCategory->setDescription('Test description');
        $this->assertEquals('Test category|Test description', $sysRelationCategory->getValeur());

        $sysRelationCategory->setDescription(null);
        $this->assertEquals('Test category|', $sysRelationCategory->getValeur());

        $sysRelationCategory->setCategory(null);
        $this->assertEquals(null, $sysRelationCategory->getValeur());
    }
}
