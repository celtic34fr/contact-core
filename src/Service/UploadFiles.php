<?php

namespace Celtic34fr\ContactCore\Service;

use Bolt\Entity\User;
use Celtic34fr\ContactCore\Entity\PiecesJointes;
use Symfony\Component\Asset\Packages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Celtic34fr\ContactCore\Enum\UtilitiesPJEnums;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;

class UploadFiles
{
    public function __construct(private EntityManagerInterface $entityManager, private RouterInterface $router,
            private Packages $assetManager)
    {
    }

    public function uploadFile(
        Request $request,
        array $valid_extensions,
        array $dimensions,
        string $cible,
        User $owner
    ): JsonResponse {
        if (!$request->isXmlHttpRequest() || empty($_FILES)) {
            return new JsonResponse(['error' => ['exécution demande impossible']], Response::HTTP_BAD_REQUEST);
        }

        $errMsg = [];

        $file = $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];
        $typ = $_FILES['file']['type'];
        $siz = $_FILES['file']['size'];
        $err = $_FILES['file']['error'];
        $dim = getimagesize($tmp);

        $ext = '.' . strtolower(pathinfo($file, PATHINFO_EXTENSION));

        $final_file = rand(1000, 1000000) . $file;

        if (in_array($ext, $valid_extensions)) {

            if (array_key_exists('width', $dimensions)) {
                if ($dim[0] > $dimensions['width']) {
                    $errMsg[] = "Image chargé trop large, maximum " . $dimensions['width'] . " points";
                }
            }
            if (array_key_exists('height', $dimensions)) {
                if ($dim[1] > $dimensions['height']) {
                    $errMsg[] = "Image chargé trop haute, maximum " . $dimensions['height'] . " points";
                }
            }
            if (array_key_exists('size', $dimensions)) {
                if ($siz > $dimensions['size']) {
                    $errMsg[] = "Image chargé trop lourde, maximum $siz octets";
                }
            }
            $utilitiesEnums = new UtilitiesPJEnums();
            if (!$utilitiesEnums->isValid($cible)) {
                $errMsg[] = "cible $cible invalide";
            }

            if (empty($errMsg)) {
                $file_raw = file_get_contents($tmp);
                $fichier = new PiecesJointes();

                $fichier->setFileContent($file_raw);
                $fichier->setFileName($final_file);
                $fichier->setFileMime($typ);
                $fichier->setDateCreated(new DateTimeImmutable('now'));
                $fichier->setTempo(true);
                $fichier->setUtility($cible);

                $this->entityManager->persist($fichier);
                $this->entityManager->flush();

                return new JsonResponse(['id' => strval($fichier->getId())]);
            }
            return  new JsonResponse(['error' => $errMsg], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(['error' => ['type de fichier non supporté (' . $ext . ')']], Response::HTTP_BAD_REQUEST);
    }

    public function prepare_initial_datas(array $pj_ids, string $mode): array
    {
        $err_msg = [];
        $initial_datas = [];
        if (!empty($pj_ids)) {
            if (!in_array($mode, ['icon', 'thumbnail'])) {
                $msg = "Mode $mode inconnu, traitement impossible";
                $item['title'] = "Une erreur est survenue";
                $item['body'] = $msg;
                $item['type'] = 'danger';
                $err_msg[] = $item;
            }
            if (!$err_msg) {
                foreach ($pj_ids as $pj_id) {
                    /** @var PiecesJointes $pj */
                    $pj = $this->entityManager->getRepository(PiecesJointes::class)->find($pj_id);

                    $item = [];
                    $item['id'] = $pj_id;
                    $item['view_url'] = $this->router->generate('view_pj', ['id' => $pj_id, 'pjs' => $table]);

                    switch ($mode) {
                        case 'icon':
                            $name = $pj->getFileName();
                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                            $file_path = __DIR__ . "/../../public/contact_assets/icons/$ext.svg";
                            if (file_exists($file_path)) {
                                $url = $this->assetManager->getUrl("contact_assets/icons/$ext.svg");
                            } else {
                                $url = $this->assetManager->getUrl('contact_assets/icons/unknown.svg');
                            }
                            $item['preview_url'] = $url;
                            break;
                        case 'thumbnail':
                            $item['preview_url'] = $this->router->generate('raw_pj', ['id' => $pj_id, 'pjs' => $table]);
                            break;
                    }
                    $initial_datas[] = $item;
                }
            }
        }
        return [$err_msg, $initial_datas];
    }
}
