<?php

namespace Celtic34fr\ContactCore;

use Bolt\Menu\ExtensionBackendMenuInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminMenu implements ExtensionBackendMenuInterface
{
    public const CRM_MENU = 'CRM - Relation client';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function addItems(MenuItem $menu): void
    {
        if (!$menu->getChild("Gestion des Contacts")) {
            $menu->addChild('Gestion des Contacts', [
                'extras' => [
                    'name' => 'Gestion des Contacts',
                    'type' => 'separator',
                    'group' => 'Contact',
                ]
            ]);

            if (!$menu->getChild("Utilitaires")) {
                $menu->addChild('Utilitaires', [
                    'uri' => $this->urlGenerator->generate('bolt_menupage', [
                        'slug' => 'utilitaires',
                    ]),
                    'extras' => [
                        'group' => 'Contact',
                        'name' => 'Utilitaires',
                        'slug' => 'utilitaires',
                    ]
                ]);
                $menu['Utilitaires']->addChild('Gestion des courriels', [
                    'uri' => $this->urlGenerator->generate('courriel_list'),
                    'extras' => [
                        'icon' => 'fa-envelope-circle-check',
                        'group' => 'Contact',
                    ]
                ]);
            }
        }
    }
}
