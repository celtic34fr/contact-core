<?php

namespace Celtic34fr\ContactCore;

use Bolt\Menu\ExtensionBackendMenuInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminMenu implements ExtensionBackendMenuInterface
{
    private UrlGeneratorInterface $urlGenerator;
    public const CRM_MENU = 'CRM - Relation client';

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function addItems(MenuItem $menu): void
    {
        if (!$menu->getChild("CRM - Relation client")) {
            $menu->addChild('CRM - Relation client', [
                'extras' => [
                    'name' => 'CRM - Relation client',
                    'type' => 'separator',
                    'group' => 'CRM',
                ]
            ]);
        }
    }
}
