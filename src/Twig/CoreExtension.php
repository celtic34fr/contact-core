<?php

namespace Celtic34fr\ContactCore\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Service\CourrielsDbInfos;

class CoreExtension extends AbstractExtension
{
    public function __construct(private ExtensionConfig $extensionConfig, private CourrielsDbInfos $courrielsDbInfos)
    {
    }


    /**
     * Register Twig functions.
     */
    public function getFunctions(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];

        return [
            // new TwigFunction('template_exist', [$this, 'twigFunction_template_exist']), -> à récréer par rapport à Bolt CMS
            new TwigFunction('isExtensionInstall', [$this, 'twigFunction_isExtensionInstall']),
            new TwigFunction('mailError', [$this, 'twigFunction_mailError'], $safe),
        ];
    }

    public function twigFunction_isExtensionInstall($name): bool
    {
        return $this->extensionConfig->isExtnsionInstall($name);
    }

    public function twigFunction_mailError()
    {
        return $this->courrielsDbInfos->countCourrielsToSend();
    }
}