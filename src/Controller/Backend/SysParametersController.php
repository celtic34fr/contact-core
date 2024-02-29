<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Celtic34fr\ContactCore\Entity\Parameter;
use Celtic34fr\ContactCore\EntityRedefine\SocialNetwork;
use Celtic34fr\ContactCore\Form\SysSocialNetworkType;
use Celtic34fr\ContactCore\FormEntity\SysSocialNetwork;
use Celtic34fr\ContactCore\Repository\ParameterRepository;
use Celtic34fr\ContactCore\Traits\UtilitiesTrait;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function socialnetworks_list(Request $request)
    {
        $paramsList = $this->parameterRepo->findSocialNetworks();
        $socialNetwork = new SysSocialNetwork();
        $form = $this->createForm(SysSocialNetworkType::class, $socialNetwork);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parameter = new Parameter();
            $parameter->setCle(SocialNetwork::CLE);
            $parameter->setOrd(sizeof($paramsList));
            $parameter->setValeur($socialNetwork->getValeur());
            $parameter->setUpdatedAt(new DateTimeImmutable('now'));
            $this->parameterRepo->save($parameter, true);
        }

        return $this->render('@contact-core/sys-params/socialnetworks_list.html.twig', [
            'paramsList' => $paramsList,
            'title' => "Liste des réseaux sociaux utilisés",
            'form' => $form->createView(),
        ]);
    }
}