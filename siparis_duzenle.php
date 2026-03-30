<?php
require 'DB.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $musteri_ismi = $_POST['musteri_ismi'];
    $musteri_telefonu = $_POST['musteri_telefonu'];
    $musteri_adresi = $_POST['musteri_adresi'];
    $siparis_tarihi = $_POST['siparis_tarihi'];
    $odeme_sarti = $_POST['odeme_sarti'];
    $urunler = $_POST['urunler'];
    $yonetici_notu = $_POST['yonetici_notu'];
    $kargo = $_POST['kargo'];
    $faturalandirma_durumu = $_POST['faturalandirma_durumu'];
    $barkod_basilma_durumu = $_POST['barkod_basilma_durumu'];

    $db = new DB();
    $sql = "UPDATE siparisler SET 
                musteri_ismi = '$musteri_ismi',
                musteri_telefonu = '$musteri_telefonu',
                musteri_adresi = '$musteri_adresi',
                siparis_tarihi = '$siparis_tarihi',
                odeme_sarti = '$odeme_sarti',
                urunler = '$urunler',
                yonetici_notu = '$yonetici_notu',
                kargo = '$kargo',
                faturalandirma_durumu = '$faturalandirma_durumu',
                barkod_basilma_durumu = '$barkod_basilma_durumu'
            WHERE id = $id";

    if ($db->query($sql)) {
        header("Location: girilen_siparisler.php?status=success&message=Güncelleme başarılı");
    } else {
        header("Location: girilen_siparisler.php?status=error&message=Güncelleme başarısız");
    }
}
?>
