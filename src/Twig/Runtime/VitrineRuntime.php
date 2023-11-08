<?php

namespace Celtic34fr\ContactCore\Twig\Runtime;

use Bolt\Configuration\Config;
use Celtic34fr\ContactCore\Service\ToolsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class VitrineRuntime implements RuntimeExtensionInterface
{
    public function __construct(private Config $config, private UrlGeneratorInterface $urlGenerator, private ToolsService $tools)
    {
    }

    public function twigFunction_buildBreadcrumbs(string $menuName = null, Request $request = null, bool $bs5 = false)
    {
        $menu = [];
        $uri = "";
        $allMenus = $this->config->get('menu');

        if ($menuName && $allMenus) {
            $menu = $allMenus[$menuName];
        }
        if ($request) {
            $uri = substr($request->getRequestUri(), 1);
            $baseUrl = $request->getBaseUrl();
        }

        return $this->tools->generateBreadcrumbsFromMenu($menu, $uri, $baseUrl, $bs5);
    }

}