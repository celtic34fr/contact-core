<?php

namespace Celtic34fr\ContactCore\Tests\Service;

use Bolt\Entity\User;
use Celtic34fr\ContactCore\Service\UploadFiles;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class UploadFilesTest extends TestCase
{
    private UploadFiles $uploadFiles;

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $router = $this->createMock(RouterInterface::class);
        $assetManager = $this->createMock(Packages::class);

        $this->uploadFiles = new UploadFiles($entityManager, $router, $assetManager);
    }

    public function testUploadFileWithValidExtension()
    {
        $request = $this->createMock(Request::class);
        $valid_extensions = ['.jpg', '.png'];
        $dimensions = ['width' => 100, 'height' => 100, 'size' => 1024];
        $cible = 'test';
        $owner = $this->createMock(User::class);

        $result = $this->uploadFiles->uploadFile($request, $valid_extensions, $dimensions, $cible, $owner);

        $this->assertInstanceOf(JsonResponse::class, $result);
    }

    public function testPrepareInitialDatas()
    {
        $pj_ids = [1, 2, 3];
        $mode = 'icon';

        list($err_msg, $initial_datas) = $this->uploadFiles->prepare_initial_datas($pj_ids, $mode);

        $this->assertEmpty($err_msg);
        $this->assertIsArray($initial_datas);
    }

    public function testFileSizeFormatted()
    {
        $size = 1024;
        $formattedSize = $this->uploadFiles->filesize_formated($size);

        $this->assertIsString($formattedSize);
    }
}