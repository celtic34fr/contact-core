<?php

namespace Celtic34fr\ContactCore\Tests\Service;

use Bolt\Configuration\Config;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpKernel\KernelInterface;

class ExtensionConfigTest extends TestCase
{
    public function testGetInstalledExtensions()
    {
        //$appKernelMock = $this->createMock(KernelInterface::class);
        //$configMock = $this->createMock(Config::class);
        list($appKernelMock, $configMock) = $this->initMock();
        
        $extensionConfig = new ExtensionConfig($appKernelMock, $configMock);
        
        $extInstall = ['extension1', 'extension2'];
        $reflection = new ReflectionClass($extensionConfig);
        $extInstallProperty = $reflection->getProperty('extInstall');
        $extInstallProperty->setAccessible(true);
        $extInstallProperty->setValue($extensionConfig, $extInstall);
        
        $this->assertEquals($extInstall, $extensionConfig->getInstalledExtensions());
    }
    
    public function testIsExtensionInstall()
    {
        //$appKernelMock = $this->createMock(KernelInterface::class);
        //$configMock = $this->createMock(Config::class);
        list($appKernelMock, $configMock) = $this->initMock();
        
        $extensionConfig = new ExtensionConfig($appKernelMock, $configMock);
        
        $extInstall = ['extension1', 'extension2'];
        $reflection = new ReflectionClass($extensionConfig);
        $extInstallProperty = $reflection->getProperty('extInstall');
        $extInstallProperty->setAccessible(true);
        $extInstallProperty->setValue($extensionConfig, $extInstall);
        
        $this->assertTrue($extensionConfig->isExtensionInstall('extension2'));
        $this->assertFalse($extensionConfig->isExtensionInstall('extension3'));
    }


    private function initMock(): array
    {
        $appKernelMock = $this->createMock(KernelInterface::class);
        $appKernelMock->expects($this->once())
            ->method('getProjectDir')
            ->willReturn(__DIR__.'/../datas');

        $configMock = $this->createMock(Config::class);

        return [$appKernelMock, $configMock];
    }
}