<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Celtic34fr\ContactCore\Entity\Courriels;
use Celtic34fr\ContactCore\Enum\StatusCourrielEnums;
use Celtic34fr\ContactCore\Service\SendMailer;
use Celtic34fr\ContactCore\Trait\Utilities;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('courriels')]
class CourrielsController extends AbstractController
{
    use Utilities;

    private $schemaManager;

    public function __construct(private EntityManagerInterface $entityManager, private Environment $twigEnvironment)
    {
        $this->schemaManager = $entityManager->getConnection()->getSchemaManager();
    }

    #[Route('/list/{currentPage}', name: 'courriel_list')]
    /**
     * interface pour afficher les requêtes adressées par les internautes
     * @throws Exception
     */
    public function index(Request $request, SendMailer $mailer, $currentPage = 1): Response
    {
        $courriels = [];
        $dbPrefix = $this->getParameter('bolt.table_prefix');
        $form = [
            'page' => 1,
            'limit' => 10
        ];

        if ($this->existsTable($dbPrefix . 'courriels') == true) {
            $choices =  [];
            $nav = 0;
            $currentPage = 1;
            $limit = 10;
            $type = "all";

            if ($request->getMethod() == 'POST') {
                $form = $this->formatFormData($_POST);

                /** aiguillage pour action */
                $nav = $form['nav'] ?? null;
                if (!$nav) {
                    throw new Exception("Code $nav non géré");
                }
                $nav = (int) $nav;
                $currentPage = $form['page'] ?? 1;
                $type = $form['type'] ?? "all";
                $limit = $form['limit'] ?? 10;
                $choices = $form['choices'] ?? [];
                $pages = $form['pages'];

                $courriels = $this->entityManager->getRepository(Courriels::class)
                    ->findCourrielsAll($type, $currentPage, $limit);

                switch ($nav) {
                    case 99: // retour au tableau de bord
                        $response =  $this->redirectToRoute('bolt_dashboard');
                        break;
                    case 4: // bascule courriel en errur à tout courriel
                        $type = "all";
                        $currentPage = 1;
                        break;
                    case 3: // bascule tout courriel à courriel en erreur
                        $type = StatusCourrielEnums::Error;
                        $currentPage = 1;
                        break;
                    case 2: // renvoi des courriel(s) sélectionné(s)
                        foreach ($choices as $choice) {
                            // réémission des courriels choisis
                            /** @var Courriels $courriel */
                            $courriel = $this->entityManager->getRepository(Courriels::class)->find((int) $choice);
                            $sended = $mailer->sendTemplate(
                                $courriel->getDestinataire(),
                                $courriel->getTemplateCourriel(),
                                $courriel->getSujet(),
                                $courriel->getContextCourriel()
                            );
                            if ($sended) {
                                $courriel->setSendStatus(StatusCourrielEnums::Send->_toString());
                            }
                        }
                        $this->entityManager->flush();
                        break;
                    case 1: // navigation +1 page
                    case -1: // navigation -1 page
                        if ($currentPage > 1 && $nav === -1) {
                            $currentPage -= 1;
                        }
                        if ($currentPage < $pages && $nav === 1) {
                            $currentPage += 1;
                        }
                }
            }
            $courriels = $this->entityManager->getRepository(Courriels::class)
                ->findCourrielsAll($type, $currentPage, $limit);
            $response =
                $this->render('@contact-core/courriels/index.html.twig', [
                    'courriels' => $courriels['datas'] ?? [],
                    'currentPage' => $courriels['page'] ?? 1,
                    'pages' => $courriels['pages'] ?? 0,
                    'nav' => $nav,
                    'choices' => $choices,
                ]);
        } else {
            $this->addFlash('danger', "La table Courriels n'existe pas, veuillez en avertir l'administrateur");
        }
        return $response;
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
