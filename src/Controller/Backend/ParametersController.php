<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Bolt\Configuration\Config;
use Celtic34fr\ContactCore\Entity\Parameter;
use Celtic34fr\ContactCore\Entity\PieceJointe;
use Celtic34fr\ContactCore\Enum\UtilitiesPJEnums;
use Celtic34fr\ContactCore\Form\EntrepriseInfosType;
use Celtic34fr\ContactCore\Form\ParameterType;
use Celtic34fr\ContactCore\FormEntity\EntrepriseInfosFE;
use Celtic34fr\ContactCore\FormEntity\ParameterFE;
use Celtic34fr\ContactCore\Repository\ParameterRepository;
use Celtic34fr\ContactCore\Repository\PieceJointeRepository;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Service\UploadFiles;
use Celtic34fr\ContactCore\Traits\UtilitiesTrait;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

#[Route('parameters', name: 'parameters-')]
class ParametersController extends AbstractController
{
    use UtilitiesTrait;

    const newEditAction = __CLASS__ . '::newEditAction';

    private $schemaManager;
    private ExtensionConfig $extConfig;
    private UploadFiles $uploadFiles;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RouterInterface $router,
        private Packages $assetManager,
        private Environment $twigEnvironment,
        private KernelInterface $kernel,
        private Config $config,
        private PieceJointeRepository $pieceJointeRepo,
        private ParameterRepository $parameterRepo,
    ) {
        $this->schemaManager = $entityManager->getConnection()->getSchemaManager();
        $this->extConfig = new ExtensionConfig($this->kernel, $this->config);
        $this->uploadFiles = new UploadFiles($entityManager, $router, $assetManager);
    }

    /**
     * @throws Exception
     */
    #[Route('/informations', name: 'info-structure')]
    public function index(Request $request): Response
    {
        if ($this->extConfig->isExtnsionInstall("contactcore")) {
            if ($request->getMethod() == "GET") {
                /* recherche des logo en temporaire (tempo: true) pour suppression de la base */
                /* mise en commentaire pour remplacement par tache CRON journalière
                $logosTempo = $this->pieceJointeRepo
                    ->findBy(['tempo' => true, 'utility' => UtilitiesPJEnums::Logo->_toString()]);
                if ($logosTempo) {
                    foreach ($logosTempo as $logoTempo) {
                        $this->entityManager->remove($logoTempo);
                    }
                    $this->entityManager->flush();
                }
                */
            }

            $entreprise = $this->extConfig->get('celtic34fr-contactcore/entreprise');
            if (!$entreprise) {
                $entreprise  = [
                    'designation' => null,
                    'siren' => null,
                    'siret' => null,
                    'courriel' => null,
                    'telephone' => null,
                    'courriel_reponse' => null,
                    'logoID' => null,
                ];
            }
            $ouverture = $this->extConfig->get('celtic34fr-contactcore/ouverture');
            if (!$ouverture) {
                $ouverture = [
                    'lundi' => '-',
                    'mardi' => '-',
                    'mercredi' => '-',
                    'jeudi' => '-',
                    'vendredi' => '-',
                    'samedi' => '-',
                    'dimanche' => '-',
                ];
            }
            $ouverture = $this->formatOpened($ouverture);

            $logo = null;
            if ($entreprise['logoID']) {
                /** récupération du logo de l'entreprise si déjà paramétré */
                /** @var PieceJointe $logo */
                $logo = $this->pieceJointeRepo->find((int) $entreprise['logoID']);
            }
            $item = [];
            if ($logo) {
                list($errors, $item) = $this->uploadFiles->prepare_initial_datas([$logo->getId()], 'thumbnail');
            }
            $logoDB = (empty($errors)) ? $item : [];

            $entrepriseInfos = new EntrepriseInfosFE();
            $entrepriseInfos->setByArray($entreprise);
            $form = $this->createForm(EntrepriseInfosType::class, $entrepriseInfos);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //traitement du formulaire
                $configFile = $this->getParameter('kernel.project_dir') . '/config/extensions/celtic34fr-contactcore.yaml';
                $yaml = Yaml::parse(file_get_contents($configFile));
                $yaml['entreprise']['designation'] = $entrepriseInfos->getDesignation() ?? "";
                $yaml['entreprise']['siren'] = $entrepriseInfos->getSiren() ?? "";
                $yaml['entreprise']['siret'] = $entrepriseInfos->getSiret() ?? "";
                $yaml['entreprise']['courriel'] = $entrepriseInfos->getCourriel() ?? "";
                $yaml['entreprise']['telephone'] = $entrepriseInfos->getTelephone() ?? "";
                $yaml['entreprise']['courriel_reponse'] = $entrepriseInfos->getReponse();

                /** 
                 * traitement du logo de l'entreprise
                 * -> cas 1 : pas de logo au départ, pas de logo à validation du formulaire => ne rien faire
                 * -> cas 2 : logo au départ et logo à validation du formulaire non changé => ne rien faire
                 * -> cas 3 : logo au départ et logo à validation du formulaire différent
                 *              => invalider le logo existant,
                 *              => basculer de tempo à définitif le logo dans PieceJointe,
                 *              => initialiser logoID avec ID du logo dans PieceJointe
                 * -> cas 4 : pas de logo au départ et logo à validation du formulaire
                 *              => basculer de tempo à définitif le logo dans PieceJointe,
                 *              => initialiser logoID avec ID du logo dans PieceJointe
                 * -> cas 5 : logo au départ et pas de logo à validation du formulaire
                 *              => invalider le logo existant,
                 */
                $logoID = "";
                if ($entrepriseInfos->getLogoID()) {
                    $logoIDs = $this->extractLogoID($entrepriseInfos->getLogoID());
                    $prevID = $logoIDs['prev'];
                    $nextID = $logoIDs['next'];

                    if ($prevID && $nextID) {
                        // suppression du logo existant => invalidation dans PieceJointe et logoID à vide
                        /** @var PieceJointe $logoDB */
                        $logoDB = $this->pieceJointeRepo->find((int) $prevID);
                        $logoDB->setUpdatedAt(new DateTimeImmutable('now'));
                        $this->pieceJointeRepo->save($logoDB, false);
                    }
                    if ($nextID) {
                        /** @var PieceJointe $logoDB */
                        $logoDB = $this->pieceJointeRepo->find((int) $nextID);
                        $logoDB->setTempo(false);
                        $logoDB->setUpdatedAt(new DateTimeImmutable('now'));
                        $this->pieceJointeRepo->save($logoDB, false);
                        $logoID .= $nextID;
                    }
                    $this->entityManager->flush();
                }
                $yaml['entreprise']['logoID'] = $logoID;

                //traitement des horraires d'ouveture
                $horaires = $request->request->get('horaires');
                

                $logo = $this->pieceJointeRepo->findOneBy(['tempo' => true, 'utility' => UtilitiesPJEnums::Logo->_toString()]);
                if ($logo) { // ajout ou mise à jour si present dans le formulaire
                    /** @var PieceJointe $logo */
                    $logo->setTempo(false);
                    $this->pieceJointeRepo->save($logo, true);
                    $yaml['entreprise']['logoID'] = $logo->getId();
                } else { // supression du logo si présent en fichier et non dans le formulaire
                    if (array_key_exists('logoID', $yaml['entreprise'])) {
                        unset($yaml['entreprise']['logoID']);
                    }
                }

                $new_yaml = Yaml::dump($yaml, 2);
                file_put_contents($configFile, $new_yaml);
                $this->addFlash('success', 'Fichier de configuration bien modifié et enregistré');
                $this->redirectToRoute('bolt_dashboard');
            }

            $myPreset = uniqid();
            $request->getSession()->set("myPreset", $myPreset);
            $response =
                $this->render('@contact-core/parameters/information.html.twig', [
                    'form' => $form->createView(),
                    'logoDB' => $logoDB,
                    'logoID' => $entreprise['logoID'] ?? "",
                    'acceptFiles' => ".png,.gif,.jpg,.jpeg,.svg",
                    'route' => "parameters-upload-logo",
                    'myPreset' => $myPreset,
                    'ouverture' => $ouverture,
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

    #[Route('/new_params_list', name: 'params-list-new')]
    public function new_params_list(Request $request)
    {
        $paramList = [];
        $mode = "new";
        $cle = "";

        $args = compact('mode', 'cle', 'paramList');
        return $this->forward(self::newEditAction, $args);
    }

    #[Route('/edit_params_list/{id}', name: 'params-list-edt')]
    public function edit_params_list(Request $request, Parameter $parameter)
    {
        $mode = "edt";
        $cle = "";
        $paramList = [];

        $cle = $parameter->getCle();
        $paramList = $this->parameterRepo->getValuesParamterList($parameter->getCle());

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
                    $parameterList->setValues($this->extractValues($paramList));
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
            $message =  (($mode == "new") ? "Création " : "Mise à jour ") . "de la liste de paramètre effectuée avec succès";
            $this->addFlash('success', $message);
            $this->redirectToRoute("parameters-params-list");
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

    #[Route('/delete_params_list/{id}', name: 'params-list-del')]
    /** suppression de liste de paramètres */
    public function delete_params_list(Request $request, Parameter $parameter)
    {
        $parameterValues = $this->parameterRepo->getValuesParamterList($parameter->getCle());
        $title = "Demande de suppression de la liste " . $parameter->getCle();

        if ($request->getMethod() == "POST") {
            /** tritement demande suppression */
            $datas = $request->request->all();
            if (array_key_exists("delt", $datas)) {
                foreach ($parameterValues as $ord => $value) {
                    $paramItem = $this->parameterRepo->findOneBy(['cle' => $parameter->getCle(), 'ord' => (int) $ord + 1]);
                    $this->entityManager->remove($paramItem);
                }
                $this->entityManager->remove($parameter);
                $this->entityManager->flush();

                $this->redirectToRoute("parameter-params-list");
            }
        }

        return $this->render("@contact-core/parameters/delt.html.twig", [
            'title' => $title,
            'parameter' => $parameter,
            'parameterValues' => $parameterValues,
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

    /**
     * @param array $paramsValues
     * @return array
     */
    private function extractValues(array $paramsValues): array
    {
        $values = [];
        /** @var Parameter $paramValue */
        foreach ($paramsValues as $paramValue) {
            $values[$paramValue->getId()] = $paramValue->getValeur();
        }
        return $values;
    }

    /**
     * @param array $opened
     * @return array
     */
    private function formatOpened(array $opened): array
    {
        $formatedOpened = [];
        $jour = [
            'md' => '',
            'mf' => '',
            'sd' => '',
            'sf' => '',
        ];

        foreach ($opened as $day => $open) {
            $tempo = explode('/', $open);
            foreach ($tempo as $id => $value) {
                if ($value == '-') {
                    $formatedOpened[$day] = $jour;
                } else {
                    $value = explode('-', $value);
                    if ($id == 0) {
                        if (!array_key_exists($day, $formatedOpened)) $formatedOpened[$day] = [];
                        $formatedOpened[$day]['md'] = str_replace('h', ':', trim($value[0]));
                        $formatedOpened[$day]['mf'] = str_replace('h', ':', trim($value[1]));
                    } else {
                        if (!array_key_exists($day, $formatedOpened)) $formatedOpened[$day] = [];
                        $formatedOpened[$day]['sd'] = str_replace('h', ':', trim($value[0]));
                        $formatedOpened[$day]['sf'] = str_replace('h', ':', trim($value[1]));
                    }
                }
            }
        }
        return $formatedOpened;
    }

    /**
     * @param string $logoIDs
     * @return array
     */
    private function extractLogoID(string $logoIDs): array
    {
        // éclatement de logoIDs en 2, indice 0 => logo(s) avant, indice 1 => logo(s) après
        $logoIDs = explode('_', $logoIDs);
        $prevIDs = array_key_exists(0, $logoIDs) ? explode('-', $logoIDs[0]) : [];
        $nextIDs = array_key_exists(1, $logoIDs) ? explode('-', $logoIDs[1]) : [];

        foreach ($prevIDs as $key => $prevID) {
            if (!is_numeric($prevID)) unset($prevIDs[$key]);
        }

        foreach ($nextIDs as $key => $nextID) {
            if (!is_numeric($nextID)) unset($nextIDs[$key]);
        }
        return ['prev' => $prevIDs, 'next' => $nextIDs];
    }
}
