<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Celtic34fr\ContactCore\Repository\ParameterRepository;
use Celtic34fr\ContactCore\Traits\UtilitiesTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('sys_params', name: 'sys-params-')]
class SysParametersController extends AbstractController
{
    use UtilitiesTrait;

    public function __construct(private ParameterRepository $parameterRepo)
    {
    }

    #[Route('/activities_list', name: 'activities-list')]
    public function activity_list()
    {
        $paramsList = $this->parameterRepo->findActivitiesSectors();

        return $this->render('@contact-core/sys-params/activities_list.html.twig', [
            'paramsList' => $paramsList,
            'title' => "Liste des secteur d'activité",
        ]);
    }

    #[Route('/rcategories_list', name: 'rcategories-list')]
    public function rcategories_list()
    {
        $paramsList = $this->parameterRepo->findRelationCategories();

        return $this->render('@contact-core/sys-params/rcategories_list.html.twig', [
            'paramsList' => $paramsList,
            'title' => "Liste des catégories par type de relation (client / prospect / fournisseur / partenaire)",
        ]);
    }

    #[Route('/socialnetworks_list', name: 'socialnetworks-list')]
    public function socialnetworks_list()
    {
        $paramsList = $this->parameterRepo->findSocialNetworks();

        return $this->render('@contact-core/sys-params/socialnetworks_list.html.twig', [
            'paramsList' => $paramsList,
            'title' => "Liste des réseaux sociaux utilisés",
        ]);
    }
}