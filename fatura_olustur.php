<?php

// Hata ayıklama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Gerekli dosyaları dahil et
require_once '/home/hpanel-semre/htdocs/semre.hpanel.com.tr/vendor/autoload.php';
require_once '/home/hpanel-semre/htdocs/semre.hpanel.com.tr/vendor/tecnickcom/tcpdf/tcpdf.php';
include 'DB.php';

// Çıktı tamponunu başlat
ob_start();

session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new DB();

// Sipariş ID'sini kontrol et
if (!isset($_GET['id'])) {
    die('Geçersiz sipariş ID');
}

$orderId = intval($_GET['id']);

// Sipariş bilgilerini çek
$sql = "SELECT * FROM siparisler WHERE id = ?";
$order = $db->query($sql, [$orderId], "i")->fetch_assoc();

if (!$order) {
    die('Sipariş bulunamadı.');
}

// Hesaplamalar
$odemeSarti = floatval($order['odeme_sarti']);
$birimFiyat = $odemeSarti / 1.10; // KDV Hariç Birim Fiyat
$malHizmetTutari = $birimFiyat;   // Miktar 1 olarak varsayılmış
$kdvTutari = $malHizmetTutari * 0.10; // KDV %10
$toplamTutar = $malHizmetTutari + $kdvTutari;

// TCPDF nesnesi oluştur
$pdf = new TCPDF('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Fatura Tasarımı
$pdf->SetFont('helvetica', '', 10);

// Gönderici bilgisi
$pdf->Cell(0, 10, 'Fatura', 0, 1, 'C');
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Gönderici Bilgileri:', 0, 1);
$pdf->MultiCell(0, 5, "Yunus Emre AYDIN\nBaraj Mah. Kırçiçeği Cad. 1A+2A Blok 104 A\nKepez / ANTALYA", 0, 'L');

// Alıcı Bilgileri
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Alıcı Bilgileri:', 0, 1);
$pdf->MultiCell(0, 5, "{$order['musteri_ismi']}\n{$order['musteri_adresi']}\n{$order['musteri_ilce']} / {$order['musteri_il']}\nTel: {$order['musteri_telefonu']}", 0, 'L');

// Ürün Detayları
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Ürün Detayları:', 0, 1);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(60, 10, 'Malzeme/Hizmet Açıklaması', 1, 0, 'C', 1);
$pdf->Cell(30, 10, 'Birim Fiyat', 1, 0, 'C', 1);
$pdf->Cell(30, 10, 'Mal Hizmet Tutarı', 1, 0, 'C', 1);
$pdf->Cell(30, 10, 'KDV', 1, 0, 'C', 1);
$pdf->Cell(30, 10, 'Toplam', 1, 1, 'C', 1);

$pdf->Cell(60, 10, 'Ürün: ' . $order['urunler'], 1);
$pdf->Cell(30, 10, number_format($birimFiyat, 2) . ' ₺', 1, 0, 'R');
$pdf->Cell(30, 10, number_format($malHizmetTutari, 2) . ' ₺', 1, 0, 'R');
$pdf->Cell(30, 10, number_format($kdvTutari, 2) . ' ₺', 1, 0, 'R');
$pdf->Cell(30, 10, number_format($toplamTutar, 2) . ' ₺', 1, 1, 'R');

// Barkod (Sayfanın altına ortalanacak şekilde)
$pdf->Ln(20);
$pdf->SetY(-40); // Sayfanın altına yerleştir
$style = [
    'align' => 'C',
    'stretch' => true,
    'fitwidth' => true,
    'fgcolor' => [0, 0, 0],
    'bgcolor' => false,
    'text' => true,
    'fontsize' => 10,
];
$pdf->write1DBarcode($order['kargo_barkodu'], 'C128', '', '', '', 18, 0.4, $style, 'N');

// Çıktı tamponunu temizle
ob_end_clean();

// PDF çıktısını göster
$pdf->Output('fatura.pdf', 'I');
?>
