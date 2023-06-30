<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Exception;
use Bolt\Entity\User;
use Twig\Environment;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Celtic34fr\ContactCore\Traits\Utilities;
use Symfony\Component\HttpFoundation\Request;
use Celtic34fr\ContactCore\Entity\PieceJointe;
use Symfony\Component\HttpFoundation\Response;
use Celtic34fr\ContactCore\Service\UploadFiles;
use Symfony\Component\Routing\Annotation\Route;
use Celtic34fr\ContactCore\Enum\UtilitiesPJEnums;
use Symfony\Component\HttpFoundation\JsonResponse;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Form\EntrepriseInfosType;
use Celtic34fr\ContactCore\FormEntity\EntrepriseInfos;
use Celtic34fr\ContactCore\Repository\PieceJointeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('parameters')]
class ParametersController extends AbstractController
{
    use Utilities;

    private $schemaManager;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Environment $twigEnvironment,
        private ExtensionConfig $extConfig,
        private PieceJointeRepository $pieceJointeRepo,
        private UploadFiles $uploadFiles,
    ) {
        $this->schemaManager = $entityManager->getConnection()->getSchemaManager();
    }

    #[Route('/informations', name: 'info-structure')]
    /**
     * interface pour afficher les requêtes adressées par les internautes
     * @throws Exception
     */
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
            }
            $entreprise['logo'] = $item;

            $entrepriseInfos = new EntrepriseInfos();
            $entrepriseInfos->setByArray($entreprise);
            $form = $this->createForm(EntrepriseInfosType::class, $entrepriseInfos);
            $form->handleRequest();

            if ($form->isSubmitted() && $form->isValid()) {
                //traitement du formulaire
                $configFile = $this->container->get('kernel')->getRootDir().'config/extensions/celtic34fr-contactcore.yaml';
                $yaml = Yaml::parse(file_get_contents( $configFile));
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
                }

                $new_yaml = Yaml::dump($yaml, 2);
                file_put_contents($configFile, $new_yaml);
                $this->addFlash('success','Fichier de configuration bien modifié et enregistré');
                $this->redirectToRoute('bolt_dashboard');
            }

            $response =
                $this->render('@contact-core/parameters/information.html.twig', [
                    'form' => $form->createView(),
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
            $request, [".png",".gif",".jpg",".jpeg",".svg"], [], UtilitiesPJEnums::Logo->_toString(), $owner);
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
