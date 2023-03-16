<?php

namespace Celtic34fr\ContactCore\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfRender
{
    private Dompdf $domPdf;
    private Options $domPdfOptions;

    /**
     * PdfRender constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->domPdf = new DomPdf();
        foreach($options as $key => $option) {
            switch ($key) {
                case "paper":
                    $size = substr($option, 0, strpos($option, '|')) ?? "A4";
                    $orientation = substr($option, strpos($option, '|') + 1) ?? "portrait";
                    $this->domPdf->setPaper($size, $orientation);
                    break;
                case 'isRemoteEnabled':
                    $this->domPdf->getOptions()->setIsRemoteEnabled($option);
                    break;
                case 'isPhpEnabled':
                    $this->domPdf->getOptions()->setIsPhpEnabled($option);
                    break;
            }
        }
        $this->domPdfOptions = $this->domPdf->getOptions();
    }

    /**
     * @param $html
     */
    public function showPdfFile($html)
    {
        $tmpPdf = $this->generateDomPDF($html);
        $tmpPdf->stream("detail.pdf", [
            'Attachment' => true
        ]);
        exit(0);
    }

    /**
     * @param $html
     * @return string|null
     */
    public function generateBinaryPdf($html): ?string
    {
        $tmpPdf = $this->generateDomPDF($html);
        return $tmpPdf->output();
    }

    /**
     * @param $html
     * @return Dompdf
     */
    public function generateDomPDF($html): Dompdf
    {
        $domPdf = new Dompdf($this->domPdfOptions);
        $domPdf->loadHtml($html);
        $domPdf->render();

        $html = str_replace('DOMPDF_PAGE_COUNT_PLACEHOLDER', $domPdf->getCanvas()->get_page_count(), $html);

        $tmpPdf = new Dompdf($domPdf->getOptions());
        $tmpPdf->loadHtml($html);
        $tmpPdf->render();
        return $tmpPdf;
    }
}