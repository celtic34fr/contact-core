<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Celtic34fr\ContactCore\Entity\Courriels;
use Celtic34fr\ContactCore\Service\Utilities;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('courriels')]
class CourrielsController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private Environment $twigEnvironment,
                                protected ContainerInterface $container)
    {
    }

    #[Route('/list/{currentPage}', name: 'courriel_list')]
    /**
     * interface pour afficher les requêtes adressées par les internautes
     * @throws Exception
     */
    public function index(Utilities $utility, $currentPage = 1): Response
    {
        $courriels = [];
        $dbPrefix = $this->getParameter('bolt.table_prefix');

        if ($utility->existsTable($dbPrefix.'courriels') == true) {
            $courriels = $this->entityManager->getRepository(Courriels::class)
                ->findCourrielsAll($currentPage);
        } else {
            $this->addFlash('danger', "La table Courriels n'existe pas, veuillez en avertir l'administrateur");
        }
        return $this->render('@contact-gestion/courriels/index.html.twig', [
            'courriels' => $courriels['datas'] ?? [],
            'currentPage' => $courriels['page'] ?? 0,
            'pages' => $courriels['pages'] ?? 0,
        ]);
    }
}