<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // PTT SOAP WSDL Adresi
    $wsdl = "https://pttws.ptt.gov.tr/PttVeriYukleme/services/Sorgu?wsdl";
    $client = new SoapClient($wsdl, ['trace' => true]);

    // Veritabanı bağlantısı
    require_once 'DB.php'; // Veritabanı bağlantısını içerir.
    $db = new DB();

    // 1. Adım: `siparisler` ve `ptt_kargo_barkodlari` tablosundaki verileri al
    $query = "
        SELECT 
            s.id AS siparis_id,
            s.musteri_ismi AS aliciAdi,
            s.musteri_il AS aliciIlAdi,
            s.musteri_ilce AS aliciIlceAdi,
            s.musteri_adresi AS aAdres, -- Müşteri adresini alıyoruz
            s.odeme_sarti AS odeme_sart_ucreti,
            s.musteri_telefonu AS aliciSms,
            b.kod AS barkodNo,
            b.id AS barkod_id
        FROM siparisler s
        INNER JOIN ptt_kargo_barkodlari b ON b.durum = 0
        WHERE s.kargo_cron = 0 
          AND s.hangikargo = 'MeyruKids' 
          AND s.kargo = 'Ödeme Şartlı'
        LIMIT 1
    ";

    $result = $db->query($query);
    if ($result->num_rows == 0) {
        die("İşlenecek kayıt bulunamadı.");
    }

    // 2. Adım: Her bir kayıt için SOAP isteğini gönder
    while ($row = $result->fetch_assoc()) {
        // Rastgele dosya adı oluştur
        $randomNumber = rand(10000000, 99999999);
        $currentDate = date('Ymd');
        $dosyaAdi = $randomNumber . $currentDate;

        // SOAP verisini hazırla
        $data = [
            'input' => [
                'dongu' => [
                    'aAdres' => $row['aAdres'], // Müşteri adresini buraya ekliyoruz
                    'aliciAdi' => $row['aliciAdi'],
                    'aliciIlAdi' => $row['aliciIlAdi'],
                    'aliciIlceAdi' => $row['aliciIlceAdi'],
                    'barkodNo' => $row['barkodNo'],
                    'agirlik' => 430, // Varsayılan değer
                    'boy' => 1,
                    'en' => 1,
                    'yukseklik' => 1,
                    'desi' => 1,
                    'gondericiBilgi' => [
                        'gonderici_adi' => 'Merve İNCİR - MEYRUKİDS',
                        'gonderici_adresi' => 'Varsak Süleyman Demirel Bulvarı , Karşıyaka mahallesi no:137/A kepez/Antalya',
                        'gonderici_il_ad' => 'Antalya',
                        'gonderici_ilce_ad' => 'Kepez'
                    ],
                    'odeme_sart_ucreti' => $row['odeme_sart_ucreti'],
                    'rezerve1' => '18038415',
                    'rezerve2' => '18038415',
                    'aliciSms' => $row['aliciSms'],
                    'ekhizmet' => 'OS'
                ],
                'dosyaAdi' => $dosyaAdi,
                'gonderiTip' => 'NORMAL',
                'gonderiTur' => 'KARGO',
                'kullanici' => 'PttWs',
                'sifre' => 'JQkoqaMXDzK0I1YIHR4jA',
                'musteriId' => '704992342'
            ]
        ];

        try {
            // SOAP isteğini gönder
            $response = $client->__soapCall('kabulEkle2', [$data]);

            // Dönen yanıttan linki al
            $trackingLink = $response->return->dongu->donguAciklama;

            // Başarılı işlem sonrası veritabanını güncelle
            $db->query("UPDATE siparisler SET kargo_cron = 1, kargo_barkodu = '{$row['barkodNo']}', kargolink = '$trackingLink' WHERE id = {$row['siparis_id']}");
            $db->query("UPDATE ptt_kargo_barkodlari SET durum = 1 WHERE id = {$row['barkod_id']}");
        } catch (SoapFault $e) {
            echo "Hata: {$e->getMessage()} (Sipariş ID: {$row['siparis_id']})\n";
        }
    }

    echo "İşlem tamamlandı.";

} catch (Exception $e) {
    echo "Genel hata: " . $e->getMessage();
}
?>
