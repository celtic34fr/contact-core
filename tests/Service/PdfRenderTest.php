<?php

namespace Celtic34fr\ContactCore\Tests\Service;

use Celtic34fr\ContactCore\Service\PdfRender;
use Dompdf\Dompdf;
use PHPUnit\Framework\TestCase;

class PdfRenderTest extends TestCase
{
    public function testGenerateDomPDF()
    {
        $html = '<html><body>Hello World!</body></html>';
        $pdfRender = new PdfRender(['paper' => 'A4|landscape', 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);

        $domPdf = $pdfRender->generateDomPDF($html);

        $this->assertInstanceOf(Dompdf::class, $domPdf);
        $this->assertStringContainsString('Hello World!', $domPdf->output());
    }

    public function testGenerateBinaryPdf()
    {
        $html = '<html><body>Hello World!</body></html>';
        $pdfRender = new PdfRender(['paper' => 'A4|landscape', 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);

        $binaryPdf = $pdfRender->generateBinaryPdf($html);

        $this->assertIsString($binaryPdf);
        $this->assertStringContainsString('Hello World!', $binaryPdf);
    }

    public function testShowPdfFile()
    {
        // As this method contains an actual exit statement, we cannot perform a unit test for it
        $this->markTestSkipped('Cannot test method with exit statement');
    }
}