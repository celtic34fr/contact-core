<?php

namespace Celtic34fr\ContactCore\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Service\CourrielsDbInfos;

class CoreMethodsRuntime implements RuntimeExtensionInterface
{
    public function __construct(private ExtensionConfig $extensionConfig, private CourrielsDbInfos $courrielsDbInfos)
    {
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
