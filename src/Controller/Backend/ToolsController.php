<?php

namespace Celtic34fr\ContactCore\Controller\Backend;

use Celtic34fr\ContactCore\Entity\PieceJointe;
use Celtic34fr\ContactCore\Enum\UtilitiesPJEnums;
use Celtic34fr\ContactCore\Repository\PieceJointeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tools', name: 'tools_')]
class ToolsController extends AbstractController
{
    const view_doc = __CLASS__ . '::view_doc';
    const raw_doc = __CLASS__ . '::raw_doc';
    const print_doc = __CLASS__ . '::print_doc';

    private PieceJointeRepository $pieceJointeRepo;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        /** @var PieceJointeRepository $pieceJointeRepo */
        $pieceJointeRepo = $this->entityManager->getRepository(PieceJointe::class);
        $this->pieceJointeRepo = $pieceJointeRepo;
    }

    #[Route('/view_doc/{id}', name: 'view_doc')]
    public function view_pj(PieceJointe $pieceJointe): Response
    {
        $contexte = [
            'mime'  => $pieceJointe->getFileMime(),
            'width' => '80%',
            'title' => "Fichier {$pieceJointe->getFileName()}",
            'route' => 'tools_view_doc',
        ];
        $contexte['contenu'] = $pieceJointe->getFileContentBase64();
        return $this->render("@contact-core/main/visu_doc.html.twig", $contexte);
    }

    #[Route('/raw_doc/{id}', name: 'raw_doc')]
    public function raw_doc(PieceJointe $pieceJointe): Response
    {
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

    #[Route('print_doc/{id}', name: 'print_doc')]
    public function print_docu(PieceJointe $document): Response
    {
        $context = [
            'mime' => $document->getFileMime(),
            'width' => '50%',
            'content' => $document->getFileContentBase64(),
        ];
        return $this->render('@contact-core/main/print_doc.html.twig', $context);
    }

    #[Route('/delt_doc/{id}', name: 'delt_doc')]
    public function delt_pj(PieceJointe $pieceJointe)
    {
        $this->pieceJointeRepo->remove($pieceJointe, true);
        return new JsonResponse(array('type' => 'success', "message" => "Suppression de documents terminée OK"));
    }

    #[Route('/upload_doc', name: 'upload_doc', methods: ['POST'])]
    public function uploadDoc(Request $request)
    {
        $medias = $_FILES;
        foreach ($medias as $media) {
            $document = new PieceJointe();
            $document->setFileName($media['name']);
            $document->setFileMime($media['type']);
            $document->setFileSize($this->filesize_formated((int) $media['size']));
            $content = file_get_contents($media['tmp_name']);
            $document->setFileContent($content);
            $document->setDateCreated(new DateTimeImmutable('now'));
            $document->setTempo(true);
            $document->setUtility(UtilitiesPJEnums::Logo->_toString());
            $this->entityManager->persist($document);
        }
        $this->entityManager->flush();
        return new JsonResponse(array('type' => 'success', "message" => "Téléchargement de documents terminé OK"));
    }

    private function filesize_formated(int $size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}
