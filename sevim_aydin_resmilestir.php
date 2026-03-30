<?php
// Gerekli değişkenler
$company_id = '438580'; // Şirket ID'nizi buraya ekleyin
$api_url = "https://api.parasut.com/v4/$company_id/e_archives";

// API erişim tokeni
$access_token = 'nM2UhFf_dzBbQyBZWSBbBLeCTKF_PGdpsh7DordfyG4'; // OAuth tokeninizi buraya ekleyin

// Gönderilecek veri
$data = [
    "data" => [
                "type" => "e_archives",
                "relationships" => [
                    "sales_invoice" => [
                        "data" => [
                            "id" => "233479707", //invoice_id
                            "type" => "sales_invoices"
                            ]
                        ]
                    ]
                ]
           
];

// cURL oturumu başlat
$ch = curl_init($api_url);

// cURL ayarları
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// API'ye isteği gönder
$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// cURL oturumunu kapat
curl_close($ch);

// Sonuçları kontrol et
if ($http_status == 201) {
    echo "Fatura başarıyla resmileştirildi: " . $response;
} else {
    echo "Hata oluştu! HTTP Durum Kodu: $http_status\n";
    echo "Hata Detayı: " . $response;
}
