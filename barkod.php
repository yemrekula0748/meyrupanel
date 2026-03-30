<?php
// barkod.php

if(!isset($_GET['kod'])){
   die("Barkod kodu belirtilmedi!");
}

$barcodeCode = $_GET['kod'];

// 1) İçerik türünü PNG yapın
header('Content-Type: image/png'); 
// İsterseniz cache kontrolü de kapatabilirsiniz
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// 2) TCPDF veya benzeri kütüphaneler yerine 
//    PHP Barcode Generator gibi bir kütüphane ile PNG oluşturabilirsiniz.

// Örnek 1: "Picqer" barcodes: composer require picqer/php-barcode-generator
use Picqer\Barcode\BarcodeGeneratorPNG;

// Barkod generator nesnesi
$generator = new BarcodeGeneratorPNG();
$pngData = $generator->getBarcode($barcodeCode, $generator::TYPE_CODE_128);

// 3) Ekrana bas (PNG binary)
echo $pngData;
