<?php

namespace Celtic34fr\ContactCore\Trait;

use Exception;
use Celtic34fr\ContactCore\Menu\MenuItem;
use Knp\Menu\MenuItem as KnpMenuItem;

trait AdminMenuTrait
{
    private function extractsMenus(KnpMenuItem $menu): array
    {
        /** @var MenuItem $menu */
        $factory = $menu->getFactory();

        $menuBefore = new MenuItem('before', $factory);
        $menuContacts = new MenuItem('contacts', $factory);
        $menuAfter = new MenuItem('after', $factory);
        $children = $menu->getChildren();
        $contact = false;
        $idx = 0;

        /** @var MenuItem $child */
        foreach ($children as $name => $child) {
            if ((!$child->getExtra('group') || $child->getExtra('group') != 'Contact') && !$contact) {
                $menuBefore->addChild($name, $child->getOptions());
                if ($child->getChildren()) {
                    /** @var MenuItem $childChild */
                    foreach ($child->getChildren() as $childName => $childChild) {
                        $menuBefore[$name]->addChild($childName, $childChild->getOptions());
                    }
                }
                $idx += 1;
            } elseif (!$contact || $child->getExtra('group') == 'Contact') {
                $contact = true;
                $menuContacts->addChild($name, $child->getOptions());
                if ($child->getChildren()) {
                    /** @var MenuItem $childChild */
                    foreach ($child->getChildren() as $childName => $childChild) {
                        $menuContacts[$name]->addChild($childName, $childChild->getOptions());
                    }
                }
                $idx += 1;
            } else {
                $menuAfter->addChild($name, $child->getOptions());
                if ($child->getChildren()) {
                    /** @var MenuItem $childChild */
                    foreach ($child->getChildren() as $childName => $childChild) {
                        $menuAfter[$name]->addChild($childName, $childChild->getOptions());
                    }
                }
                break;
            }
        }

        return [$menuBefore, $menuContacts, $menuAfter];
    }

    private function addMenu(array $menusToAdd, MenuItem $menu): MenuItem
    {
        foreach ($menusToAdd as $name => $datas) {
            switch (true) {
                case (!array_key_exists($name, $menu->getChildren()) && $datas['type'] === "menu"):
                    $menu->addChild($name, $datas['item']);
                    break;
                case ($datas['type'] === "smenu"):
                    $menuParent = $datas['parent'];
                    if (empty($menuParent)) {
                        throw new Exception("SouMenu $name sans menu parent");
                    } else if (!empty($menuParent) && (!array_key_exists($menuParent, $menu->getChildren()))) {
                        if (!array_key_exists($menuParent, $menusToAdd)) {
                            throw new Exception("SousMenu $name dont le menu parent $menuParent est introuvable");
                        } else {
                            $menu->addChild($menuParent, $menusToAdd[$menuParent]['item']);
                        }
                    }
                    if (array_key_exists($menuParent, $menu->getChildren())) {
                        $menu[$menuParent]->addChild($name, $datas['item']);
                    }
                    break;
            }
        }
        return $menu;
    }

    private function emptyMenuItem(MenuItem $menu): MenuItem
    {
        $children = $menu->getChildren();
        foreach ($children as $name => $child) {
            $menu->removeChild($name);
        }
        return $menu;
    }

    private function rebuildMenu(string $menuName, MenuItem $menuBefore, MenuItem $menuContacts, MenuItem $menuAfter): MenuItem
    {
        $menu = new MenuItem($menuName, $menuBefore->getFactory());
        /** @var MenuItem $child */
        foreach ($menuBefore->getChildren() as $name => $child) {
            $menu->addChild($name, $child->getOptions());
            if ($child->getChildren()) {
                /** @var MenuItem $childChild */
                foreach ($child->getChildren() as $childName => $childChild) {
                    $menu[$name]->addChild($childName, $childChild->getOptions());
                }
            }
        }
        /** @var MenuItem $child */
        foreach ($menuContacts->getChildren() as $name => $child) {
            $menu->addChild($name, $child->getOptions());
            if ($child->getChildren()) {
                /** @var MenuItem $childChild */
                foreach ($child->getChildren() as $childName => $childChild) {
                    $menu[$name]->addChild($childName, $childChild->getOptions());
                }
            }
        }
        /** @var MenuItem $child */
        foreach ($menuAfter->getChildren() as $name => $child) {
            $menu->addChild($name, $child->getOptions());
            if ($child->getChildren()) {
                /** @var MenuItem $childChild */
                foreach ($child->getChildren() as $childName => $childChild) {
                    $menu[$name]->addChild($childName, $childChild->getOptions());
                }
            }
        }
        return $menu;
    }
}
