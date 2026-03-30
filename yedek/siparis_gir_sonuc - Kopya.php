<?php
// Veritabanı bağlantısını içe aktar
require_once 'DB.php'; // DB sınıfını çağırdığınız dosyanın yolu

// DB sınıfını kullanarak veritabanı bağlantısı oluştur
$db = new DB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al ve gerekli doğrulamaları yap
    $siparis_tarihi = $db->escape($_POST['siparis_tarihi']);
    $musteri_ismi = $db->escape($_POST['musteri_ismi']);
    $musteri_telefonu = $db->escape($_POST['musteri_telefonu']);
    $musteri_adresi = $db->escape($_POST['musteri_adresi']);
    $musteri_il = $db->escape($_POST['Iller']); // İl verisi
    $musteri_ilce = $db->escape($_POST['Ilceler']); // İlçe verisi
    $odeme_sarti = $db->escape($_POST['odeme_sarti']);
    $urunler = $db->escape($_POST['urunler']);
    $desi = $db->escape($_POST['desi']);
    $agirlik = $db->escape($_POST['agirlik']);
    $kargo = $db->escape($_POST['kargo']);
	$yoneticinotu = $db->escape($_POST['yoneticinotu']);
    $faturalandirma_durumu = $db->escape($_POST['faturalandirma_durumu']);
    $barkod_basilma_durumu = $db->escape($_POST['barkod_basilma_durumu']);

    // SQL sorgusu
    $sql = "INSERT INTO siparisler (
                siparis_tarihi, musteri_ismi, musteri_telefonu, musteri_adresi, 
                musteri_il, musteri_ilce, odeme_sarti, urunler, desi, agirlik, 
                kargo,yonetici_notu , faturalandirma_durumu, barkod_basilma_durumu
            ) VALUES (
                '$siparis_tarihi', '$musteri_ismi', '$musteri_telefonu', '$musteri_adresi', 
                '$musteri_il', '$musteri_ilce', '$odeme_sarti', '$urunler', $desi, $agirlik, 
                '$kargo', '$yoneticinotu','$faturalandirma_durumu', '$barkod_basilma_durumu'
            )";

    // Sorguyu çalıştır ve sonuçları kontrol et
    if ($db->query($sql)) {
        echo "<div style='color: green;'>Sipariş başarıyla eklendi! Sipariş Ekleme Sayfasına Yönlendiriliyorsunuz.</div>";
        header("Refresh: 2; url=siparis_gir.php"); // Form sayfasına yönlendir
        exit();
    } else {
        echo "<div style='color: red;'>Hata: Sipariş eklenemedi. Lütfen tekrar deneyin.</div>";
    }
} else {
    echo "<div style='color: red;'>Geçersiz istek yöntemi.</div>";
}
?>
