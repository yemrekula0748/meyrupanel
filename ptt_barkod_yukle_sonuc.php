<?php
require 'vendor/autoload.php'; // PhpSpreadsheet kütüphanesini dahil edin
require 'DB.php'; // Veritabanı bağlantı dosyanızı dahil edin

use PhpOffice\PhpSpreadsheet\IOFactory;

session_start(); // Oturum başlat

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

        // Mesajı oturumda sakla
        $_SESSION['message'] = "$addedCount barkod başarıyla eklendi!";
        header("Location: barkodlar.php"); // Barkodlar sayfasına yönlendir
        exit;
    } catch (Exception $e) {
        $_SESSION['message'] = "Hata: " . $e->getMessage();
        header("Location: barkodlar.php"); // Hata durumunda bile yönlendir
        exit;
    }
} else {
    $_SESSION['message'] = "Dosya yüklenmedi veya yanlış bir istek yapıldı.";
    header("Location: barkodlar.php"); // Hatalı yükleme için yönlendirme
    exit;
}
?>
