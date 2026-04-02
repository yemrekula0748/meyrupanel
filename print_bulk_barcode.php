<?php
// Hata ayıklama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Gerekli kütüphaneler
require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/autoload.php';
require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/tecnickcom/tcpdf/tcpdf.php';
require_once 'DB.php';

session_start();

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new DB();

// Geçerli filtreleri tanımla
$validFilters = ['UcretAlici', 'Bedelsiz'];

// Parametre kontrolü
if (!isset($_GET['odeme_sarti']) || !in_array($_GET['odeme_sarti'], $validFilters)) {
    die('Geçersiz filtre.');
}

$filter = $_GET['odeme_sarti'];

// Filtreye göre sorgu oluştur
if ($filter === 'UcretAlici') {
    // Ücreti Alıcıdan
    $kargoValues = ['Ücreti Alıcıdan'];
} elseif ($filter === 'Bedelsiz') {
    // Bedelsiz
    $kargoValues = ['Bedelsiz'];
} else {
    die('Geçersiz filtre.');
}

// Kargo tiplerine göre SQL sorgusunu oluştur
$kargoList = "'" . implode("','", array_map('addslashes', $kargoValues)) . "'";
$sql = "SELECT * FROM siparisler
        WHERE TRIM(kargo) IN ($kargoList)
          AND barkod_basilma_durumu = 'Basılmamış'
          AND kargo_barkodu IS NOT NULL
        ORDER BY id DESC
        LIMIT 100";

$result = $db->query($sql);
$orders = $result->fetch_all(MYSQLI_ASSOC);

if (!$orders) {
    die('Barkod basılacak sipariş bulunamadı.');
}

// Font yolunu belirt
$fontPath = '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/Ubuntu-Regular.ttf'; // Font dosyasının doğru yolu
$fontName = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);

// TCPDF nesnesini oluştur
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistem');
$pdf->SetTitle('Toplu Barkod Listesi');
$pdf->SetMargins(2, 2, 2);
$pdf->SetAutoPageBreak(TRUE, 0);

// Siparişleri döngüye alarak her biri için barkod sayfası oluştur
foreach ($orders as $order) {
    $pdf->AddPage('P', [100, 100]); // 100x100 boyutlu barkod sayfası

    // Font ayarı
    $pdf->SetFont($fontName, '', 10);

    // ---------- GÖNDERİCİ SOL ÜST ----------
    $pdf->SetXY(5, 5);
    $pdf->SetFont($fontName, 'B', 10);
    $pdf->Cell(40, 5, 'GÖNDERİCİ:', 0, 2, 'L');
    $pdf->SetFont($fontName, '', 9);
    $pdf->MultiCell(
        40, 4,
        "MeyruKids\nvarsak Süleyman Demirel bulvarı \n Karşıyaka mahallesi\nNo 137 A\nKepez / ANTALYA\n" . $order['kargo'],
        0,
        'L',
        false
    );

    // ---------- SAĞ ÜSTTE ALICI ----------
    $pdf->SetXY(55, 5);
    $pdf->SetFont($fontName, '', 9);
    $pdf->Cell(40, 5, 'PTT', 0, 2, 'R');
    $pdf->SetFont($fontName, 'B', 10);
    $pdf->Cell(40, 5, 'ALICI:', 0, 2, 'R');
    $pdf->SetFont($fontName, '', 9);
    $pdf->MultiCell(
        40, 4,
        $order['musteri_ismi'] . "\n" .
        "TEL : " . $order['musteri_telefonu'] . "\n" .
        $order['urunler'],
        0,
        'R',
        false
    );

    // ---------- ADRES ORTA KISIM ----------
    $pdf->Ln(4); // Biraz boşluk
    $pdf->SetFont($fontName, '', 9);
    $pdf->Cell(0, 5, "ADRES : " . $order['musteri_adresi'], 0, 1, 'C');
    $pdf->Cell(0, 5, $order['musteri_ilce'] . " / " . $order['musteri_il'], 0, 1, 'C');

    // ---------- BARKOD VE ALT YAZILARI ----------
    $pdf->Ln(2); // Barkod öncesi boşluk
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
    $barcodeX = 20; 
    $barcodeW = 82; 
    $barcodeH = 25; 
    $pdf->write1DBarcode(
        $order['kargo_barkodu'],
        'C128',
        $barcodeX,
        '',     
        $barcodeW,
        $barcodeH,
        0.4,
        $style,
        'N'
    );

    // Barkod altındaki kod
    $pdf->Ln(2);
    $pdf->SetFont($fontName, 'B', 15);
    $pdf->Cell(0, 5, 'PTT', 0, 1, 'C');
}

// Barkod basım durumlarını güncelle
$orderIds = array_column($orders, 'id');
if (!empty($orderIds)) {
    // Hazırlanan yer tutucuları '?' ile oluştur
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $updateSql = "UPDATE siparisler
                  SET barkod_basilma_durumu = 'Basılmış'
                  WHERE id IN ($placeholders)";
    
    // Prepared statement kullanarak güvenli bir şekilde güncelleme yap
    $stmt = $db->prepare($updateSql);
    if ($stmt) {
        // Her bir id'yi bind et
        $types = str_repeat('i', count($orderIds)); // 'i' tipi integer için
        $stmt->bind_param($types, ...$orderIds);
        $stmt->execute();
        $stmt->close();
    } else {
        die('Güncelleme sorgusu hazırlanamadı.');
    }
}

// PDF çıktısını göster
$pdf->Output('toplu_barkod_listesi.pdf', 'I');
?>
