<?php
require_once 'DB.php';

$db = new DB();
$sql = "SELECT id, musteri_ismi, kargo_barkodu, siparis_tarihi, musteri_telefonu, musteri_adresi, odeme_sarti, urunler, yonetici_notu, kargo, faturalandirma_durumu, barkod_basilma_durumu, musteri_il, musteri_ilce FROM siparisler";
$result = $db->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
