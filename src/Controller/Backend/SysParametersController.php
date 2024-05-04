<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Bolt\Configuration\Config;
use Celtic34fr\ContactCore\Entity\Parameter;
use Celtic34fr\ContactCore\Entity\PieceJointe;
use Celtic34fr\ContactCore\EntityRedefine\SocialNetwork;
use Celtic34fr\ContactCore\Enum\UtilitiesPJEnums;
use Celtic34fr\ContactCore\Form\SysSocialNetworkType;
use Celtic34fr\ContactCore\FormEntity\SysSocialNetwork;
use Celtic34fr\ContactCore\Repository\ParameterRepository;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Service\UploadFiles;
use Celtic34fr\ContactCore\Traits\UtilitiesTrait;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;

#[Route('sys_params', name: 'sys-params-')]
class SysParametersController extends AbstractController
{
    use UtilitiesTrait;

    private UploadFiles $uploadFiles;
    private ExtensionConfig $extConfig;

    public function __construct(
        private ParameterRepository $parameterRepo,
        private EntityManagerInterface $entityManager,
        private RouterInterface $router,
        private Packages $assetManager,
        private KernelInterface $kernel,
        private Config $config,
    )
    {
        $this->uploadFiles = new UploadFiles($entityManager, $router, $assetManager);
        $this->extConfig = new ExtensionConfig($this->kernel, $this->config);
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
            'title' => "Liste des fontions ou catégories des relations",
        ]);
    }

    #[Route('/socialnetworks_list', name: 'socialnetworks-list')]
    public function socialnetworks_list(Request $request)
    {
        $paramsList = [];
        $entreprise = $this->extConfig->get('contact-core/entreprise');
        $socialNetworkList = $this->parameterRepo->findValidSocialNetworks();
        if ($socialNetworkList) {
            foreach ($socialNetworkList as $id => $infos) {
                list ($error, $logo) = $this->uploadFiles->prepare_initial_datas([$infos['logoID']], "thumbnail");
                $item = [
                    'id'   => $id,
                    'name' => $infos['name'],
                    'logo' => $logo[0],
                    'pUrl' => ($entreprise[$infos['name']] ?? ""),
                ];
                $paramsList[] = $item;
            }
        }

        $socialNetwork = new SysSocialNetwork();
        $form = $this->createForm(SysSocialNetworkType::class, $socialNetwork);

        $myPreset = uniqid();
        $request->getSession()->set("myPreset", $myPreset);
        return $this->render('@contact-core/sys-params/socialnetworks_list.html.twig', [
            'paramsList' => $paramsList,
            'title' => "Liste des réseaux sociaux utilisés",
            'form' => $form->createView(),
            'logoDB' => [],
            'acceptFiles' => ".png,.gif,.jpg,.jpeg,.svg",
            'myPreset' => $myPreset,
            'logoID' => "non",
        ]);
    }

    #[Route('/socialnetworks_form/{id}', name: 'socialnetworks-form')]
    public function socialnetworks_form(Parameter $parameter, Request $request): JsonResponse
    {
        $response = "";
        if ($request->getMethod() == "GET" ) {
            // récupération des informations pour alimentation du formulaire
            /*$paramId = $request->query->get('paramId');
            $parameter = $this->parameterRepo->find($paramId);*/
            $socialNetwork = new SocialNetwork($parameter);
            $entreprise = $this->extConfig->get('contact-core/entreprise');
            list($error_prepare, $prepare_initial_datas) = $this->uploadFiles->prepare_initial_datas([$socialNetwork->getLogoID()], "thumbnail", "array");
            if (!$error_prepare) {
                $prepare_initial_datas = $prepare_initial_datas[0];

                $response = [
                    'type'      => 'success',
                    'message'   => 'informations récupérées',
                    'name'      => $socialNetwork->getName(),
                    'urlPage'   => ($entreprise[$socialNetwork->getName()] ?? ""),
                    'logoID'    => $prepare_initial_datas,
                ];
            } else {
                $response = [
                    'type' => $error_prepare[0]['type'],
                    'message' => $error_prepare[0]['title']." ".$error_prepare[0]['body'],
                ];
            }
        } elseif ($request->getMethod() == "POST") {
            // traitement du formulaire de saisie d'informations réseaux sociaux
            $myPreset = $request->getSession()->get("myPreset");

            $socialNetwork = new SysSocialNetwork();
            $socialNetwork->setLogoID($request->request->get('logoId'));
            $form = $this->createForm(SysSocialNetworkType::class, $socialNetwork);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // traitement de l'icone du réseau social
                $socialNetworkName = $socialNetwork->getName();
                if ($socialNetworkName) {
                    $parameter = $this->parameterRepo->findSocialNetworkByName($socialNetworkName);
                }
                /** @var Parameter $parameter */
                if (!$parameter) {
                    $parameterList = $this->parameterRepo->findSocialNetworks();
                    $parameter = new Parameter();
                    $parameter->setCle(SocialNetwork::CLE);
                    $parameter->setOrd(sizeof($parameterList));
                    $parameter->setValeur($socialNetwork->getValeur() + 1);
                    $this->parameterRepo->save($parameter, true);
                    $logo = $this->entityManager->getRepository(PieceJointe::class)->find($socialNetwork->getLogoID());
                    $logo->setTempo(false);
                    $this->entityManager->flush();
                } else {
                    if ($parameter->getValeur() != $socialNetwork->getValeur()) {
                        $parameter->setValeur($socialNetwork->getValeur());
                        $this->parameterRepo->updateParameter($parameter, true);    
                    }
                }
                // traitement de la référence à la page du réseau social
                $configFile = $this->getParameter('kernel.project_dir') . '/config/extensions/celtic34fr-contactcore.yaml';
                $yaml = Yaml::parse(file_get_contents($configFile));
                $entreprise = $yaml['entreprise'];

                $socialNetworkUrlPage = $socialNetwork->getUrlPage();
                if ($socialNetworkUrlPage && $entreprise[$socialNetworkName] != $socialNetworkUrlPage) {
                    $entreprise[$socialNetworkName] = $socialNetworkUrlPage;
                    $yaml['entreprise'] = $entreprise;
                    $new_yaml = Yaml::dump($yaml, 2);
                    file_put_contents($configFile, $new_yaml);
                }
                $response = [
                    'type' => "success",
                    'message' => "Enregistrement du réseau social ".$socialNetwork->getName()." réalisé avec succés",
                ];
            }    
        } else {
            $response = [
                'type' => "error",
                'message' => "Type de traitement non pris en charge (".$request->getMethod().")",
            ];
        }

        return new JsonResponse($response);
    }

    #[Route('/socialnetworks_upload', name: 'socialnetworks-upload', methods: ['POST'])]
    public function socialnetworks_upload(Request $request): JsonResponse
    {
        $operator = $this->getUser();
        return $this->uploadFiles->uploadFile(
            $request,
            [".png", ".gif", ".jpg", ".jpeg", ".svg"],
            [],
            UtilitiesPJEnums::Network->_toString(),
            $operator
        );
    }

    #[Route('/socialnetworks_del/{id}', name: 'socialnetworks-del')]
    public function socialnetworks_del(Parameter $social, Request $request): JsonResponse
    {
        $response = [];

        return new JsonResponse($response);
    }

    #[Route('socialnetworks_toggle/{status}', name:'socialnetworks-toggle')]
    public function socialnetworks_toggle(bool $status, Request $request): JsonResponse
    {
        $paramsList = [];
        if ($status) {
            $values = $this->parameterRepo->findAllSocialNetworks();
        } else {
            $values = $this->parameterRepo->findValidSocialNetworks();
        }
        if ($values) {
            foreach ($values as $id => $infos) {
                list ($error, $logo) = $this->uploadFiles->prepare_initial_datas([$infos['logoID']], "thumbnail");
                $item = [
                    'id'   => $id,
                    'name' => $infos['name'],
                    'logo' => $logo[0],
                    'pUrl' => ($entreprise[$infos['name']] ?? ""),
                ];
                $paramsList[] = $item;
            }
        }
        $response = [
            'type' => "success",
            'message' => "Récupération des informations des réseaux sociaux exécutée avecc succès",
            'paramsList' =>$paramsList,
        ];
        return new JsonResponse($response);
    }
}