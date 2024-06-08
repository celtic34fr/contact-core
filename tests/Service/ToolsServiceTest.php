<?php

namespace Celtic34fr\ContactCore\Tests\Service;

use Bolt\Collection\DeepCollection;
use Bolt\Configuration\Config;
use Celtic34fr\ContactCore\Service\ToolsService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ToolsServiceTest extends TestCase
{
    public function testGenerateBreadcrumbsFromMenuArray()
    {
        // Create a mock for Config and UrlGeneratorInterface
        $configMock = $this->createMock(Config::class);
        $urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        // Create an instance of ToolsService with the mock objects
        $toolsService = new ToolsService($configMock, $urlGeneratorMock);

        // Mock menu and uri
        $menu = new DeepCollection([
            new DeepCollection(['label' => 'Home', 'link' => 'homepage']),
            new DeepCollection(['label' => 'About Us', 'link' => 'about']),
            new DeepCollection(['label' => 'Contact Us', 'link' => 'contact']),
        ]);
        $uri = 'about';
        $baseUrl = 'http://example.com';
        $bs5 = false;

        // Call the method to test
        $breadcrumbs = $toolsService->generateBreadcrumbsFromMenuArray($menu, $uri, $baseUrl, $bs5);

        // Assert the generated breadcrumbs
        $expectedBreadcrumbs = [
            ['label' => 'Home', 'link' => 'homepage'],
            ['label' => 'About Us', 'link' => 'about'],
        ];
        $this->assertEquals($expectedBreadcrumbs, $breadcrumbs);
    }

    public function testGenerateBreadcrumbsFromMenu()
    {
        // Create a mock for Config and UrlGeneratorInterface
        $configMock = $this->createMock(Config::class);
        $urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        // Create an instance of ToolsService with the mock objects
        $toolsService = new ToolsService($configMock, $urlGeneratorMock);

        // Mock menu and uri
        $menu = new DeepCollection([
            new DeepCollection(['label' => 'Home', 'link' => 'homepage']),
            new DeepCollection(['label' => 'About Us', 'link' => 'about']),
            new DeepCollection(['label' => 'Contact Us', 'link' => 'contact']),
        ]);
        $uri = 'about';
        $baseUrl = 'http://example.com';
        $bs5 = false;

        // Call the method to test
        $breadcrumbs = $toolsService->generateBreadcrumbsFromMenu($menu, $uri, $baseUrl, $bs5);

        // Assert the generated breadcrumbs as string
        $expectedBreadcrumbs = '<a href="http://example.com/homepage">Home</a><a href="http://example.com/about">About Us</a>';
        $this->assertEquals($expectedBreadcrumbs, $breadcrumbs);
    }
}