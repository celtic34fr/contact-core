<?php

namespace Celtic34fr\ContactCore;

use Bolt\Menu\ExtensionBackendMenuInterface;
use Celtic34fr\ContactCore\Traits\AdminMenuTrait;
use Celtic34fr\ContactCore\Menu\MenuItem as MenuItemContacts;
use Knp\Menu\MenuItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** classe d'ajout des menu spécifiques pour le projet */
class AdminMenu implements ExtensionBackendMenuInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    use AdminMenuTrait;

    public function addItems(MenuItem $menu): void
    {
        /** @var MenuItemContacts $menuContact */
        list($menuBefore, $menuContacts, $menuAfter) = $this->extractsMenus($menu);
        if (!$menuContacts->hasChild("Gestion des Contacts")) {
            $menuContacts->addChild('Gestion des Contacts', [
                'extras' => [
                    'name' => 'Gestion des Contacts',
                    'type' => 'separator',
                    'group' => 'Contact',
                ]
            ]);
        }

        $utilitairesItems = [];
        if (!$menuContacts->hasChild("Utilitaires")) {
            $utilitairesItems = [
                "Utilitaires" => [
                    'type' => 'menu',
                    'item' => [
                        'uri' => $this->urlGenerator->generate('bolt_menupage', [
                            'slug' => 'utilitaires',
                        ]),
                        'extras' => [
                            'group' => 'Contact',
                            'name' => 'Utilitaires',
                            'slug' => 'utilitaires',
                            'icon' => 'fa-tools'
                        ]
                    ]
                ]
            ];
        }
        $utilitairesItems['Gestion des courriels'] = [
            'type' => 'smenu',
            'parent' => "Utilitaires",
            'item' => [
                'uri' => $this->urlGenerator->generate('courriel_list'),
                'extras' => [
                    'icon' => 'fa-envelope-circle-check',
                    'group' => 'Contact',
                ]
            ]
        ];
        $menuContacts = $this->addMenu($utilitairesItems, $menuContacts);

        $menu = $this->rebuildMenu($menu, $menuBefore, $menuContacts, $menuAfter);

        /*
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
        */
    }
}
