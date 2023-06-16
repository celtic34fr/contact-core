<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('parameters')]
class ParametersController extends AbstractController
{
    #[Route('/informations', name: 'info-structure')]
    public function index(): Response
    {
        $dbPrefix = $this->getParameter('bolt.table_prefix');
        $filesystem = new Filesystem();
        $projectDir = $this->getContainer()->getParameter('kernel.project_dir');

        return $this->render('@contact-core/parameters/information.html.twig', [
        ]);
    }
}
