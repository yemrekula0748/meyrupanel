<?php
// Veritabanı bağlantısı
include 'DB.php';
$db = new DB();

// Siparişlerden uygun kaydı çek
$query = "SELECT * FROM siparisler WHERE kargo = 'Ödeme Şartlı' AND kargo_cron = 0 LIMIT 20";
$result = $db->query($query);

if ($result->num_rows > 0) {
    $siparis = $result->fetch_assoc();

    // Hepsijet tablosundan token al
    $hepsijetQuery = "SELECT token FROM hepsijet WHERE id = 1";
    $hepsijetResult = $db->query($hepsijetQuery);
    $hepsijet = $hepsijetResult->fetch_assoc();
    $authToken = $hepsijet['token'];

    // SMR Kodu Oluştur
    $randomNumber = rand(10000, 999999);
    $date = date("dmy");
    $customerDeliveryNo = "SMR{$date}{$randomNumber}";

    // Tarihi 3 gün sonrasına ayarla
    $deliveryDateOriginal = date('Y-m-d', strtotime('+3 days'));

    // İsimleri ayır
    $musteriIsim = explode(' ', $siparis['musteri_ismi']);
    $firstName = $musteriIsim[0];
    $lastName = isset($musteriIsim[1]) ? $musteriIsim[1] : '';

    // Ödeme Şartlı Tutarı
    $odemeTutari = $siparis['odeme_sarti'] * 100;

    // API isteği için JSON oluştur
    $payload = json_encode([
        "company" => [
            "name" => "SEMRE BUTIK",
            "abbreviationCode" => "SMRBTK"
        ],
        "serviceType" => ["POD"],
        "delivery" => [
            "customerDeliveryNo" => $customerDeliveryNo,
            "customerOrderId" => $customerDeliveryNo,
            "deliveryDateOriginal" => $deliveryDateOriginal,
            "totalParcels" => "1",
            "desi" => "1",
            "deliverySlotOriginal" => "0",
            "deliveryType" => "RETAIL",
            "product" => [
                "productCode" => "HX_STD"
            ],
            "senderAddress" => [
                "companyAddressId" => "semr-smrbtk-703",
                "country" => ["name" => "Türkiye"],
                "city" => ["name" => "Antalya"],
                "town" => ["name" => "Kepez"],
                "district" => ["name" => "Baraj"],
                "addressLine1" => "Kuştepe Mah. Mecidiyeköy Yolu Cad. Trump Towers Kule 2 Kat:3 No:14 34387 Şişli / İstanbul"
            ],
            "receiver" => [
                "companyCustomerId" => $customerDeliveryNo,
                "firstName" => $firstName,
                "lastName" => $lastName,
                "phone1" => $siparis['musteri_telefonu'],
                "email" => "yemrekula0748@gmail.com"
            ],
            "recipientAddress" => [
                "companyAddressId" => $customerDeliveryNo,
                "country" => ["name" => "Türkiye"],
                "city" => ["name" => $siparis['musteri_il']],
                "town" => ["name" => $siparis['musteri_ilce']],
                "district" => ["name" => $siparis['musteri_ilce']],
                "addressLine1" => $siparis['musteri_adresi']
            ],
            "recipientPerson" => $siparis['musteri_ismi'],
            "recipientPersonPhone1" => $siparis['musteri_telefonu']
        ],
        "deliveryAmountList" => [
            [
                "amount" => $odemeTutari,
                "description" => "Hizmet Bedeli",
                "type" => "SERVICE_AMOUNT",
                "currency" => "TRY"
            ]
        ]
    ]);

    // CURL ile API isteği
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://integration.hepsijet.com/delivery/sendDeliveryOrderEnhanced',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            "X-Auth-Token: $authToken",
            "Content-Type: application/json",
            "Authorization: Basic c2VtcmVidXRpa19pbnRlZ3JhdGlvbjphZG1pbjEyMw=="
        ],
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpCode == 200) {
        // Başarılı durumda mevcut işlemler
        $updateQuery = "UPDATE siparisler SET kargo_barkodu = ?, kargo_cron = 1 WHERE id = ?";
        $db->query($updateQuery, [$customerDeliveryNo, $siparis['id']], 'si');
        echo "Gönderi başarıyla oluşturuldu: $customerDeliveryNo";
    } else {
        // Hata durumunda PTT'ye yönlendir
        $updateQuery = "UPDATE siparisler SET hangikargo = 'MeyruKids' WHERE id = ?";
        $db->query($updateQuery, [$siparis['id']], 'i');
        
        error_log("Hepsijet Hata (ID: {$siparis['id']}): HTTP Kodu: $httpCode, Yanıt: $response");
        echo "Gönderi PTT'ye yönlendirildi. HTTP Kodu: " . $httpCode . "\n";
    }
} else {
    echo "Koşullara uyan kayıt bulunamadı.";
}
?>
