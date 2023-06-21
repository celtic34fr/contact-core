<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Celtic34fr\ContactCore\Entity\PiecesJointes;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('tools')]
class ToolsController extends AbstractController
{
    const view_pj = __CLASS__ . '::view_pj';
    const raw_pj = __CLASS__ . '::raw_pj';

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/visu_pj/{id}', name: 'visu_pj')]
    public function view_pj(int $id): Response
    {
        /** @var PiecesJointes $pieceJointe */
        $pieceJointe = $this->entityManager->getRepository(PiecesJointes::class)->find($id);
        $contexte = [
            'mime' => $pieceJointe->getFileMime(),
            'width' => '80%',
            'titl' => "pièce jointe {$pieceJointe->getFileName()}",
            'route' => 'view_pj',
        ];
        $contenu = $pieceJointe->getFileContent();
        if (!is_string($contenu)) {
            $contenu = stream_get_contents($contenu);
        }
        $contexte['contenu'] = base64_encode($contenu);
        return $this->render("@contact-core/main/view.html.twig", $contexte);
    }

    #[Route('/raw_pj/{id}', name: 'raw_pj')]
    public function raw_pj(int $id): Response
    {
        /** @var PiecesJointes $pieceJointe */
        $pieceJointe = $this->entityManager->getRepository(PiecesJointes::class)->find($id);

        $response = new Response();
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $pieceJointe->getFileName());
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', $pieceJointe->getFileMime());
        $contenu = $pieceJointe->getFileContent();
        if (!is_string($contenu)) {
            $contenu = stream_get_contents($contenu);
        }
        $response->setContent($contenu);
        return $response;
    }
}
