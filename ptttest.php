<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php';

use Ahmeti\PttKargoApi\PttVeriYukle2;

$ptt = (new PttVeriYukle2())->kullanici('PttWs')
    ->sifre(env('J3fbefKlzi5oBOfMsWQ'))
    ->musteriId(env('703083141'))
    ->dosyaAdi(date('Ymd-His-') . uniqid())
    ->gonderiTur('KARGO')
    ->gonderiTip('NORMAL');

// Sabit değerler
$adSoyad = "yunus emre kula";
$adres = "yeni emek mahallesi 2589 sokak no 14 daire 1 tüzün apartmanı";
$il = "Antalya";
$ilce = "Kepez";
$desi = "2";
$barkodNo = "KP07063498218";

// Bilgileri set et
$ptt->aAdres($adres)
    ->agirlik($desi) // Desi değerini ağırlık olarak kabul ettik
    ->aliciAdi($adSoyad)
    ->aliciIlAdi($il)
    ->aliciIlceAdi($ilce)
    ->aliciSms("5436763863") // Opsiyonel: Sabit bir telefon numarası
    ->barkodNo($barkodNo)
    ->boy("10") // Örnek değer
    ->deger_ucreti("50.00") // Örnek değer
    ->desi($desi)
    ->ekhizmet("Sigorta") // Opsiyonel: Ek hizmet
    ->en("10") // Örnek değer
    ->musteriReferansNo("SABITREF12345") // Sabit bir referans numarası
    ->odemesekli("Gönderici Öder")
    ->odeme_sart_ucreti("0") // Ödeme şartı yok
    ->rezerve1(null)
    ->yukseklik("10") // Örnek değer
    ->ekle();

// Veriyi yükle
$result = $ptt->yukle();

// Sonucu kontrol et
if (is_array($result) && $result['hataKodu'] == 1) {
    echo "Kargo başarıyla gönderildi. Barkodlar:\n";
    print_r($result['dongu']);
} else {
    echo "Hata oluştu: " . $result['aciklama'];
}
	