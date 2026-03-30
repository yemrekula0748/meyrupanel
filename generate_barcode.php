<?php
ob_start();

// Hata ayıklama için
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/autoload.php';
require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/tecnickcom/tcpdf/tcpdf.php';

if (!isset($_GET['code'])) {
    die("Barkod kodu belirtilmedi.");
}

// Barkod kodunu alıyoruz
$barcodeCode = $_GET['code'];

// Sayfa boyutlarını ayarla (10x10 cm)
$pageWidth = 100;
$pageHeight = 100;

// TCPDF nesnesini oluştur
$pdf = new TCPDF('P', 'mm', [$pageWidth, $pageHeight]);
$pdf->SetMargins(5, 5, 5);
$pdf->AddPage();

// Barkod ayarları
$style = [
    'position' => '',
    'align' => 'C',
    'stretch' => true,
    'fitwidth' => true,
    'border' => false,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => [0, 0, 0],
    'bgcolor' => false,
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 10,
    'stretchtext' => 4
];

// Sayfanın ortasına hizalamak için pozisyon hesaplaması
$barcodeWidth = 80;  // Barkod genişliği
$barcodeHeight = 30; // Barkod yüksekliği
$x = ($pageWidth - $barcodeWidth) / 2; // Yatay merkez
$y = ($pageHeight - $barcodeHeight) / 2; // Dikey merkez

// Barkodu ekle
$pdf->write1DBarcode($barcodeCode, 'C128', $x, $y, '', $barcodeHeight, 0.4, $style, 'N');

// Çıktı tamponunu temizle
ob_end_clean();

// PDF çıktısını göster
$pdf->Output('barkod.pdf', 'I');
?>
