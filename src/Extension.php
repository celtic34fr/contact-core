<?php

namespace Celtic34fr\ContactCore;

use Bolt\Extension\BaseExtension;
use Celtic34fr\ContactCore\Widget\CourrielsWidget;
use Symfony\Component\Filesystem\Filesystem;

class Extension extends BaseExtension
{
    public function getName(): string
    {
        return 'Bolt Celtic34fr Contact Extension';
    }

    public function initialize($cli = false): void
    {
        /** ajout de l'espace de nommage pour accès aux templates de l'extension */
        $this->addTwigNamespace("contactcore", dirname(__DIR__)."/templates");
        $this->addWidget(new CourrielsWidget());
    }

    public function initializeCli(): void
    {
        // Nothing
    }

    public function install(): void
    {
        $filesystem = new Filesystem();
        $projectDir = $this->getContainer()->getParameter('kernel.project_dir');

        /** test existance contact_assets/css */
        $source = dirname(__DIR__) . '/public';
        $destination = $projectDir . '/public/contact-assets';
        if (!$filesystem->exists($destination)) {
            $mkdirCmd = sprintf(
                'mkdir -p %s && chmod -R 0777 %s',
                $destination,
                $destination
            );
            $this->executeShellCommand($mkdirCmd);
        }
        $this->doCopy($source, $destination, $filesystem);
    }

    /**
     * @param string $source
     * @param string $destination
     */
    private function doCopy(string $source, string $destination, Filesystem $filesystem): void
    {
        $filesystem->mirror($source, $destination);
    }
}