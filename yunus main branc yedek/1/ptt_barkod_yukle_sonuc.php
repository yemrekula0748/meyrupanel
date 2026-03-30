<?php
require 'vendor/autoload.php'; // PhpSpreadsheet kütüphanesini dahil edin
require 'DB.php'; // Veritabanı bağlantı dosyanızı dahil edin

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    // Geçici dosya yolu
    $fileTmpPath = $_FILES['excel_file']['tmp_name'];

    try {
        // Excel dosyasını yükle
        $spreadsheet = IOFactory::load($fileTmpPath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true); // Excel'deki tüm satırları oku

        // Veritabanı bağlantısını başlat
        $db = new DB();

        $addedCount = 0; // Eklenen barkod sayısını takip et
        foreach ($rows as $index => $row) {
            if ($index == 1) continue; // İlk satırı atla (Başlıklar varsa)

            $barkod = $db->escape(trim($row['A'])); // A sütunundan barkod oku
            if (!empty($barkod)) {
                // Barkodu tabloya ekle, durum = 0
                $db->query("INSERT INTO ptt_kargo_barkodlari (kod, durum) VALUES ('$barkod', 0)");
                $addedCount++;
            }
        }

        $message = "$addedCount barkod başarıyla eklendi!";
    } catch (Exception $e) {
        $message = "Hata: " . $e->getMessage();
    }
} else {
    $message = "Dosya yüklenmedi veya yanlış bir istek yapıldı.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTT Barkod Yükleme Sonucu</title>
</head>
<body>
    <h1>Yükleme Sonucu</h1>
    <p><?php echo $message; ?></p>
    <a href="ptt_barkod_yukle.php">Yeni dosya yükle</a>
</body>
</html>
