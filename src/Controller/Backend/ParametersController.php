<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Exception;
use Twig\Environment;
use Bolt\Configuration\Config;
use Celtic34fr\ContactCore\Entity\Parameter;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Celtic34fr\ContactCore\Traits\Utilities;
use Symfony\Component\HttpFoundation\Request;
use Celtic34fr\ContactCore\Entity\PieceJointe;
use Symfony\Component\HttpFoundation\Response;
use Celtic34fr\ContactCore\Service\UploadFiles;
use Symfony\Component\Routing\Annotation\Route;
use Celtic34fr\ContactCore\Enum\UtilitiesPJEnums;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Form\EntrepriseInfosType;
use Celtic34fr\ContactCore\Form\ParameterType;
use Celtic34fr\ContactCore\FormEntity\EntrepriseInfosFE;
use Celtic34fr\ContactCore\FormEntity\ParameterFE;
use Celtic34fr\ContactCore\Repository\ParameterRepository;
use Celtic34fr\ContactCore\Repository\PieceJointeRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('parameters')]
class ParametersController extends AbstractController
{
    use Utilities;

    const newEditAction = __CLASS__ . '::newEditAction';

    private $schemaManager;
    private ExtensionConfig $extConfig;
    private PieceJointeRepository $pieceJointeRepo;
    private ParameterRepository $parameterRepo;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Environment $twigEnvironment,
        private UploadFiles $uploadFiles,
        private KernelInterface $kernel,
        private Config $config
    ) {
        $this->schemaManager = $entityManager->getConnection()->getSchemaManager();
        $this->extConfig = new ExtensionConfig($this->kernel, $this->config);
        $this->pieceJointeRepo = $entityManager->getRepository(PieceJointe::class);
        $this->parameterRepo = $entityManager->getRepository(Parameter::class);
    }

    /**
     * interface pour afficher les requêtes adressées par les internautes
     * @throws Exception
     */
    #[Route('/informations', name: 'info-structure')]
    public function index(Request $request): Response
    {
        if ($this->extConfig->isExtnsionInstall("contactcore")) {
            /* recherche des logo en temporaire (tempo: true) pour suppression de la base */
            $logosTempo = $this->pieceJointeRepo
                ->findBy(['tempo' => true, 'utility' => UtilitiesPJEnums::Logo->_toString()]);
            if ($logosTempo) {
                foreach ($$logosTempo as $logoTempo) {
                    $this->entityManager->remove($logoTempo);
                }
                $this->entityManager->flush();
            }

            $entreprise = $this->extConfig->get('contact-core/entreprise');
            if (!$entreprise) {
                $entreprise  = [
                    'designation' => null,
                    'siren' => null,
                    'siret' => null,
                    'courriel' => null,
                    'telephone' => null,
                    'courriel_reponse' => null,
                ];
            }
            /** @var PieceJointe $logo */
            $logo = $this->pieceJointeRepo->findOneBy(['utility' => UtilitiesPJEnums::Logo->_toString()]);
            $item = [];
            if ($logo) {
                list($errors, $item) = $this->uploadFiles->prepare_initial_datas([$logo->getId()], 'thumbnail');
                $entreprise['logoID'] = $logo->getId();
            }
            $logoDB = (empty($errors)) ? $item : [];

            $entrepriseInfos = new EntrepriseInfosFE();
            $entrepriseInfos->setByArray($entreprise);
            $form = $this->createForm(EntrepriseInfosType::class, $entrepriseInfos);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //traitement du formulaire
                $configFile = $this->container->get('kernel')->getRootDir() . 'config/extensions/celtic34fr-contactcore.yaml';
                $yaml = Yaml::parse(file_get_contents($configFile));
                $yaml['entreprise']['designation'] = $entrepriseInfos->getDesignation() ?? "";
                $yaml['entreprise']['siren'] = $entrepriseInfos->getSiren() ?? "";
                $yaml['entreprise']['siret'] = $entrepriseInfos->getSiret() ?? "";
                $yaml['entreprise']['courriel'] = $entrepriseInfos->getCourriel() ?? "";
                $yaml['entreprise']['telephone'] = $entrepriseInfos->getTelephone() ?? "";
                $yaml['entreprise']['courriel_reponse'] = $entrepriseInfos->getReponse();

                $logo = $this->pieceJointeRepo->findOneBy(['utility' => UtilitiesPJEnums::Logo->_toString()]);
                if ($logo) {
                    /** @var PieceJointe $logo */
                    $logo->setTempo(false);
                    $this->pieceJointeRepo->save($logo, true);
                    $yaml['entreprise']['LogoID'] = $logo->getId();
                }

                $new_yaml = Yaml::dump($yaml, 2);
                file_put_contents($configFile, $new_yaml);
                $this->addFlash('success', 'Fichier de configuration bien modifié et enregistré');
                $this->redirectToRoute('bolt_dashboard');
            }

            $response =
                $this->render('@contact-core/parameters/information.html.twig', [
                    'form' => $form->createView(),
                    'logoDB' => $logoDB,
                ]);
        } else {
            throw new Exception("L'extension contact-core semble ne pas être installée, vérifiez votre configuration");
        }
        return $response;
    }

    /**
     * méthode AJAX de chargement du logo par drag&Drop
     */
    #[Route('/uploadLogo', name: 'upload-logo', methods: ['POST'])]
    public function uploadLogo(Request $request): JsonResponse
    {
        $owner = $this->getUser();
        return $this->uploadFiles->uploadFile(
            $request,
            [".png", ".gif", ".jpg", ".jpeg", ".svg"],
            [],
            UtilitiesPJEnums::Logo->_toString(),
            $owner
        );
    }

    #[Route('/params_list', name: 'params-list')]
    public function params_list()
    {
        $paramsList = $this->parameterRepo->getNameParametersList();

        return $this->render('@contact-core/parameters/params_list.html.twig', [
            'paramsList' => $paramsList,
        ]);
    }

    #[Route('/new_params_list', name: 'new-params-list')]
    public function new_params_list(Request $request)
    {
        $paramList = [];
        $mode = "new";
        $cle = "";

        $args = compact('mode', 'cle', 'paramList');
        return $this->forward(self::newEditAction, $args);
    }

    #[Route('/edit_params_list/{id}', name: 'edt-params-list')]
    public function edit_params_list(Request $request, Parameter $parameter)
    {
        $mode = "edt";
        $cle = "";
        $paramList = [];

        $cle = $parameter->getCle();
        $paramList = $this->parameterRepo->getParamtersList($parameter->getCle());

        $args = compact('mode', 'cle', 'paramList');
        return $this->forward(self::newEditAction, $args);
    }

    public function newEditAction(Request $request, string $mode, string $cle = null, array $paramList = [])
    {
        $errMsgs = [];
        $paramDescription = null;

        /** double contrôle pour accès direct même si peu probable */
        $parameterList = new ParameterFE();
        if ($cle) {
            $paramDescription = $this->parameterRepo->findOneBy(['cle' => $cle]);
        }
        if (!$cle && $paramList) {
            $errMsgs[] = "Valeurs de liste sans description, traitement impossible";
        }
        if ($paramList && !is_array($paramList)) {
            $errMsgs[] = "Liste de valeurs format imcompatible, traitement impossible";
        }
        switch ($mode) {
            case "new":
                if ($paramDescription) {
                    $errMsgs[] = "Liste de valeur $cle existe déjà, impossible d'en créer une ayant la même clé d'accès";
                }
                $title = "Création d'une liste de paramètres";
                break;
            case "edt":
                if (!$paramDescription) {
                    $errMsgs[] = "Demande de modification de la liste de valeur $cle qui n'est pas trouvée, traitement impossible";
                }
                $title = "Edition de la liste de paramètres $cle";
                $parameterList->setName($cle);
                $parameterList->setDescription($paramDescription->getValeur());
                if ($paramList) {
                    $parameterList->setValues($paramList);
                }
                break;
            default:
                $errMsgs[] = "$mode non prévue, traitement impossible";
                break;
        }

        $form = $this->createForm(ParameterType::class, $parameterList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** traitement du formulaire pour création / mise à jour de liste de paramètres */
            /** traitement entête de descrition */
            $cle = $parameterList->getName();
            $paramDescription = $this->parameterRepo->findOneBy(['cle' => $cle]);
            if (!$paramDescription) {
                $paramDescription = new Parameter();
                $paramDescription->setCle($cle);
                $paramDescription->setOrd(0);
                $paramDescription->setValeur($parameterList->getDescription());
                $this->parameterRepo->save($paramDescription, false);
            } else {
                $paramDescription->setValeur($parameterList->getDescription());
                $paramDescription->setUpdatedAt(new DateTimeImmutable('now'));
            }

            /** traitement des valeur dans la liste */
            if ($parameterList->getValues()) {
                foreach ($parameterList->getValues() as $idx => $value) {
                    $parameterOccur = new Parameter();
                    $parameterOccur->setCle($paramDescription->getCle());
                    $parameterOccur->setOrd($idx + 1);
                    $parameterOccur->setValeur($value);
                    $this->parameterRepo->save($parameterOccur, false);
                }
            }
            $this->entityManager->flush();
            $message =  (($mode == "new") ? "Création " : "Mise à jour "). "de la liste de paramètre effectuée avec succès";
            $this->addFlash('success',$message);
            $this->redirectToRoute("params-list");
        }

        return $this->render("@contact-core/parameters/form.html.twig", [
            'cle' => $cle,
            'title' => $title,
            'form' => $form->createView(),
            'errMsgs' => $errMsgs,
            'mode' => $mode,
            'id' => ($mode == "edt") ? $paramDescription->getId() : null,
        ]);
    }

    #[Route('/delete_params_list/{id}', name: 'del-params-list')]
    /** suppression de liste de paramètres */
    public function delete_params_list(Request $request, Parameter $parameter)
    {
        $parameterList = $this->parameterRepo->getParamtersList($parameter->getCle());

        if ($request->getMethod() == "POST") {
            /** tritement demande suppression */
            $datas = $request->request->all();
            if (array_key_exists("delt", $datas)) {
                foreach($parameterList as $ord => $value) {
                    $paramItem = $this->parameterRepo->findOneBy(['cle' => $parameter->getCle(), 'ord' => (int) $ord + 1]);
                    $this->entityManager->remove($paramItem);
                }
                $this->entityManager->remove($parameter);
                $this->entityManager->flush();
            
                $this->redirectToRoute("params-list");
            }
        }

        return $this->render("@contact-core/parameters/delt.html.twig", [
            'parameter' => $parameter,
            'parameterList' => $parameterList,
        ]);
    }

    /**
     * @param array $post
     * @return array
     */
    private function formatFormData(array $post): array
    {
        $form = [];
        $form['choices'] = [];
        foreach ($post as $key => $val) {
            switch ($key) {
                case 'nav':
                    $form[$key] = $val;
                    break;
                case 'choices':
                    $form['choices'] = json_decode(str_replace('\\', '', $val), true);
                    break;
                default:
                    if ((int)$key > 0 && strpos($key, 'choice') !== false) {
                        $form['choices'][(int)$key] = $val;
                    }
                    break;
            }
        }
        return $form;
    }
}
