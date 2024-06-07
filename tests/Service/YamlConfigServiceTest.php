<?php

namespace Celtic34fr\ContactCore\Tests\Service;

use Celtic34fr\ContactCore\Service\YamlConfigService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class YamlConfigServiceTest extends TestCase
{
    public function testFilePath()
    {
        $yamlConfigService = new YamlConfigService();

        $yamlConfigService->setFilePath('/path/to/config.yaml');

        $this->assertEquals('/path/to/config.yaml', $yamlConfigService->getFilePath());

        $yamlConfigService2 = new YamlConfigService('/path/to/config.yaml');

        $this->assertEquals('/path/to/config.yaml', $yamlConfigService2->getFilePath());
    }

    public function testRead()
    {
        $filePath = __DIR__ . '/../../config/test_config.yaml';
        
        $yamlConfigService = new YamlConfigService($filePath);
        
        $data = $yamlConfigService->read();
        
        $this->assertIsArray($data);
    }
    
    public function testWrite()
    {
        $filePath = __DIR__ . '/../../config/test_config.yaml';
        
        $yamlConfigService = new YamlConfigService($filePath);
        
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        
        $yamlConfigService->write($data);
        
        $this->assertFileExists($filePath);
        
        $this->assertEquals($data, Yaml::parseFile($filePath));
    }
}