<?php
require 'DB.php'; // DB sınıfını içeri aktar

$db = new DB(); // Veritabanı bağlantısını başlat

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Güncellenecek müşteri isimlerini al
        $query = "SELECT musteri_ismi FROM siparisler WHERE kargo = 'Ödeme Şartlı' AND resmimi = 0 AND islem = 0 LIMIT 50";

        $result = $db->query($query);

        $musteriler = [];
        while ($row = $db->fetchAssoc($result)) {
            $musteriler[] = $row['musteri_ismi'];
        }

        if (!empty($musteriler)) {
            // Kayıtları güncelle
            $updateQuery = "UPDATE siparisler SET resmimi = 1 WHERE kargo = 'Ödeme Şartlı' AND resmimi = 0 LIMIT 50";
            $db->query($updateQuery);

            // JSON formatında cevap gönder
            echo json_encode([
                'status' => 'success',
                'message' => 'Aşağıdaki ' . count($musteriler) . ' müşterinin faturası oluşturma emri ve resmileştirme emri verildi.',
                'musteriler' => $musteriler
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Resmileştirilecek uygun kayıt bulunamadı.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Hata: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Geçersiz istek!'
    ]);
}
?>
