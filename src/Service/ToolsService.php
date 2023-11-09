<?php

namespace Celtic34fr\ContactCore\Service;

use Bolt\Collection\DeepCollection;
use Bolt\Configuration\Config;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ToolsService
{
    /**
     * constructor
     *
     * @param Config $config
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(private Config $config, private UrlGeneratorInterface $urlGenerator)
    {
        
    }

    /**
     * generateBreadcrumbsFromMenu : generate breadscrumbs string according BoltCMS menu and current URI
     *
     * @param DeepCollection|null $menu
     * @param string|null $uri
     * @param string $baseUrl
     * @return string
     */
    public function generateBreadcrumbsFromMenu(DeepCollection $menu = null, string $uri = null, 
        string $baseUrl, bool $bs5 = false)
    : string
    {
        if (!$menu || !$uri) return [];
        $breadcrumbs = [];
        $strBreadcrumbs = "";

        /**
         * -> le menu est un arbre à parcourir
         * -> tout noeud ayant des enfants devra être parcouru enfant après enfant 
         * -> si dans le parcours enfant après enfant on découdre un nouveau noeud, on doit reproduire le même traitement
         * -> si l'item de menu n'est pas trouvé et que l'on est sur une feuille de l'arbre, il faut
         *      -> retourner faux à la recherche,
         *      -> remonter au dernier noeud rencontré
         *      -> remonter le chemin trouvé quelque soit le cas
         * -> si l'item de menu est trouvé, il faut :
         *      -> retourner vrai à la recherche,
         *      -> retourner le chemin trouvé
         *      ->  remonter au dernier noeud trouvé pour propager le résultat jusqu'à la racine du menu
         * -> la structure à retourner en guise de chemin d'accès à l'item de menu doit comprendre :
         *      -> la route Symfony pour générer l'URL d'accès,
         *      -> le libellé de l'item de menu pour supporter le lien hypertexte
         * -> dans la cas ou le noued de menu lu ne doit pas générer de lien, ka route Symfony sera retournée vide.
         */

        /** @bar DeepCollection $item */
        foreach ($menu as $rang => $item) {
            $label = $item->get('label');
            $link = $item->get('link');
            $bItem = [];

            if ($item->isKeyNotEmpty('submenu')) { // extract from submenus
                $bItem = $this->processNode($item->get('submenu'), $breadcrumbs, $uri);
            }

            if (($bItem && $bItem['find']) || ($uri == $link)) {
                $breadcrumbs[] = [
                    'label' => $label,
                    'link' => $link,
                ];
                break;
            }
        }

        if ($breadcrumbs && $bItem && $bItem['find']) {
            $breadcrumbs = array_merge($bItem['breadcrumbs'], $breadcrumbs);
            $breadcrumbs[] = $this->getHomepage($menu);
        }

        if ($breadcrumbs) {
            $strBreadcrumbs = $this->formatBreadcrumbs($breadcrumbs, $baseUrl, $bs5);
        }


        return $strBreadcrumbs;
    }

    private function processNode(DeepCollection $node, array $breadcrumbs, string $uri, bool $find = false): array
    {
        foreach ($node as $item) {
            $label = $item->get('label');
            $link = $item->get('link');
            $bItem = [];

            if ($item->isKeyNotEmpty('submenu')) {
                $bItem = $this->processNode($item->get('submenu'), $breadcrumbs, $uri, $find);
            }

            $nItem = $this->extractInfos($label, $link, $uri, $bItem);
            if ($nItem) $breadcrumbs[] = $nItem;
            if (array_key_exists('find', $nItem) && $nItem['find']) break;
        }
        return ['breadcrumbs' => $breadcrumbs, 'find' => $nItem['find'] ?? false];
    }

    /**
     * Search and return Homrpage information for first element of breadcrumbs
     *
     * @param DeepCollection $menu
     * @return array
     */
    private function getHomepage(DeepCollection $menu): array
    {
        $breadcrumbs = [];
        /** @bar DeepCollection $item */
        foreach ($menu as $rang => $item) {
            if ($item->get('link') == 'homepage') {
                $label = $item->get('label');
                $link = "homepage";
                break;
            }
        }
        return (isset($label) && $label && isset($link) && $link) ? [
            'label' => $label,
            'link' => $link,
        ] : [];
    }

    private function extractInfos($label, $link, $uri, $bItem) {
        $item = [];
        if ($link == $uri) {
            $item = [
                'label' => $label,
                'link' => "",
                'find' => true,
            ];
        } elseif ($bItem && $bItem['find']) {
            $item = [
                'label' => $label,
                'link' => $link,
                'find' => $bItem['find'],
            ];
        }
        return $item;
    }

    /**
     * Format breadcrumbs as string or BootStrap 5 object
     *
     * @param array $breadcrumbs
     * @param string $baseUrl
     * @param boolean $bs5
     * @return string
     */
    private function formatBreadcrumbs(array $breadcrumbs, string $baseUrl, bool $bs5 = false): string
    {
        $breadcrumbs = array_reverse($breadcrumbs, true);
        $strBreadcrumbs = $bs5 ? '<ul class="breadcrumb-nav">' : "";
        if (empty($baseUrl)) $baseUrl = $this->urlGenerator->generate("homepage", [], UrlGenerator::ABSOLUTE_URL);
        foreach ($breadcrumbs as $key => $item) {
            $label = $item['label'];
            $link = $item['link'];
            if ($link == "homepage") {
                $link = $this->urlGenerator->generate("homepage", [], UrlGenerator::ABSOLUTE_URL);
            } else {
                $link = (!empty($link)) ? $baseUrl  . '/' . $link : "";
            }

            if ($bs5) { // format as Bootstrap 5 Breadcrumbs

                if ($key !== array_key_last($breadcrumbs)) {
                    if (!empty($link)) {
                        $strBreadcrumbs .= '<li class="breadcrumb-item"><a href="' . $link .'">' . $label .'</a></li>';
                    } else {
                        $strBreadcrumbs .= '<li class="breadcrumb-item">' . $label .'</li>';
                    }
                } else {
                    $strBreadcrumbs .= '<li class="breadcrumb-item active" aria-current="page">'. $label .'</li>';
                }

                                    
            } else { // format as string
                if (!empty($link)) {
                    $strBreadcrumbs .= '<a href="' . $link . '">' . $label . '</a>';
                } else {
                    $strBreadcrumbs .= $label;
                }
            }
        }
        if ($bs5) $strBreadcrumbs .= '</ul>';

        return $strBreadcrumbs;
    }
}