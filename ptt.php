<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



require_once __DIR__ . '/vendor/ahmeti/ptt-kargo-api/src/PttVeriYukle2.php';

use Ahmeti\PttKargoApi\PttVeriYukle2;

$ptt = (new PttVeriYukle2())
    ->kullanici('pttWs')
    ->sifre('J3fbefKlzi5oBOfMsWQ')
    ->musteriId('703083141')
    ->dosyaAdi(date('Ymd-His-') . uniqid())
    ->gonderiTur('KARGO')
    ->gonderiTip('NORMAL');

$items = [
    (object)[
        'aAdres' => 'yeni emek mahallesi 2589 sokak no 14 daire 1 tüzün apartmanı',
        'agirlik' => '5',
        'aliciAdi' => 'Yunus Emre KULA',
        'aliciIlAdi' => 'Antalya',
        'aliciIlceAdi' => 'Kepez',
        'aliciSms' => '05551234567',
        'barkodNo' => 'KP07063498232',
        'boy' => '1',
        'deger_ucreti' => '100',
        'desi' => '1',
        'ek_hizmet' => '',
        'en' => '20',
        'musteriReferansNo' => 'REF123',
        'odemesekli' => 'Gönderici Ödemeli',
        'odeme_sart_ucreti' => '0',
        'rezerve1' => '',
        'yukseklik' => '1',
    ]
];

foreach ($items as $item) {
    $ptt->aAdres($item->aAdres)
        ->agirlik($item->agirlik)
        ->aliciAdi($item->aliciAdi)
        ->aliciIlAdi($item->aliciIlAdi)
        ->aliciIlceAdi($item->aliciIlceAdi)
        ->aliciSms($item->aliciSms)
        ->barkodNo($item->barkodNo)
        ->boy($item->boy)
        ->deger_ucreti($item->deger_ucreti)
        ->desi($item->desi)
        ->ekhizmet($item->ek_hizmet)
        ->en($item->en)
        ->musteriReferansNo($item->musteriReferansNo)
        ->odemesekli($item->odemesekli)
        ->odeme_sart_ucreti($item->odeme_sart_ucreti)
        ->rezerve1($item->rezerve1)
        ->yukseklik($item->yukseklik)
        ->ekle();
}

$result = $ptt->yukle();

if (is_array($result) && $result['hataKodu'] == 1) {
    echo "Başarılı! Gönderilen barkodlar:";
    print_r($result['dongu']);
} else {
    echo "Hata: " . $result['aciklama'];
}
