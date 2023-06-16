<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Celtic34fr\ContactCore\Traits\Utilities;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('parameters')]
class ParametersController extends AbstractController
{
    use Utilities;

    private $schemaManager;

    public function __construct(private EntityManagerInterface $entityManager, private Environment $twigEnvironment)
    {
        $this->schemaManager = $entityManager->getConnection()->getSchemaManager();
    }

    #[Route('/informations', name: 'my_informations')]
    public function information(): Response
    {
        $dbPrefix = $this->getParameter('bolt.table_prefix');
        $filesystem = new Filesystem();
        $projectDir = $this->getContainer()->getParameter('kernel.project_dir');

        $response =
        $this->render('@contact-core/parameters/information.html.twig', [
        ]);

        return $response;
    }
}
