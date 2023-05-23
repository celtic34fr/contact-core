<?php

namespace Celtic34fr\ContactCore\Twig\Runtime;

use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Service\CourrielsDbInfos;

class CoreMethodsRuntime implements RuntimeExtensionInterface
{
    public function __construct(private ExtensionConfig $extensionConfig, private CourrielsDbInfos $courrielsDbInfos,
        private Environment $twig_env)
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

    public function twigFunction_template_exist($template_name): bool
    {
        /* $template_name : @namespace/local_path/name.html.twig */
        return (bool) $this->twig_env->getLoader()->exists($template_name);
    }

    public function twigFunction_extentionsConfig(): ExtensionConfig
    {
        return $this->extensionConfig;
    }
}
