<?php
require 'DB.php'; // Veritabanı bağlantısı dosyasını dahil ediyoruz.

$db = new DB(); // DB sınıfından bir nesne oluşturuyoruz.

// Gelen ID'yi alıyoruz ve kontrol ediyoruz.
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Siparişi resmileştirme işlemi
    $sql = "UPDATE siparisler SET resmilestir = 1 WHERE id = ?";
    try {
        $db->query($sql, [$id], "i"); // Parametreyi hazırlıklı sorgu ile gönderiyoruz.

        // Eğer sorgu başarılı olduysa, resmilestir_cron.php sayfasına istek gönderiyoruz.
        $cronUrl = "https://semre.hpanel.com.tr/resmilestir_cron.php";
        $ch = curl_init($cronUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // Yanıt beklenmiyor.
        curl_setopt($ch, CURLOPT_HEADER, false); // HTTP başlıklarını alma.
        curl_setopt($ch, CURLOPT_NOBODY, true); // Yanıt gövdesini almadan gönder.
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Maksimum 1 saniye bekle.
        curl_exec($ch);
        curl_close($ch);

        echo "Resmileştirme talebi alındı, e-arşiv kuyruğuna eklendi.";
    } catch (Exception $e) {
        echo "Hata: " . $e->getMessage();
    }
} else {
    echo "Hata: Geçersiz ID değeri.";
}
