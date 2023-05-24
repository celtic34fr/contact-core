<?php

namespace Celtic34fr\ContactCore\Twig\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Celtic34fr\ContactCore\Twig\Runtime\CoreMethodsRuntime;

class CoreMethodsExtension extends AbstractExtension
{
    const SAFE = ['is_safe' => ['html']];

    public function getFunctions(): array
    {
        return [
            new TwigFunction('isExtensionInstall', [CoreMethodsRuntime::class, 'twigFunction_isExtensionInstall'], self::SAFE),
            new TwigFunction('mailError', [CoreMethodsRuntime::class, 'twigFunction_mailError'], self::SAFE),
            new TwigFunction('template_exist', [CoreMethodsRuntime::class, 'twigFunction_template_exist', self::SAFE]),
            new TwigFunction('extConfig_get', [CoreMethodsRuntime::class, 'twigFunction_extConfig_get', self::SAFE]),
        ];
    }
}
