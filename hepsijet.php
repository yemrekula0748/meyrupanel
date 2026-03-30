<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function sendDeliveryOrder() {
    $url = "https://integration.hepsijet.com/delivery/sendDeliveryOrderEnhanced";

    $headers = [
        "X-Auth-Token: aaf807a9-2cea-43be-8855-5db9fc06bb58",
        "Content-Type: application/json",
        "Authorization: Basic c2VtcmVidXRpa19pbnRlZ3JhdGlvbjoxNDk2OTUxMg=="
    ];

    $data = [
        "company" => [
            "name" => "SEMRE BUTIK",
            "abbreviationCode" => "SMRBTK"
        ],
        "delivery" => [
            "customerDeliveryNo" => "SMR900127122024",
            "customerOrderId" => "SMR900127122024",
            "totalParcels" => "1",
            "desi" => "2",
            "deliverySlotOriginal" => "0",
            "deliveryDateOriginal" => "2024-12-29",
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
                "companyCustomerId" => "SMR900127122024",
                "firstName" => "YUNUS EMRE",
                "lastName" => "KULA",
                "phone1" => "5436763863",
                "email" => "yemrekula0748@gmail.com"
            ],
            "recipientAddress" => [
                "companyAddressId" => "SMR900127122024",
                "country" => [
                    "name" => "Türkiye"
                ],
                "city" => [
                    "name" => "Antalya"
                ],
                "town" => [
                    "name" => "Kepez"
                ],
                "district" => [
                    "name" => "Kepez"
                ],
                "addressLine1" => "Yeni emek mahallesi 2589 sokak no 14 daire 1 tüzün apartmanı"
            ],
            "recipientPerson" => "Yunus Emre KULA",
            "recipientPersonPhone1" => "5555555555"
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

    return ['response' => $response, 'httpcode' => $httpcode];
}


$response = sendDeliveryOrder();


if ($response['httpcode'] == 200) {
    echo "Gönderi başarıyla iletildi!";
    echo "Yanıt: " . $response['response'];
} else {
    echo "Gönderi iletilirken hata oluştu. HTTP Kodu: " . $response['httpcode'];
    echo "Hata Yanıtı: " . $response['response'];
}

?>
