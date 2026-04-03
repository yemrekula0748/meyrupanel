<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/autoload.php';
require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/tecnickcom/tcpdf/tcpdf.php';
require_once 'DB.php';

$db = new DB();

// ID parametresini al
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    die("Geçersiz ID");
}

// Yazı tipi dosyasını tanımla
$fontPath = '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/Ubuntu-Regular.ttf';
if (!file_exists($fontPath)) {
    die("Yazı tipi dosyası bulunamadı: $fontPath");
}
$fontName = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);

// Tek siparişi al
$sql = "SELECT musteri_ismi, musteri_adresi, musteri_telefonu, kargo_barkodu, musteri_ilce, musteri_il, hangisayfa, urunler, hangikargo, kargo
        FROM siparisler 
        WHERE id = ?";

$result = $db->query($sql, [$id], 'i');

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // TCPDF ayarları
    $pdf = new TCPDF('P', 'mm', [100, 150]);
    $pdf->SetMargins(-1, -1, -1);
    $pdf->SetAutoPageBreak(false);
    $pdf->AddPage();

    // Oval Kenarlık
    $pdf->SetLineWidth(0.5);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->RoundedRect(5, 5, 90, 140, 4, '1111', 'D');

    $gondericiIsmi = '';
    if (stripos(strtolower($row['hangikargo']), 'sevim') !== false) {
        $gondericiIsmi = "Sevim Aydın";
    } elseif (stripos(strtolower($row['hangikargo']), 'yunus') !== false) {
        $gondericiIsmi = "MeyruKids";
    }

    // Ücret tipini belirle
    $ucretTipi = ($row['kargo'] == 'Ücreti Alıcıdan') ? 'ÜCRETİ ALICIDAN' : 'BEDELSİZ';

    // Gönderici Bilgileri
    $pdf->SetFont($fontName, 'B', 10);
    $pdf->SetXY(6, 6);
    $pdf->Cell(90, 5, 'GÖNDERİCİ:', 0, 1, 'L', false);
    $pdf->SetFont($fontName, '', 9);
    $pdf->SetX(6);
    $pdf->MultiCell(90, 4, $gondericiIsmi . "\nVarsak Süleyman Demirel bulvarı \nKarşıyaka mahallesi\nNo 137/a \nKepez / ANTALYA\n" . $ucretTipi, 0, 'L', false);

    // Alıcı Bilgileri
    $pdf->SetFont($fontName, 'B', 10);
    $pdf->SetXY(55, 6);
    $pdf->Cell(40, 5, 'ALICI:', 0, 1, 'R', false);
    $pdf->SetFont($fontName, '', 9);
    $pdf->SetX(55);
    $pdf->MultiCell(40, 4, 
        $row['musteri_ismi'] . "\nTELEFON: " . $row['musteri_telefonu'] . 
        "\n" . $row['musteri_il'] . "/" . $row['musteri_ilce'], 
        0, 'R', false);
    

    // Ürün Bilgileri
    $urunler = explode(",", $row['urunler']);
    $urunler = array_map('trim', $urunler);
    $urunSayilari = array_count_values($urunler);
    $urunlerLines = [];
    foreach ($urunSayilari as $urun => $adet) {
        if ($adet > 1) {
            $urunlerLines[] = $urun . ' - ' . $adet . ' ADET';
        } else {
            $urunlerLines[] = $urun;
        }
    }
    $urunlerString = implode("\n", $urunlerLines);

    $pdf->SetFont($fontName, '', 9);
    $pdf->SetXY(6, 45);
    $pdf->MultiCell(90, 4, $urunlerString, 0, 'C', false);

    // Adres
    $pdf->SetXY(6, 72);
    $pdf->MultiCell(90, 4, "ADRES: " . $row['musteri_adresi'], 0, 'C', false);
    
    // Barkod (en alta taşındı)
    $pdf->SetXY(20, 109);
    $style = [
        'align' => 'C',
        'stretch' => true,
        'fitwidth' => true,
        'fgcolor' => [0, 0, 0],
        'bgcolor' => false,
        'text' => true,
        'font' => $fontName,
        'fontsize' => 10,
    ];
    $pdf->write1DBarcode($row['kargo_barkodu'], 'C128', '', '', 80, 20, 0.4, $style, 'N');

    // Kargo Firması
    $kargoFirmasi = getKargoFirmasi($row['kargo_barkodu']);
    $pdf->SetFont($fontName, 'B', 10);
    $pdf->SetXY(10, 131);
    $pdf->Cell(0, 5, $kargoFirmasi, 0, 1, 'C');

    // Kaynak bilgisi
    $pdf->SetFont($fontName, '', 10);
    $pdf->SetXY(10, 138);
    $pdf->Cell(0, 5, $row['hangisayfa'], 0, 1, 'C');

    // PDF çıktısı
    $pdf->Output('barkod.pdf', 'I');
} else {
    die("Sipariş bulunamadı");
}

function getKargoFirmasi($barkod) {
    if (strpos($barkod, 'KP') === 0) {
        return 'PTT';
    } elseif (strpos($barkod, 'SM') === 0) {
        return 'Hepsijet';
    }
    return '';
}
