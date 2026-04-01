<?php
try {
    // PTT SOAP WSDL Adresi
    $wsdl = "https://pttws.ptt.gov.tr/PttVeriYukleme/services/Sorgu?wsdl";

    // SOAP İstemcisi
    $client = new SoapClient($wsdl, ['trace' => true]);

    // Random dosya adı oluşturma (8 haneli sayı + günün tarihi birleşik format)
    $randomNumber = rand(10000000, 99999999); // 8 haneli rastgele sayı
    $currentDate = date('Ymd'); // Günün tarihi: YYYYMMDD
    $dosyaAdi = $randomNumber . $currentDate;

    // Gönderilecek Veri
    $data = [
        'input' => [
            'dongu' => [
                'aAdres' => 'YENI EMEK MAHALLESI 2589 SOKAK NO 14 DAIRE 1 TUZUN APARTMANI',
                'aliciAdi' => 'Yunus Emre KULA',
                'aliciIlAdi' => 'ANTALYA',
                'aliciIlceAdi' => 'KEPEZ',
                'barkodNo' => 'KP07063498690',
                'agirlik' => 430,
                'boy' => 1,
                'en' => 1,
                'yukseklik' => 1,
				'desi' => 1,
                'gondericiBilgi' => [
                    'gonderici_adi' => 'Merve İncir - Meyrukids',
                    'gonderici_adresi' => 'varsak Süleyman Demirel bulvarı Karşıyaka mahallesi no 137 A kepez Antalya',
                    'gonderici_il_ad' => 'Antalya',
                    'gonderici_ilce_ad' => 'Kepez'
                ],
                'odeme_sart_ucreti' => 100.00, // Şartlı ödeme tutarı
                'rezerve1' => '15735142',      // Rezerve alanı 1
                'rezerve2' => '15735142',      // Rezerve alanı 2
                'aliciSms' => '5436763863',    // Alıcının SMS numarası
                'ekhizmet' => 'OS'             // Ek hizmet bilgisi
            ],
            'dosyaAdi' => $dosyaAdi,          // Random dosya adı
            'gonderiTip' => 'NORMAL',
            'gonderiTur' => 'KARGO',
            'kullanici' => 'PttWs',
            'sifre' => 'J3fbefKlzi5oBOfMsWQ',
            'musteriId' => '703083141'
        ]
    ];

    // SOAP Fonksiyonunu Çağır
    $response = $client->__soapCall('kabulEkle2', [$data]);

    // Yanıtı Yazdır
    echo "Sonuç: ";
    print_r($response);

} catch (SoapFault $e) {
    echo "Hata: {$e->getMessage()}";
}
?>
