<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Exception;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Celtic34fr\ContactCore\Traits\Utilities;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Form\EntrepriseInfosType;
use Celtic34fr\ContactCore\FormEntity\EntrepriseInfos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('parameters')]
class ParametersController extends AbstractController
{
    use Utilities;

    private $schemaManager;

    public function __construct(private EntityManagerInterface $entityManager, private Environment $twigEnvironment,
            private ExtensionConfig $extConfig)
    {
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

        $entreprise = $this->extConfig->get('contact-core/entreprise');
        if (!$entreprise) {
            $entreprise  = [
                'designation' => null,
                'siren' => null,
                'siret' => null,
                'courriel' => null,
                'telephone' => null,
                'courriel_reponse'=> null,              
            ];
        }

        $entrepriseInfos = new EntrepriseInfos();
        $entrepriseInfos->setByArray($entreprise);
        $form = $this->createForm(EntrepriseInfosType::class, $entrepriseInfos);

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
    #[Route('/uploadLogo', name: 'upload-logo', methods:['POST'])]
    public function uploadLogo(): void
    {
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