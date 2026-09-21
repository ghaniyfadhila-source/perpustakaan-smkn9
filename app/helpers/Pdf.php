<?php
// app/helpers/Pdf.php

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf
{
  /**
   * Render HTML -> PDF then stream to browser.
   * $filename: nama file PDF
   * $download: false = buka di tab (inline), true = langsung download
   */
  public static function stream(string $html, string $filename, bool $download = false): void
  {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Footer: copyright kiri + page number kanan
    $canvas = $dompdf->getCanvas();
    $font = $dompdf->getFontMetrics()->get_font('Helvetica', 'normal');

    // posisi y disesuaikan (A4 tinggi ~842pt)
    $canvas->page_text(36, 820, "© " . date('Y') . " Perpustakaan SMKN 9 Semarang", $font, 9, [0,0,0]);
    $canvas->page_text(520, 820, "Hal {PAGE_NUM} / {PAGE_COUNT}", $font, 9, [0,0,0]);

    header('Content-Type: application/pdf');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    $dompdf->stream($filename, ['Attachment' => $download ? 1 : 0]);
    exit;
  }
}