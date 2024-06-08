<?php

namespace Celtic34fr\ContactCore\Tests\Service;

use Celtic34fr\ContactCore\Doctrine\ConnectionConfig;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Service\TntEngine;
use PHPUnit\Framework\TestCase;
use TeamTNT\TNTSearch\TNTSearch;

class TntEngineTest extends TestCase
{
    public function testGet()
    {
        $connectionConfig = new ConnectionConfig('host', 'username', 'password');
        $extConfig = new ExtensionConfig();

        $tntEngine = new TntEngine($connectionConfig, $extConfig);
        $tnt = $tntEngine->get();

        $this->assertInstanceOf(TNTSearch::class, $tnt);
    }
}