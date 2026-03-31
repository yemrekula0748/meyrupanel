<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
require_once 'DB.php';

function sendDeliveryOrder() {
    try {
        $db = new DB();

        // Hepsijet tablosundan token alınıyor
        $hepsijetQuery = "SELECT token FROM hepsijet WHERE id = 1 LIMIT 1";
        $hepsijetResult = $db->query($hepsijetQuery);
        $hepsijetToken = $db->fetchAssoc($hepsijetResult)['token'];

        if (!$hepsijetToken) {
            echo "Hepsijet token bulunamadı.";
            return;
        }

        // Sipariş tablosundan koşula uygun ilk kaydı alıyoruz
        $query = "SELECT * FROM siparisler WHERE kargo_cron = 0 AND hangikargo = 'Yunus Emre - Hepsijet' AND (kargo = 'Bedelsiz' OR kargo = 'Ücreti Alıcıdan') LIMIT 1";
        $result = $db->query($query);
        $order = $db->fetchAssoc($result);

        if (!$order) {
            echo "İşlem yapılacak uygun sipariş bulunamadı.";
            return;
        }

        // Random sayı ve tarih kombinasyonu ile numara oluşturma
        $randomNumber = rand(10000, 99999);
        $customerDeliveryNo = "SMR" . date('dmy') . $randomNumber ;

        // API verilerini hazırlama
        $url = "https://integration.hepsijet.com/delivery/sendDeliveryOrderEnhanced";

        $headers = [
            "X-Auth-Token: " . $hepsijetToken,
            "Content-Type: application/json",
            "Authorization: Basic 0dd78986-4fdd-4927-ab9e-79a8618adc66"
        ];

        $data = [
            "company" => [
                "name" => "SEMRE BUTIK",
                "abbreviationCode" => "SMRBTK"
            ],
            "delivery" => [
                "customerDeliveryNo" => $customerDeliveryNo,
                "customerOrderId" => $customerDeliveryNo,
                "totalParcels" => "1",
                "desi" => "1",
                "deliverySlotOriginal" => "0",
                "deliveryDateOriginal" => date('Y-m-d'),
                "deliveryType" => "RETAIL",
                "product" => [
                    "productCode" => "HX_STD"
                ],
                "senderAddress" => [
                    "companyAddressId" => "semr-smrbtk-703",
                    "country" => [
                        "name" => "Türkiye"
                    ],
                    "city" => [
                        "name" => "Antalya"
                    ],
                    "town" => [
                        "name" => "KEPEZ"
                    ],
                    "district" => [
                        "name" => "BARAJ"
                    ],
                    "addressLine1" => "BARAJ MAH. KIRC CAD. 1A+2A BLOK NO: 104 A KEPEZ/ ANTALYA"
                ],
                "receiver" => [
                    "companyCustomerId" => $customerDeliveryNo,
                    "firstName" => explode(' ', $order['musteri_ismi'])[0],
                    "lastName" => explode(' ', $order['musteri_ismi'])[1] ?? '',
                    "phone1" => $order['musteri_telefonu'],
                    "email" => $order['musteri_email'] ?? ''
                ],
                "recipientAddress" => [
                    "companyAddressId" => $customerDeliveryNo,
                    "country" => [
                        "name" => "Türkiye"
                    ],
                    "city" => [
                        "name" => $order['musteri_il']
                    ],
                    "town" => [
                        "name" => $order['musteri_ilce']
                    ],
                    "district" => [
                        "name" => $order['musteri_ilce']
                    ],
                    "addressLine1" => $order['musteri_adresi']
                ],
                "recipientPerson" => $order['musteri_ismi'],
                "recipientPersonPhone1" => $order['musteri_telefonu']
            ],
            "currentXDock" => [
                "abbreviationCode" => "SMRBTKKEPEZ"
            ]
        ];

        $payload = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == 200) {
            // Başarılı durumda mevcut kod
            $updateQuery = "UPDATE siparisler SET kargo_barkodu = ?, kargo_cron = 1 WHERE id = ?";
            $db->query($updateQuery, [$customerDeliveryNo, $order['id']], 'si');
            echo "Gönderi başarıyla iletildi!\n";
            echo "Yanıt: " . $response;
        } else {
            // Hata durumunda PTT'ye yönlendir
            $updateQuery = "UPDATE siparisler SET hangikargo = 'MeyruKids' WHERE id = ?";
            $db->query($updateQuery, [$order['id']], 'i');
            
            error_log("Hepsijet Hata (ID: {$order['id']}): HTTP Kodu: $httpcode, Yanıt: $response");
            echo "Gönderi PTT'ye yönlendirildi. HTTP Kodu: " . $httpcode . "\n";
        }
    } catch (Exception $e) {
        // Hata durumunda siparişi PTT'ye yönlendir
        if (isset($order['id'])) {
            $updateQuery = "UPDATE siparisler SET hangikargo = 'MeyruKids' WHERE id = ?";
            $db->query($updateQuery, [$order['id']], 'i');
            error_log("Hepsijet Bedelsiz Hata: " . $e->getMessage());
        }
        return false;
    }
}

sendDeliveryOrder();

?>
