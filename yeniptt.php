<?php

require 'vendor/autoload.php'; // Composer ile yüklenen autoload

use Ahmeti\PttKargoApi\PttVeriYukle2;

try {
    // PTT API istemcisi
    $ptt = (new PttVeriYukle2())
        ->kullanici('PttWs') // Kullanıcı adı
        ->sifre('sGG62WGcR7X3kjRJTJbHQ') // Şifre
        ->musteriId('402955632') // Müşteri ID
        ->dosyaAdi('dosyaAdi-' . date('Ymd-His')) // Dinamik dosya adı
        ->gonderiTur('KARGO') // Gönderi türü
        ->gonderiTip('NORMAL'); // Gönderi tipi

    // Kargo bilgilerini ekle
    $row = [
        'aAdres' => 'Test Mahallesi, Test Sokak No:1, Test Şehir',
        'aliciAdi' => 'Ali Veli',
        'aliciIlAdi' => 'Ankara',
        'aliciIlceAdi' => 'Çankaya',
        'barkodNo' => '1234567890123',
        'odeme_sart_ucreti' => 50.00,
        'aliciSms' => '5551234567',
    ];

    $ptt->aAdres($row['aAdres'])
        ->aliciAdi($row['aliciAdi'])
        ->aliciIlAdi($row['aliciIlAdi'])
        ->aliciIlceAdi($row['aliciIlceAdi'])
        ->barkodNo($row['barkodNo'])
        ->agirlik(430)
        ->boy(1)
        ->en(1)
        ->yukseklik(1)
        ->desi(1)
        ->gondericiBilgi([
            'gonderici_adi' => 'S. AYDIN TEKSTİL GİYİM',
            'gonderici_adresi' => 'Baraj mahallesi kırçiçeği caddesi 1A+2A blok no 104/A',
            'gonderici_il_ad' => 'Antalya',
            'gonderici_ilce_ad' => 'Kepez'
        ])
        ->odeme_sart_ucreti($row['odeme_sart_ucreti'])
        ->rezerve1('14086565')
        ->rezerve2('14086565')
        ->aliciSms($row['aliciSms'])
        ->ekhizmet('OS')
        ->ekle();

    // Veriyi yükle
    $result = $ptt->yukle();

    // Sonuçları kontrol et
    if (is_array($result) && $result['hataKodu'] == 1) {
        echo "Kargo gönderimi başarılı:\n";
        print_r($result);

        foreach ($result['dongu'] as $barcode) {
            echo "Barkod No: " . $barcode . "\n";
        }

    } else {
        echo "Hata: " . $result['aciklama'] . "\n";
    }
} catch (Exception $e) {
    echo "Bir hata oluştu: " . $e->getMessage();
}
