<?php

namespace Celtic34fr\ContactCore\Twig\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Celtic34fr\ContactCore\Twig\Runtime\AssetThemeRuntime;

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
