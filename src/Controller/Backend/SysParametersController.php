<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Bolt\Configuration\Config;
use Celtic34fr\ContactCore\Entity\Parameter;
use Celtic34fr\ContactCore\Entity\PieceJointe;
use Celtic34fr\ContactCore\EntityRedefine\ActivitySector;
use Celtic34fr\ContactCore\EntityRedefine\RelationCategory;
use Celtic34fr\ContactCore\EntityRedefine\SocialNetwork;
use Celtic34fr\ContactCore\Enum\UtilitiesPJEnums;
use Celtic34fr\ContactCore\Form\SysActivitySectorType;
use Celtic34fr\ContactCore\Form\SysRelationsCategoryType;
use Celtic34fr\ContactCore\Form\SysSocialNetworkType;
use Celtic34fr\ContactCore\FormEntity\SysActivitySector;
use Celtic34fr\ContactCore\FormEntity\SysRelationCategory;
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

    /**
     * Socials Networks Parameter Management
     */

    #[Route('/socialnetworks_list', name: 'socialnetworks-list')]
    public function socialnetworks_list(Request $request)
    {
        $paramsList = [];
        $entreprise = $this->extConfig->get('celtic34fr-contactcore/entreprise');
        $socialNetworkList = $this->parameterRepo->findValidSocialNetworks();
        if ($socialNetworkList) {
            foreach ($socialNetworkList as $id => $infos) {
                list ($error, $logo) = $this->uploadFiles->prepare_initial_datas([$infos['logoID']], "thumbnail");
                if ($logo) {
                    $item = [
                        'id'        => $infos['id'],
                        'name'      => $infos['name'],
                        'logo'      => $logo[0],
                        'pUrl'      => ($entreprise[$infos['name']] ?? ""),
                        'created'   => $infos['created'],
                        'updated'   => $infos['updated'],
                    ];
                    $paramsList[] = $item;
                }
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
    public function socialnetworks_form(Request $request, int $id=null): JsonResponse
    {
        $parameter = $id ? $this->parameterRepo->find($id) : null;
        $response = "";
        if ($request->getMethod() == "GET") {
            $socialNetwork = new SocialNetwork($parameter);
            $entreprise = $this->extConfig->get('contact-core/entreprise');
            list($error_prepare, $prepare_initial_datas) = $this->uploadFiles->prepare_initial_datas([$socialNetwork->getLogoID()], "thumbnail", "array");
            if (!$error_prepare) {
                $prepare_initial_datas = $prepare_initial_datas[0];
                $response = [
                    'type'      => 'success',
                    'message'   => 'informations récupérées',
                    'id'        => $socialNetwork->getId(),
                    'cle'       => $socialNetwork->getCle(),
                    'ord'       => $socialNetwork->getOrd(),
                    'created'   => $socialNetwork->getCreatedAt(),
                    'updated'   => $socialNetwork->getUpdatedAt(),
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
 
            $sysSocialNetwork = new SysSocialNetwork();
            $sysSocialNetwork->setLogoID($request->request->get('logoId'));
            $form = $this->createForm(SysSocialNetworkType::class, $sysSocialNetwork);
 
            $form->handleRequest($request);
 
            if ($form->isSubmitted() && $form->isValid()) {
                // traitement de l'icone du réseau social
                $socialNetworkName = $sysSocialNetwork->getName();
                $parameter = null;
                if ($socialNetworkName) {
                    $socialNetworkArray = $this->parameterRepo->findSocialNetworkByName($socialNetworkName);
                    $socialNetwork = new SocialNetwork($socialNetworkArray);
                    $socialNetwork->setLogoID($request->request->get('logoId'));
                    $parameter = $socialNetwork->getId() ? $this->parameterRepo->find($socialNetwork->getId()) : $parameter;
                }
                /** @var Parameter $parameter */
                if (!$parameter) {
                    $ord = $this->parameterRepo->findNextOrdSocialNetworks();
                    $parameter = new Parameter();
                    $parameter->setCle(SocialNetwork::CLE);
                    $parameter->setOrd($ord);
                    $parameter->setValeur($socialNetwork->getValeur());
                    $this->parameterRepo->save($parameter, true);
                    $logo = $this->entityManager->getRepository(PieceJointe::class)->find($socialNetwork->getLogoID());
                    $logo->setTempo(false);
                    $this->entityManager->flush();
                } else {
                    if ($parameter->getValeur() != $socialNetwork->getValeur()) {
                        $this->parameterRepo->update($parameter, $socialNetwork->getValeur(), true);    
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
 
    #[Route('/socialnetworks_delt/{id}', name: 'socialnetworks-del')]
    public function socialnetworks_del(Parameter $social, Request $request): JsonResponse
    {
        $response = [];
        $social->setUpdatedAt(new DateTimeImmutable('now'));
        $this->parameterRepo->save($social, true);
        $socialNetwork = new SocialNetwork($social);
 
        $response = [
            'type' => "success",
            'message' => "Suppressiion / Invalidation du réseau social ".$socialNetwork->getName()." réalisée avec succés",
        ];
 
        return new JsonResponse($response);
    }
 
    #[Route('/socialnetworks_actv/{id}', name: 'socialnetworks-act')]
    public function socialnetworks_act(Parameter $social, Request $request): JsonResponse
    {
        $response = [];
        $socialNetwork = new SocialNetwork($social);
        $active = $this->parameterRepo->findSocialNetworkByName($socialNetwork->getName());
        $lastSocial = new SocialNetwork($active);
        if ($active  && $active['updated'] == null) {
            $response = [
                'type' => "error",
                'message' => "Réseau social ".$socialNetwork->getName()." déjà actif, veuillez modifier ce dernier plutôt que de demander une réactivation",
            ];
        } else {
            $this->parameterRepo->update($lastSocial->getParameter(), $social->getValeur(), true);
            $response = [
                'type' => "success",
                'message' => "Réactivation du réseau social ".$socialNetwork->getName()." réalisée avec succés",
            ];
        }
 
        return new JsonResponse($response);
    }
 
    #[Route('/socialnetworks_toggle/{status}', name:'socialnetworks-toggle')]
    public function socialnetworks_toggle(string $status, Request $request): JsonResponse
    {
        $paramsList = [];
        if ($status == "true") {
            $values = $this->parameterRepo->findAllSocialNetworks();
        } else {
            $values = $this->parameterRepo->findValidSocialNetworks();
        }
        if ($values) {
            foreach ($values as $id => $infos) {
                list ($error, $logo) = $this->uploadFiles->prepare_initial_datas([$infos['logoID']], "thumbnail");
                $item = [
                   'id'        => $infos['id'],
                    'name'      => $infos['name'],
                    'logo'      => $logo[0],
                    'pUrl'      => ($entreprise[$infos['name']] ?? ""),
                    'cle'       => $infos['cle'],
                    'ord'       => $infos['ord'],
                    'created'   => $infos['created'],
                    'updated'   => $infos['updated'],
                ];
                $paramsList[] = $item;
            }
        }
        $response = [
            'type' => "success",
            'message' => "Récupération des informations des réseaux sociaux exécutée avec succès",
            'paramsList' =>$paramsList,
        ];
        return new JsonResponse($response);
    }
 
    /**
     * Contact Categories Management
     */

    #[Route('/rcategories_list', name: 'rcategories-list')]
    public function rcategories_list(Request $request)
    {
        $paramsList = $this->parameterRepo->findValidRelationCategories();
        $rcategory = new SysRelationCategory();
        $form = $this->createForm(SysRelationsCategoryType::class, $rcategory);
 
        $myPreset = uniqid();
        $request->getSession()->set("myPreset", $myPreset);
        return $this->render('@contact-core/sys-params/rcategories_list.html.twig', [
            'paramsList' => $paramsList,
            'title' => "Liste des fontions ou catégories des relations",
            'form' => $form->createView(),
            'myPreset' => $myPreset,
        ]);
    }
 
    #[Route('/rcategories_form/{id}', name: 'rcategories-form')]
    public function rcategories_form(Request $request, int $id=null): JsonResponse
    {
        $parameter = $id ? $this->parameterRepo->find($id) : null;
        $response = "";
        if ($request->getMethod() == "GET") {
            // récupération des informations pour alimentation du formulaire en retour d'appel AJAX
            $relationCategory = new RelationCategory($parameter);
            $response = [
                'type'          => 'success',
                'message'       => 'informations récupérées',
                'id'            => $relationCategory->getId(),
                'cle'           => $relationCategory->getCle(),
                'ord'           => $relationCategory->getOrd(),
                'created'       => $relationCategory->getCreatedAt(),
                'updated'       => $relationCategory->getUpdatedAt(),
                'category'      => $relationCategory->getCategory(),
                'description'   => $relationCategory->getDescription() ?? "",
            ];
        } elseif ($request->getMethod() == "POST") {
            //  traitement du formulaire de saisie des information de catégorie de relation (client/prospect/fournisseur/partenaire)
            $myPreset = $request->getSession()->get("myPreset");
 
            $sysRelationCategory = new SysRelationCategory();
            $form = $this->createForm(SysRelationsCategoryType::class, $sysRelationCategory);
 
            $form->handleRequest($request);
 
            if ($form->isSubmitted() && $form->isValid()) {
                $category = $sysRelationCategory->getCategory();
                $parameter = null;
                if ($category) {
                    $relationCategoryArray = $this->parameterRepo->findRelationCategoryByName($category);
                    $relationCategory = new RelationCategory($relationCategoryArray);
                    $parameter = $relationCategory->getId() ? $this->parameterRepo->find($relationCategory->getId()) : $parameter;
                }
                /** @var Parameter $parameter */
                if (!$parameter) {
                    $ord = $this->parameterRepo->findNextOrdRelationCategories();
                    $parameter = new Parameter();
                    $parameter->setCle(RelationCategory::CLE);
                    $parameter->setOrd($ord);
                    $parameter->setValeur($sysRelationCategory->getValeur());
                    $this->parameterRepo->save($parameter, true);
                } else {
                    if ($parameter->getValeur() != $sysRelationCategory->getValeur()) {
                        $this->parameterRepo->update($parameter, $sysRelationCategory->getValeur(), true);    
                    }
                }
                $response = [
                    'type' => "success",
                    'message' => "Enregistrement de la catégorie ".$sysRelationCategory->getCategory()." réalisé avec succés",
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
 
    #[Route('/rcategories_delt/{id}', name: 'rcategories-del')]
    public function rcategories_del(Parameter $category, Request $request): JsonResponse
    {
        $response = [];
        $category->setUpdatedAt(new DateTimeImmutable('now'));
        $this->parameterRepo->save($category, true);
        $relationCategory = new RelationCategory($category);
 
        $response = [
            'type' => "success",
            'message' => "Suppressiion / Invalidation de la catégorie ".$relationCategory->getCategory()." réalisée avec succés",
        ];
 
        return new JsonResponse($response);
    }
  
    #[Route('/rcategories_actv/{id}', name: 'rcategories-act')]
    public function rcategories_act(Parameter $category, Request $request): JsonResponse
    {
        $response = [];
        $relationCategory = new RelationCategory($category);
        $active = $this->parameterRepo->findRelationCategoryByName($relationCategory->getCategory());
        $lastCategory = new RelationCategory($active);
        if ($active  && $active['updated'] == null) {
            $response = [
                'type' => "error",
                'message' => "Catégorie ".$relationCategory->getCategory()." déjà active, veuillez modifier cette dernière plutôt que de demander une réactivation",
            ];
        } else {
            $this->parameterRepo->update($lastCategory->getParameter(), $category->getValeur(), true);
            $response = [
                'type' => "success",
                'message' => "Réactivation de la catégorie ".$relationCategory->getCategory()." réalisée avec succés",
            ];
        }
 
        return new JsonResponse($response);
    }

    #[Route('/rcategories_toggle/{status}', name: 'rcategories-toggle')]
    public function rcategories_toggle(string $status, Request $request): JsonResponse
    {
        $paramsList = [];
        if ($status == "true") {
            $values = $this->parameterRepo->findAllRelationCategories();
        } else {
            $values = $this->parameterRepo->findValidRelationCategories();
        }
        if ($values) {
            foreach ($values as $id => $infos) {
                $item = [
                    'id'            => $infos['id'],
                    'category'      => $infos['category'],
                    'description'   => $infos['description'],
                    'cle'           => $infos['cle'],
                    'ord'           => $infos['ord'],
                    'created'       => $infos['created'],
                    'updated'       => $infos['updated'],
                ];
                $paramsList[] = $item;
            }
        }
        $response = [
            'type' => "success",
            'message' => "Récupération des informations des catégories exécutée avec succès",
            'paramsList' =>$paramsList,
        ];
        return new JsonResponse($response);
    }
 
    /**
     * Activities Sectorsd Management
     */

    #[Route('/activities_list', name: 'activities-list')]
    public function activity_list(Request $request)
    {
        $paramsList = $this->parameterRepo->findValidActivitiesSectors();
        $acticitySector = new SysActivitySector();
        $form = $this->createForm(SysActivitySectorType::class, $acticitySector);
 
        $myPreset = uniqid();
        $request->getSession()->set("myPreset", $myPreset);
        return $this->render('@contact-core/sys-params/activities_list.html.twig', [
            'paramsList' => $paramsList,
            'title' => "Liste des secteurs d'activités",
            'form' => $form->createView(),
        ]);
    }

    #[Route('/activities_form/{id}', name: 'activities-form')]
    public function activities_form(Request $request, int $id=null): JsonResponse
    {
        $parameter = $id ? $this->parameterRepo->find($id) : null;
        $response = "";
        if ($request->getMethod() == "GET") {
            // récupération des informations pour alimentation du formulaire en retour d'appel AJAX
            $activitySector = new ActivitySector($parameter);
            $response = [
                'type'          => 'success',
                'message'       => 'informations récupérées',
                'id'            => $activitySector->getId(),
                'cle'           => $activitySector->getCle(),
                'ord'           => $activitySector->getOrd(),
                'created'       => $activitySector->getCreatedAt(),
                'updated'       => $activitySector->getUpdatedAt(),
                'name'          => $activitySector->getName(),
                'description'   => $activitySector->getDescription() ?? "",
                'parentId'      => $activitySector->getParentId() ?? null,
            ];
        } elseif ($request->getMethod() == "POST") {
            $myPreset = $request->getSession()->get("myPreset");
 
            $sysActivitySector = new SysActivitySector();
            $form = $this->createForm(SysActivitySectorType::class, $sysActivitySector);
 
            $form->handleRequest($request);
 
            if ($form->isSubmitted() && $form->isValid()) {
                $name = $sysActivitySector->getName();
                $parameter = null;
                if ($name) {
                    $activitySectorArray = $this->parameterRepo->findActivitySectorByName($name);
                    $activitySector = new ActivitySector($activitySectorArray);
                    $parameter = $activitySector->getId() ? $this->parameterRepo->find($activitySector->getId()) : $parameter;
                }
                /** @var Parameter $parameter */
                if (!$parameter) {
                    $ord = $this->parameterRepo->findNextOrdRelationCategories();
                    $parameter = new Parameter();
                    $parameter->setCle(RelationCategory::CLE);
                    $parameter->setOrd($ord);
                    $parameter->setValeur($sysActivitySector->getValeur());
                    $this->parameterRepo->save($parameter, true);
                } else {
                    if ($parameter->getValeur() != $sysActivitySector->getValeur()) {
                        $this->parameterRepo->update($parameter, $sysActivitySector->getValeur(), true);    
                    }
                }
                $response = [
                    'type' => "success",
                    'message' => "Enregistrement du secteur ".$sysActivitySector->getName()." réalisé avec succés",
                ];
            }
        }

        return new JsonResponse($response);
    }

    #[Route('/activities_delt/{id}', name: 'activities-delt')]
    public function activities_del(Parameter $sector, Request $request): JsonResponse
    {
        $paramsList = [];
        $response =  null;
        $sector->setUpdatedAt(new DateTimeImmutable('now'));
        $this->parameterRepo->save($sector, true);
        $acticitySector = new ActivitySector($sector);
 
        $response = [
            'type' => "success",
            'message' => "Suppressiion / Invalidation du secteur ".$acticitySector->getName()." réalisé avec succés",
        ];

        return new JsonResponse($response);
    }

    #[Route('/activities_actv/{id}', name: 'activities-actv')]
    public function activities_act(Parameter $sector, Request $request): JsonResponse
    {
        $paramsList = [];
        $response =  null;
        $activitySector = new ActivitySector($sector);
        $active = $this->parameterRepo->findActivitySectorByName($activitySector->getName());
        $lastCategory = new ActivitySector($active);
        if ($active  && $active['updated'] == null) {
            $response = [
                'type' => "error",
                'message' => "Secteur ".$activitySector->getName()." déjà actif, veuillez modifier ce dernier plutôt que de demander une réactivation",
            ];
        } else {
            $this->parameterRepo->update($lastCategory->getParameter(), $sector->getValeur(), true);
            $response = [
                'type' => "success",
                'message' => "Réactivation de la catégorie ".$activitySector->getName()." réalisée avec succés",
            ];
        }

        return new JsonResponse($response);
    }

    #[Route('/activities_upst/{id}', name: 'activities-upst')]
    public function activities_upst(Parameter $sector, Request $request): JsonResponse
    {
        $paramsList = [];
        $response =  null;
        $sector->setUpdatedAt(null);
        $this->parameterRepo->save($sector, true);
        $acticitySector = new ActivitySector($sector);
 
        $response = [
            'type' => "success",
            'message' => "Passage en Secteur du sous secteur ".$acticitySector->getName()." réalisé avec succés",
        ];

        return new JsonResponse($response);
    }

    #[Route('/activities_toggle/{status}', name: 'activities-toggle')]
    public function activities_toggle(string $status, Request $request): JsonResponse
    {
        $paramsList = [];
        $response =  null;
        if ($status == "true") {
            $values = $this->parameterRepo->findAllActivitiesSectors();
        } else {
            $values = $this->parameterRepo->findValidActivitiesSectors();
        }

        return new JsonResponse($response);
   }
}