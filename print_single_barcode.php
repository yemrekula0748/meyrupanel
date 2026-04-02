<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'DB.php';
require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/autoload.php';
require_once '/home/meyrupanel/htdocs/meyrupanel.com.tr/meyrupanel/vendor/tecnickcom/tcpdf/tcpdf.php';
if (class_exists('TCPDF')) {
    echo 'TCPDF yüklü!<br>';
    echo 'TCPDF Versiyonu: ' . TCPDF::VERSION . '<br>';
} else {
    echo 'TCPDF yüklü değil.';
}

$db = new DB();

// SQL Sorgusu
$sql = "SELECT musteri_ismi, musteri_adresi, musteri_telefonu, kargo_barkodu 
        FROM siparisler 
        WHERE barkod_basilma_durumu = 'Basılmamış' 
          AND kargo IN ('Bedelsiz', 'Ücreti Alıcıdan') 
          AND kargo_barkodu IS NOT NULL";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    // TCPDF Ayarları
    $pdf = new TCPDF('P', 'mm', 'A6'); // A6 boyutunda (105 x 148 mm)
    $pdf->SetMargins(5, 5, 5);
    $pdf->SetAutoPageBreak(true, 10);

    // Her bir sipariş için çıktı oluştur
    while ($row = $result->fetch_assoc()) {
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        // Gönderici bilgileri
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 5, 'GÖNDERİCİ:', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(0, 5, "MeyruKids\nvarsak Süleyman Demirel bulvarı \n Karşıyaka mahallesi\nno 137/a \nKepez / ANTALYA\nBEDELSİZ", 0, 'L');

        // Alıcı bilgileri
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 5, 'ALICI:', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(0, 5, $row['musteri_ismi'] . "\n" . "TELEFON: " . $row['musteri_telefonu'], 0, 'L');

        // Adres bilgisi
        $pdf->Ln(5);
        $pdf->MultiCell(0, 5, "ADRES: " . $row['musteri_adresi'], 0, 'L');

        // Barkod
        $pdf->Ln(10);
        $style = [
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false,
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
        ];
        $pdf->write1DBarcode($row['kargo_barkodu'], 'C128', '', '', '', 20, 0.4, $style, 'N');

        // Barkod numarası
        $pdf->Ln(2);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, $row['kargo_barkodu'], 0, 1, 'C');

        // Kargo firması
        $pdf->Ln(2);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 5, 'PTT', 0, 1, 'C');

        // Güncelleme
        $update_sql = "UPDATE siparisler SET barkod_basilma_durumu = 'Basılmış' WHERE kargo_barkodu = ?";
        $db->query($update_sql, [$row['kargo_barkodu']], 's');
    }

    // PDF çıktısı
    $pdf->Output('barkodlar.pdf', 'I'); // Tarayıcıda açmak için 'I', indirmek için 'D' kullanın.
} else {
    echo "Kriterlere uygun kayıt bulunamadı.";
}
?>
