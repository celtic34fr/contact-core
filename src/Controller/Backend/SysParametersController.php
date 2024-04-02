<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Bolt\Configuration\Config;
use Celtic34fr\ContactCore\Entity\Parameter;
use Celtic34fr\ContactCore\EntityRedefine\SocialNetwork;
use Celtic34fr\ContactCore\Enum\UtilitiesPJEnums;
use Celtic34fr\ContactCore\Form\SysSocialNetworkType;
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
            'title' => "Liste des catégories par type de relation (client / prospect / fournisseur / partenaire)",
        ]);
    }

    #[Route('/socialnetworks_list', name: 'socialnetworks-list')]
    public function socialnetworks_list(Request $request)
    {
        $paramsList = [];
        $entreprise = $this->extConfig->get('contact-core/entreprise');
        $socialNetworkList = $this->parameterRepo->findSocialNetworks();
        if ($socialNetworkList) {
            foreach ($socialNetworkList as $name => $logoId) {
                $item = [
                    'name' => $name,
                    'logo' => $logoId,
                    'pUrl' => ($entreprise[$name] ?? ""),
                ];
                $paramsList[] = $item;
            }
        }

        $socialNetwork = new SocialNetwork();
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
        ]);
    }

    #[Route('/socialnetworks_form', name: 'socialnetworks-form', methods: ['POST'])]
    public function socialnetworks_form(Request $request): JsonResponse
    {
        $response = "";

        if ($request->getMethod() == "GET" ) {
            // récupération des informations pour alimentation du formulaire
            $paramId = $request->query->get('paramId');
            $parameter = $this->parameterRepo->find($paramId);
            $socialNetwork = new SocialNetwork($parameter);
            $entreprise = $this->extConfig->get('contact-core/entreprise');

            $response = [
                'type'      => 'success',
                'message'   => 'informations récupérées',
                'name'      => $socialNetwork->getName(),
                'urlPage'   => ($entreprise[$socialNetwork->getName()] ?? ""),
                'logoID'    => $this->uploadFiles->prepare_initial_datas([$socialNetwork->getLogoID()], "thumbnail"),
            ];
        } elseif ($request->getMethod() == "POST") {
            // traitement du formulaire de saisie d'informations réseaux sociaux
            $myPreset = $request->getSession()->get("myPreset");

            $socialNetwork = new SocialNetwork();
            $socialNetwork->setLogoID($request->request->get('logoId'));
            $form = $this->createForm(SysSocialNetworkType::class, $socialNetwork);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // traitement de l'icone du réseau social
                $socialNetworkName = $socialNetwork->getName();
                if ($socialNetworkName) {
                    $parameter = $this->parameterRepo->findSocialNetworksByName($socialNetworkName);
                }
                /** @var Parameter $parameter */
                if (!$parameter) {
                    $parameterList = $this->parameterRepo->findSocialNetworks();
                    $parameter = new Parameter();
                    $parameter->setCle(SocialNetwork::CLE);
                    $parameter->setOrd(sizeof($parameterList));
                    $parameter->setValeur($socialNetwork->getValeur());
                    $this->parameterRepo->save($parameter, true);
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
}