<?php

namespace Celtic34fr\ContactCore;

use Bolt\Menu\ExtensionBackendMenuInterface;
use Celtic34fr\ContactCore\Menu\MenuItem as MenuItemContacts;
use Celtic34fr\ContactCore\Traits\AdminMenuTrait;
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

        $configurationItems = [];
        if (!$menuContacts->hasChild("Paramètres")) {
            $configurationItems = [
                "Paramètres" => [
                    'type' => 'menu',
                    'item' => [
                        'uri' => $this->urlGenerator->generate('bolt_menupage', [
                            'slug' => 'parametres',
                        ]),
                        'extras' => [
                            'group' => 'Contact',
                            'name' => 'Paramètres',
                            'slug' => 'parametres',
                            'icon' => 'fa-tools'
                        ]
                    ]
                ]
            ];
        }
        $configurationItems['Mes Informations'] = [
            'type' => 'smenu',
            'parent' => "Paramètres",
            'item' => [
                'uri' => $this->urlGenerator->generate('parameters-info-structure'),
                'extras' => [
                    'icon' => 'fa-building',
                    'group' => 'Contact',
                ]
            ]
        ];
        $configurationItems['Mes Listes de Parameters'] = [
            'type' => 'smenu',
            'parent' => "Paramètres",
            'item' => [
                'uri' => $this->urlGenerator->generate('parameters-params-list'),
                'extras' => [
                    'icon' => 'fa-building',
                    'group' => 'Contact',
                ]
            ]
        ];
        $configurationItems['Les catégories de contact'] = [
            'type' => 'smenu',
            'parent' => "Paramètres",
            'item' => [
                'uri' => $this->urlGenerator->generate('sys-params-rcategories-list'),
                'extras' => [
                    'icon' => 'fa-building',
                    'group' => 'Contact',
                ]
            ]
        ];
        $configurationItems['Les Secteurs d\'activité'] = [
            'type' => 'smenu',
            'parent' => "Paramètres",
            'item' => [
                'uri' => $this->urlGenerator->generate('sys-params-activities-list'),
                'extras' => [
                    'icon' => 'fa-building',
                    'group' => 'Contact',
                ]
            ]
        ];
        $configurationItems['Les Réseaux Sociaux'] = [
            'type' => 'smenu',
            'parent' => "Paramètres",
            'item' => [
                'uri' => $this->urlGenerator->generate('sys-params-socialnetworks-list'),
                'extras' => [
                    'icon' => 'fa-building',
                    'group' => 'Contact',
                ]
            ]
        ];
        $menuContacts = $this->addMenu($configurationItems, $menuContacts);

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
                            'icon' => 'fa-toolbox'
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
                    'icon' => 'fa-envelope',
                    'group' => 'Contact',
                ]
            ]
        ];
        $menuContacts = $this->addMenu($utilitairesItems, $menuContacts);

        $menu = $this->rebuildMenu($menu, $menuBefore, $menuContacts, $menuAfter);
    }
}
