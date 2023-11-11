<?php

namespace Celtic34fr\ContactCore\Twig\Extension;

use Celtic34fr\ContactCore\Twig\Runtime\VitrineRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class VitrineExtension extends AbstractExtension
{
    const SAFE = ['is_safe' => ['html']];

    public function getFunctions(): array
    {
        return [
            new TwigFunction('buildBreadcrumbs', [VitrineRuntime::class, 'twigFunction_buildBreadcrumbs']),
            new TwigFunction('buildArrayBreadcrumbs', [VitrineRuntime::class, 'twigFunction_buildArrayBreadcrumbs']),
        ];
    }
}