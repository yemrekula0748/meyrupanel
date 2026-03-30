<?php
require_once 'DB.php';

// 1) Veritabanından en son kaydettiğimiz Access Token'ı alalım
$db = new DB();
$sql = "SELECT parasut_token_sevimaydin FROM parasut_token WHERE id = 1 LIMIT 1";
$result = $db->query($sql);
$accessToken = $result[0]['parasut_token_sevimaydin'] ?? null;

if(!$accessToken) {
    die("Access token bulunamadı. Önce token almayı deneyin.");
}

// 2) Şirket ID’niz
$company_id = "438580";

// 3) Satış faturası oluşturma endpoint’i
$api_url = "https://api.parasut.com/v4/$company_id/sales_invoices";

// 4) POST edilecek data (Basit örnek)
$data = [
    "data" => [
        "type" => "sales_invoices",
        "attributes" => [
            "description" => "API ile oluşturuldu (örnek)",
            "document_date" => date('Y-m-d'),  // bugünün tarihi
            "document_number" => "API-TEST-001", // Fatura No
            "currency" => "TRL",
            "withholding_rate" => 0
        ]
        // relationships (contact, details vs.) eklenebilir
    ]
];

// 5) cURL isteği
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 6) Sonuç kontrolü
if ($http_status == 201) {
    // Başarılı
    $jsonData = json_decode($response, true);
    $sales_invoice_id = $jsonData['data']['id']; // Parasüt’ün döndürdüğü fatura ID

    echo "Satış faturası oluşturuldu! ID: " . $sales_invoice_id;

    // Şimdi bu invoice_id'yi (sales_invoice_id) kendi siparisler tablonuza kaydedelim.
    // Örneğin, siparis_id = 123 olan kaydı güncelleyelim.
    // Ya da yeni sipariş tablonuza ekliyor olabilirsiniz.
    $siparisId = 123; // Bu size bağlı, hangi siparişle eşleşiyorsa orayı set edeceksiniz
    
    $sqlUpdate = "UPDATE siparisler SET parasut_sales_invoice_id = ? WHERE id = ?";
    $paramsUpdate = [$sales_invoice_id, $siparisId];
    $typesUpdate = "si"; // 1. param string (sales_invoice_id), 2. param int (siparisId)

    try {
        $db->query($sqlUpdate, $paramsUpdate, $typesUpdate);
        echo "\nSiparisler tablosunda parasut_sales_invoice_id başarıyla güncellendi.";
    } catch (Exception $e) {
        echo "\nSiparisler tablosuna yazarken hata oluştu: " . $e->getMessage();
    }

} else {
    echo "Satış faturası oluşturulurken hata oluştu! HTTP Kod: $http_status\n";
    echo "Yanıt: " . $response;
}
?>
