<?php

namespace Celtic34fr\ContactCore\Twig\Runtime;

use Exception;
use Bolt\Configuration\Config;
use Bolt\Twig\AssetsExtension;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Extension\RuntimeExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AssetThemeRuntime implements RuntimeExtensionInterface
{
    public function __construct(private AssetsExtension $asset, private ContainerInterface $container, private Filesystem $filesystem,
    private Config $config)
    {
    }

    public function twigFunction_assetTheme(string $path, string $theme = null): string
    {
        if ($theme === null) {
            $theme = $this->config->get('bolt.theme');
        }
        $theme_path = 'theme/'.$theme.'/'.$path;

        $search_path = $this->container->getParameter('kernel.project_dir').'/';
        $search_path .= $this->container->getParameter('bolt.public_folder').'/theme/';
        $search_path .= $theme;
        if ($this->existFolder($search_path)) {
            /** cas ou le thème est connu */
            $full_path = $search_path . '/' . $path;
            if ($this->filesystem->exists($full_path)) {
                /** cas ou le fichier recherché existe */
                return  sprintf(
                    "%s://%s%s",
                    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
                    $_SERVER['SERVER_NAME'],
                    $this->asset->getAssetUrl($theme_path, null)
                  );
            }
        }
        throw new Exception("resource thème $theme et/ou fichier $path inconnu ");
    }


    private function existFolder(string $path_folder): bool {
        return $this->filesystem->exists($path_folder);
    }
}
