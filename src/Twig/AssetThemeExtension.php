<?php

namespace Celtic34fr\ContactCore\Twig;

use Exception;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AssetThemeExtension extends AbstractExtension
{
    public function __construct(private AssetExtension $assets, private ContainerInterface $container, private Filesystem $filesystem)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('assetTheme', [$this, 'twigFunction_assetTheme']),
        ];
    }

    public function twigFunction_assetTheme(string $path, string $theme = null): string
    {
        $theme_path = 'theme/'.$theme.'/'.$path;

        $search_path = $this->container->getParameter('kernel.project_dir').'/';
        $search_path .= $this->container->getParameter('bolt.public_folder').'/theme/';
        $search_path .= $theme;
        if ($this->existFolder($search_path)) {
            /** cas ou le thème est connu */
            $full_path = $search_path . '/' . $path;
            if ($this->filesystem->exists($full_path)) {
                /** cas ou le fichier recherché existe */
                return $this->assets->getAssetUrl($theme_path, null);
            }
        }
        throw new Exception("resource thème $theme et/ou fichier $path inconnu ");
    }


    private function existFolder(string $path_folder): bool {
        return $this->filesystem->exists($path_folder);
    }
}
