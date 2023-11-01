<?php

namespace Bolt\Extension\Celtic34fr\ContactCore\Twig\Extension;

use Bolt\Extension\Celtic34fr\ContactCore\Twig\Runtime\AssetThemeRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/** classe complément à la fonction TWIG asset() pour prise en charge des thème hors package */
class AssetThemeExtension extends AbstractExtension
{
    const SAFE = ['is_safe' => ['html']];

    public function getFunctions(): array
    {
        return [
            new TwigFunction('assetTheme', [AssetThemeRuntime::class, 'twigFunction_assetTheme']),
        ];
    }
}
